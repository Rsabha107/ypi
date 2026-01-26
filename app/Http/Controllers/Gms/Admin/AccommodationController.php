<?php

namespace App\Http\Controllers\Gms\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gms\ClientGroup;
use App\Models\Designation;
use App\Models\Event;
use App\Models\Gms\Guest;
use App\Models\Gms\GuestAccommodation;
use App\Models\Gms\GuestFlight;
use App\Models\Gms\GuestType;
use App\Models\Gms\HostedBy;
use App\Models\Nationality;
use App\Models\Gms\Prefix;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AccommodationController extends Controller
{
    //
    public function index()
    {
        $guests = GuestAccommodation::all();
        $events = Event::all();
        $client_groups = ClientGroup::all();
        $guest_types = GuestType::all();
        $prefixes = Prefix::all();
        $client_groups = ClientGroup::all();
        $hosted_by = HostedBy::all();
        $designations = Designation::all();
        $nationalities = Nationality::all();
        // $guests = Guest::with('client', 'schedule_period', 'cargo', 'zone', 'status', 'driver')->get();

        return view('gms.admin.guest.list', compact(
            'guests',
            'events',
            'guest_types',
            'prefixes',
            'client_groups',
            'hosted_by',
            'designations',
            'nationalities'
        ));
    }

    public function list($id = null)
    {
        // $guests = Guest::with('client', 'schedule_period', 'cargo', 'zone', 'status', 'driver')->get();
        // $guests = Guest::all();
        // $guests = Guest::with('client', 'schedule_period', 'cargo', 'zone', 'status', 'driver')->get();
        // $guests = Guest::with('client')->get();
        // $guests = Guest::with('client')->where('id', $id)->get();

        // dd($guests);
    
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $mds_schedule_event_filter = (request()->mds_schedule_event_filter) ? request()->mds_schedule_event_filter : "";
        $mds_schedule_venue_filter = (request()->mds_schedule_venue_filter) ? request()->mds_schedule_venue_filter : "";
        $mds_schedule_rsp_filter = (request()->mds_schedule_rsp_filter) ? request()->mds_schedule_rsp_filter : "";

        if ($id) {
            $guest = Guest::find($id);
            $ops = $guest->accommodations()->orderBy($sort, $order);
        } else {
            $ops = GuestAccommodation::orderBy($sort, $order);
        }

        // if ($search) {
        //     $venue = $venue->where(function ($query) use ($search) {
        //         $query->where('status', 'like', '%' . $search . '%')
        //         ->orWhere('period', 'like', '%' . $search . '%')
        //         ->orWhere('period', 'like', '%' . $search . '%')
        //             ->orWhere('id', 'like', '%' . $search . '%');
        //     });
        // }
        // if (session()->has('EVENT_ID')) {
        //     $current_event_id = session()->get('EVENT_ID');
        //     $ops = $ops->where('event_id', '=', $current_event_id);
        // }

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

            $actions =

                '<div class="font-sans-serif btn-reveal-trigger position-static">' .
                '<a href="javascript:void(0)" class="btn btn-sm" id="bookingDetails" data-id="' .
                $op->id .
                '" data-table="guest_table" data-bs-toggle="tooltip" data-bs-placement="right" title="View Booking Details">' .
                '<i class="fas fa-lightbulb text-warning"></i></a>' .
                '<a href="' . route('mds.admin.booking.pass.pdf', $op->id) . '"  target="_blank" class="btn btn-sm" id="generateBookingPass" data-id="' .
                $op->id .
                '" data-table="guest_table" data-bs-toggle="tooltip" data-bs-placement="right" title="Generate Pass">' .
                '<i class="fas fa-passport text-success"></i></a>' .
                '<a href="javascript:void(0)" class="btn btn-sm" id="edit_guest_offcanv" data-id="' .
                $op->id .
                '" data-table="guest_table" data-bs-toggle="tooltip" data-bs-placement="right" title="Update">' .
                '<i class="fa-solid fa-pen-to-square text-primary"></i></a>' .
                '<a href="javascript:void(0)" class="btn btn-sm" data-table="guest_table" data-id="' .
                $op->id .
                '" id="deleteGuest" data-bs-toggle="tooltip" data-bs-placement="right" title="Delete">' .
                '<i class="bx bx-trash text-danger"></i></a></div></div>';

            $details_url = route('gms.admin.guest.detail', $op->id);

            return  [
                'id' => $op->id,
                'name' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->name . '</div>',
                'room_type' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->roomType->name . '</div>',
                'property_type' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->property->name . '</div>',
                'room_number' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->room_number . '</div>',
                'check_in_date' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->check_in_date . '</div>',
                'check_out_date' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->check_out_date . '</div>',
                // 'status' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->flightStatus->status_name . '</div>',
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
            'flight_number' => 'required',
            'guest_id' => 'required',
            'airline_id' => 'required',
            'flight_cabin_id' => 'required',
            'flight_type_id' => 'required',
            'departure_airport_id' => 'required',
            'arrival_airport_id' => 'required',
            'departure_time' => 'required',
            'arrival_time' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info($validator->errors());
            $error = true;
            // $type = 'success';
            // $message = 'Guest could not be created';
            $message = implode($validator->errors()->all('<div>:message</div>'));

        } else {

            $user_id = Auth::user()->id;
            $op = new GuestFlight();

            $error = false;
            $message = 'Flight created succesfully.';
            $op->guest_id = intval($request->guest_id);
            $op->flight_number = $request->flight_number;
            // $op->booking_date = Carbon::createFromFormat('d/m/Y', $request->booking_date)->toDateString();
            $op->airline_id = intval($request->airline_id);
            $op->stops_number = $request->stops_number;
            $op->flight_cabin_id = intval($request->flight_cabin_id);
            $op->flight_type_id = intval($request->flight_type_id);
            $op->departure_airport_id = intval($request->departure_airport_id);
            $op->arrival_airport_id = intval($request->arrival_airport_id);
            $op->departure_point_id = intval($request->departure_point_id);
            $op->departure_time = Carbon::createFromFormat('d/m/Y', $request->departure_time)->toDateString();
            $op->arrival_time = Carbon::createFromFormat('d/m/Y', $request->arrival_time)->toDateString();
            $op->duration_minutes = $request->duration_minutes;
            $op->status_id = intval($request->status_id);
            $op->created_by = $user_id;
            $op->updated_by = $user_id;

            $op->save();
        }

        // $notification = array(
        //     'message'       => $message,
        //     'alert-type'    => $type
        // );

        // return redirect()->route('gms.admin.guests')->with($notification);
        // return view('mds.admin.booking.confirmation', ['data' => $guest]);


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

        $ops = Guest::find($id);
        $client_group = $ops->client_group;



        // dd($taskData);
        $count = $ops->count();
        return view('gms.admin.guest.detail', [
            'count' => $count,
            'guest_data' => $ops,
            'client_group' => $client_group,
        ]);
    }  // end detail

    // public function detail(Request $request, $id)
    // {

    //     $ops = Guest::find($id);
    //     $client_group = $ops->client_group;



    //     // dd($taskData);
    //     $count = $ops->count();
    //     return view('gms.admin.guest.d', [
    //         'count' => $count,
    //         'guest_data' => $ops,
    //         'client_group' => $client_group,
    //     ]);
    // }  // end detail
}
