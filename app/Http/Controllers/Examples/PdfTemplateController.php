<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Services\PdfTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PdfTemplateController extends Controller
{
    protected PdfTemplateService $pdfService;

    public function __construct(PdfTemplateService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Example: Fill a single PDF template with participant name
     */
    public function generateCertificate(Request $request)
    {
        // Example: Replace "Name Surname" with actual participant name
        $participantName = $request->input('name', 'John Doe');
        
        $replacements = [
            'Name Surname' => $participantName,
            // You can add more replacements as needed
            // 'Date' => now()->format('d/m/Y'),
            // 'Event Name' => 'Youth Program 2026',
        ];

        try {
            // Option 1: Using existing PDF template
            $pdfContent = $this->pdfService->fillTemplate(
                templatePath: 'templates/certificate_template.pdf', // Path in storage
                replacements: $replacements,
                outputPath: 'certificates/' . str_slug($participantName) . '.pdf'
            );

            return Response::make(
                $pdfContent,
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="certificate.pdf"'
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Example: Using HTML template (RECOMMENDED for better formatting)
     */
    public function generateFromHtml(Request $request)
    {
        $participantName = $request->input('name', 'John Doe');
        $eventName = $request->input('event', 'Youth Program 2026');
        
        $replacements = [
            'participant_name' => $participantName,
            'event_name' => $eventName,
            'date' => now()->format('F d, Y'),
            'certificate_id' => 'CERT-' . strtoupper(uniqid()),
        ];

        try {
            $pdfContent = $this->pdfService->fillHtmlTemplate(
                htmlTemplatePath: 'templates/certificate.html', // Path in storage
                replacements: $replacements,
                outputPath: 'certificates/' . str_slug($participantName) . '_' . time() . '.pdf'
            );

            return response()->download(
                storage_path('app/certificates/' . str_slug($participantName) . '_' . time() . '.pdf')
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Example: Batch generate certificates for multiple participants
     */
    public function batchGenerate(Request $request)
    {
        // Example participants array
        $participants = [
            ['id' => 1, 'name' => 'Ahmed Al-Mansoori'],
            ['id' => 2, 'name' => 'Fatima Hassan'],
            ['id' => 3, 'name' => 'Mohammed Ali'],
        ];

        try {
            $generatedFiles = $this->pdfService->batchFillTemplate(
                templatePath: 'templates/certificate_template.pdf',
                participants: $participants,
                placeholderKey: 'name',
                placeholder: 'Name Surname',
                outputDirectory: 'certificates/batch_' . date('Y-m-d')
            );

            return response()->json([
                'success' => true,
                'message' => count($generatedFiles) . ' certificates generated',
                'files' => $generatedFiles
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Example: Generate from database participant data
     */
    public function generateForParticipant($participantId)
    {
        // Assuming you have a Participant model
        // $participant = \App\Models\Participant::findOrFail($participantId);
        
        // For demonstration:
        $participant = (object)[
            'id' => $participantId,
            'first_name' => 'Ahmed',
            'last_name' => 'Al-Mansoori',
            'program_name' => 'Youth Leadership Program',
            'completion_date' => now(),
        ];

        $replacements = [
            'Name Surname' => $participant->first_name . ' ' . $participant->last_name,
            'Program Name' => $participant->program_name,
            'Date' => $participant->completion_date->format('d/m/Y'),
        ];

        try {
            $fileName = "certificate_{$participant->id}_{$participant->last_name}.pdf";
            
            $this->pdfService->fillTemplate(
                templatePath: 'templates/certificate_template.pdf',
                replacements: $replacements,
                outputPath: "certificates/{$fileName}"
            );

            return response()->download(storage_path("app/certificates/{$fileName}"));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
