<?php

namespace App\Http\Controllers\Gms\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gms\ClientGroup;
use App\Models\Designation;
use App\Models\Event;
use App\Models\Gms\AirlineCarriers;
use App\Models\Gms\Airport;
use App\Models\Gms\FlightCabin;
use App\Models\Gms\FlightStatus;
use App\Models\Gms\FlightType;
use App\Models\Gms\Guest;
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

class FlightController extends Controller
{
    //

    // public function index()
    // {
    //     $guestFlights = GuestFlight::with('guest')->get();

    //     $events = Event::all();
    //     $client_groups = ClientGroup::all();
    //     $guest_types = GuestType::all();
    //     $prefixes = Prefix::all();
    //     $hosted_by = HostedBy::all();
    //     $designations = Designation::all();
    //     $nationalities = Nationality::all();
    //     $airlines = AirlineCarriers::all();
    //     $cabins = FlightCabin::all();
    //     $airports = Airport::all();
    //     $flightTypes = FlightType::all();
    //     $flightStatuses = FlightStatus::all();

    //     return view('gms.admin.flight.list', compact(
    //         'guestFlights',
    //         'events',
    //         'guest_types',
    //         'prefixes',
    //         'client_groups',
    //         'hosted_by',
    //         'designations',
    //         'nationalities',
    //         'airlines',
    //         'cabins',
    //         'airports',
    //         'flightTypes',
    //         'flightStatuses'
    //     ));
    // }

    public function index()
    {
        // All guest flights for the table
        $guestFlights = GuestFlight::with('guest')->get();

        // All guests for the dropdown in Add Flight modal
        $guests = Guest::all()->map(function ($g) {
            $g->full_name = $g->first_name . ' ' . $g->last_name;
            return $g;
        });
        $events = Event::all();
        $client_groups = ClientGroup::all();
        $guest_types = GuestType::all();
        $prefixes = Prefix::all();
        $hosted_by = HostedBy::all();
        $designations = Designation::all();
        $nationalities = Nationality::all();
        $airlines = AirlineCarriers::all();
        $cabins = FlightCabin::all();
        $airports = Airport::all();
        $flightTypes = FlightType::all();
        $flightStatuses = FlightStatus::all();

        return view('gms.admin.flight.list', compact(
            'guestFlights',
            'guests',          // this is important for the dropdown
            'events',
            'guest_types',
            'prefixes',
            'client_groups',
            'hosted_by',
            'designations',
            'nationalities',
            'airlines',
            'cabins',
            'airports',
            'flightTypes',
            'flightStatuses'
        ));
    }

    public function list($id = null)
    {
        $search = request('search');
        $sort = request('sort') ?? 'id';
        $order = request('order') ?? 'DESC';
        $limit = request('limit') ?? 10;

        // Start query from GuestFlight with Guest relationship
        $query = GuestFlight::with(['guest', 'airline', 'cabin', 'flight_type', 'departureAirport', 'arrivalAirport', 'flightStatus']);

        if ($id) {
            $query->where('guest_id', $id);
        }

        // Search by guest ref_number, flight_number, or airline name
        if ($search) {
            $query->whereHas('guest', function ($q) use ($search) {
                $q->where('ref_number', 'like', "%$search%")
                    ->orWhere('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%");
            })->orWhereHas('airline', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        $total = $query->count();

        $flights = $query->orderBy($sort, $order)
            ->paginate($limit)
            ->through(function ($flight) {

                $guest = $flight->guest;



                $details_url = route('gms.admin.flight.detail', $guest->id);

                $actions =
                    '<div class="font-sans-serif btn-reveal-trigger position-static">' .
                    '<a href="javascript:void(0)" class="btn btn-sm" id="bookingDetails" data-id="' . $flight->id . '" title="View Booking Details">
                <i class="fas fa-lightbulb text-warning"></i></a>' .
                    '<a href="#" target="_blank" class="btn btn-sm" title="Generate Pass">
                <i class="fas fa-passport text-success"></i></a>' .
                    '<a href="javascript:void(0)" class="btn btn-sm" id="edit_guest_offcanv" data-id="' . $flight->id . '" title="Update">
                <i class="fa-solid fa-pen-to-square text-primary"></i></a>' .
                    '<a href="javascript:void(0)" class="btn btn-sm" id="deleteGuest" data-id="' . $flight->id . '" title="Delete">
                <i class="bx bx-trash text-danger"></i></a></div>';

                return [
                    'id' => $flight->id,
                    'ref_number' => '<div class="fw-bold fs-9 ms-2">
                        <a href="' . $details_url . '">' . $flight->guest->first_name . ' ' . $flight->guest->last_name . '</a>
                    </div>',

                    'flight_number' => '<div class="fs-9 ps-2">' . $flight->flight_number . '</div>',
                    'airline' => '<div class="fs-9 ps-2">' . optional($flight->airline)->name . '</div>',
                    'cabin' => '<div class="fs-9 ps-2">' . optional($flight->cabin)->cabin_name . '</div>',
                    'flight_type' => '<div class="fs-9 ps-2">' . optional($flight->flight_type)->title . '</div>',
                    'departure_airport' => '<div class="fs-9 ps-2">' . optional($flight->departureAirport)->airport_name . '</div>',
                    'arrival_airport' => '<div class="fs-9 ps-2">' . optional($flight->arrivalAirport)->airport_name . '</div>',
                    'departure_time' => '<div class="fs-9 ps-2">' . format_date($flight->departure_time) . '</div>',
                    'arrival_time' => '<div class="fs-9 ps-2">' . format_date($flight->arrival_time) . '</div>',
                    'duration' => '<div class="fs-9 ps-2">' . $flight->duration_minutes . '</div>',
                    'status' => '<div class="fs-9 ps-2">' . optional($flight->flightStatus)->status_name . '</div>',
                    'action' => $actions,
                    'created_at' => format_date($flight->created_at, 'H:i:s'),
                    'updated_at' => format_date($flight->updated_at, 'H:i:s'),
                ];
            });

        return response()->json([
            'rows' => $flights->items(),
            'total' => $total,
        ]);
    }


    public function store(Request $request)
    {
        $rules = [
            'flight_number' => 'required|string',
            'guest_id' => 'required|integer|exists:guests,id',
            'airline_id' => 'required|integer|exists:airline_carriers,id',
            'flight_cabin_id' => 'required|exists:cabin_types,id',
            'flight_type_id' => 'required|integer|exists:flight_types,id',
            'departure_airport_id' => 'required|integer|exists:airports,id',
            'arrival_airport_id' => 'required|integer|exists:airports,id',
            'departure_time' => 'required|date_format:d/m/Y',
            'arrival_time' => 'required|date_format:d/m/Y',
        ];


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => implode($validator->errors()->all('<div>:message</div>'))
            ]);
        }

        $user_id = Auth::id();

        $flight = new GuestFlight();
        $flight->guest_id = $request->guest_id;
        $flight->flight_number = $request->flight_number;
        $flight->airline_id = $request->airline_id;
        $flight->stops_number = $request->stops_number;
        $flight->flight_cabin_id = $request->flight_cabin_id;
        $flight->flight_type_id = $request->flight_type_id;
        $flight->departure_airport_id = $request->departure_airport_id;
        $flight->arrival_airport_id = $request->arrival_airport_id;
        $flight->departure_point_id = $request->departure_point_id;
        $flight->departure_time = Carbon::createFromFormat('d/m/Y', $request->departure_time)->toDateString();
        $flight->arrival_time = Carbon::createFromFormat('d/m/Y', $request->arrival_time)->toDateString();
        $flight->duration_minutes = $request->duration_minutes;
        $flight->status_id = $request->status_id;
        $flight->created_by = $user_id;
        $flight->updated_by = $user_id;

        $flight->save();

        return response()->json([
            'error' => false,
            'message' => 'Flight created successfully.'
        ]);
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
        $guest = Guest::find($id);
        $client_group = $guest->client_group;
        $events = Event::all(); // add this
        $airlines = AirlineCarriers::all();
        $cabins = FlightCabin::all();
        $airports = Airport::all();
        $flightTypes = FlightType::all();
        $flightStatuses = FlightStatus::all();

        return view('gms.admin.flight.detail', [
            'guestData' => $guest,
            'client_group' => $client_group,
            'airlines' => $airlines,
            'cabins' => $cabins,
            'events' => $events,
            'airports' => $airports,
            'flightTypes' => $flightTypes,
            'flightStatuses' => $flightStatuses,
        ]);
    }

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
