<?php

namespace Tests\Feature;

use App\Services\MediaStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * MediaStorage is the last check before a file is written, and the only one
 * that looks at the file's own bytes.
 *
 * These go through the service rather than an HTTP request on purpose:
 * Laravel's faked uploads report the mime type derived from the filename,
 * so a request-level test can never present a file whose bytes disagree
 * with its name — which is the whole case being guarded against.
 */
class MediaStorageTest extends TestCase
{
    private MediaStorage $media;

    protected function setUp(): void
    {
        parent::setUp();

        $this->media = new MediaStorage;
        Storage::fake('public');
        Storage::fake('local');
    }

    /** A plain Symfony upload: getMimeType() reads the file, not the name. */
    private function upload(string $contents, string $name, string $declaredType): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'upload');
        file_put_contents($path, $contents);

        return new UploadedFile($path, $name, $declaredType, null, true);
    }

    private function pdfBytes(): string
    {
        return "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF";
    }

    private function pngBytes(): string
    {
        // 1×1 transparent PNG.
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8DwHwAFAAH/q842iQAAAABJRU5ErkJggg==');
    }

    public function test_a_real_pdf_is_stored_on_the_private_disk(): void
    {
        $stored = $this->media->storeDocument(
            $this->upload($this->pdfBytes(), 'rapport 2024.pdf', 'application/pdf'),
            'rapports-annuels'
        );

        Storage::disk('local')->assertExists($stored['path']);
        // Never the public disk: the plan keeps documents out of the web root.
        Storage::disk('public')->assertMissing($stored['path']);
        $this->assertSame('rapport 2024.pdf', $stored['name']);
        $this->assertGreaterThan(0, $stored['size']);
    }

    public function test_a_file_renamed_to_pdf_is_refused(): void
    {
        $this->expectException(HttpException::class);

        $this->media->storeDocument(
            $this->upload($this->pngBytes(), 'fiche.pdf', 'application/pdf'),
            'fiches-pratiques'
        );
    }

    public function test_a_pdf_renamed_to_an_image_is_refused(): void
    {
        $this->expectException(HttpException::class);

        $this->media->storeImage(
            $this->upload($this->pdfBytes(), 'vignette.png', 'image/png'),
            'actualites'
        );
    }

    /** The mockup lists SVG for partner logos; it carries script, so no. */
    public function test_an_svg_is_refused_as_an_image(): void
    {
        $this->expectException(HttpException::class);

        $svg = '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>';

        $this->media->storeImage(
            $this->upload($svg, 'logo.svg', 'image/svg+xml'),
            'partenaires'
        );
    }

    public function test_the_stored_name_cannot_reach_outside_its_folder(): void
    {
        $stored = $this->media->storeDocument(
            $this->upload($this->pdfBytes(), '../../../etc/passwd.pdf', 'application/pdf'),
            'fiches-pratiques'
        );

        $this->assertStringStartsWith('documents/fiches-pratiques/', $stored['path']);
        $this->assertStringNotContainsString('..', $stored['path']);
        Storage::disk('local')->assertExists($stored['path']);
    }

    /** Two uploads of the same name must not overwrite one another. */
    public function test_two_files_with_the_same_name_get_separate_paths(): void
    {
        $first = $this->media->storeImage($this->upload($this->pngBytes(), 'photo.png', 'image/png'), 'actualites');
        $second = $this->media->storeImage($this->upload($this->pngBytes(), 'photo.png', 'image/png'), 'actualites');

        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertExists($first);
        Storage::disk('public')->assertExists($second);
    }
}
