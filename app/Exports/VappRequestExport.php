<?php

namespace App\Exports;

use App\Models\Vapp\VappRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VappRequestExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function headings(): array
    {
        return [
            'REQUEST NUMBER',
            'EVENT',
            'VENUE',
            'VARIATION',
            'PARKING',
            'MATCH CATEGORY',
            'MATCH',
            'FA',
            'REQUEST DATE',
            'VAPP SIZE',
            'REQUESTED',
            'APPROVED',
            'STATUS',
            'JUSTIFICATION',
            'COMMENTS',
            'CREATED AT',
        ];
    }
    public function collection()
    {
        $vappRequests = VappRequest::all();
        $vappRequests->transform(function ($vappRequest) {
            return [
                'request_number' => $vappRequest->request_number,
                'event' => $vappRequest->event->name,
                'venue' => $vappRequest->venue->title,
                'variation' => 'VAR-' . $vappRequest->variation_id,
                'parking' => $vappRequest->parking?->parking_code,
                'match_category' => $vappRequest->match_category?->title,
                'match' => $vappRequest->match?->match_code,
                'fa' => $vappRequest->functional_area?->title,
                'request_date' => format_date($vappRequest->request_date),
                'vapp_size' => $vappRequest->vapp_size?->title,
                'requested' => $vappRequest->requested_vapps,
                'approved' => $vappRequest->approved_vapps,
                'status' => $vappRequest->status?->title,
                'justification' => $vappRequest->justification,
                'comments' => $vappRequest->comments,
                'created_at' => $vappRequest->created_at,
            ];
        });
        return $vappRequests;
    }
}
