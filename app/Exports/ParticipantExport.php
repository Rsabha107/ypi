<?php

namespace App\Exports;

use App\Models\Vapp\VappRequest;
use App\Models\Ypi\Participant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ParticipantExport implements FromCollection, WithHeadings
{
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
            'FOOD ALLERGY',
            'FOOD ALLERGTY DETAILS',
            'HEALTH ISSUES',
            'HEALTH ISSUE DETAILS',
            'CREATED AT',
        ];
    }
    public function collection()
    {
        $participants = Participant::all();
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
                'pants_size' => $participant->pants_size,
                'jersey_size' => $participant->jersey_size,
                'jacket_size' => $participant->jacket_size,
                'shoe_size' => $participant->shoe_size,
                'food_allergy' => $participant->food_allergy,
                'food_allergy_details' => $participant->food_allergy_details,
                'health_issues' => $participant->health_issues,
                'health_issue_details' => $participant->health_issue_details,
                'created_at' => $participant->created_at,
            ];
        });
        return $participants;
    }
}
