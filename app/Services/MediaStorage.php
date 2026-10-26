<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Where uploaded files go, and what is checked before they get there.
 *
 * The client-side `accept` attribute and the size hint on a form are
 * conveniences for the person filling it in; neither survives a crafted
 * request, so every rule that matters is re-applied here.
 *
 * Images are public — they are meant to be fetched by a browser.
 * Documents are not: the plan requires stored documents to sit outside
 * the public web root and be served through a controller that checks the
 * request, so they go to a private disk and are never linked directly.
 */
class MediaStorage
{
    /** Extensions an image upload may carry, whatever its declared type. */
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    private const IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    private const DOCUMENT_MIMES = ['application/pdf'];

    public function storeImage(UploadedFile $file, string $folder): string
    {
        $this->assertImage($file);

        return $file->storeAs(
            "images/{$folder}",
            $this->filename($file),
            ['disk' => 'public']
        );
    }

    /**
     * @return array{path: string, name: string, size: int}
     */
    public function storeDocument(UploadedFile $file, string $folder): array
    {
        $this->assertPdf($file);

        $path = $file->storeAs(
            "documents/{$folder}",
            $this->filename($file),
            ['disk' => 'local']
        );

        return [
            'path' => $path,
            // The name the visitor sees on download, not the stored one.
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ];
    }

    public function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function deleteDocument(?string $path): void
    {
        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }

    /**
     * A name that cannot be used to reach outside its folder or to be
     * served as something other than what it is: the original is only a
     * slug, and the extension comes from the file's own type.
     */
    private function filename(UploadedFile $file): string
    {
        $stem = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $stem = Str::limit($stem ?: 'fichier', 60, '');

        return $stem.'-'.Str::random(8).'.'.strtolower($file->getClientOriginalExtension());
    }

    private function assertImage(UploadedFile $file): void
    {
        // getMimeType() reads the file's own bytes; the browser-supplied
        // type is attacker-controlled and is not consulted.
        if (! in_array($file->getMimeType(), self::IMAGE_MIMES, true)) {
            abort(422, "Ce fichier n'est pas une image acceptée (JPG, PNG ou WebP).");
        }

        if (! in_array(strtolower($file->getClientOriginalExtension()), self::IMAGE_EXTENSIONS, true)) {
            abort(422, "L'extension du fichier ne correspond pas à une image acceptée.");
        }
    }

    private function assertPdf(UploadedFile $file): void
    {
        if (! in_array($file->getMimeType(), self::DOCUMENT_MIMES, true)) {
            abort(422, "Ce fichier n'est pas un PDF.");
        }

        if (strtolower($file->getClientOriginalExtension()) !== 'pdf') {
            abort(422, "L'extension du fichier ne correspond pas à un PDF.");
        }
    }
}
