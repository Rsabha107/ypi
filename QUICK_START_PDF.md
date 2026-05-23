# Quick Start - PDF Template Service

## ✅ What's Already Done
1. ✅ PdfTemplateService class created
2. ✅ Example controller with multiple use cases
3. ✅ Beautiful HTML certificate template
4. ✅ Test route added to web.php
5. ✅ Comprehensive documentation

## 🚀 Quick Start (HTML Template Method - Works Immediately!)

### Step 1: Test the HTML Template (No Installation Required!)
Visit this URL in your browser:
```
http://your-domain.test/test-pdf-template
```

This will generate a beautiful certificate PDF with the name "Ahmed Al-Mansoori".

### Step 2: Customize for Your Data
To use with your actual participants, use this code in any controller:

```php
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

public function generateCertificate($participantId)
{
    // Get your participant data
    $participant = Participant::findOrFail($participantId);
    
    // Define what to replace
    $replacements = [
        '{{participant_name}}' => $participant->full_name,
        '{{event_name}}' => $participant->event->name,
        '{{date}}' => now()->format('F d, Y'),
        '{{certificate_id}}' => 'CERT-' . $participant->id,
    ];
    
    // Load template and replace placeholders
    $html = Storage::get('templates/certificate.html');
    $html = str_replace(array_keys($replacements), array_values($replacements), $html);
    
    // Generate PDF
    $pdf = Pdf::loadHTML($html);
    
    // Return for download or display
    return $pdf->stream('certificate.pdf'); // Display in browser
    // OR
    return $pdf->download('certificate.pdf'); // Force download
}
```

## 📋 Optional: Install PDF Parser (Only if you need to read existing PDF files)

If you want to READ and modify existing PDF files (not just generate new ones), install:

```powershell
composer require smalot/pdfparser
```

Then you can use the full PdfTemplateService:

```php
use App\Services\PdfTemplateService;

$pdfService = new PdfTemplateService();
$pdfService->fillTemplate(
    templatePath: 'templates/your_existing_template.pdf',
    replacements: ['Name Surname' => 'Ahmed Al-Mansoori'],
    outputPath: 'certificates/ahmed.pdf'
);
```

## 🎨 Customize the HTML Template

Edit the template at:
```
storage/app/templates/certificate.html
```

Available placeholders:
- `{{participant_name}}` - Participant's full name
- `{{event_name}}` - Event/Program name
- `{{date}}` - Certificate date
- `{{certificate_id}}` - Unique certificate ID

Add more placeholders as needed by:
1. Adding `{{your_placeholder}}` in the HTML
2. Including it in the replacements array

## 📁 File Locations

```
storage/app/
  ├── templates/
  │   ├── certificate.html                    # HTML template (✅ Created)
  │   ├── README_PDF_TEMPLATE_SERVICE.md      # Full documentation (✅ Created)
  │   └── your_pdf_template.pdf               # Your PDF template (if needed)
  └── certificates/
      └── generated_certificates_here.pdf     # Output directory

app/
  ├── Services/
  │   └── PdfTemplateService.php              # Main service (✅ Created)
  └── Http/Controllers/Examples/
      └── PdfTemplateController.php            # Examples (✅ Created)
```

## 🔥 Real-World Integration Example

Add this to your existing GuestController or ParticipantController:

```php
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

public function downloadCertificate($guestId)
{
    $guest = Guest::findOrFail($guestId);
    
    // Make sure guest has completed the program
    if ($guest->status !== 'completed') {
        return back()->with('error', 'Certificate not available yet.');
    }
    
    $replacements = [
        '{{participant_name}}' => $guest->full_name,
        '{{event_name}}' => $guest->event->name ?? 'Youth Program',
        '{{date}}' => $guest->completion_date?->format('F d, Y') ?? now()->format('F d, Y'),
        '{{certificate_id}}' => 'CERT-' . str_pad($guest->id, 6, '0', STR_PAD_LEFT),
    ];
    
    $html = Storage::get('templates/certificate.html');
    $html = str_replace(array_keys($replacements), array_values($replacements), $html);
    
    $pdf = Pdf::loadHTML($html)
        ->setPaper('a4', 'landscape'); // Landscape orientation for certificates
    
    $fileName = 'certificate_' . $guest->full_name . '.pdf';
    
    return $pdf->download($fileName);
}
```

Then add to routes:
```php
Route::get('/guest/{guest}/certificate', [GuestController::class, 'downloadCertificate'])
    ->name('guest.certificate');
```

## 🎯 Next Steps

1. **Test immediately**: Visit `/test-pdf-template` to see it working
2. **Customize template**: Edit `storage/app/templates/certificate.html`
3. **Add to your controller**: Copy the example code above
4. **Add route**: Add certificate download route
5. **Add button to view**: Add "Download Certificate" button in your guest/participant detail page

## 💡 Pro Tips

1. **Landscape for certificates**: Use `->setPaper('a4', 'landscape')` for certificate layouts
2. **Save to storage**: Use `$pdf->save(storage_path('app/certificates/file.pdf'))` to keep copies
3. **Email certificates**: Attach the PDF to emails using `$pdf->output()` 
4. **Batch generate**: Loop through participants and generate all certificates at once
5. **Arabic support**: Add RTL CSS if you need Arabic text on certificates

## ❓ Need Help?

- Check `storage/app/templates/README_PDF_TEMPLATE_SERVICE.md` for full documentation
- Look at `app/Http/Controllers/Examples/PdfTemplateController.php` for more examples
- The HTML template method is recommended over parsing existing PDFs
