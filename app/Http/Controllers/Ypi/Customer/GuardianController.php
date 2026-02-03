<?php

namespace App\Http\Controllers\Ypi\Customer;

use App\Http\Controllers\Controller;
use App\Mail\NewRequestMail;
use App\Models\Ypi\ClientGroup;
use App\Models\Ypi\Event;
use App\Models\Ypi\Gender;
use App\Models\Ypi\Guardian;
use App\Models\Ypi\GuestType;
use App\Models\Ypi\Prefix;
use App\Models\Ypi\Nationality;
use App\Models\Ypi\Participant;
use App\Models\Ypi\ParticipantDocument;
use App\Models\Ypi\ParticipantType;
use App\Models\Ypi\SizeLookup;
use App\Models\Ypi\TempUpload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\Guard;

class GuardianController extends Controller
{
    //
    public function index()
    {
        $participants = Participant::all();
        $events = Event::all();
        $participant_types = ParticipantType::all();
        $genders = Gender::all();
        $nationalities = Nationality::all();
        $pant_sizes   = SizeLookup::type('pant')->get();
        $jersey_sizes = SizeLookup::type('jersey')->get();
        $shoe_sizes   = SizeLookup::type('shoe')->get();
        $jacket_sizes = SizeLookup::type('jacket')->get();

        // $guests = Guest::with('client', 'schedule_period', 'cargo', 'zone', 'status', 'driver')->get();

        return view('ypi.customer.guardian.list', compact(
            'participants',
            'events',
            'participant_types',
            'genders',
            'nationalities',
            'pant_sizes',
            'jersey_sizes',
            'jacket_sizes',
            'shoe_sizes'
        ));
    }

    private function commitFilepondUploads(array $serverIds, int $model_id, string $category = 'qid'): void
    {
        foreach ($serverIds as $id) {
            Log::info('GuardianController::commitFilepondUploads: Committing FilePond upload with temp ID: ' . $id);
            $temp = TempUpload::where('path', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$temp) continue;

            $filename = basename($temp->path);
            $newPath = "uploads/participants/{$model_id}/{$filename}";

            Log::info('Moving temp file from ' . $temp->disk . ':' . $temp->path . ' to ' . $newPath);

            Storage::disk($temp->disk)->move($temp->path, $newPath);


            ParticipantDocument::create([
                'participant_id' => $model_id,
                'disk' => $temp->disk,
                'path' => $newPath,
                'original_name' => $temp->original_name,
                'mime' => $temp->mime,
                'size' => $temp->size,
                'created_by' => auth()->id(),
            ]);

            $temp->delete();
        }
    }

    public function get($id)
    {
        $op = Participant::findOrFail($id);
        // $image_path = Storage::path('private/vapp/event/logo/' . $op->event_logo);
        $docs = $op->documents()
            // ->where('category', 'qid')
            ->get()
            ->map(fn($d) => [
                'id' => $d->id,
                'original_name' => $d->original_name ?? basename($d->path),
                'size' => (int) ($d->size ?? 0),
                'download_url' => route('participant.docs.download', $d->id),
                'delete_url' => route('participant.docs.destroy', $d->id),
            ]);

        return response()->json(['op' => $op, 'venues' => $op->venues, 'event_docs' => $docs, 'image_path' => route('ypi.setting.event.file', $op->id)]);
        // return response()->json(['op' => $op, 'venues' => $op->venues, 'image_path' => $image_path]);
    }

    public function list(Request $request)
    {

        Log::info('inside guest list');
        Log::info('request data: ');
        Log::info($request);
        Log::info($request->all());

        $search = request('search');
        $filter = request('filter');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $mds_schedule_event_filter = (request()->mds_schedule_event_filter) ? request()->mds_schedule_event_filter : "";
        $mds_schedule_venue_filter = (request()->mds_schedule_venue_filter) ? request()->mds_schedule_venue_filter : "";
        $mds_schedule_rsp_filter = (request()->mds_schedule_rsp_filter) ? request()->mds_schedule_rsp_filter : "";

        $ops = Guardian::where('user_id', Auth::id())
            ->where('event_id', session()->get('EVENT_ID'))
            ->firstOrFail();

        $ops = $ops->participants()->orderBy($sort, $order);

        // $ops = Participant::orderBy($sort, $order);


        if ($search) {
            $ops = $ops->whereHas('client', function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            })
                ->orWhereHas(
                    'schedule_period',
                    function ($query) use ($search) {
                        $query->where('period', 'like', '%' . $search . '%');
                    }
                )
                ->orWhereHas(
                    'cargo',
                    function ($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%');
                    }
                )
                ->orWhereHas(
                    'zone',
                    function ($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%');
                    }
                )
                ->orWhereHas(
                    'status',
                    function ($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%');
                    }
                )
                ->orWhereHas(
                    'driver',
                    function ($query) use ($search) {
                        $query->where('first_name', 'like', '%' . $search . '%');
                    }
                )
                ->orWhereHas(
                    'driver',
                    function ($query) use ($search) {
                        $query->where('last_name', 'like', '%' . $search . '%');
                    }
                );
        }


        if ($mds_schedule_event_filter) {
            $ops = $ops->where('event_id', $mds_schedule_event_filter);
        }

        if ($mds_schedule_venue_filter) {
            $ops = $ops->where('venue_id', $mds_schedule_venue_filter);
        }

        if ($mds_schedule_rsp_filter) {
            $ops = $ops->where('rsp_id', $mds_schedule_rsp_filter);
        }

        $total = $ops->count();
        $ops = $ops->paginate(request("limit"))->through(function ($op) {


            if ($op->is_admin == 'X') {
                $avatar_status = 'status-away';
            } else {
                $avatar_status = '';
            }

            if ($op->photo) {
                $image = ' <div class="avatar avatar-m ' . $avatar_status . '">
                                <a  href="#" role="button" title="' . $op->full_name . '">
                                    <img class="rounded-circle pull-up" src="/storage/upload/profile_images/' . $op->photo . '" alt="" />
                                </a>
                            </div>';
            } else {
                $image = '  <div class="avatar avatar-m ' . $avatar_status . '  me-1" id="project_team_members_init">
                                <a class="dropdown-toggle dropdown-caret-none d-inline-block" href="#" role="button" title="' . $op->full_name . '">
                                    <div class="avatar avatar-m  rounded-circle pull-up">
                                        <div class="avatar-name rounded-circle me-2"><span>' . generateInitials($op->full_name) . '</span></div>
                                    </div>
                                </a>
                            </div>';
            }

            $qid_image_route = $op->qidDocument
                ? '<a href="' . route('participant.docs.download', $op->qidDocument) . '" target="_blank" ><span><i class="fa-solid fa-eye me-2"></i>' . $op->qid . '</span></a>'
                : $op->qid;

            // Log::info('QID image route: ' . $qid_image_route);

            $actions = '<div class="font-sans-serif btn-reveal-trigger position-static">';

            $edit_actions_js =
                '<a href="javascript:void(0)" class="btn btn-sm" id="edit_participant_offcanvas" data-id="' .
                $op->id .
                '" data-table="participant_table" data-bs-toggle="tooltip" data-bs-placement="right" title="Update">' .
                '<i class="fa-solid fa-pen-to-square text-primary"></i></a>';
            $edit_actions =
                '<a href="' . route('ypi.customer.participant.edit', $op->id) . '" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="right" title="Update">' .
                '<i class="fa-solid fa-pen-to-square text-primary"></i></a>';
            $delete_actions =
                '<a href="javascript:void(0)" class="btn btn-sm" data-table="participant_table" data-id="' .
                $op->id .
                '" id="deleteParticipant" data-bs-toggle="tooltip" data-bs-placement="right" title="Delete">' .
                '<i class="bx bx-trash text-danger"></i></a>';

            $actions .= ($op->status->title <> 'Approved') ?  $actions . $edit_actions . $delete_actions : '';
            $actions .= '</div>';

            $details_url = route('ypi.admin.participant.detail', $op->id);
            $order_status =  '<span class="badge badge-phoenix fs--2 ms-2 badge-phoenix-' . $op->status?->color . ' "><span class="badge-label" id="editprojectStatus" data-id="' . $op->id . '" data-table="project_table">' . $op->status->title . '</span><span class="ms-1" data-feather="x" style="height:12.8px;width:12.8px;"></span></span>';

            return  [
                'id' => $op->id,
                'image' => '<div class="align-middle white-space-wrap fs-9 px-3">' . $image . '</div>',
                'participant_status' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $order_status . '</div>',
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$op->id. '</div>',
                'ref_number' => '<div class="align-middle white-space-wrap fw-bold fs-9 ms-2">
                        <a href="' . $details_url . '" >' . $op->ref_number . '</a></div>',
                'event_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->event?->name . '</div>',
                'participant_type' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->participantType?->title . '</div>',
                'full_name' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->full_name . '</div>',
                'date_of_birth' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  format_date($op->date_of_birth, 'd/m/Y') . '</div>',
                'gender' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->gender?->title . '</div>',
                'pants_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->pantSize?->label . '</div>',
                'jersey_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->jerseySize?->label . '</div>',
                'jacket_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->jacketSize?->label . '</div>',
                'shoe_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->shoeSize?->label . '</div>',
                'food_allergies' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  ($op->food_allergy ? 'Yes' : 'No') . '</div>',
                'health_issues' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  ($op->health_issues ? 'Yes' : 'No') . '</div>',
                'qid' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $qid_image_route . '</div>',
                'nationality' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->nationality?->title . '</div>',
                'action' => $actions,
                'created_at' => format_date($op->created_at,  'H:i:s'),
                'updated_at' => format_date($op->updated_at, 'H:i:s'),
            ];
        });

        return response()->json([
            "rows" => $ops->items(),
            "total" => $total,
        ]);
    }

    public function create()
    {
        $participants = Participant::all();
        $event = Event::findOrFail(session()->get('EVENT_ID'));
        $participant_types = ParticipantType::all();
        $genders = Gender::all();
        $nationalities = Nationality::all();
        $pant_sizes   = SizeLookup::type('pant')->get();
        $jersey_sizes = SizeLookup::type('jersey')->get();
        $shoe_sizes   = SizeLookup::type('shoe')->get();
        $jacket_sizes = SizeLookup::type('jacket')->get();

        return view('ypi.customer.guardian.create', compact(
            'event',
            'participant_types',
            'genders',
            'nationalities',
            'pant_sizes',
            'jersey_sizes',
            'shoe_sizes',
            'jacket_sizes',
        ));
    }

    public function edit($id)
    {
        $participant = Participant::findOrFail($id);
        $event = Event::findOrFail(session()->get('EVENT_ID'));
        $participant_types = ParticipantType::all();
        $genders = Gender::all();
        $nationalities = Nationality::all();
        $pant_sizes   = SizeLookup::type('pant')->get();
        $jersey_sizes = SizeLookup::type('jersey')->get();
        $shoe_sizes   = SizeLookup::type('shoe')->get();
        $jacket_sizes = SizeLookup::type('jacket')->get();

        $age = age_from_dob($participant->date_of_birth, 'Y-m-d');

        return view('ypi.customer.guardian.edit', compact(
            'participant',
            'event',
            'participant_types',
            'genders',
            'nationalities',
            'pant_sizes',
            'jersey_sizes',
            'shoe_sizes',
            'jacket_sizes',
            'age'
        ));
    }

    public function store(Request $request)
    {

        $rules = [
            'participant_type_id' => 'required',
            'gender_id' => 'required',
            'full_name' => 'required',
            'qid' => 'required|unique:participants,qid',
            'date_of_birth' => ['required', 'date_format:d/m/Y'],
            'nationality_id' => 'required',
            'school_name' => 'required',
            // 'guardian_id' => 'required',
            'pants_size_id' => 'required',
            'jersey_size_id' => 'required',
            'jacket_size_id' => 'required',
            'shoe_size_id' => 'required',
            // 'qid_file' => 'required|file|max:2048|mimes:jpg,jpeg,png,pdf',
            // 'food_allergy' => 'required',
            // 'health_issues' => 'required',
            // FilePond temp ids
            'qid_server_ids' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info($validator->errors());
            $error = true;
            $type = 'error';
            // $message = 'Guest could not be created';
            $message = implode($validator->errors()->all(':message'));
            $toastr_message = [
                'alert-type' => $type,
                'message' => $message,
            ];

            return redirect()->back()->with($toastr_message)->withInput();

            // return response()->json(['error' => $error, 'message' => $message]);
        }

        // $user_id = Auth::user()->id;
        $serverIds = json_decode($request->input('qid_server_ids', '[]'), true) ?: [];

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $op = new Participant();

            $user_id = $user->id;

            $guardian = Guardian::where('user_id', $user_id)->firstOrFail();

            $submitted_id = getStatusIdByLabel('Submitted');
            $seq = nextSequence('ypi');

            $op->reference_number = 'YPI-' . date('Y') . '-' . get_current_event_id() . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
            $op->participant_type_id = $request->participant_type_id;
            $op->status_id = $submitted_id;
            $op->event_id = session()->get('EVENT_ID');
            $op->date_of_birth = $request->date_of_birth ? Carbon::createFromFormat('d/m/Y', $request->date_of_birth)->toDateString() : null;
            $op->full_name = $request->full_name;
            $op->qid = $request->qid;
            $op->school_name = $request->school_name;
            $op->gender_id = intval($request->gender_id);
            $op->guardian_id = $guardian->id;
            $op->nationality_id = intval($request->nationality_id);
            $op->pants_size_id = intval($request->pants_size_id);
            $op->jersey_size_id = intval($request->jersey_size_id);
            $op->jacket_size_id = intval($request->jacket_size_id);
            $op->shoe_size_id = intval($request->shoe_size_id);
            $op->food_allergy = $request->boolean('food_allergy');
            $op->health_issues = $request->boolean('health_issues');
            $op->food_allergy_details = $request->food_allergy_details;
            $op->food_allergy_details = $request->food_allergy_details;
            $op->created_by = $user_id;
            $op->updated_by = $user_id;

            $op->save();


            $qidFiles = $request->input('qid_files', []);

            // If somehow a single value comes, normalize to array
            if (!is_array($qidFiles) && $qidFiles) {
                $qidFiles = [$qidFiles];
            }

            foreach ($qidFiles as $tempId) {

                Log::info("Processing QID file temp ID: {$tempId} for participant ID: {$op->id}");
                $temp = TempUpload::where('path', $tempId)
                    ->where('user_id', auth()->id())
                    ->first();

                if (!$temp) {
                    throw new \Exception("Invalid uploaded file reference: {$tempId}");
                }

                $ext = pathinfo($temp->path, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = time() . '_' . uniqid() . '.' . $ext;

                $finalDir  = "uploads/participants/{$op->id}/";
                $finalPath = $finalDir . $fileName;

                // move from temp disk -> private disk
                $contents = Storage::disk($temp->disk)->get($temp->path);
                Storage::disk('private')->put($finalPath, $contents);

                // create document row
                $doc = new ParticipantDocument();
                $doc->participant_id = $op->id;
                $doc->disk = 'private';
                $doc->path = $finalPath; // full file path
                $doc->original_name = $temp->original_name ?? $fileName;
                $doc->mime = $temp->mime ?? 'image/' . $ext;
                $doc->size = $temp->size ?? strlen($contents);
                $doc->created_by = $user_id;
                $doc->save();

                // cleanup temp
                Storage::disk($temp->disk)->delete($temp->path);
                $temp->delete();
            }

            // if ($request->hasFile('qid_file')) {

            //     $file = $request->file('qid_file');
            //     // $fileNameWithExt = $file->getClientOriginalName();
            //     // // get file name
            //     // $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            //     // // get extension
            //     // $extension = $request->file('qid_file')->getClientOriginalExtension();

            //     // $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            //     // Log::info($fileNameWithExt);
            //     // Log::info($filename);
            //     // Log::info($extension);
            //     // Log::info($fileNameToStore);

            //     // $path = $request->file('file_name')->storeAs('public/upload/profile_images', $fileNameToStore);
            //     // $path = $file->move('upload/profile_images/', $fileNameToStore);
            //     // Log::info($path);

            //     // $dir = "reports/{$report->id}";
            //     $dir = "uploads/participants/{$op->id}";
            //     // $filename = uniqid() . '.jpg'; // normalize to jpg

            //     // // 🔥 Resize image
            //     // // $image = Image::read($photo);
            //     // $manager = new ImageManager(new Driver());

            //     // // ✅ Read image
            //     // $image = $manager->read($photo)
            //     //     ->orient() // replaces orientate()
            //     //     ->resize(1600, null, function ($constraint) {
            //     //         $constraint->aspectRatio();
            //     //         $constraint->upsize();
            //     //     })
            //     //     ->toJpeg(85); // encode


            //     // // 🔥 Store in PRIVATE disk
            //     // $path = Storage::disk('private')->put(
            //     //     "{$dir}/{$filename}",
            //     //     $image
            //     // );
            //     $path = $file->store($dir, 'private');

            //     ParticipantDocument::create([
            //         'participant_id' => $op->id,
            //         'disk' => 'private',
            //         'path' => $path,
            //         'original_name' => $file->getClientOriginalName(),
            //         'mime' => $file->getClientMimeType(),
            //         'size' => $file->getSize(),
            //         'created_by' => auth()->id(),
            //     ]);
            // }

            DB::commit();

            if (config('settings.send_notifications')) {
                
                $details = [
                    'email' => config('settings.admin_email'),
                    'guardian_name' => $guardian->full_name,
                    'participant_name' => $op->full_name,
                    'reference_number' => $op->reference_number,
                    'event' => $op->event?->name,
                    'participant_type' => $op->participantType?->title,
                ];
                // SendNewRequestEmailJob::dispatch($details);
                $filePath = null; // Adjust if you generate a QR code file
                Mail::to($user->email)->send(new NewRequestMail($details, $filePath));
            }


            $error = false;
            // $type = 'success';
            $message = 'Participant created succesfully.';

            // return response()->json(['error' => $error, 'message' => $message]);
            $toastr_message = [
                'type' => 'success',
                'message' => 'Report submitted successfully!',
            ];

            return redirect()->route('home')->with($toastr_message);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('GuardianController::store failed', [
                'error' => $e->getMessage(),
            ]);

            $toastr_message = [
                'alert-type' => 'error',
                'message' => $e->getMessage(),
            ];

            return redirect()->back()->with($toastr_message)->withInput();

            // return response()->json([
            //     'error'   => true,
            //     'message' => 'Failed to create participant. ' . $e->getMessage(),
            // ], 500);
        }
    }

    public function update(Request $request)
    {

        Log::info('inside GuardianController update');
        Log::info('request data: ' . json_encode($request->all()));

        $rules = [
            'participant_type_id' => 'required',
            'gender_id' => 'required',
            'full_name' => 'required',
            'qid' => 'required',
            'date_of_birth' => ['required', 'date_format:d/m/Y'],
            'nationality_id' => 'required',
            'school_name' => 'required',
            // 'guardian_id' => 'required',
            'pants_size_id' => 'required',
            'jersey_size_id' => 'required',
            'jacket_size_id' => 'required',
            'shoe_size_id' => 'required',
            // 'food_allergy' => 'required',
            // 'health_issues' => 'required',
            // FilePond temp ids
            'qid_server_ids' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info($validator->errors());
            $error = true;
            $type = 'error';
            // $message = 'Guest could not be created';
            $message = implode($validator->errors()->all('<div>:message</div>'));
            return response()->json(['error' => $error, 'message' => $message]);
        }

        $user_id = Auth::user()->id;
        $serverIds = json_decode($request->input('qid_server_ids', '[]'), true) ?: [];
        $deleteIds = json_decode($request->input('delete_doc_ids', '[]'), true) ?: [];

        DB::beginTransaction();
        try {
            $op = Participant::findOrFail($request->participant_id);

            if ($request->hasFile('file_name')) {

                $file = $request->file('file_name');
                $fileNameWithExt = $file->getClientOriginalName();
                // get file name
                $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                // get extension
                $extension = $request->file('file_name')->getClientOriginalExtension();

                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                Log::info($fileNameWithExt);
                Log::info($filename);
                Log::info($extension);
                Log::info($fileNameToStore);

                // $path = $request->file('file_name')->storeAs('public/upload/profile_images', $fileNameToStore);
                // $path = $file->move('upload/profile_images/', $fileNameToStore);
                // Log::info($path);

                $op->photo = $fileNameToStore;
            }

            $userId = auth()->id();

            $guardian = Guardian::where('user_id', $userId)->firstOrFail();

            $op->participant_type_id = $request->participant_type_id;
            $op->event_id = session()->get('EVENT_ID');
            $op->date_of_birth = $request->date_of_birth ? Carbon::createFromFormat('d/m/Y', $request->date_of_birth)->toDateString() : null;
            $op->full_name = $request->full_name;
            $op->qid = $request->qid;
            $op->school_name = $request->school_name;
            $op->gender_id = intval($request->gender_id);
            // $op->guardian_id = $guardian->id;
            $op->nationality_id = intval($request->nationality_id);
            $op->pants_size_id = intval($request->pants_size_id);
            $op->jersey_size_id = intval($request->jersey_size_id);
            $op->jacket_size_id = intval($request->jacket_size_id);
            $op->shoe_size_id = intval($request->shoe_size_id);
            $op->food_allergy = $request->food_allergy;
            $op->health_issues = $request->health_issues;
            $op->food_allergy_details = $request->food_allergy_details;
            $op->health_issues_details = $request->health_issues_details;
            // $op->created_by = $user_id;
            $op->updated_by = $user_id;

            $op->save();
            // $path = $request->file('file_name')->storeAs('public/upload/profile_images', $fileNameToStore);
            // =========================
            // Commit FilePond uploads
            // =========================
            if (!empty($serverIds)) {
                $this->commitFilepondUploads($serverIds, $op->id, 'qid');
            }

            // =========================
            // 4) Delete docs ONLY ON SAVE (staged deletes)
            // =========================
            // Ensure the table has: id, event_id, disk, path (or equivalents)
            if (!empty($deleteIds)) {
                $docs = ParticipantDocument::where('participant_id', $op->id)
                    ->whereIn('id', $deleteIds)
                    ->get();

                foreach ($docs as $doc) {
                    Storage::disk($doc->disk ?? 'private')->delete($doc->path);
                    $doc->delete();
                }
            }

            DB::commit();

            $toastr_message = [
                'alert-type' => 'success',
                'message' => 'Report submitted successfully!',
            ];

            return redirect()->route('home')->with($toastr_message);

            // $error = false;
            // $message = 'Participant updated successfully.';

            // return response()->json(['error' => $error, 'message' => $message]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('GuardianController::store failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error'   => true,
                'message' => 'Failed to update participant. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        // LOG::info('inside delete');
        $op = Participant::find($id);
        Log::info($op);
        if (!$op) {
            $error = true;
            $message = 'Guest not found.';
            $notification = array(
                'message'       => 'Guest not found',
                'alert-type'    => 'error'
            );
            return response()->json(['error' => $error, 'message' => $message]);
        }

        if ($op->photo) {
            Storage::delete('public/upload/profile_images/' . $op->photo);
        }

        $op->delete();

        $error = false;
        $message = 'Guest deleted succesfully.';

        $notification = array(
            'message'       => 'Guest deleted successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
        // return redirect()->route('tracki.setup.workspace')->with($notification);
    } // delete

    public function getView($id)
    {
        $guest = Guest::find($id);
        $events = Event::all();
        $client_groups = ClientGroup::all();
        $guest_types = GuestType::all();
        $prefixes = Prefix::all();
        $client_groups = ClientGroup::all();
        $hosted_by = HostedBy::all();
        $designations = Designation::all();
        $nationalities = Nationality::all();
        // $guests = Guest::with('client', 'schedule_period', 'cargo', 'zone', 'status', 'driver')->get();
        $view = view('/gms/admin/guest/mv/edit', [
            'guest' => $guest,
            'events' => $events,
            'guestTypes' => $guest_types,
            'prefixes' => $prefixes,
            'clientGroups' => $client_groups,
            'hostedBy' => $hosted_by,
            'designations' => $designations,
            'nationalities' => $nationalities
        ])->render();
        return response()->json(['view' => $view]);
    } // end getView

    public function detail(Request $request, $id)
    {

        $guest = Guest::find($id);
        $events = Event::all();
        $airlines = AirlineCarriers::all();
        $cabins = FlightCabin::all();
        $flight_types = FlightType::all();
        $airports = Airport::all();
        $flight_statuses = FlightStatus::all();
        $client_group = $guest->client_group;

        // dd($guest);

        // dd($taskData);
        $count = $guest->count();
        return view('ypi.admin.participant.detail', [
            'count' => $count,
            'events' => $events,
            'airlines' => $airlines,
            'cabins' => $cabins,
            'flightTypes' => $flight_types,
            'airports' => $airports,
            'flightStatuses' => $flight_statuses,
            'guestData' => $guest,
            'client_group' => $client_group,
        ]);
    }  // end detail

    public function switch($id)
    {
        if ($id) {
            if (Event::findOrFail($id)) {
                appLog('Event ID: ' . $id);

                session()->put('EVENT_ID', $id);
                appLog('Event ID: ' . session()->get('EVENT_ID'));
                // return redirect()->route('tracki.project.show.card')->with('message', 'Workspace switched successfully.');
                return redirect()->route('ypi.customer.guardian')->with('message', 'Event Switched.');
                // return back()->with('message', 'Event Switched.');
            } else {
                // return back()->with('error', 'Workspace not found.');
                // return redirect()->route('tracki.project.show.card')->with('error', 'Workspace not found.');
                return back()->with('error', 'Event not found.');
            }
        } else {
            session()->forget('EVENT_ID');
            // return redirect()->route('tracki.project.show.card')->with('message', 'Workspace switched successfully. now showing all workspace data');
            return back()->withInput();
        }
    }

    public function pickEvent(Request $request)
    {
        // $events = Event::all();
        // $this->switch($request->event_id);
        // return view('vapp.admin.booking.pick', compact('events'));
        if ($request->event_id) {
            appLog('Event ID: ' . $request->event_id);
            if (Event::findOrFail($request->event_id) && !session()->has('EVENT_ID')) {
                appLog('Inside if statement Event ID: ' . $request->event_id);

                session()->put('EVENT_ID', $request->event_id);
                appLog('session EVENT_ID: ' . session()->get('EVENT_ID'));
                appLog('before redirect');
                // return redirect()->route('tracki.project.show.card')->with('message', 'Workspace switched successfully.');
                return redirect()->route('ypi.customer.guardian')->with('message', 'Event Switched.');
                // return back()->with('message', 'Event Switched.');
            }
        }
        //  else {
        // return back()->with('error', 'Workspace not found.');
        // return redirect()->route('tracki.project.show.card')->with('error', 'Workspace not found.');
        appLog('event_id is null');
        return redirect()->route('ypi.customer.guardian')->with('error', 'Event not found.');
        // }
    }
}
