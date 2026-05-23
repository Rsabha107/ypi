<?php

namespace App\Console\Commands;

// use App\Models\Ypi\Guest;
use App\Models\Ypi\Participant;
use Illuminate\Console\Command;
use App\Services\PdfTemplateService;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class GenerateCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:generate
                            {--model= : The model to use (Guest or Participant)}
                            {--event= : Filter by event ID}
                            {--template=default : Template to use (default or m4h)}
                            {--test : Generate test certificate only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate certificates for participants or guests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('test')) {
            return $this->generateTestCertificate();
        }

        $model = $this->option('model') ?? 'Guest';
        $eventId = $this->option('event');
        $template = $this->option('template') ?? 'default';

        $templateFile = match($template) {
            'm4h'   => 'templates/m4h_certificate.html',
            default => 'templates/certificate.html',
        };

        if (!Storage::exists($templateFile)) {
            $this->error("Template file not found: {$templateFile}");
            return 1;
        }

        $this->info("Generating certificates for {$model} using template '{$template}'...");

        $query = match($model) {
            // 'Guest'       => Guest::query(),
            'Participant' => Participant::query(),
            default       => null,
        };

        if (!$query) {
            $this->error("Invalid model. Use --model=Guest or --model=Participant");
            return 1;
        }

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            $this->warn('No records found to generate certificates for.');
            return 0;
        }

        $this->info("Found {$records->count()} records. Generating certificates...");

        $bar = $this->output->createProgressBar($records->count());
        $bar->start();

        $successCount = 0;
        $outputDir = 'certificates/' . date('Y-m-d_His');
        $templateHtml = Storage::get($templateFile);
        $logoPath = storage_path('app/public/event/arab_cup_logo.jpg');
        $logoSrc  = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath));
        $sigPath  = storage_path('app/public/event/sig.png');
        $sigSrc   = 'data:image/png;base64,' . base64_encode(file_get_contents($sigPath));

        foreach ($records as $record) {
            try {
                $name = $record->full_name ?? $record->name;

                $replacements = $template === 'm4h'
                    ? ['{{participant_name}}' => $name, '{{logo_src}}' => $logoSrc, '{{sig_src}}' => $sigSrc]
                    : [
                        '{{participant_name}}' => $name,
                        '{{event_name}}'       => $record->event->name ?? 'Youth Program',
                        '{{date}}'             => now()->format('F d, Y'),
                        '{{certificate_id}}'   => 'CERT-' . str_pad($record->id, 6, '0', STR_PAD_LEFT),
                    ];

                $html = str_replace(array_keys($replacements), array_values($replacements), $templateHtml);

                $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

                $fileName = $outputDir . '/' . $record->id . '_' . Str::slug($name) . '.pdf';
                Storage::put($fileName, $pdf->output());

                $successCount++;
            } catch (\Exception $e) {
                $this->error("\nFailed for record {$record->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info("✓ Successfully generated {$successCount} certificates!");
        $this->info("📁 Saved to: storage/app/{$outputDir}");

        return 0;
    }

    /**
     * Generate a single test certificate
     */
    protected function generateTestCertificate()
    {
        $this->info('Generating test certificate...');

        $template = $this->option('template') ?? 'default';
        $templateFile = match($template) {
            'm4h'   => 'templates/m4h_certificate.html',
            default => 'templates/certificate.html',
        };

        try {
            $templateHtml = Storage::get($templateFile);

            $logoPath = storage_path('app/public/event/arab_cup_logo.jpg');
            $logoSrc  = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath));
            $sigPath  = storage_path('app/public/event/sig.png');
            $sigSrc   = 'data:image/png;base64,' . base64_encode(file_get_contents($sigPath));

            $replacements = $template === 'm4h'
                ? ['{{participant_name}}' => 'Ahmed Al-Mansoori', '{{logo_src}}' => $logoSrc, '{{sig_src}}' => $sigSrc]
                : [
                    '{{participant_name}}' => 'Ahmed Al-Mansoori',
                    '{{event_name}}'       => 'Youth Leadership Program 2026',
                    '{{date}}'             => now()->format('F d, Y'),
                    '{{certificate_id}}'   => 'CERT-TEST-' . strtoupper(uniqid()),
                ];

            $html = str_replace(array_keys($replacements), array_values($replacements), $templateHtml);

            $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');
            
            $fileName = 'certificates/test_certificate_' . date('Y-m-d_His') . '.pdf';
            Storage::put($fileName, $pdf->output());

            $fullPath = storage_path('app/' . $fileName);

            $this->info("✓ Test certificate generated successfully!");
            $this->info("📁 Location: {$fullPath}");
            
            // Try to open it on Windows
            if (PHP_OS_FAMILY === 'Windows') {
                $this->info("🔍 Opening PDF...");
                exec("start \"\" \"{$fullPath}\"");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to generate test certificate: " . $e->getMessage());
            return 1;
        }
    }
}
