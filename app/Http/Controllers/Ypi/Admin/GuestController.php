<?php

namespace App\Http\Controllers\Ypi\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ypi\Event;
use App\Models\Ypi\AirlineCarriers;
use App\Models\Ypi\Airport;
use App\Models\Ypi\FlightCabin;
use App\Models\Ypi\FlightStatus;
use App\Models\Ypi\FlightType;
use App\Models\Ypi\Gender;
use App\Models\Ypi\Nationality;
use App\Models\Ypi\Participant;
use App\Models\Ypi\ParticipantStatus;
use App\Models\Ypi\ParticipantType;
use App\Models\Ypi\SizeLookup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
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
        $statuses = ParticipantStatus::all();

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
            'statuses'
        ));
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

        $ops = Participant::orderBy($sort, $order);
        $ops = $ops->where('event_id', session()->get('EVENT_ID'));

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
        $ops = $ops->paginate(request("limit"))->through(function ($op) {

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
                '" data-table="guest_table" data-bs-toggle="tooltip" data-bs-placement="right" title="Update">' .
                '<i class="fa-solid fa-pen-to-square text-primary"></i></a>';
            $delete_actions = 
                '<a href="javascript:void(0)" class="btn btn-sm" data-table="guest_table" data-id="' .
                $op->id .
                '" id="deleteGuest" data-bs-toggle="tooltip" data-bs-placement="right" title="Delete">' .
                '<i class="bx bx-trash text-danger"></i></a>';
            $upload_img_actions = 
                '<a href="javascript:void(0)" class="btn btn-sm" data-table="guest_table" data-id="' .
                $op->id .
                '" id="uploadImagesGuest" data-bs-toggle="tooltip" data-bs-placement="right" title="Delete">' .
                '<i class="bx bx-arrow-to-top text-success"></i></a>';

            $actions .=  $actions . (($op->status?->title == 'Approved')? $upload_img_actions: '') . $delete_actions;
            $actions .= '</div>';

            $order_status =  '<span class="badge badge-phoenix fs--2 ms-2 badge-phoenix-' . $op->status?->color . ' "><span class="badge-label" id="change_participant_status" style="cursor:pointer" data-id="' . $op->id . '"data-status_id="' . $op->status?->id . '" data-table="participant_table">' . $op->status?->title . '</span><span class="ms-1" data-feather="x" style="height:12.8px;width:12.8px;cursor:pointer"></span></span>';
            $qid_image_route = $op->qidDocument
                ? '<a href="' . route('participant.docs.download', $op->qidDocument) . '" target="_blank" ><span><i class="fa-solid fa-eye me-2"></i>' . $op->qid . '</span></a>'
                : $op->qid;

            $gardian_qid_image_route = $op->guardian->qidDocument
                ? '<a href="' . route('guardian.docs.download', $op->guardian->qidDocument) . '" target="_blank" ><span><i class="fa-solid fa-eye me-2"></i>' . $op->guardian->qid . '</span></a>'
                : $op->guardian->qid;
            return  [
                'id' => $op->id,
                'image' => '<div class="align-middle white-space-wrap fs-9 px-3">' . $image,
                'participant_status' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $order_status . '</div>',
                'participant_type' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->participantType?->title . '</div>',
                'event_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->event?->name . '</div>',
                'guest_type' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->guest_type?->title . '</div>',
                'guardian_name' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->guardian->full_name . '</div>',
                'guardian_qid' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $gardian_qid_image_route . '</div>',
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
                'food_allergies' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  ($op->food_allergy ? 'Yes' : 'No') . '</div>',
                'health_issues' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  ($op->health_issues ? 'Yes' : 'No') . '</div>',
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

    public function store(Request $request)
    {
        //
        // dd($request);

        // $timeslots = DeliverySchedulePeriod::findOrFail($request->schedule_period_id);

        $rules = [
            'participant_type_id' => 'required',
            'gender_id' => 'required',
            'full_name' => 'required',
            'qid' => 'required',
            'date_of_birth' => 'required',
            'nationality_id' => 'required',
            'school_name' => 'required',
            'guardian_id' => 'required',
            'pants_size_id' => 'required',
            'jersey_size_id' => 'required',
            'jacket_size_id' => 'required',
            'shoe_size_id' => 'required',
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
            $participant->event_id = session()->get('EVENT_ID');
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
            'guest_type_id' => 'required',
            'prefix_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_number' => 'required',
            'email' => 'required',
            'client_group_id' => 'required',
            'hosted_by_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Log::info($validator->errors());
            $error = true;
            // $message = 'Employee not create.' . $op->id;
            $message = implode($validator->errors()->all('<div>:message</div>'));
        } else {
            $op = Guest::findOrFail($request->id);
            $user_id = Auth::user()->id;

            $error = false;
            $message = 'Guest successfully updated';

            if ($request->hasFile('file_name')) {

                $file = $request->file('file_name');
                $fileNameWithExt = $request->file('file_name')->getClientOriginalName();
                // get file name
                $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                // get extension
                $extension = $request->file('file_name')->getClientOriginalExtension();

                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $fileNameToStore = rand() . date('ymdHis') . $file->getClientOriginalName();  // use this

                Log::info($fileNameWithExt);
                Log::info($filename);
                Log::info($extension);
                Log::info($fileNameToStore);

                // upload
                if ($op->photo != 'default.png') {
                    Storage::delete('mds/event/logo/' . $op->photo);
                }

                // $path = $request->file('file_name')->storeAs('private/mds/event/logo', $fileNameToStore);
                // Storage::disk('private')->putFileAs('mds/event/logo', $file, $fileNameToStore);
                $path = $file->move('storage/upload/profile_images/', $fileNameToStore);

                // Log::info($path);


            } else {
                $fileNameToStore = 'noimage.jpg';
            }

            $op->photo = $fileNameToStore;

            $op->guest_type_id = intval($request->guest_type_id);
            $op->prefix_id = intval($request->prefix_id);
            // $guest->event_id = session()->get('EVENT_ID');
            // $guest->booking_date = Carbon::createFromFormat('d/m/Y', $request->booking_date)->toDateString();
            $op->first_name = $request->first_name;
            $op->middle_name = $request->middle_name;
            $op->last_name = $request->last_name;
            $op->mobile_number = $request->mobile_number;
            $op->email = $request->email;
            $op->qid_passport = $request->qid_passport;
            $op->popular_name = $request->popular_name;
            $op->client_group_id = intval($request->client_group_id);
            $op->hosted_by_id = intval($request->hosted_by_id);
            $op->nationality_id = intval($request->nationality_id);
            $op->flight_preference = $request->flight_preference;
            $op->accomodation_preference = $request->accomodation_preference;
            $op->transportation_preference = $request->transportation_preference;
            $op->updated_by = $user_id;

            $op->save();
        }

        return response()->json([
            'error' => $error,
            'message' => $message,
        ]);
    }


    public function destroy($id)
    {
        // LOG::info('inside delete');
        $op = Guest::find($id);
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
        ];

        $validator = Validator::make($request->all(), $rules);

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
        $user_id = Auth::user()->id;

        $error = false;
        $message = 'Status successfully updated';

        $op->status_id = intval($request->status_id);
        $op->updated_by = $user_id;

        $op->save();


        return response()->json([
            'error' => $error,
            'message' => $message,
        ]);
    }

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
                return redirect()->route('ypi.admin.participant')->with('message', 'Event Switched.');
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
}
