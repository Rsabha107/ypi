<?php

namespace App\Http\Controllers\Vapp\Admin;

use App\Exports\VappRequestExport;
use App\Http\Controllers\Controller;
use App\Jobs\SendNewRequestEmailJob;
use App\Mail\ApprovedRequestMail;
use App\Mail\CollectedRequestMail;
use App\Mail\NewRequestMail;
use App\Mail\QrCodeMail;
use App\Mail\RejectedRequestMail;
use App\Mail\RfcRequestMail;
use App\Models\User;
use App\Models\Vapp\BookingSlot;
use App\Models\Vapp\CollectionDetail;
use App\Models\Vapp\FunctionalArea;
use App\Models\Vapp\DeliveryBooking;
use App\Models\Vapp\DeliveryRsp;
use App\Models\Vapp\DeliveryType;
use App\Models\Vapp\DeliveryVehicle;
use App\Models\Vapp\Venue;
use App\Models\Vapp\DeliveryZone;
use App\Models\Vapp\VappDriver;
use App\Models\Vapp\Event;
use App\Models\Vapp\MatchCategory;
use App\Models\Vapp\MatchList;
use App\Models\Vapp\ParkingMaster;
use App\Models\Vapp\VappInventory;
use App\Models\Vapp\VappRequest;
use App\Models\Vapp\VappRequestStatus;
use App\Models\Vapp\VappSize;
use App\Models\Vapp\VappVariation;

use Carbon\Carbon;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all();
        $venues = Venue::all();
        $statuses = VappRequestStatus::all();
        $variations = VappVariation::where('event_id', session()->get('EVENT_ID'))->get();
        $parkings = ParkingMaster::where('event_id', session()->get('EVENT_ID'))->get();
        $vapp_sizes = VappSize::all();
        $fas = FunctionalArea::all();

        return view('vapp.admin.booking.list', [
            'events' => $events,
            'venues' => $venues,
            'statuses' => $statuses,
            'parkings' => $parkings,
            'vapp_sizes' => $vapp_sizes,
            'variations' => $variations,
            'fas' => $fas
        ]);
    }

    public function export()
    {
        $fileName = 'vapp_requests_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new VappRequestExport, $fileName);
    }

    public function dashboard()
    {
        return view('vapp.admin.dashboard.index');
    }

    public function list()
    {
        appLog('inside Admin BookingController::list');

        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $event_filter = (request()->event_filter) ? request()->event_filter : "";
        $venue_filter = (request()->venue_filter) ? request()->venue_filter : "";
        $parking_filter = (request()->parking_filter) ? request()->parking_filter : "";
        $status_filter = (request()->status_filter) ? request()->status_filter : "";
        $vapp_size_filter = (request()->vapp_size_filter) ? request()->vapp_size_filter : "";
        $fa_filter = (request()->fa_filter) ? request()->fa_filter : "";
        $variation_filter = (request()->variation_filter) ? request()->variation_filter : "";
        $date_range_filter = (request()->date_range_filter) ? request()->date_range_filter : "";

        // if ($mds_date_range_filter == "") {
        //     $mds_date_range_filter = date('Y-m-d') . ' to ' . date('Y-m-d');
        // }

        // Carbon::createFromFormat('d/m/Y', $request->slot_visibility)->toDateString()

        $ops = VappRequest::orderBy($sort, $order);
        $ops = $ops->where('event_id', session()->get('EVENT_ID'));
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
            $ops = $ops->where(function ($query) use ($search) {
                $query->where('request_number', 'like', '%' . $search . '%');
            })
                ->orWhereHas('venue', function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('match_category', function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('match', function ($query) use ($search) {
                    $query->where('match_code', 'like', '%' . $search . '%');
                })
                ->orWhereHas('functional_area', function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('vapp_size', function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('parking', function ($query) use ($search) {
                    $query->where('parking_code', 'like', '%' . $search . '%');
                })
                ->orWhereHas(
                    'event',
                    function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    }
                );
        }


        if ($event_filter) {
            $ops = $ops->where('event_id', $event_filter);
        }

        if ($venue_filter) {
            $ops = $ops->where('venue_id', $venue_filter);
        }

        if ($parking_filter) {
            $ops = $ops->where('parking_id', $parking_filter);
        }

        if ($status_filter) {
            $ops = $ops->where('request_status_id', $status_filter);
        }

        if ($variation_filter) {
            $ops = $ops->where('variation_id', $variation_filter);
        }

        if ($vapp_size_filter) {
            $ops = $ops->where('vapp_size_id', $vapp_size_filter);
        }

        if ($fa_filter) {
            $ops = $ops->where('vapp_functional_area_id', $fa_filter);
        }

        if ($date_range_filter) {
            $dates = explode('to', $date_range_filter);
            $startDate = trim($dates[0]);
            if (count($dates) > 1) {
                $endDate = trim($dates[1]);
            } else {
                $endDate = null;
            }
            if ($startDate) {
                $startDate = Carbon::createFromFormat('d/m/y', $startDate)->toDateString();
            }
            if ($endDate) {
                $endDate = Carbon::createFromFormat('d/m/y', $endDate)->toDateString();
            }

            if ($startDate && $endDate) {
                $ops = $ops->whereBetween('request_date', [$startDate, $endDate]);
            } else if ($startDate) {
                $ops = $ops->where('request_date', '>=', $startDate);
            } else if ($endDate) {
                $ops = $ops->where('request_date', '<=', $endDate);
            }
        }

        $total = $ops->count();
        $limit = request("limit");
        $limit = max(1, min($limit, 100)); // min=1, max=100
        $ops = $ops->paginate($limit)->through(function ($op) {

            // $location = Location::find($booking->location_id);
            $details_url = route('vapp.admin.booking.request', $op->id);

            $actions =
                '<a href="' . $details_url . '" class="btn btn-sm" id="editBooking" data-id="' .
                $op->id .
                '" data-table="bookings_table" data-bs-toggle="tooltip" data-bs-placement="right" title="Update">' .
                '<i class="fa-solid fa-pen-to-square text-primary"></i></a>' .
                '<a href="javascript:void(0)" class="btn btn-sm" data-table="bookings_table" data-id="' .
                $op->id .
                '" id="deleteBooking" data-bs-toggle="tooltip" data-bs-placement="right" title="Delete">' .
                '<i class="bx bx-trash text-danger"></i></a></div></div>';

            $order_status =  '<span class="badge badge-phoenix fs--2 badge-phoenix-' . $op->status?->color . ' "><span class="badge-label" style="cursor: default;">' . $op->status?->title . '</span><span class="ms-1" data-feather="x" style="height:12.8px;width:12.8px;"></span></span>';
            $approved_vapps = $op->approved_vapps ?? '0';
            return  [
                'id' => $op->id,
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$op->id. '</div>',
                'request_number' => '<div class="align-middle white-space-wrap fs-9 ps-2"><a href="' . $details_url . '">' .  $op->request_number . '</a></div>',
                'event_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->event?->name . '</div>',
                'venue_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->venue?->short_name . '</div>',
                'variation_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">VAR-' . $op->variation_id . '</div>',
                'parking_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->parking?->parking_code . '</div>',
                'match_category_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->match_category?->title . '</div>',
                'match_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->match?->match_code . '</div>',
                'request_date' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . format_date($op->request_date) . '</div>',
                'functional_area_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->functional_area?->title . '</div>',
                'vapp_size_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->vapp_size?->title . '</div>',
                'requested_vapps' => '<div class="align-middle white-space-wrap fs-9 fw-bold ps-2">' . $op->requested_vapps . '</div>',
                'approved_vapps' => '<div class="align-middle white-space-wrap fs-9 ps-2 fw-bold text-success">' . $approved_vapps . '</div>',
                'status' => $order_status,
                'action' => $actions,
                'created_at' => format_date($op->created_at,  'H:i:s'),
                'updated_at' => format_date($op->updated_at, 'H:i:s'),
                'created_by' => $op->user_created_by?->name,
                'updated_by' => $op->user_updated_by?->name,
            ];
        });

        return response()->json([
            "rows" => $ops->items(),
            "total" => $total,
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $user_fa = $user->fa;
        $event = Event::find(session()->get('EVENT_ID'));
        $fa = $user->fa->pluck('id')->toArray();
        $varParkingCode = VappVariation::with('functional_areas', 'parking')
            ->when($fa, function ($query, $fa) {
                $query->whereHas('functional_areas', function ($q2) use ($fa) {
                    $q2->whereIn('fa_id', $fa)
                        ->where('event_id', session()->get('EVENT_ID'));
                });
            })
            ->get()
            ->unique('parking_id');

        $matchCategories = MatchCategory::all();

        $userFa = $user_fa;
        return view('vapp.admin.booking.create', compact(
            'varParkingCode',
            'matchCategories',
            'userFa',
            'event'
        ));
    }

    // get vairations for the parking code selected and display the match category to choose from
    public function getParkingCodeByFa(Request $request)
    {
        appLog('inside getParkingCodeByFa');
        appLog('request: '. json_encode($request->all()));
        // $user = Auth::user();

        // get the parking based on the functional areas from Variations
        // $results = VappVariation::join('parking_master as pm', 'vapp_variations.parking_id', '=', 'pm.id')
        //     ->join('vapp_variation_fa as favv', 'vapp_variations.id', '=', 'favv.vapp_variation_id')
        //     ->where('favv.fa_id', '=', $request->var_fa_id)
        //     ->where('vapp_variations.event_id', session()->get('EVENT_ID'))
        //     ->select('vapp_variations.parking_id', 'pm.parking_code')
        //     ->distinct()
        //     ->get();

        $results = VappVariation::join('parking_master as pm', 'vapp_variations.parking_id', '=', 'pm.id')
            ->leftJoin('vapp_variation_fa as favv', 'vapp_variations.id', '=', 'favv.vapp_variation_id')
            ->where(function ($query) use ($request) {
                $query->where('favv.fa_id', $request->var_fa_id)
                    ->orWhereNull('favv.fa_id'); // This means: no vapp_variation_fa entry exists
            })
            ->where('vapp_variations.event_id', session()->get('EVENT_ID'))
            ->where('pm.event_id', session()->get('EVENT_ID'))
            ->select('vapp_variations.parking_id', 'pm.parking_code')
            ->distinct()
            ->orderBy('pm.parking_code')
            ->get();

        // $baseQuery = DB::table('vapp_variations')
        //     ->join('parking_master as pm', 'vapp_variations.parking_id', '=', 'pm.id')
        //     ->join('vapp_variation_fa as favv', 'vapp_variations.id', '=', 'favv.vapp_variation_id')
        //     ->where('favv.fa_id', '=', $request->var_fa_id)
        //     ->where('vapp_variations.event_id', session()->get('EVENT_ID'))
        //     ->select('vapp_variations.parking_id', 'pm.parking_code')
        //     ->distinct();

        // $additionalQuery = DB::table('vapp_variations')
        //     ->join('parking_master as pm', 'vapp_variations.parking_id', '=', 'pm.id')
        //     ->join('vapp_variation_fa as favv', 'vapp_variations.id', '=', 'favv.vapp_variation_id')
        //     ->where('favv.fa_id', '=', $another_fa_id) // change this value
        //     ->where('vapp_variations.event_id', session()->get('EVENT_ID'))
        //     ->select('vapp_variations.parking_id', 'pm.parking_code')
        //     ->distinct();

        // $results = $baseQuery->union($additionalQuery)->get();

        // $variationCategory = VappVariation::with('match_category')
        //     ->where('parking_id', $request->parking_id)
        //     ->where('event_id', session()->get('EVENT_ID'))
        //     ->get();

        return response()->json(['variationParkingCode' => $results]);
    }

    // get vairations for the parking code selected and display the match category to choose from
    public function getVariationsFromParkingCode(Request $request)
    {
        appLog('inside getVariationsFromParkingCode');
        appLog($id);
        $user = Auth::user();

        // get the variations based on the parking code and functional areas
        $variationCategory = VappVariation::with('match_category')
            ->where('parking_id', $request->parking_id)
            ->where('event_id', session()->get('EVENT_ID'))
            ->get();

        return response()->json(['variationCategory' => $variationCategory]);
    }

    //get all matches from match list if match category selected is 'MATCH' ortherwise get all matches from vapp variations based on the parking code and functional areas
    public function getMatchesFromMatchCategory(Request $request)
    {
        appLog('inside getMatchesFromMatchCategory');
        appLog('request: '. json_encode($request->all()));
        $user = Auth::user();
        $fas = $user->fa;
        $fa = $user->fa->pluck('id')->toArray();

        // get the match category id
        $matchCategory = MatchCategory::find($request->match_category_id);

        // get all matches from match list
        $matches = MatchList::where('event_id', session()->get('EVENT_ID'))
            ->where('match_category_id', $request->match_category_id)
            ->orderBy('match_code')
            ->get();

        appLog('matchCategory: ' . $matchCategory->title);
        if ($matchCategory->title == 'MATCH') {
            return response()->json([
                'matches' => $matches,
                'functional_areas' => $fas,
                'variation_venues' => []
            ]);
        } elseif ($matchCategory->title == 'ALL') {

            // get all venues from vapp variations based on the parking code and functional areas
            $varVenues = Venue::whereHas('vapp_variations', function ($q) use ($request, $fa) {
                $q->where('parking_id', $request->parking_id)
                    ->where('match_category_id', $request->match_category_id)
                    ->where('event_id', session()->get('EVENT_ID'));
            })->distinct()->get();

            return response()->json(['matches' => $matches, 'functional_areas' => $fas, 'variation_venues' => $varVenues]);
        }

        return response()->json(['matches' => $matches]);
    }

    // get all venues from matches, if match category is 'MATCH' then get all venues from match list, otherwise get all venues from vapp variations based on the parking code and functional areas
    public function getVenuesFromMatch(Request $request)
    {
        appLog('inside getVenuesFromMatchCategory');
        appLog('request: '. json_encode($request->all()));
        $user = Auth::user();
        $fas = $user->fa;
        $fa = $user->fa->pluck('id')->toArray();

        // get the match category id
        $matchCategory = MatchCategory::find($request->match_category_id);

        // get all venues from match list
        $matches = MatchList::with('venue')
            ->where('event_id', session()->get('EVENT_ID'))
            ->where('match_category_id', $request->match_category_id)
            ->where('id', $request->match_id)
            ->get();

        appLog('matchCategory: ' . $matchCategory->title);
        if ($matchCategory->title == 'MATCH') {
            return response()->json([
                'matches' => $matches,
                'functional_areas' => $fas
            ]);
        } elseif ($matchCategory->title == 'ALL') {

            // get all venues from vapp variations based on the parking code and functional areas
            $varVenues = Venue::whereHas('vapp_variations', function ($q) use ($request, $fa) {
                $q->where('parking_id', $request->parking_id)
                    ->where('match_category_id', $request->match_category_id)
                    ->where('event_id', session()->get('EVENT_ID'));
            })->distinct()->get();

            return response()->json(['matches' => $matches, 'functional_areas' => $fas, 'variation_venues' => $varVenues]);
        }
    }

    //get the color from parking code
    public function getParkingColor(Request $request)
    {
        appLog('inside getParkingColor');
        appLog('request: '. json_encode($request->all()));
        $parking = ParkingMaster::find($request->parking_id);
        if ($parking) {
            return response()->json(['parking_color' => $parking->vapp_color]);
        } else {
            return response()->json(['parking_color' => null]);
        }
    }

    // get all distinct venues from vapp variations based on the parking code
    // public function getVenuesFromVappCode($id = null)
    // {

    //     $varVenues = Venue::whereHas('vapp_variations', function ($q) use ($id) {
    //         $q->where('parking_id', $id);
    //     })->distinct()->get();

    //     return response()->json(['venues' => $varVenues]);
    // }

    // get all match codes from vapp variations based on the venue code
    // public function getMatchesFromVappVenue($id = null)
    // {
    //     $matches = MatchList::where('venue_id', $id)
    //         ->orderBy('match_code')
    //         ->get();

    //     return response()->json(['matches' => $matches]);
    // }

    public function getMatchesOfParking(Request $request)
    {

        $user = Auth::user();

        $matches = MatchList::where('parking_id', $request->parking_id)
            ->where('event_id', session()->get('EVENT_ID'))
            ->get();

        $fa = $user->fa->pluck('id')->toArray();

        $venueid = $request->venue_id;
        $venueidArr = [$venueid];

        // get the variation match codes based on the parking code and functional areas
        $varMatchCode = VappVariation::with('functional_areas', 'match_category')
            ->where('parking_id', $request->parking_id)
            ->when($fa, function ($query, $fa) {
                appLog('inside when');
                appLog($fa);
                $query->whereHas('functional_areas', function ($q2) use ($fa) {
                    $q2->whereIn('fa_id', $fa)
                        ->where('event_id', session()->get('EVENT_ID'));
                });
            })
            ->when($venueidArr, function ($query, $venueid) {
                appLog('inside when');
                appLog($venueid);
                $query->whereHas('venues', function ($q2) use ($venueid) {
                    $q2->whereIn('venue_id', $venueid)
                        ->where('event_id', session()->get('EVENT_ID'));
                });
            })
            ->get();

        $varMatchCategoryId = $varMatchCode->pluck('match_category_id')->toArray();
        $variationId = $varMatchCode->pluck('match_category_id')->toArray();

        $matches = MatchList::where('venue_id', $venueid)
            ->where('event_id', session()->get('EVENT_ID'))
            ->whereIn('match_category_id', $varMatchCategoryId)
            ->get();

        return response()->json(['matches' => $matches]);
    }

    public function getMatchesFromVappVenue(Request $request)
    {

        $user = Auth::user();
        $fa = $user->fa->pluck('id')->toArray();

        $venueid = $request->venue_id;
        $venueidArr = [$venueid];

        // get the variation match codes based on the parking code and functional areas
        $varMatchCode = VappVariation::with('functional_areas', 'match_category')
            ->where('parking_id', $request->parking_id)
            ->when($fa, function ($query, $fa) {
                appLog('inside when');
                appLog($fa);
                $query->whereHas('functional_areas', function ($q2) use ($fa) {
                    $q2->whereIn('fa_id', $fa)
                        ->where('event_id', session()->get('EVENT_ID'));
                });
            })
            ->when($venueidArr, function ($query, $venueid) {
                appLog('inside when');
                appLog($venueid);
                $query->whereHas('venues', function ($q2) use ($venueid) {
                    $q2->whereIn('venue_id', $venueid)
                        ->where('event_id', session()->get('EVENT_ID'));
                });
            })
            ->get();

        $varMatchCategoryId = $varMatchCode->pluck('match_category_id')->toArray();
        $variationId = $varMatchCode->pluck('match_category_id')->toArray();

        $matches = MatchList::where('venue_id', $venueid)
            ->where('event_id', session()->get('EVENT_ID'))
            ->whereIn('match_category_id', $varMatchCategoryId)
            ->get();

        return response()->json(['matches' => $matches]);
    }

    // get all match codes from vapp variations based on the venue code
    public function getVariation(Request $request)
    {

        try {
            $variationRecord = VappVariation::where('parking_id', $request->parking_id)
                ->where('match_category_id', $request->match_category_id)
                ->where('event_id', session()->get('EVENT_ID'))
                ->with('vapp_sizes', 'venues', 'functional_areas')
                // ->whereHas('venues', function ($q) use ($request) {
                //     $q->where('venues.id', $request->venue_id);
                // })
                ->first();

            if (!$variationRecord) {
                return response()->json(['error' => true, 'message' => 'Variation rule not found, please contact VAPP Admin']);
            }
            return response()->json(['error' => false, 'variation' => $variationRecord]);
        } catch (\Exception $e) {
            appLog('Error fetching variation: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Error fetching variation']);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        // dd($request);
        // $user_id = Auth::user()->id;
        $user = Auth::user();
        $op = new VappRequest();
        $inventory = VappInventory::where('variation_id', $request->variation_id)
            ->where('event_id', session()->get('EVENT_ID'))
            ->first();

        $variation = VappVariation::with('inventory', 'vapp_sizes')
            ->where('id', $request->variation_id)
            ->where('event_id', session()->get('EVENT_ID'))
            ->first();

       // appLog('BookingController::store variation: ' . $variation);
       // appLog($variation->inventory()->where('vapp_size_id', 39)->first());
        // $timeslots = DeliverySchedulePeriod::findOrFail($request->schedule_period_id);

        $rules = [
            'var_functional_area_id' => 'required',
            'parking_id' => 'required',
            'match_category_id' => 'required',
            // 'match_id' => 'required',
            'venue_id' => 'required',
            // 'variation_id' => 'required',
            'vapp_size_id' => 'required',
            'requested_vapps' => 'required',
        ];

        $message = [
            'var_functional_area_id.required' => 'Functional Area is required.',
            'parking_id.required' => 'Parking Code is required.',
            'match_category_id.required' => 'Match Category is required.',
            // 'match_id.required' => 'Match is required.',
            'venue_id.required' => 'Venue is required.',
            // 'variation_id.required' => 'Variation is required.',
            'vapp_size_id.required' => 'Vapp Size is required.',
            'requested_vapps.required' => 'Requested Vapps are required.'
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            appLog($validator->errors());
            $error = true;

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // check number of slots available.  if available slots = 0 then exit with a warning message.
        // this is incase a user grabed the last slot with this user is waiting ..
        DB::beginTransaction();
        try {
            $error = false;
            $type = 'success';
            $message = 'Request created succesfully.' . $op->id;

            appLog('request: '. json_encode($request->all()));
            $op->venue_id = $request->venue_id;
            $op->parking_id = $request->parking_id;
            $op->match_id = $request->match_id;
            $op->match_category_id = $request->match_category_id;
            $op->variation_id = $request->variation_id;
            $op->vapp_size_id = $request->vapp_size_id;
            $op->vapp_functional_area_id = $request->var_functional_area_id;
            $op->requested_vapps = $request->requested_vapps;
            $op->approved_vapps = 0;
            $op->request_date = now()->toDateString();
            $op->justification = $request->justification;
            $op->comments = $request->comments;
            $op->request_status_id = getRequestStatusIdByLabel('In-progress'); // in progress
            $op->event_id = session()->get('EVENT_ID');
            $op->created_by = $user->id;
            $op->updated_by = $user->id;
            $op->save();

            $result = Builder::create()
                ->writer(new PngWriter())           // Ensure PNG format
                ->data($op->request_number)                       // QR code content
                ->size(300)                         // Image size (px)
                ->margin(10)                        // Margin (quiet zone)
                ->backgroundColor(new Color(255, 255, 255)) // Background color
                ->build();

            $filename = 'qrcodes/profile-' . $op->id . '-' . Str::random(6) . '.png';

            Storage::put('public/' . $filename, $result->getString());
            // Get the full path
            $filePath = storage_path('app/public/' . $filename);

            // $filePath = public_path('qrcodes/sps-visitor-' . $profile->id . '-' . Str::random(6) . '.png');
            // $result->saveToFile($filePath);
            if (config('settings.send_notifications')) {
                if ($op->match_category_id == getMatchCategoryIdByLabel('ALL')) {
                    $match_code = 'All Matches';
                } else {
                    $match_code = $op->match?->match_code_date;
                }
                $details = [
                    'email' => config('settings.admin_email'),
                    'requester_name' => $user->name,
                    'event' => $op->event?->name,
                    'venue' => $op->venue?->title,
                    'match' => $match_code,
                    'request_status' => $op->status?->title,
                    'request_ref_number' => $op->request_number,
                    'parking_code' => $op->parking?->parking_code,
                    'vapp_size' => $op->vapp_size?->title,
                    'requested_quantity' => $op->requested_vapps,
                    'approved_quantity' => $op->approved_vapps,
                ];
                // SendNewRequestEmailJob::dispatch($details);

                Mail::to($user->email)->send(new NewRequestMail($details, $filePath));
            }

            $notification = array(
                'message'       => $message,
                'alert-type'    => $type
            );

            DB::commit();

            $notification = array(
                'message'       => $message,
                'alert-type'    => $type
            );

            // return redirect()->route('vapp.booking.add')->with($notification);
            return redirect()->route('vapp.admin.booking')->with($notification);
            // return response()->json(['error' => $error, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            appLog('Error creating request: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while creating the request. Please try again.')
                ->withInput();
        }
    }

    public function delete($id)
    {
        appLog('inside delete');
        $op = VappRequest::find($id);

        $op->delete();

        $error = false;
        $message = 'Request deleted succesfully.';

        $notification = array(
            'message'       => 'Booking deleted successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
        // return redirect()->route('tracki.setup.workspace')->with($notification);
    } // delete

    public function editStatus($id)
    {
        //  dd('editTaskProgress');
        $data = VappRequest::find($id);
        //dd($data);
        $data_arr = [];

        $data_arr[] = [
            "id"        => $data->id,
            "status_id"  => $data->request_status_id,
        ];

        $response = ["retData"  => $data_arr];
        return response()->json($response);
    } // editStatus

    public function updateStatus(Request $request)
    {

        $head = VappRequest::findOrFail($request->id);
        $status_title = VappRequestStatus::findOrFail($request->status_id);

        appLog($status_title->title);
        $head->update([
            'request_status_id' => $request->status_id,
        ]);

        $notification = array(
            'message'       => 'Request status updated successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => false, 'message' => 'Order Status updated successfully.', 'id' => $head->id]);
    } //updateStatus

    /**
     * Display the specified resource.
     */
    public function showRequest(string $id)
    {

        appLog('BookingController::showRequest id: ' . $id);
        $vapp = VappRequest::findOrFail($id);
        // dd($vapp);
        $requestStatus = VappRequestStatus::all();
        // dd($op);

        appLog('BookingController::showRequest vapp: ' . json_encode($vapp));
        $variation = VappVariation::with('inventory', 'vapp_sizes')
            ->where('id', $vapp->variation_id)
            ->where('venue_id', $vapp->venue_id)
            // ->where('event_id', session()->get('EVENT_ID'))
            ->where('event_id', $vapp->event_id)
            ->first();

        appLog('BookingController::showRequest variation: ' . $variation);

        // Inventory calculation
        // MATCH and ALL calculate separately
        if ($vapp->match_category_id === getMatchCategoryIdByLabel('MATCH')) {
            appLog('BookingController::showRequest MATCH category');
            $inventory = VappInventory::where('variation_id', $vapp->variation_id)
                // ->where('event_id', session()->get('EVENT_ID'))
                ->where('event_id', $vapp->event_id)
                ->where('venue_id', $vapp->venue_id)
                ->where('parking_id', $vapp->parking_id)
                ->where('vapp_size_id', $vapp->vapp_size_id)
                ->where('match_category_id', $vapp->match_category_id)
                ->where('match_id', $vapp->match_id)
                ->first();

            $inventory_collected_vaps = get_inv_totals_match($vapp->event_id, $vapp->venue_id, $vapp->vapp_size_id, $vapp->parking_id, $vapp->variation_id, $vapp->match_category_id, $vapp->match_id, 'Collected');
            $inventory_rfc_vaps = get_inv_totals_match($vapp->event_id, $vapp->venue_id, $vapp->vapp_size_id, $vapp->parking_id, $vapp->variation_id, $vapp->match_category_id, $vapp->match_id, 'Ready for Collection');
        } elseif ($vapp->match_category_id === getMatchCategoryIdByLabel('ALL')) {
            appLog('BookingController::showRequest ALL category');
            $inventory = VappInventory::where('variation_id', $vapp->variation_id)
                // ->where('event_id', session()->get('EVENT_ID'))
                ->where('event_id', $vapp->event_id)
                ->where('venue_id', $vapp->venue_id)
                ->where('parking_id', $vapp->parking_id)
                ->where('vapp_size_id', $vapp->vapp_size_id)
                ->where('match_category_id', $vapp->match_category_id)
                ->first();

            $inventory_collected_vaps = get_inv_totals_all($vapp->event_id, $vapp->venue_id, $vapp->vapp_size_id, $vapp->parking_id, $vapp->variation_id, $vapp->match_category_id, 'Collected');
            $inventory_rfc_vaps = get_inv_totals_all($vapp->event_id, $vapp->venue_id, $vapp->vapp_size_id, $vapp->parking_id, $vapp->variation_id, $vapp->match_category_id, 'Ready for Collection');
        }

        // $inventory_collected_vaps = get_totals($vapp->event_id, $vapp->venue_id, $vapp->vapp_size_id, $vapp->parking_id, $vapp->variation_id, 'Collected');
        // $inventory_rfc_vaps = get_totals($vapp->event_id, $vapp->venue_id, $vapp->vapp_size_id, $vapp->parking_id, $vapp->variation_id, 'Ready for Collection');

        // dd($collected_vaps, $rfc_vaps);
        $inv_total_collected_vaps = $inventory_collected_vaps + $inventory_rfc_vaps;
        $inv_total_available_vaps = $inventory?->printed_vaps - $inv_total_collected_vaps;


        // Capacity/Availablity calculation  *************************************************
        // initialize capacity variable
        $capacity = 0;

        $approved = 0;
        $rfc = 0;
        $collected = 0;
        $approved_all = 0;
        $rfc_all = 0;
        $collected_all = 0;
        $approved_sta_inf = 0;
        $rfc_sta_inf = 0;
        $collected_sta_inf = 0;

        $capacity = get_parking_capacity($vapp->event_id, $vapp->venue_id,  $vapp->vapp_size_id,  $vapp->parking_id, $vapp->variation_id, 'c');
        // getting totals for approved, ready for collection and collected for matchs and all for the request.
        $approved = get_capacity_totals($vapp->event_id, $vapp->venue_id,  $vapp->vapp_size_id,  $vapp->parking_id, $vapp->variation_id, $vapp->match_category_id, $vapp->match_id, 'Approved');
        $rfc = get_capacity_totals($vapp->event_id, $vapp->venue_id,  $vapp->vapp_size_id,  $vapp->parking_id, $vapp->variation_id, $vapp->match_category_id, $vapp->match_id, 'Ready for Collection');
        $collected = get_capacity_totals($vapp->event_id, $vapp->venue_id,  $vapp->vapp_size_id,  $vapp->parking_id, $vapp->variation_id, $vapp->match_category_id, $vapp->match_id, 'Collected');

        if ($vapp->match_category_id === getMatchCategoryIdByLabel('MATCH')) {
            appLog('BookingController::showRequest MATCH category');

            // we need to get sum of match category ALL for the same event, venue, parking, vapp_size.
            $approved_all = get_capacity_totals_all($vapp->event_id, $vapp->venue_id,  $vapp->vapp_size_id,  $vapp->parking_id, $vapp->match_category_id, 'Approved');
            $rfc_all = get_capacity_totals_all($vapp->event_id, $vapp->venue_id,  $vapp->vapp_size_id,  $vapp->parking_id, $vapp->match_category_id, 'Ready for Collection');
            $collected_all = get_capacity_totals_all($vapp->event_id, $vapp->venue_id,  $vapp->vapp_size_id,  $vapp->parking_id, $vapp->match_category_id, 'Collected');
        }

        // get the sum of STA and INF venues for event, parking, vapp_size.
        $approved_sta_inf = get_capacity_totals_sta_inf($vapp->event_id,  $vapp->vapp_size_id,  $vapp->parking_id, 'Approved');
        $rfc_sta_inf = get_capacity_totals_sta_inf($vapp->event_id,  $vapp->vapp_size_id,  $vapp->parking_id, 'Ready for Collection');
        $collected_sta_inf = get_capacity_totals_sta_inf($vapp->event_id, $vapp->vapp_size_id,  $vapp->parking_id, 'Collected');

        $total_approved_capacity_match = $approved + $rfc + $collected;
        $total_approved_capacity_all = $approved_all + $rfc_all + $collected_all;
        $total_approved_capacity_sta_inf = $approved_sta_inf + $rfc_sta_inf + $collected_sta_inf;
        $total_approved_capacity = $total_approved_capacity_match + $total_approved_capacity_all + $total_approved_capacity_sta_inf;
        $available_capacity = $capacity - $total_approved_capacity;
        appLog('BookingController::showRequest variation: ' . $variation);

        return view('vapp.admin.booking.request', compact(
            'vapp',
            'variation',
            'capacity',
            'total_approved_capacity',
            'available_capacity',
            'inventory',
            'inv_total_collected_vaps',
            'inv_total_available_vaps',
            'requestStatus',
        ));
    }

    public function saveRequest(Request $request)
    {
        // dd($request);
        $user = Auth::user();
        $op = VappRequest::find($request->id);

        $rules = [
            'approved_vapps' => 'required',
            'request_status_id' => 'required',
            'comments' => 'nullable|string|max:500'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            appLog($validator->errors());
            $error = true;
            $message = implode('<br>', $validator->errors()->all());
            return redirect()->back()->with(['alert-type' => 'error', 'message' => $message]);
        }

        DB::beginTransaction();
        try {
            $error = false;
            $type = 'success';
            $message = 'Booking updated succesfully.' . $op->id;

            appLog('BookingController::saveRequest: ' . json_encode($request->all()));

            $op->approved_vapps = $request->approved_vapps;
            $op->comments = $request->comments;
            $op->request_status_id = $request->request_status_id;
            // $op->updated_by = $user_id;
            $op->save();

            if (config('settings.send_notifications')) {
                // get the users details
                $collection_detail = CollectionDetail::where('event_id', $op->event_id)->first();
                $requester_user = User::find($op->created_by);
                if ($op->match_category_id == getMatchCategoryIdByLabel('ALL')) {
                    $match_code = 'All Matches';
                } else {
                    $match_code = $op->match?->match_code_date;
                }
                $details = [
                    'email' => $requester_user->email,
                    'requester_name' => $requester_user->name,
                    'event' => $op->event?->name,
                    'venue' => $op->venue?->title,
                    'match' => $match_code,
                    'request_status' => $op->status?->title,
                    'request_ref_number' => $op->request_number,
                    'parking_code' => $op->parking?->parking_code,
                    'vapp_size' => $op->vapp_size?->title,
                    'requested_quantity' => $op->requested_vapps,
                    'approved_quantity' => $op->approved_vapps,
                    'collection_location' => $collection_detail->collection_location,
                    'collection_time' => $collection_detail->collection_time,
                ];
                if (getRequestStatusIdByLabel('Approved') == $request->request_status_id) {
                    // SendNewRequestEmailJob::dispatch($details);
                    $filePath = "";
                    Mail::to($requester_user->email)->send(new ApprovedRequestMail($details, $filePath));
                } elseif (getRequestStatusIdByLabel('Rejected') == $request->request_status_id) {
                    appLog('BookingController::saveRequest sending RejectedRequestMail to ' . $requester_user->email);
                    // SendNewRequestEmailJob::dispatch($details);
                    $filePath = "";
                    Mail::to($requester_user->email)->send(new RejectedRequestMail($details, $filePath));
                } elseif (getRequestStatusIdByLabel('Collected') == $request->request_status_id) {
                    // SendNewRequestEmailJob::dispatch($details);
                    $filePath = "";
                    Mail::to($requester_user->email)->send(new CollectedRequestMail($details, $filePath));
                } else if (getRequestStatusIdByLabel('Ready for Collection') == $request->request_status_id) {
                    $result = Builder::create()
                        ->writer(new PngWriter())           // Ensure PNG format
                        ->data($op->request_number)                       // QR code content
                        ->size(300)                         // Image size (px)
                        ->margin(10)                        // Margin (quiet zone)
                        ->backgroundColor(new Color(255, 255, 255)) // Background color
                        ->build();

                    $filename = 'qrcodes/profile-' . $op->id . '-' . Str::random(6) . '.png';

                    Storage::put('public/' . $filename, $result->getString());
                    // Get the full path
                    $filePath = storage_path('app/public/' . $filename);
                    Mail::to($requester_user->email)->send(new RfcRequestMail($details, $filePath));
                }
            }

            DB::commit();

            return redirect()->route('vapp.admin.booking')->with([
                'error' => $error,
                'message' => $message,
                'type' => $type
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            appLog('Error updating booking: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while updating the request. Please try again.')
                ->withInput();
        }
    }

    // public function detail($id)
    // {
    //     $booking = DeliveryBooking::findOrFail($id);

    //     // dd($project);

    //     // Log::alert('EmployeeController::getEmpEditView file_name: ' . $emp->emp_files?->file_name);

    //     $view = view('/vapp/admin/booking/mv/detail', [
    //         'booking' => $booking,
    //     ])->render();

    //     return response()->json(['view' => $view]);
    // }
    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(string $id)
    // {
    //     $booking = DeliveryBooking::find($id);
    //     // $intervals = DeliverySchedulePeriod::all();
    //     $venues = Venue::all();
    //     $rsps = DeliveryRsp::all();
    //     $drivers = VappDriver::all();
    //     $vehicles = DeliveryVehicle::all();
    //     // $vehicle_types = DeliveryVehicleType::all();
    //     $delivery_types = DeliveryType::all();
    //     $cargos = DeliveryType::all();
    //     $loading_zones = DeliveryZone::all();
    //     $clients = FunctionalArea::all();

    //     return view('vapp.admin.booking.edit', compact(
    //         'booking',
    //         // 'intervals',
    //         'venues',
    //         'rsps',
    //         'drivers',
    //         'vehicles',
    //         'vehicle_types',
    //         'delivery_types',
    //         'cargos',
    //         'loading_zones',
    //         'clients'
    //     ));
    // }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request)
    // {
    //     //
    //     //  dd($request);
    //     $user_id = Auth::user()->id;
    //     $booking = DeliveryBooking::find($request->id);
    //     $timeslots = BookingSlot::findOrFail($request->schedule_period_id);

    //     // dd($booking);
    //     $rules = [
    //         'booking_date' => 'required',
    //         'schedule_period_id' => 'required',
    //         'venue_id' => 'required',
    //         'driver_id' => 'required',
    //         'vehicle_id' => 'required',
    //         'vehicle_type_id' => 'required',
    //         'receiver_name' => 'required',
    //         'receiver_contact_number' => 'required',
    //         'dispatch_id' => 'required',
    //         'cargo_id' => 'required',
    //         'loading_zone_id' => 'required',
    //     ];

    //     $validator = Validator::make($request->all(), $rules);

    //     if ($validator->fails()) {
    //         appLog('validator: ' . $validator->errors());;
    //         $error = true;
    //         $type = 'error';
    //         $message = 'Booking could not be created';
    //     } else {

    //         // check number of slots available.  if available slots = 0 then exit with a warning message.
    //         // this is incase a user grabed the last slot with this user is waiting ..

    //         if ($timeslots->available_slots > 0) {

    //             $error = false;
    //             $type = 'success';
    //             $message = 'Booking updated succesfully.' . $booking->id;

    //             appLog('booking->schedule_period_id: ' . $booking->schedule_period_id);
    //             appLog('request->schedule_period_id: ' . $request->schedule_period_id);

    //             if ($request->schedule_period_id != $booking->schedule_period_id) {
    //                 appLog('booking->schedule_period_id: ' . $booking->schedule_period_id);
    //                 appLog('request->schedule_period_id: ' . $request->schedule_period_id);

    //                 $timeslots->available_slots = $timeslots->available_slots - 1;
    //                 $timeslots->used_slots = $timeslots->used_slots + 1;

    //                 $old_timeslot = BookingSlot::findOrFail($booking->schedule_period_id);
    //                 $old_timeslot->available_slots = $old_timeslot->available_slots + 1;
    //                 $old_timeslot->used_slots = $old_timeslot->used_slots - 1;
    //             } else {
    //                 $timeslots->available_slots = $timeslots->available_slots;
    //                 $timeslots->used_slots = $timeslots->used_slots;
    //             }

    //             // $booking->booking_ref_number = 'MDS' . $booking->id;
    //             // $booking->schedule_id =  $timeslots->delivery_schedule_id;
    //             $booking->schedule_period_id = $request->schedule_period_id;
    //             // $booking->booking_date = Carbon::createFromFormat('Y/m/d', $request->booking_date)->toDateString();
    //             $booking->booking_date = $request->booking_date;
    //             $booking->venue_id = $request->venue_id;
    //             $booking->client_id = $request->client_id;
    //             $booking->rsp_id = $timeslots->rsp_id;
    //             $booking->booking_party_company_name = $request->booking_party_company_name;
    //             $booking->booking_party_contact_name = $request->booking_party_contact_name;
    //             $booking->booking_party_contact_email = $request->booking_party_contact_email;
    //             $booking->booking_party_contact_number = $request->booking_party_contact_number;
    //             // $booking->delivering_party_company_name = $request->delivering_party_company_name;
    //             // $booking->delivering_party_contact_number = $request->delivering_party_contact_number;
    //             // $booking->delivering_party_contact_email = $request->delivering_party_contact_email;
    //             $booking->driver_id = $request->driver_id;
    //             $booking->vehicle_id = $request->vehicle_id;
    //             $booking->vehicle_type_id = $request->vehicle_type_id;
    //             $booking->receiver_name = $request->receiver_name;
    //             $booking->receiver_contact_number = $request->receiver_contact_number;
    //             $booking->dispatch_id = $request->dispatch_id;
    //             $booking->loading_zone_id = $request->loading_zone_id;
    //             $booking->cargo_id = $request->cargo_id;
    //             $booking->active_flag = $request->active_flag;
    //             $booking->created_by = $user_id;
    //             $booking->updated_by = $user_id;
    //             $booking->active_flag = 1;

    //             $timeslots->save();
    //             if (isset($old_timeslot)) {
    //                 $old_timeslot->save();
    //             }
    //             $booking->save();
    //         } else {
    //             $error = true;
    //             $type = 'error';
    //             $message = 'Time slot choosing has been used. please choose a different time slot.' . $booking->id;
    //         }
    //     }

    //     $notification = array(
    //         'message'       => $message,
    //         'alert-type'    => $type
    //     );

    //     return redirect()->route('vapp.admin.booking')->with($notification);
    //     // return view('vapp.admin.booking');


    //     // return response()->json(['error' => $error, 'message' => $message]);
    // }

    // public function passPdf(Request $request, $id)
    // {
    //     // set_time_limit(300);
    //     $booking = DeliveryBooking::findOrFail($id);
    //     $qr_code = getQrCode($booking->id, 100);


    //     $data = [
    //         'to' => 'Sam Example',
    //         'subtotal' => '5.00',
    //         'tax' => '.35',
    //         'total' => '5.35',
    //         'receipeint_company' => 'GWC Logistics',
    //         'booking' => $booking,
    //         'qr_code' => $qr_code,

    //     ];

    //     if ($request->has('preview')) {
    //         $data['css'] = asset('assets/css/invoice.css');
    //         return view('vapp.booking.passx', $data);
    //     } else {
    //         $data['css'] = public_path('assets/css/invoice.css');
    //     }

    //     // Pdf::view('vapp.booking.passx');
    //     // Pdf::view('vapp.booking.passx')->save('/upload/passx.pdf');
    //     // return view('vapp.booking.passx', $data);
    //     $pdf = Pdf::loadView('vapp.admin.booking.passx', $data);
    //     // return $pdf->download('itsolutionstuff.pdf');
    //     return $pdf->stream();
    // }  //taskDetailsPDF

    // public function get_times($date, $venue_id)
    // {
    //     appLog('inside get_times');
    //     $formated_date = Carbon::createFromFormat('dmY', $date)->toDateString();
    //     appLog('formated_date: '.$formated_date);
    //     appLog('venue_id: '.$venue_id);
    //     // $venue = DeliverySchedulePeriod::where('period_date', '=', $formated_date)
    //     //     ->where('venue_id', '=', $venue_id)
    //     //     // ->where('available_slots', '>', '0')
    //     //     ->get();

    //     // $venue = DeliverySchedulePeriod::all();

    //     // return response()->json(['venue' => $venue]);
    // }

    public function switch($id)
    {
        if ($id) {
            if (Event::findOrFail($id)) {
                appLog('Event ID: ' . $id);

                session()->put('EVENT_ID', $id);
                appLog('Event ID: ' . session()->get('EVENT_ID'));
                // return redirect()->route('tracki.project.show.card')->with('message', 'Workspace switched successfully.');
                return redirect()->route('vapp.admin.booking')->with('message', 'Event Switched.');
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
                return redirect()->route('vapp.admin.booking')->with('message', 'Event Switched.');
                // return back()->with('message', 'Event Switched.');
            }
        }
        //  else {
        // return back()->with('error', 'Workspace not found.');
        // return redirect()->route('tracki.project.show.card')->with('error', 'Workspace not found.');
        appLog('event_id is null');
        return redirect()->route('vapp.admin.booking')->with('error', 'Event not found.');
        // }
    }
}
