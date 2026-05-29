<?php

namespace App\Exports;

use App\Models\Vapp\VappRequest;
use App\Models\Ypi\Participant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ParticipantExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */

    public function headings(): array
    {
        return [
            'PARTICIPANT STATUS',
            'EVENT',
            'PARTICIPANT TYPE',
            'PARTICIPANT NAME',
            'GUARDIAN NAME',
            'GUARDIAN ID',
            'GUARDIAN EMAIL',
            'GUARDIAN PHONE',
            'PARTICIPANT QID',
            'DATE OF BIRTH',
            'GENDER',
            'NATIONALITY',
            'PANTS SIZE',
            'JERSEY SIZE',
            'JACKET SIZE',
            'SHOE SIZE',
            'ASSIGNED VENUE',
            'ASSIGNED MATCH',
            'FOOD ALLERGY',
            'FOOD ALLERGY TYPE',
            'FOOD ALLERGTY OTHERS',
            'HEALTH ISSUES',
            'HEALTH ISSUE DETAILS',
            'CREATED AT',
        ];
    }
    public function collection()
    {
        // Start query with relationships
        $query = Participant::with(['status', 'event', 'participantType', 'guardian', 'gender', 'nationality', 'venue', 'match']);

        // Apply event filter if provided
        if (!empty($this->filters['export_event_filter'])) {
            $query->where('event_id', $this->filters['export_event_filter']);
        }

        // Apply venue filter if provided
        if (!empty($this->filters['export_venue_filter'])) {
            $query->where('assigned_venue_id', $this->filters['export_venue_filter']);
        }

        // Apply date range filter if provided
        if (!empty($this->filters['export_date_range_filter'])) {
            $dateRange = explode(' to ', $this->filters['export_date_range_filter']);
            if (count($dateRange) === 2) {
                $query->whereBetween('created_at', [$dateRange[0], $dateRange[1]]);
            }
        }

        $participants = $query->get();
        $participants->transform(function ($participant) {
            return [
                'participant_status' => $participant->status?->title,
                'event' => $participant->event?->name,
                'participant_type' => $participant->participantType?->title,
                'participant_name' => $participant->full_name,
                'guardian_name' => $participant->guardian?->full_name,
                'guardian_id' => $participant->guardian_id,
                'guardian_email' => $participant->guardian?->email,
                'guardian_phone' => $participant->guardian?->phone_main,
                'participant_qid' => $participant->qid,
                'date_of_birth' => $participant->date_of_birth,
                'gender' => $participant->gender?->title,
                'nationality' => $participant->nationality?->title,
                'pants_size' => $participant->pantSize?->label,
                'jersey_size' => $participant->jerseySize?->label,
                'jacket_size' => $participant->jacketSize?->label,
                'shoe_size' => $participant->shoeSize?->label,
                'assigned_venue' => $participant->venue?->title,
                'assigned_match' => $participant->match ? ($participant->match->pma1 . ' vs ' . $participant->match->pma2 . ' (' . $participant->match->match_date?->format('d M Y') . ')') : '',
                'food_allergy' => ($participant->food_allergy && $participant->food_allergy_id == 22) ? 'No' : 'Yes',
                'food_allergy_type' => $participant->allergen?->title,
                'food_allergy_others' => $participant->food_allergy_others,
                'health_issues' => $participant->health_issues ? 'Yes' : 'No',
                'health_issue_details' => $participant->health_issues_details,
                'created_at' => $participant->created_at,
            ];
        });
        return $participants;
    }
}
