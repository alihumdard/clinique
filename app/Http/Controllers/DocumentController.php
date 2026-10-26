<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Serves the stored PDFs.
 *
 * The plan requires documents to sit outside the public web root, so they
 * are written to the private disk and never linked directly. Every
 * download therefore comes through here, which is also the only place
 * that can check the row is published and count the download.
 */
class DocumentController extends Controller
{
    public function guide(Guide $guide): StreamedResponse
    {
        abort_unless($guide->isPublic(), 404);

        return $this->send($guide->file_path, $guide->file_name, $guide);
    }

    public function report(Report $report): StreamedResponse
    {
        abort_unless($report->isPublic(), 404);

        return $this->send($report->file_path, $report->file_name, $report);
    }

    /**
     * @param  Guide|Report  $model  counted after the file is known to exist,
     *                               so a missing file is not a download.
     */
    private function send(?string $path, ?string $name, $model): StreamedResponse
    {
        abort_if(blank($path), 404);

        $disk = Storage::disk('local');

        /*
         * The path comes from a row this application wrote, but a stray
         * value must not be able to reach outside the documents folder:
         * a traversal attempt is a 404, not a file.
         */
        abort_unless(str_starts_with($path, 'documents/'), 404);
        abort_if(str_contains($path, '..'), 404);
        abort_unless($disk->exists($path), 404);

        $model->increment('downloads');

        return $disk->download($path, $name ?: basename($path), [
            'Content-Type' => 'application/pdf',
            // Nothing else is in this response, and a PDF viewer should not
            // be able to run anything from the document's own origin.
            'Content-Security-Policy' => "default-src 'none'; object-src 'none'",
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
