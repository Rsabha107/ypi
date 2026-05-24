<?php

namespace App\Http\Controllers\Ypi\Customer;

use App\Http\Controllers\Controller;
use App\Mail\NewRequestMail;
use App\Models\Ypi\Allergen;
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
    public function index(Request $request)
    {
        Log::info('inside GuardianController index');
        Log::info('Session participant_filter_event_id: ' . session('participant_filter_event_id'));
        
        $participants = Participant::all();
        $events = Event::where('name', 'not like', '%Admin%')
            ->where('active_flag', 1)
            ->get();
        $participant_types = ParticipantType::all();
        $genders = Gender::all();
        $nationalities = Nationality::all();
        $pant_sizes   = SizeLookup::type('pant')->get();
        $jersey_sizes = SizeLookup::type('jersey')->get();
        $shoe_sizes   = SizeLookup::type('shoe')->get();
        $jacket_sizes = SizeLookup::type('jacket')->get();

        // Get selected event from session
        $selectedEvent = null;
        if (session()->has('participant_filter_event_id')) {
            $selectedEvent = Event::find(session('participant_filter_event_id'));
            Log::info('Selected event found: ' . ($selectedEvent ? $selectedEvent->name : 'null'));
        } else {
            Log::info('No filter in session');
        }

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
            'shoe_sizes',
            'selectedEvent'
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

        // Log::info('inside guest list');
        // Log::info('request data: ');
        // Log::info($request);
        // Log::info($request->all());

        $search = request('search');
        $filter = request('filter');
        $event_filter = session('participant_filter_event_id'); // Event filter from session
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $mds_schedule_event_filter = (request()->mds_schedule_event_filter) ? request()->mds_schedule_event_filter : "";
        $mds_schedule_venue_filter = (request()->mds_schedule_venue_filter) ? request()->mds_schedule_venue_filter : "";
        $mds_schedule_rsp_filter = (request()->mds_schedule_rsp_filter) ? request()->mds_schedule_rsp_filter : "";

        $ops = Guardian::where('user_id', Auth::id())
            // ->where('event_id', session()->get('EVENT_ID'))
            ->firstOrFail();

        $ops = $ops->participants()->orderBy($sort, $order);

        // Filter by event if provided
        if ($event_filter) {
            $ops = $ops->where('event_id', $event_filter);
        }

        $total = $ops->count();
        $limit = request("limit");
        $limit = max(1, min($limit, 100)); // min=1, max=100
        $ops = $ops->paginate($limit)->through(function ($op) {


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
                ? '<a href="javascript:void(0)" class="qid-image-link" data-image-url="' . route('participant.docs.download', $op->qidDocument) . '" data-qid="' . $op->qid . '"><span><i class="fa-solid fa-eye me-2"></i>' . $op->qid . '</span></a>'
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
            $cert_image_route = $op->certDocument
                ? '<div class="d-flex align-items-center gap-2">'
                . '<a href="' . route('participant.docs.download', $op->certDocument) . '" target="_blank" class="text-warning">'
                . '<i class="fa-solid fa-eye"></i>'
                . '</a>'
                . '</div>'
                : null;

            return  [
                'id' => $op->id,
                'image' => '<div class="align-middle white-space-wrap fs-9 px-3">' . $image . '</div>',
                'participant_status' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $order_status . '</div>',
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$op->id. '</div>',
                'ref_number' => '<div class="align-middle white-space-wrap fw-bold fs-9 ms-2">
                        <a href="' . $details_url . '" >' . $op->ref_number . '</a></div>',
                'participant_cert' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $cert_image_route . '</div>',
                'event_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->event?->name . '</div>',
                'assigned_venue_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->venue?->title . '</div>',
                'assigned_match_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->match?->match_code . '</div>',
                'participant_type' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->participantType?->title . '</div>',
                'full_name' => '<div class="align-middle white-space-wrap fs-9 ps-2"><a href="javascript:void(0)" class="participant-name-link" data-participant-id="' . $op->id . '">' . $op->full_name . '</a></div>',
                'date_of_birth' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  format_date($op->date_of_birth, 'd/m/Y') . '</div>',
                'gender' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->gender?->title . '</div>',
                'pants_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->pantSize?->label . '</div>',
                'jersey_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->jerseySize?->label . '</div>',
                'jacket_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->jacketSize?->label . '</div>',
                'shoe_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->shoeSize?->label . '</div>',
                'food_allergies' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  ($op->food_allergy && $op->food_allergy_id == 22 ? 'No' : 'Yes') . '</div>',
                'health_issues' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  ($op->health_issues ? 'Yes' : 'No') . '</div>',
                'qid' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $qid_image_route . '</div>',
                'nationality' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->nationality?->title . '</div>',
                'action' => $actions,
                'created_at' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . format_date($op->created_at, 'd-M-y') . ' ' . format_date($op->created_at, 'H:i:s') . '</div>',
                'updated_at' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . format_date($op->updated_at, 'd-M-y') . ' ' . format_date($op->updated_at, 'H:i:s') . '</div>',
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
        $events = Event::where('name', 'not like', '%Admin%')
            ->where('active_flag', 1)
            ->get();
        $participant_types = ParticipantType::all();
        $genders = Gender::all();
        $nationalities = Nationality::all();
        $pant_sizes   = SizeLookup::type('pant')->get();
        $jersey_sizes = SizeLookup::type('jersey')->get();
        $shoe_sizes   = SizeLookup::type('shoe')->get();
        $jacket_sizes = SizeLookup::type('jacket')->get();
        $allergens = Allergen::all();

        return view('ypi.customer.guardian.create', compact(
            'events',
            'participant_types',
            'genders',
            'nationalities',
            'pant_sizes',
            'jersey_sizes',
            'shoe_sizes',
            'jacket_sizes',
            'allergens',
        ));
    }

    public function edit($id)
    {
        $participant = Participant::findOrFail($id);
        $this->authorize('update', $participant);
        // Get event from participant instead of session
        $event = $participant->event;
        $events = Event::where('name', 'not like', '%Admin%')
            ->where('active_flag', 1)
            ->get();
        $participant_types = ParticipantType::all();
        $genders = Gender::all();
        $nationalities = Nationality::all();
        $pant_sizes   = SizeLookup::type('pant')->get();
        $jersey_sizes = SizeLookup::type('jersey')->get();
        $shoe_sizes   = SizeLookup::type('shoe')->get();
        $jacket_sizes = SizeLookup::type('jacket')->get();
        $allergens = Allergen::all();

        $age = age_from_dob($participant->date_of_birth, 'Y-m-d');

        return view('ypi.customer.guardian.edit', compact(
            'participant',
            'event',
            'events',
            'participant_types',
            'genders',
            'nationalities',
            'pant_sizes',
            'jersey_sizes',
            'shoe_sizes',
            'jacket_sizes',
            'allergens',
            'age'
        ));
    }

    public function store(Request $request)
    {

        $rules = [
            'event_id' => 'required|exists:events,id',
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
            'food_allergy' => 'required|boolean',
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

            $op->reference_number = 'YPI-' . date('Y') . '-' . $request->event_id . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
            $op->participant_type_id = $request->participant_type_id;
            $op->status_id = $submitted_id;
            $op->event_id = $request->event_id;
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
            $op->food_allergy_id = $request->food_allergy_id;
            $op->food_allergy_others = ($request->food_allergy_id == getIdByName('allergens','Others', 'title')) ? $request->food_allergy_others : null;
            $op->health_issues_details = ($request->health_issues ? $request->health_issues_details : null);
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
                Mail::to($user->email)
                    ->cc(config('settings.admin_email'))
                    ->send(new NewRequestMail($details, $filePath));
            }

            // Update filter to match the event of the newly created participant
            session(['participant_filter_event_id' => $request->event_id]);

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
            'event_id' => 'required|exists:events,id',
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
             'food_allergy' => 'required',
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
            $op->event_id = $request->event_id;
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
            $op->food_allergy_id = $request->food_allergy_id;
            $op->health_issues = $request->health_issues;
            $op->food_allergy_id = $request->food_allergy_id;
            $op->food_allergy_others = ($request->food_allergy_id == getIdByName('allergens','Others', 'title')) ? $request->food_allergy_others : null;
            $op->health_issues_details = ($request->health_issues ? $request->health_issues_details : null);
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

            // Update filter to match the event of the updated participant
            session(['participant_filter_event_id' => $request->event_id]);

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
        $this->authorize('delete', $op);
        Log::info($op);
        if (!$op) {
            $error = true;
            $message = 'Participant not found.';
            $notification = array(
                'message'       => 'Participant not found',
                'alert-type'    => 'error'
            );
            return response()->json(['error' => $error, 'message' => $message]);
        }

        if ($op->documents) {
            foreach ($op->documents as $doc) {
                Storage::disk($doc->disk)->delete($doc->path);
                $doc->delete();
            }
        }

        $op->delete();

        $error = false;
        $message = 'Participant deleted successfully.';

        $notification = array(
            'message'       => 'Participant deleted successfully',
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

    public function setFilter(Request $request)
    {
        Log::info('setFilter called with event_id: ' . $request->event_id);
        
        if ($request->has('event_id') && $request->event_id) {
            session(['participant_filter_event_id' => $request->event_id]);
            Log::info('Filter set in session: ' . session('participant_filter_event_id'));
        } else {
            session()->forget('participant_filter_event_id');
            Log::info('Filter cleared from session');
        }
        
        return response()->json(['success' => true]);
    }

    public function clearFilter()
    {
        session()->forget('participant_filter_event_id');
        return response()->json(['success' => true]);
    }

    public function getParticipantDetails($id)
    {
        $participant = Participant::with(['event', 'participantType', 'guardian', 'gender', 'nationality', 'pantSize', 'jerseySize', 'jacketSize', 'shoeSize', 'allergen', 'status', 'venue', 'match'])->findOrFail($id);
        
        $this->authorize('view', $participant);
        
        return response()->json([
            'participant' => [
                'id' => $participant->id,
                'full_name' => $participant->full_name,
                'qid' => $participant->qid,
                'date_of_birth' => format_date($participant->date_of_birth, 'd/m/Y'),
                'gender' => $participant->gender?->title,
                'nationality' => $participant->nationality?->title,
                'school_name' => $participant->school_name,
                'event' => $participant->event?->name,
                'participant_type' => $participant->participantType?->title,
                'status' => $participant->status?->title,
                'status_color' => $participant->status?->color,
                'assigned_venue' => $participant->venue?->title,
                'assigned_match' => $participant->match?->match_code,
                'pants_size' => $participant->pantSize?->label,
                'jersey_size' => $participant->jerseySize?->label,
                'jacket_size' => $participant->jacketSize?->label,
                'shoe_size' => $participant->shoeSize?->label,
                'food_allergy' => $participant->food_allergy ? 'Yes' : 'No',
                'food_allergy_type' => $participant->allergen?->title,
                'food_allergy_others' => $participant->food_allergy_others,
                'health_issues' => $participant->health_issues ? 'Yes' : 'No',
                'health_issues_details' => $participant->health_issues_details,
                'guardian_name' => $participant->guardian?->full_name,
                'guardian_email' => $participant->guardian?->email,
                'guardian_phone' => $participant->guardian?->phone_main,
            ]
        ]);
    }
}
