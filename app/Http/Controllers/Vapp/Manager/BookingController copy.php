<?php

namespace App\Http\Controllers\Vapp\Manager;

use App\Http\Controllers\Controller;
use App\Mail\NewRequestMail;
use App\Mail\QrCodeMail;
use App\Models\Vapp\BookingSlot;
use App\Models\Vapp\FunctionalArea;
use App\Models\Vapp\Venue;
use App\Models\Vapp\Event;
use App\Models\Vapp\MatchCategory;
use App\Models\Vapp\MatchList;
use App\Models\Vapp\ParkingMaster;
use App\Models\Vapp\VappInventory;
use App\Models\Vapp\VappRequest;
use App\Models\Vapp\VappRequestStatus;
use App\Models\Vapp\VappSize;
use App\Models\Vapp\VappVariation;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $variations = VappVariation::all();
        $parkings = ParkingMaster::all();
        $vapp_sizes = VappSize::all();
        $fas = FunctionalArea::all();

        return view('vapp.manager.booking.list', [
            'events' => $events,
            'venues' => $venues,
            'statuses' => $statuses,
            'parkings' => $parkings,
            'vapp_sizes' => $vapp_sizes,
            'variations' => $variations,
            'fas' => $fas
        ]);
    }

    public function list()
    {
        appLog('inside Admin BookingController::list');
        $user = Auth::user();
        $user_fa = $user->fa;
        $event = Event::find(session()->get('EVENT_ID'));
        $fa = $user->fa->pluck('id')->toArray();

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
        // $ops = $ops->where('created_by', Auth::id());
        $ops = $ops->where('event_id', session()->get('EVENT_ID'));
        $ops = $ops->whereIn('vapp_functional_area_id', $fa);

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
                // ->orWhereHas(
                //     'schedule_period',
                //     function ($query) use ($search) {
                //         $query->where('period', 'like', '%' . $search . '%');
                //     }
                // )
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
                )
                ->orWhereHas(
                    'user_name',
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

            $actions =
                '<a href="javascript:void(0)" class="btn btn-sm" data-table="bookings_table" data-id="' .
                $op->id .
                '" id="deleteRecord" data-bs-toggle="tooltip" data-bs-placement="right" title="Delete">' .
                '<i class="bx bx-trash text-danger"></i></a></div></div>';

            $order_status =  '<span class="badge badge-phoenix fs--2 badge-phoenix-' . $op->status->color . ' "><span class="badge-label" style="cursor: default;" data-table="bookings_table">' . $op->status->title . '</span><span class="ms-1" data-feather="x" style="height:12.8px;width:12.8px;"></span></span>';
            $approved_vapps = $op->approved_vapps ?? '0';
            return  [
                'id' => $op->id,
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$op->id. '</div>',
                'request_number' => '<div class="align-middle white-space-wrap fs-9 fw-bold ps-2 ms-2">' .  $op->request_number . '</div>',
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
                'action' => $op->status->title == "In-progress" ? $actions : '',
                // 'action' => $actions,
                'created_at' => format_date($op->created_at,  'H:i:s'),
                'updated_at' => format_date($op->updated_at, 'H:i:s'),
                'created_by' => $op->user_name?->name,
                'updated_by' => $op->user_name?->name,
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
        return view('vapp.customer.booking.create', compact(
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

        $variationRecord = VappVariation::where('parking_id', $request->parking_id)
            ->where('match_category_id', $request->match_category_id)
            ->where('event_id', session()->get('EVENT_ID'))
            ->with('vapp_sizes', 'venues', 'functional_areas')
            // ->whereHas('venues', function ($q) use ($request) {
            //     $q->where('venues.id', $request->venue_id);
            // })
            ->first();

        return response()->json(['variation' => $variationRecord]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        // dd($request);
        $user = Auth::user();
        $op = new VappRequest();
        $inventory = VappInventory::where('variation_id', $request->variation_id)
            ->where('event_id', session()->get('EVENT_ID'))
            ->first();

        $variation = VappVariation::with('inventory', 'vapp_sizes')
            ->where('id', $request->variation_id)
            ->where('event_id', session()->get('EVENT_ID'))
            ->first();

        appLog('BookingController::store variation: ' . $variation);
        appLog($variation->inventory()->where('vapp_size_id', 39)->first());
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
            appLog('validator: ' . $validator->errors());;
            $error = true;

            return redirect()->back()->withErrors($validator)->withInput();
        }

        // check number of slots available.  if available slots = 0 then exit with a warning message.
        // this is incase a user grabed the last slot with this user is waiting ..
        DB::beginTransaction();
        try {
            $error = false;
            $type = 'success';
            $message = 'Request created succesfully.' . $op->id;

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
            return redirect()->route('vapp.customer.booking')->with($notification);
        } catch (\Exception $e) {
            DB::rollBack();
            appLog('BookingController::store error: ' . $e->getMessage());
            // return redirect()->back()->withErrors(['error' => 'An error occurred while creating the booking.'])->withInput();
            return redirect()->back()
                ->with('message', 'Request creation failed: ' . $e->getMessage())
                ->with('alert-type', 'error')
                ->with('status', 'error')
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

    public function switch($id)
    {
        if ($id) {
            if (Event::findOrFail($id)) {
                appLog('Event ID: ' . $id);

                session()->put('EVENT_ID', $id);
                appLog('Event ID: ' . session()->get('EVENT_ID'));
                // return redirect()->route('tracki.project.show.card')->with('message', 'Workspace switched successfully.');
                return redirect()->route('vapp.customer.booking')->with('message', 'Event Switched.');
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
                return redirect()->route('vapp.customer.booking')->with('message', 'Event Switched.');
                // return back()->with('message', 'Event Switched.');
            }
        }
        //  else {
        // return back()->with('error', 'Workspace not found.');
        // return redirect()->route('tracki.project.show.card')->with('error', 'Workspace not found.');
        appLog('event_id is null');
        return redirect()->route('vapp.customer.booking')->with('error', 'Event not found.');
        // }
    }
}
