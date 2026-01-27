<?php

namespace App\Http\Controllers\Vapp\Operator;

use App\Http\Controllers\Controller;
use App\Models\Vapp\FunctionalArea;
use App\Models\Vapp\Venue;
use App\Models\Vapp\Event;
use App\Models\Vapp\VappRequest;
use App\Models\Vapp\VappRequestStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all();
        $venues = Venue::all();
        // $rfc_requests = VappRequest::with('functional_area')
        //     ->select('functional_area_id', '')
        //     ->where('request_status_id', getRequestStatusIdByLabel('Ready for Collection'))
        //     ->where('event_id', session()->get('EVENT_ID'))
        //     ->distinct('functional_area_id')
        //     ->get();

        $rfc_requests = FunctionalArea::whereHas('vappRequests', function ($q) {
            $q->where('request_status_id', getRequestStatusIdByLabel('Ready for Collection'))
                ->where('event_id', session()->get('EVENT_ID'));
        })
            ->get();

        return view('vapp.operator.collect', [
            'events' => $events,
            'rfcRequests' => $rfc_requests
        ]);
    }

    public function list()
    {
        appLog('inside Admin BookingController::list');

        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $rfc_request_filter = (request()->rfc_request_filter) ? request()->rfc_request_filter : "";

        // if ($mds_date_range_filter == "") {
        //     $mds_date_range_filter = date('Y-m-d') . ' to ' . date('Y-m-d');
        // }

        // Carbon::createFromFormat('d/m/Y', $request->slot_visibility)->toDateString()

        $ops = VappRequest::orderBy($sort, $order);
        $ops = $ops->where('event_id', session()->get('EVENT_ID'));
        if ($rfc_request_filter == "") {
            $ops = $ops->where('vapp_functional_area_id', 0);
        }
        $ops = $ops->where('request_status_id', getRequestStatusIdByLabel('Ready for Collection'));

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


        if ($rfc_request_filter) {
            $ops = $ops->where('vapp_functional_area_id', $rfc_request_filter);
        }

        $total = $ops->count();
        $limit = request("limit");
        $limit = max(1, min($limit, 100)); // min=1, max=100
        $ops = $ops->paginate($limit)->through(function ($op) {

            // $location = Location::find($booking->location_id);
            $details_url = "javascript:void(0);";
            // $details_url = route('vapp.admin.booking.request', $op->id);

            $actions =
                '<a href="' . $details_url . '" class="btn btn-sm" id="editBooking" data-id="' .
                $op->id .
                '" data-table="bookings_table" data-bs-toggle="tooltip" data-bs-placement="right" title="Update">' .
                '<i class="fa-solid fa-pen-to-square text-primary"></i></a>' .
                '<a href="javascript:void(0)" class="btn btn-sm" data-table="bookings_table" data-id="' .
                $op->id .
                '" id="deleteBooking" data-bs-toggle="tooltip" data-bs-placement="right" title="Delete">' .
                '<i class="bx bx-trash text-danger"></i></a></div></div>';

            $order_status =  '<span class="badge badge-phoenix fs--2 badge-phoenix-' . $op->status?->color . '" style="cursor: pointer;" id="rfc-request-status" data-id="' . $op->id .
                '" data-table="bookings_table"><span class="badge-label">' . $op->status?->title . '</span><span class="ms-1" data-feather="x" style="height:12.8px;width:12.8px;"></span></span>';

            // $order_status = '<select class="form-select select2-status" style="width:240px">';
            // $order_status .= '<option value="" disabled>Change Status</option>';
            // $order_status .= '<option value="ready-for-collection" data-icon="fa fa-clock text-secondary"' . ($op->request_status_id == getRequestStatusIdByLabel('Ready for Collection') ? 'selected' : '') . '>';
            // $order_status .= 'Ready for Collection</option>';
            // $order_status .= '<option value="approved" data-icon="fa fa-clock text-secondary"' . ($op->request_status_id == getRequestStatusIdByLabel('Approved') ? 'selected' : '') . '>';
            // $order_status .= 'Approved</option>';
            // $order_status .= '<option value="collected" data-icon="fa fa-clock text-secondary"' . ($op->request_status_id == getRequestStatusIdByLabel('Collected') ? 'selected' : '') . '>';
            // $order_status .= 'Collected</option>';
            // $order_status .= '</select>';


            $approved_vapps = $op->approved_vapps ?? '0';
            return  [
                'id' => $op->id,
                // 'fa_id' => $op->vapp_functional_area_id,
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


    public function updateStatus(Request $request)
    {

        $vapp = VappRequest::findOrFail($request->id);
        $status_title = VappRequestStatus::findOrFail($vapp->request_status_id);

        appLog($status_title->title);
        if ($vapp->status->title == 'Ready for Collection') {
            $update_to_status = getStatusIdByLabel('Collected');
        } elseif ($vapp->status->title == 'Collected') {
            $update_to_status = getStatusIdByLabel('Ready for Collection');
        } else {
            return response()->json(['error' => true, 'message' => 'Only Ready for Collection and Collected status can be changed from here.']);
        }

        $vapp->update(['request_status_id' => $update_to_status]);

        $notification = array(
            'message'       => 'Request status updated successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => false, 'message' => 'Order Status updated successfully.', 'id' => $vapp->id]);
    } //updateStatus

    public function markAsCollected(Request $request)
    {
        appLog('IDs to mark as collected: ' . print_r($request->ids, true));
        $selected_id = $request->ids;
        try {
            VappRequest::whereIn('id', $selected_id)
                ->where('request_status_id', getRequestStatusIdByLabel('Ready for Collection'))
                ->update(['request_status_id' => getRequestStatusIdByLabel('Collected')]);

            $notification = array(
                'message'       => 'Request marked as Collected successfully',
                'alert-type'    => 'success'
            );

            return response()->json(['error' => false, 'message' => 'Request(s) marked as Collected successfully.']);
        } catch (\Exception $e) {
            appLog('Error updating request status: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Failed to update request status.']);
            // return redirect()->back()->with('error', 'Failed to update request status.');

        }
    } //markCompleted

    public function switch($id)
    {
        if ($id) {
            if (Event::findOrFail($id)) {
                appLog('Event ID: ' . $id);

                session()->put('EVENT_ID', $id);
                appLog('Event ID: ' . session()->get('EVENT_ID'));
                // return redirect()->route('tracki.project.show.card')->with('message', 'Workspace switched successfully.');
                return redirect()->route('vapp.operator')->with('message', 'Event Switched.');
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
        if ($request->event_id) {
            appLog('Event ID: ' . $request->event_id);
            if (Event::findOrFail($request->event_id) && !session()->has('EVENT_ID')) {
                appLog('Inside if statement Event ID: ' . $request->event_id);

                session()->put('EVENT_ID', $request->event_id);
                appLog('session EVENT_ID: ' . session()->get('EVENT_ID'));
                appLog('before redirect');
                // return redirect()->route('tracki.project.show.card')->with('message', 'Workspace switched successfully.');
                return redirect()->route('vapp.operator')->with('message', 'Event Switched.');
                // return back()->with('message', 'Event Switched.');
            }
        }
        //  else {
        // return back()->with('error', 'Workspace not found.');
        // return redirect()->route('tracki.project.show.card')->with('error', 'Workspace not found.');
        appLog('event_id is null');
        return redirect()->route('vapp.operator')->with('error', 'Event not found.');
        // }
    }

    public function generate(Request $request)
    {
        $fa_id = $request->fa_id;
        $ids = $request->ids;

        appLog('IDs: ' . print_r($ids, true));
        appLog('Functional Area ID: ' . $fa_id);

        if (empty($ids) || empty($fa_id)) {
            return redirect()->back()->with('error', 'No bookings selected or Functional Area ID missing.');
        }

        return $this->generatePDF($fa_id, $ids);
    }

    private function generatePDF($fa_id, $ids)
    {

        // dd($requestId);
        $vappRequests = VappRequest::with('event')
            ->where('event_id', session()->get('EVENT_ID'))
            ->where('request_status_id', getRequestStatusIdByLabel('Ready for Collection'))
            ->where('vapp_functional_area_id', $fa_id)
            ->whereIn('id', $ids)
            // ->where('print_receipt_flag', 0)
            ->get();

        appLog('VAPP Requests: ' . json_encode($vappRequests));
        // dd($vappRequest);

        $pdf = Pdf::loadView('vapp.operator.pdf.receipt', [
            'eventName' => getNameById('events', session()->get('EVENT_ID')),
            // 'recipient' => $vappRequest->recipient, // adapt fields
            'vapps'     => $vappRequests
        ]);

        // return $pdf->download("VAPP_Receipt_{$requestId}.pdf");
        return $pdf->stream("VAPP_Receipt_{$fa_id}.pdf");
    }
}
