<?php

namespace App\Http\Controllers\Ypi\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ApprovedRequestMail;
use App\Mail\RejectedRequestMail;
use App\Models\Ypi\Event;
use App\Models\Ypi\EventMatch;
use App\Models\Ypi\Gender;
use App\Models\Ypi\Nationality;
use App\Models\Ypi\Participant;
use App\Models\Ypi\ParticipantDocument;
use App\Models\Ypi\ParticipantStatus;
use App\Models\Ypi\ParticipantType;
use App\Models\Ypi\SizeLookup;
use App\Models\Ypi\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    //
    public function index(Request $request)
    {
        Log::info('inside GuestController participant index');
        Log::info('Session participant_filter_event_id: ' . session('participant_filter_event_id'));

        $participants = Participant::all();
        $events = Event::where('name', 'not like', '%Admin%')
            ->where('active_flag', 1)
            ->get();
        $participant_types = ParticipantType::where('active_flag', 1)->get();
        $genders = Gender::all();
        $nationalities = Nationality::all();
        $pant_sizes   = SizeLookup::type('pant')->get();
        $jersey_sizes = SizeLookup::type('jersey')->get();
        $shoe_sizes   = SizeLookup::type('shoe')->get();
        $jacket_sizes = SizeLookup::type('jacket')->get();
        $statuses = ParticipantStatus::all();
        $matches = EventMatch::all();
        $venues = Venue::all();

        // Get selected event from session
        $selectedEvent = null;
        if (session()->has('participant_filter_event_id')) {
            $selectedEvent = Event::find(session('participant_filter_event_id'));
            Log::info('Selected event found: ' . ($selectedEvent ? $selectedEvent->name : 'null'));
        } else {
            Log::info('No filter in session');
        }

        // $guests = Guest::with('client', 'schedule_period', 'cargo', 'zone', 'status', 'driver')->get();

        return view('ypi.admin.participant.list', compact(
            'participants',
            'events',
            'participant_types',
            'genders',
            'nationalities',
            'pant_sizes',
            'jersey_sizes',
            'jacket_sizes',
            'shoe_sizes',
            'statuses',
            'matches',
            'venues',
            'selectedEvent'
        ));
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

        $ops = Participant::orderBy($sort, $order);
        // Optionally filter by event if needed
        // $ops = $ops->where('event_id', session()->get('EVENT_ID'));
        
        // Filter by event if provided
        if ($event_filter) {
            $ops = $ops->where('event_id', $event_filter);
        }

        if ($search) {
            $ops = $ops->where('full_name', 'like', '%' . $search . '%')
                ->orWhere('qid', 'like', '%' . $search . '%')
                ->orWhere('date_of_birth', 'like', '%' . $search . '%')
                ->orWhere('school_name', 'like', '%' . $search . '%')
                ->orWhereHas('guardian', function ($query) use ($search) {
                    $query->where('full_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone_main', 'like', '%' . $search . '%');
                })
                ->orWhereHas(
                    'status',
                    function ($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%');
                    }
                )
                ->orWhereHas(
                    'participantType',
                    function ($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%');
                    }
                )
                ->orWhereHas(
                    'pantSize',
                    function ($query) use ($search) {
                        $query->where('label', 'like', '%' . $search . '%');
                    }
                )
                ->orWhereHas(
                    'jerseySize',
                    function ($query) use ($search) {
                        $query->where('label', 'like', '%' . $search . '%');
                    }
                )
                ->orWhereHas(
                    'shoeSize',
                    function ($query) use ($search) {
                        $query->where('label', 'like', '%' . $search . '%');
                    }
                )
                ->orWhereHas(
                    'jacketSize',
                    function ($query) use ($search) {
                        $query->where('label', 'like', '%' . $search . '%');
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
        $limit = request("limit");
        $limit = max(1, min($limit, 100)); // min=1, max=100
        $ops = $ops->paginate($limit)->through(function ($op) {

            // $location = Location::find($guests->location_id);
            $full_name = $op->first_name . ' ' . $op->last_name;
            if ($op->is_admin == 'X') {
                $avatar_status = 'status-away';
            } else {
                $avatar_status = '';
            }

            if ($op->photo) {
                $image = ' <div class="avatar avatar-m ' . $avatar_status . '">
                                <a  href="#" role="button" title="' . $full_name . '">
                                    <img class="rounded-circle pull-up" src="/storage/upload/profile_images/' . $op->photo . '" alt="" />
                                </a>
                            </div>';
            } else {
                $image = '  <div class="avatar avatar-m ' . $avatar_status . '  me-1" id="project_team_members_init">
                                <a class="dropdown-toggle dropdown-caret-none d-inline-block" href="#" role="button" title="' . $full_name . '">
                                    <div class="avatar avatar-m  rounded-circle pull-up">
                                        <div class="avatar-name rounded-circle me-2"><span>' . generateInitials($full_name) . '</span></div>
                                    </div>
                                </a>
                            </div>';
            }

            $actions = '<div class="font-sans-serif btn-reveal-trigger position-static">';
            $edit_actions = '<a href="javascript:void(0)" class="btn btn-sm" id="edit_guest_offcanv" data-id="' .
                $op->id .
                '" data-table="participant_table" data-bs-toggle="tooltip" data-bs-placement="right" title="Update">' .
                '<i class="fa-solid fa-pen-to-square text-primary"></i></a>';
            $delete_actions =
                '<a href="javascript:void(0)" class="btn btn-sm" data-table="participant_table" data-id="' .
                $op->id .
                '" id="deleteParticipant" data-bs-toggle="tooltip" data-bs-placement="right" title="Delete">' .
                '<i class="bx bx-trash text-danger"></i></a>';
            $upload_img_actions =
                '<a href="javascript:void(0)" class="btn btn-sm" data-table="participant_table" data-id="' .
                $op->id .
                '" id="ypiUploadCertificate" data-bs-toggle="tooltip" data-bs-placement="right" title="Upload Certificate">' .
                '<i class="bx bx-arrow-to-top text-success"></i></a>';

            $actions .=  $edit_actions . (($op->status?->title == 'Approved') ? $upload_img_actions : '') . $delete_actions;
            $actions .= '</div>';

            $order_status =  '<span class="badge badge-phoenix fs--2 ms-2 badge-phoenix-' . $op->status?->color . ' "><span class="badge-label" id="change_participant_status" style="cursor:pointer" data-id="' . $op->id . '" data-status_id="' . $op->status?->id . '" data-event_id="' . $op->event_id . '" data-table="participant_table">' . $op->status?->title . '</span><span class="ms-1" data-feather="x" style="height:12.8px;width:12.8px;cursor:pointer"></span></span>';
            $qid_image_route = $op->qidDocument
                ? '<a href="javascript:void(0)" class="qid-image-link" data-image-url="' . route('participant.docs.download', $op->qidDocument) . '" data-qid="' . $op->qid . '"><span><i class="fa-solid fa-eye me-2"></i>' . $op->qid . '</span></a>'
                : $op->qid;

            $cert_image_route = $op->certDocument
                ? '<div class="d-flex align-items-center gap-2">'
                . '<a href="' . route('participant.docs.download', $op->certDocument) . '" target="_blank" class="text-warning">'
                . '<i class="fa-solid fa-eye"></i>'
                . '</a>'
                . '<a href="javascript:void(0)"'
                . ' class="text-danger js-remove-cert"'
                . ' data-doc-id="' . $op->certDocument->id . '"'
                . ' data-table="participant_table"'
                . ' title="Remove certificate">'
                . '<i class="fa-solid fa-trash"></i>'
                . '</a>'
                . '<span class="ms-1 text-truncate" style="max-width:180px"'
                . ' title="' . e($op->certDocument->originalName) . '">'
                . e($op->certDocument->originalName)
                . '</span>'
                . '</div>'
                : null;

            $gardian_qid_image_route = $op->guardian->qidDocument
                ? '<a href="javascript:void(0)" class="qid-image-link" data-image-url="' . route('guardian.docs.download', $op->guardian->qidDocument) . '" data-qid="' . $op->guardian->qid . '"><span><i class="fa-solid fa-eye me-2"></i>' . $op->guardian->qid . '</span></a>'
                : $op->guardian->qid;
            return  [
                'id' => $op->id,
                'image' => '<div class="align-middle white-space-wrap fs-9 px-3">' . $image,
                'participant_status' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $order_status . '</div>',
                'participant_type' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->participantType?->title . '</div>',
                'assigned_venue_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->venue?->title . '</div>',
                'assigned_match_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  ($op->match ? ($op->match->pma1 . ' vs ' . $op->match->pma2 . ' (' . $op->match->match_date?->format('d M Y') . ')') : '') . '</div>',
                'event_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->event?->name . '</div>',
                'guest_type' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->guest_type?->title . '</div>',
                'guardian_name' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->guardian->full_name . '</div>',
                'guardian_qid' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $gardian_qid_image_route . '</div>',
                'participant_cert' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $cert_image_route . '</div>',
                'participant_name' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->full_name . '</div>',
                'guardian_email' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->guardian->email . '</div>',
                'guardian_phone' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->guardian->phone_main . '</div>',
                'qid' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $qid_image_route . '</div>',
                'date_of_birth' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->date_of_birth . '</div>',
                'gender' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->gender?->title . '</div>',
                'nationality' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->nationality?->title . '</div>',
                'pants_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->pantSize?->label . '</div>',
                'jersey_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->jerseySize?->label . '</div>',
                'jacket_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->jacketSize?->label . '</div>',
                'shoe_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->shoeSize?->label . '</div>',
                'food_allergies' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  ($op->food_allergy && $op->food_allergy_id == 22 ? 'No' : 'Yes') . '</div>',

                'health_issues' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  ($op->health_issues ? 'Yes' : 'No') . '</div>',
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

    public function store(Request $request)
    {
        //
        // dd($request);

        // $timeslots = DeliverySchedulePeriod::findOrFail($request->schedule_period_id);

        $rules = [
            'event_id' => 'required|exists:events,id',
            'participant_type_id' => 'required',
            'gender_id' => 'required',
            'full_name' => 'required',
            'qid' => 'required',
            'date_of_birth' => 'required',
            'nationality_id' => 'required',
            'school_name' => 'required',
            'guardian_id' => 'required',
            'pants_size_id' => config('settings.show_uniform_section', 1) ? 'required' : 'nullable',
            'jersey_size_id' => config('settings.show_uniform_section', 1) ? 'required' : 'nullable',
            'jacket_size_id' => config('settings.show_uniform_section', 1) ? 'required' : 'nullable',
            'shoe_size_id' => config('settings.show_uniform_section', 1) ? 'required' : 'nullable',
            'food_allergy' => 'required',
            'health_issues' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info($validator->errors());
            $error = true;
            $type = 'success';
            // $message = 'Guest could not be created';
            $message = implode($validator->errors()->all('<div>:message</div>'));
        } else {

            $user_id = Auth::user()->id;
            $participant = new Participant();

            $error = false;
            // $type = 'success';
            $message = 'Guest created succesfully.' . $participant->id;

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

                $participant->photo = $fileNameToStore;
            }


            // $guest->booking_ref_number = 'MDS' . $guest->id;
            // $guest->schedule_id =  $timeslots->delivery_schedule_id;
            // $guest->user_id =  $user_id;

            $participant->participant_type_id = $request->participant_type_id;
            $participant->event_id = $request->event_id;
            $participant->date_of_birth = Carbon::createFromFormat('d/m/Y', $request->date_of_birth)->toDateString();
            $participant->full_name = $request->full_name;
            $participant->qid = $request->qid;
            $participant->date_of_birth = $request->date_of_birth;
            $participant->school_name = $request->school_name;
            $participant->gender_id = intval($request->gender_id);
            $participant->guardian_id = intval($request->guardian_id);
            $participant->nationality_id = intval($request->nationality_id);
            $participant->pants_size_id = intval($request->pants_size_id);
            $participant->jersey_size_id = intval($request->jersey_size_id);
            $participant->jacket_size_id = intval($request->jacket_size_id);
            $participant->shoe_size_id = intval($request->shoe_size_id);
            $participant->food_allergy = $request->food_allergy;
            $participant->health_issues = $request->health_issues;
            $participant->food_allergy_details = $request->food_allergy_details;
            $participant->food_allergy_details = $request->food_allergy_details;
            $participant->created_by = $user_id;
            $participant->updated_by = $user_id;

            $participant->save();
            // $path = $request->file('file_name')->storeAs('public/upload/profile_images', $fileNameToStore);
            $path = $file->move('storage/upload/profile_images/', $fileNameToStore);



            // $this->UtilController->save_files($request, $guest->id);
        }

        $notification = array(
            'message'       => $message,
            'alert-type'    => $type
        );

        // return redirect()->route('ypi.admin.guests')->with($notification);



        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function update(Request $request)
    {
        $rules = [
            'event_id' => 'required|exists:events,id',
            'participant_type_id' => 'required',
            'gender_id' => 'required',
            'full_name' => 'required',
            'qid' => 'required',
            'date_of_birth' => 'required',
            'nationality_id' => 'required',
            'school_name' => 'required',
            'pants_size_id' => config('settings.show_uniform_section', 1) ? 'required' : 'nullable',
            'jersey_size_id' => config('settings.show_uniform_section', 1) ? 'required' : 'nullable',
            'jacket_size_id' => config('settings.show_uniform_section', 1) ? 'required' : 'nullable',
            'shoe_size_id' => config('settings.show_uniform_section', 1) ? 'required' : 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info($validator->errors());
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));
        } else {
            $participant = Participant::findOrFail($request->id);
            $user_id = Auth::user()->id;

            $error = false;
            $message = 'Participant successfully updated';

            $participant->event_id = $request->event_id;
            $participant->participant_type_id = intval($request->participant_type_id);
            $participant->gender_id = intval($request->gender_id);
            $participant->full_name = $request->full_name;
            $participant->qid = $request->qid;
            $participant->date_of_birth = $request->date_of_birth;
            $participant->nationality_id = intval($request->nationality_id);
            $participant->school_name = $request->school_name;
            $participant->pants_size_id = intval($request->pants_size_id);
            $participant->jersey_size_id = intval($request->jersey_size_id);
            $participant->jacket_size_id = intval($request->jacket_size_id);
            $participant->shoe_size_id = intval($request->shoe_size_id);
            $participant->updated_by = $user_id;

            $participant->save();
        }

        return response()->json([
            'error' => $error,
            'message' => $message,
        ]);
    }

    public function destroy($id)
    {
        // LOG::info('inside delete');
        $op = Participant::find($id);
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

        if ($op->photo) {
            Storage::delete('public/upload/profile_images/' . $op->photo);
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

    public function getStatus($id)
    {
        $participant_status = ParticipantStatus::find($id);
        return response()->json(['op' => $participant_status]);
    } // end getStatus

    public function updateStatus(Request $request)
    {
        $rules = [
            'status_id' => 'required',
            'participant_id' => 'required',
            'venue_id' => 'required_if:status_id,2', // required if status is Approved
            'match_id' => 'required_if:status_id,2', // required if status is Approved
        ];

        $messages = [
            'venue_id.required_if' => 'Venue is required when status is Approved.',
            'match_id.required_if' => 'Match is required when status is Approved.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Log::info($validator->errors());
            $error = true;
            // $message = 'Employee not create.' . $op->id;
            $message = implode($validator->errors()->all('<div>:message</div>'));
            return response()->json([
                'error' => $error,
                'message' => $message,
            ]);
        }
        $op = Participant::findOrFail($request->participant_id);
        $guardian = $op->guardian;
        $user_id = Auth::user()->id;

        $error = false;
        $message = 'Status successfully updated';

        $op->status_id = intval($request->status_id);
        $op->assigned_venue_id = intval($request->venue_id);
        $op->assigned_match_id = intval($request->match_id);
        $op->updated_by = $user_id;

        $op->save();

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

            if ($op->status?->title == 'Approved') {
                $user = $guardian->user;
                Mail::to($user->email)->send(new ApprovedRequestMail($details, $filePath));
            } elseif ($op->status?->title == 'Declined') {
                $user = $guardian->user;
                Mail::to($user->email)->send(new RejectedRequestMail($details, $filePath));
            }
        }

        return response()->json([
            'error' => $error,
            'message' => $message,
        ]);
    }

    public function uploadCertificate(Request $request)
    {
        Log::info('inside uploadCertificate');
        Log::info('request data: ');
        Log::info($request);
        Log::info($request->all());

        $rules = [
            'certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // max 5MB
            'participant_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            Log::info($validator->errors());
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));
            return response()->json(['error' => $error, 'message' => $message], 422);
        }

        DB::beginTransaction();
        try {

            $disk = 'private'; // or 'public'
            $dir  = 'certificates';

            $file = $request->file('certificate');
            $name = Str::uuid() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs($dir, $name, $disk);

            ParticipantDocument::create([
                'participant_id' => $request->input('participant_id'),
                'category' => 'certificate',
                'disk' => $disk,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json(['error' => false, 'message' => 'Certificate uploaded successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading certificate: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Failed to upload certificate.'], 500);
        }


        // $file = $request->file('certificate');

        // if (!$file) {
        //     return response('No file uploaded', 422);
        // }

        // $request->validate([
        //     'certificate' => 'required|file|mimes:jpeg,png,webp,pdf|max:5120', // max 5MB
        // ]);

        // $participant_id = $request->input('participant_id');
        // $participant = Participant::find($participant_id);

        // if (!$participant) {
        //     return response()->json(['error' => true, 'message' => 'Participant not found.'], 404);
        // }

        // $fileNameWithExt = $file->getClientOriginalName();
        // $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        // $extension = $file->getClientOriginalExtension();
        // $fileNameToStore = $filename . '_' . time() . '.' . $extension;

        // $path = $file->move('storage/upload/certificates/', $fileNameToStore);

        // $participant->certificate = $fileNameToStore;


        // $participant->save();

    }

    public function deleteCertificate($documentId)
    {
        try {
            $doc = ParticipantDocument::findOrFail($documentId);

            Storage::disk($doc->disk ?? 'private')->delete($doc->path);
            $doc->delete();

            return response()->json(['error' => false, 'message' => 'Certificate removed successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting certificate: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Failed to remove certificate.'], 500);
        }
    }

    public function getMatchesByVenue($venue_id)
    {
        $venue = Venue::findOrFail($venue_id);
        $matches = $venue->matches()   // assumes Venue has matches() relationship
            ->orderBy('match_date', 'asc')
            // Get all matches for this venue - filter by event on frontend if needed
            ->get()
            ->map(fn ($m) => [
                'id'   => $m->id,
                'text' => ($m->pma1.' vs '.$m->pma2.' - '.$m->match_date?->format('d M Y')),
            ]);

        return response()->json($matches);
    }

    public function getMatchesByVenueAndEvent($venue_id, $event_id)
    {
        $matches = EventMatch::where('venue_id', $venue_id)
            ->where('event_id', $event_id)
            ->orderBy('match_date', 'asc')
            ->get()
            ->map(fn ($m) => [
                'id'   => $m->id,
                'text' => ($m->pma1.' vs '.$m->pma2.' - '.$m->match_date?->format('d M Y')),
            ]);

        return response()->json($matches);
    }

    public function getVenuesByEvent($event_id)
    {
        $venues = Venue::whereHas('events', function ($query) use ($event_id) {
            $query->where('events.id', $event_id);
        })
            ->select('id', 'title')
            ->get();

        return response()->json($venues);
    }

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

    public function getView($id)
    {
        $participant = Participant::with(['guardian', 'status', 'event', 'participantType', 'gender', 'nationality', 'pantSize', 'jerseySize', 'jacketSize', 'shoeSize'])->findOrFail($id);
        $events = Event::where('name', 'not like', '%Admin%')
            ->where('active_flag', 1)
            ->get();
        $participant_types = ParticipantType::where('active_flag', 1)->get();
        $genders = Gender::all();
        $nationalities = Nationality::all();
        $pant_sizes   = SizeLookup::type('pant')->get();
        $jersey_sizes = SizeLookup::type('jersey')->get();
        $shoe_sizes   = SizeLookup::type('shoe')->get();
        $jacket_sizes = SizeLookup::type('jacket')->get();

        $view = view('ypi.admin.participant.mv.edit_content', [
            'participant' => $participant,
            'events' => $events,
            'participantTypes' => $participant_types,
            'genders' => $genders,
            'nationalities' => $nationalities,
            'pantSizes' => $pant_sizes,
            'jerseySizes' => $jersey_sizes,
            'jacketSizes' => $jacket_sizes,
            'shoeSizes' => $shoe_sizes,
        ])->render();
        
        return response()->json(['view' => $view]);
    }
}
