<?php

namespace App\Exports;

use App\Models\Ypi\Participant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CateringDietaryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $event_id;

    public function __construct($event_id = null)
    {
        $this->event_id = $event_id;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get Approved status ID
        $approved_status_id = getStatusIdByLabel('Approved');

        $query = Participant::with([
            'event',
            'participantType',
            'gender',
            'nationality',
            'allergen',
            'status',
            'venue',
            'match'
        ])->where('status_id', $approved_status_id);

        // Filter by event if provided
        if ($this->event_id) {
            $query->where('event_id', $this->event_id);
        }

        return $query->orderBy('full_name', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'Participant Name',
            'QID',
            'Date of Birth',
            'Gender',
            'Nationality',
            'Event',
            'Participant Type',
            'Assigned Venue',
            'Assigned Match',
            'Food Allergies',
            'Allergy Type',
            'Allergy Details',
            'Health Issues',
            'Health Issue Details',
        ];
    }

    public function map($participant): array
    {
        return [
            $participant->full_name,
            $participant->qid,
            format_date($participant->date_of_birth, 'd/m/Y'),
            $participant->gender?->title,
            $participant->nationality?->title,
            $participant->event?->name,
            $participant->participantType?->title,
            $participant->venue?->title,
            $participant->match ? ($participant->match->pma1 . ' vs ' . $participant->match->pma2 . ' (' . $participant->match->match_date?->format('d M Y') . ')') : '',
            $participant->food_allergy ? 'Yes' : 'No',
            $participant->allergen?->title,
            $participant->food_allergy_others ?? '',
            $participant->health_issues ? 'Yes' : 'No',
            $participant->health_issues_details ?? '',
        ];
    }
}
