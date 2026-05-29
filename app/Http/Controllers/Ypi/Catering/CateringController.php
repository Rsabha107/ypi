<?php

namespace App\Http\Controllers\Ypi\Catering;

use App\Http\Controllers\Controller;
use App\Exports\CateringDietaryExport;
use App\Models\Ypi\Event;
use App\Models\Ypi\Participant;
use App\Models\Ypi\ParticipantType;
use App\Models\Ypi\Gender;
use App\Models\Ypi\Nationality;
use App\Models\Ypi\SizeLookup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CateringController extends Controller
{
    /**
     * Display participant list page
     */
    public function index(Request $request)
    {
        Log::info('inside CateringController index');
        Log::info('Session participant_filter_event_id: ' . session('participant_filter_event_id'));
        
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

        // Get selected event from session
        $selectedEvent = null;
        if (session()->has('participant_filter_event_id')) {
            $selectedEvent = Event::find(session('participant_filter_event_id'));
            Log::info('Selected event found: ' . ($selectedEvent ? $selectedEvent->name : 'null'));
        } else {
            Log::info('No filter in session');
        }

        return view('ypi.catering.participant.list', compact(
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

    /**
     * Get participant list data (AJAX)
     */
    public function list(Request $request)
    {
        $search = request('search');
        $filter = request('filter');
        $event_filter = session('participant_filter_event_id');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";

        // Get Approved status ID
        $approved_status_id = getStatusIdByLabel('Approved');

        $ops = Participant::orderBy($sort, $order);

        // Filter by Approved status only
        if ($approved_status_id) {
            $ops = $ops->where('status_id', $approved_status_id);
        }

        // Filter by event if provided
        if ($event_filter) {
            $ops = $ops->where('event_id', $event_filter);
        }

        if ($search) {
            $ops = $ops->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('qid', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $total = $ops->count();
        $limit = request("limit");
        $limit = max(1, min($limit, 100));
        
        $ops = $ops->paginate($limit)->through(function ($op) {
            $avatar_status = ($op->is_admin == 'X') ? 'status-away' : '';

            if ($op->photo) {
                $image = '<div class="avatar avatar-m ' . $avatar_status . '">
                            <img class="rounded-circle pull-up" src="/storage/upload/profile_images/' . $op->photo . '" alt="" />
                        </div>';
            } else {
                $image = '<div class="avatar avatar-m ' . $avatar_status . '">
                            <div class="avatar avatar-m rounded-circle pull-up">
                                <div class="avatar-name rounded-circle me-2"><span>' . generateInitials($op->full_name) . '</span></div>
                            </div>
                        </div>';
            }

            $qid_image_route = $op->qidDocument
                ? '<a href="javascript:void(0)" class="qid-image-link" data-image-url="' . route('participant.docs.download', $op->qidDocument) . '" data-qid="' . $op->qid . '"><span><i class="fa-solid fa-eye me-2"></i>' . $op->qid . '</span></a>'
                : $op->qid;

            $order_status = '<span class="badge badge-phoenix fs--2 ms-2 badge-phoenix-' . $op->status?->color . '">
                                <span class="badge-label">' . $op->status->title . '</span>
                            </span>';

            $cert_image_route = $op->certDocument
                ? '<a href="' . route('participant.docs.download', $op->certDocument) . '" target="_blank" class="text-warning">
                    <i class="fa-solid fa-eye"></i>
                </a>'
                : null;

            return [
                'id' => $op->id,
                'image' => '<div class="align-middle white-space-wrap fs-9 px-3">' . $image . '</div>',
                'participant_status' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $order_status . '</div>',
                'ref_number' => '<div class="align-middle white-space-wrap fw-bold fs-9 ms-2">' . $op->ref_number . '</div>',
                'participant_cert' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $cert_image_route . '</div>',
                'event_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->event?->name . '</div>',
                'assigned_venue_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->venue?->title . '</div>',
                'assigned_match_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . ($op->match ? ($op->match->pma1 . ' vs ' . $op->match->pma2 . ' (' . $op->match->match_date?->format('d M Y') . ')') : '') . '</div>',
                'participant_type' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->participantType?->title . '</div>',
                'full_name' => '<div class="align-middle white-space-wrap fs-9 ps-2"><a href="javascript:void(0)" class="participant-name-link" data-participant-id="' . $op->id . '">' . $op->full_name . '</a></div>',
                'date_of_birth' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . format_date($op->date_of_birth, 'd/m/Y') . '</div>',
                'gender' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->gender?->title . '</div>',
                'pants_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->pantSize?->label . '</div>',
                'jersey_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->jerseySize?->label . '</div>',
                'jacket_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->jacketSize?->label . '</div>',
                'shoe_size' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->shoeSize?->label . '</div>',
                'food_allergies' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . ($op->food_allergy && $op->food_allergy_id == 22 ? 'No' : 'Yes') . '</div>',
                'food_allergy_others' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . ($op->food_allergy_others ?? '-') . '</div>',
                'health_issues' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . ($op->health_issues ? 'Yes' : 'No') . '</div>',
                'health_issues_details' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . ($op->health_issues_details ?? '-') . '</div>',
                'qid' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $qid_image_route . '</div>',
                'nationality' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->nationality?->title . '</div>',
                'created_at' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . format_date($op->created_at, 'd-M-y') . ' ' . format_date($op->created_at, 'H:i:s') . '</div>',
                'updated_at' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . format_date($op->updated_at, 'd-M-y') . ' ' . format_date($op->updated_at, 'H:i:s') . '</div>',
            ];
        });

        return response()->json([
            "rows" => $ops->items(),
            "total" => $total,
        ]);
    }

    /**
     * View participant details
     */
    public function detail($id)
    {
        $participant = Participant::with([
            'event',
            'participantType',
            'guardian',
            'gender',
            'nationality',
            'pantSize',
            'jerseySize',
            'jacketSize',
            'shoeSize',
            'allergen',
            'status',
            'venue',
            'match'
        ])->findOrFail($id);

        $events = Event::all();

        return view('ypi.catering.participant.detail', [
            'participant' => $participant,
            'events' => $events,
        ]);
    }

    /**
     * Get participant details (AJAX)
     */
    public function getParticipantDetails($id)
    {
        $participant = Participant::with([
            'event',
            'participantType',
            'guardian',
            'gender',
            'nationality',
            'pantSize',
            'jerseySize',
            'jacketSize',
            'shoeSize',
            'allergen',
            'status',
            'venue',
            'match'
        ])->findOrFail($id);

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
                'assigned_match' => $participant->match ? ($participant->match->pma1 . ' vs ' . $participant->match->pma2 . ' (' . $participant->match->match_date?->format('d M Y') . ')') : '',
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

    /**
     * Set event filter (Session)
     */
    public function setFilter(Request $request)
    {
        Log::info('Catering setFilter called with event_id: ' . $request->event_id);
        
        if ($request->has('event_id') && $request->event_id) {
            session(['participant_filter_event_id' => $request->event_id]);
            Log::info('Filter set in session: ' . session('participant_filter_event_id'));
        } else {
            session()->forget('participant_filter_event_id');
            Log::info('Filter cleared from session');
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Clear event filter
     */
    public function clearFilter()
    {
        session()->forget('participant_filter_event_id');
        return response()->json(['success' => true]);
    }

    /**
     * Export dietary information to Excel
     */
    public function exportDietary()
    {
        $event_filter = session('participant_filter_event_id');
        
        $filename = 'dietary_information_' . date('Y-m-d_His') . '.xlsx';
        
        if ($event_filter) {
            $event = Event::find($event_filter);
            $filename = 'dietary_' . ($event ? str_replace(' ', '_', $event->name) : 'filtered') . '_' . date('Y-m-d_His') . '.xlsx';
        }
        
        return Excel::download(new CateringDietaryExport($event_filter), $filename);
    }
}
