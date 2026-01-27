<?php

namespace App\Http\Controllers\GeneralSettings;

use App\Http\Controllers\Controller;
use App\Models\Ypi\ParticipantDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ParticipantDocumentController extends Controller
{
    public function download(ParticipantDocument $document)
    {
        Log::info('Request to download participant document: ' . $document->id);
        // TODO: add policy check (important!)
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        Log::info('Downloading participant document: ' . $document->id);

        abort_unless(Storage::disk($document->disk)->exists($document->path), 404);

        // inline preview for images/pdf
        return Storage::disk($document->disk)->response($document->path, $document->original_name ?? basename($document->path), [
            'Content-Disposition' => 'inline; filename="' . ($document->original_name ?? basename($document->path)) . '"',
        ]);

        // download for other file types
        return Storage::disk($document->disk)->download(
            $document->path,
            $document->original_name ?? basename($document->path)
        );
    }

    public function view($id)
    {
        Log::info('Request to view participant document: ' . $id);
        $doc = ParticipantDocument::find($id);

        abort_unless(Storage::disk($doc->disk)->exists($doc->path), 404);

        $mime = Storage::disk($doc->disk)->mimeType($doc->path) ?? 'image/png';
        $content = Storage::disk($doc->disk)->get($doc->path);
        // $file = Storage::disk($doc->disk)->get($doc->path);
        // // read file contents and return inline
        // Log::info('Viewing participant document: ' . $id . ' with mime type: ' . $mime);
        // Log::info('File size: ' . Str::limit(strlen($file), 100) . ' bytes');
        // Log::info('File preview: ' . Str::limit($file, 100));
        // return response()->file(storage_path($doc->path));
        return response($content, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($doc->path) . '"',
            'Cache-Control' => 'public, max-age=86400',
        ]);

        // return Storage::disk($doc->disk)->response($doc->path);
    }

    public function destroy(ParticipantDocument $document)
    {
        // TODO: add policy check (important!)
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }
        Storage::disk($document->disk)->delete($document->path);
        $document->delete();

        Log::info('Deleted participant document: ' . $document->id);

        return response()->json(['error' => false, 'message' => 'Document deleted']);
    }
}
