<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfTemplateService
{
    protected $pdfParser = null;

    public function __construct()
    {
        // PDF Parser is optional - only needed for reading existing PDF files
        if (class_exists('Smalot\PdfParser\Parser')) {
            $this->pdfParser = new \Smalot\PdfParser\Parser();
        }
    }

    /**
     * Read a PDF file and replace placeholder text with participant data
     * Note: Requires 'smalot/pdfparser' package: composer require smalot/pdfparser
     *
     * @param string $templatePath Path to the PDF template file (storage path or absolute path)
     * @param array $replacements Associative array of placeholder => replacement values
     * @param string|null $outputPath Optional output path to save the new PDF (storage path)
     * @return string The content of the new PDF or path to saved file
     */
    public function fillTemplate(string $templatePath, array $replacements, ?string $outputPath = null): string
    {
        if (!$this->pdfParser) {
            throw new \Exception(
                "PDF Parser not installed. Install it with: composer require smalot/pdfparser\n" .
                "Or use the fillHtmlTemplate() method instead (recommended)."
            );
        }
        
        // Read the PDF file
        $pdfContent = $this->readPdfFile($templatePath);
        
        // Extract text from PDF
        $extractedText = $this->extractTextFromPdf($pdfContent);
        
        // Replace placeholders in the text
        $updatedText = $this->replacePlaceholders($extractedText, $replacements);
        
        // Generate new PDF from the updated text
        $newPdf = $this->generatePdfFromText($updatedText);
        
        // Save or return the PDF
        if ($outputPath) {
            Storage::put($outputPath, $newPdf);
            return $outputPath;
        }
        
        return $newPdf;
    }

    /**
     * Read PDF file content
     */
    protected function readPdfFile(string $path): string
    {
        // Check if it's a storage path or absolute path
        if (Storage::exists($path)) {
            return Storage::get($path);
        }
        
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        
        throw new \Exception("PDF file not found at: {$path}");
    }

    /**
     * Extract text from PDF content
     */
    protected function extractTextFromPdf(string $pdfContent): string
    {
        try {
            $pdf = $this->pdfParser->parseContent($pdfContent);
            return $pdf->getText();
        } catch (\Exception $e) {
            throw new \Exception("Failed to parse PDF: " . $e->getMessage());
        }
    }

    /**
     * Replace placeholders in text
     */
    protected function replacePlaceholders(string $text, array $replacements): string
    {
        foreach ($replacements as $placeholder => $value) {
            $text = str_replace($placeholder, $value, $text);
        }
        
        return $text;
    }

    /**
     * Generate PDF from text content
     * Note: This generates a simple PDF. For maintaining formatting,
     * consider using HTML template approach instead.
     */
    protected function generatePdfFromText(string $text): string
    {
        // Convert text to basic HTML for better formatting
        $html = '<html><body><pre style="font-family: Arial, sans-serif; font-size: 12pt; white-space: pre-wrap;">';
        $html .= htmlspecialchars($text);
        $html .= '</pre></body></html>';
        
        $pdf = Pdf::loadHTML($html);
        return $pdf->output();
    }

    /**
     * Advanced method: Use HTML template with placeholders
     * This maintains better formatting and layout
     *
     * @param string $htmlTemplatePath Path to HTML template file
     * @param array $replacements Associative array of placeholder => replacement values
     * @param string|null $outputPath Optional output path to save the PDF
     * @return string
     */
    public function fillHtmlTemplate(string $htmlTemplatePath, array $replacements, ?string $outputPath = null): string
    {
        // Read HTML template
        $html = $this->readFile($htmlTemplatePath);
        
        // Replace placeholders (using {{placeholder}} syntax)
        foreach ($replacements as $placeholder => $value) {
            // Support both {{placeholder}} and bare placeholder syntax
            $html = str_replace([
                '{{' . $placeholder . '}}',
                $placeholder
            ], $value, $html);
        }
        
        // Generate PDF
        $pdf = Pdf::loadHTML($html);
        $pdfContent = $pdf->output();
        
        // Save or return
        if ($outputPath) {
            Storage::put($outputPath, $pdfContent);
            return $outputPath;
        }
        
        return $pdfContent;
    }

    /**
     * Read file content (storage or absolute path)
     */
    protected function readFile(string $path): string
    {
        if (Storage::exists($path)) {
            return Storage::get($path);
        }
        
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        
        throw new \Exception("File not found at: {$path}");
    }

    /**
     * Batch process multiple participants
     *
     * @param string $templatePath Path to the PDF template
     * @param array $participants Array of participant data
     * @param string $placeholderKey The key in each participant array to replace (e.g., 'name')
     * @param string $placeholder The placeholder text in PDF (e.g., 'Name Surname')
     * @param string $outputDirectory Directory to save generated PDFs
     * @return array Array of generated file paths
     */
    public function batchFillTemplate(
        string $templatePath, 
        array $participants, 
        string $placeholderKey, 
        string $placeholder,
        string $outputDirectory
    ): array {
        $generatedFiles = [];
        
        foreach ($participants as $index => $participant) {
            $replacements = [$placeholder => $participant[$placeholderKey]];
            $outputPath = $outputDirectory . '/' . ($participant['id'] ?? $index) . '.pdf';
            
            $filePath = $this->fillTemplate($templatePath, $replacements, $outputPath);
            $generatedFiles[] = $filePath;
        }
        
        return $generatedFiles;
    }
}
