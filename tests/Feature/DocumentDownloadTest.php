<?php

namespace Tests\Feature;

use App\Models\Guide;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The plan requires stored documents to sit outside the public web root and
 * be served through a controller. These guard both halves of that: the file
 * is reachable only through the route, and only for a published row.
 */
class DocumentDownloadTest extends TestCase
{
    use RefreshDatabase;

    private function guide(array $overrides = []): Guide
    {
        Storage::disk('local')->put('documents/fiches-pratiques/fiche.pdf', '%PDF-1.4 test');

        return Guide::create(array_merge([
            'title' => 'Droits de la famille',
            'slug' => 'droits-de-la-famille',
            'file_path' => 'documents/fiches-pratiques/fiche.pdf',
            'file_name' => 'droits-de-la-famille.pdf',
            'file_size' => 13,
            'status' => Guide::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ], $overrides));
    }

    public function test_a_published_fiche_can_be_downloaded(): void
    {
        Storage::fake('local');
        $guide = $this->guide();

        $response = $this->get(route('documents.guide', $guide));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString(
            'droits-de-la-famille.pdf',
            $response->headers->get('content-disposition')
        );
    }

    public function test_a_download_is_counted(): void
    {
        Storage::fake('local');
        $guide = $this->guide();

        $this->get(route('documents.guide', $guide))->assertOk();

        $this->assertSame(1, $guide->fresh()->downloads);
    }

    public function test_a_draft_fiche_cannot_be_downloaded(): void
    {
        Storage::fake('local');
        $guide = $this->guide(['status' => Guide::STATUS_DRAFT, 'published_at' => null]);

        $this->get(route('documents.guide', $guide))->assertNotFound();
        $this->assertSame(0, $guide->fresh()->downloads);
    }

    /** Published, but dated ahead: not yet available to anyone. */
    public function test_a_scheduled_fiche_cannot_be_downloaded(): void
    {
        Storage::fake('local');
        $guide = $this->guide(['published_at' => now()->addWeek()]);

        $this->get(route('documents.guide', $guide))->assertNotFound();
    }

    public function test_a_missing_file_is_a_404_and_not_a_download(): void
    {
        Storage::fake('local');
        $guide = $this->guide();
        Storage::disk('local')->delete($guide->file_path);

        $this->get(route('documents.guide', $guide))->assertNotFound();
        $this->assertSame(0, $guide->fresh()->downloads);
    }

    /**
     * A stray path must not be able to reach outside the documents folder,
     * even though only this application writes the column.
     */
    public function test_a_path_outside_the_documents_folder_is_refused(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('secret.txt', 'not for the public');

        $guide = $this->guide();
        $guide->forceFill(['file_path' => 'secret.txt'])->save();

        $this->get(route('documents.guide', $guide))->assertNotFound();
    }

    public function test_a_traversal_attempt_is_refused(): void
    {
        Storage::fake('local');

        $guide = $this->guide();
        $guide->forceFill(['file_path' => 'documents/../.env'])->save();

        $this->get(route('documents.guide', $guide))->assertNotFound();
    }

    public function test_a_report_can_be_downloaded_and_counted(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('documents/rapports-annuels/rapport.pdf', '%PDF-1.4 test');

        $report = Report::create([
            'title' => "Rapport annuel d'activité 2024",
            'slug' => 'rapport-2024',
            'year' => 2024,
            'file_path' => 'documents/rapports-annuels/rapport.pdf',
            'file_name' => 'rapport-2024.pdf',
            'file_size' => 13,
            'status' => Report::STATUS_PUBLISHED,
            'published_at' => now()->subMonth(),
        ]);

        $this->get(route('documents.report', $report))->assertOk();

        $this->assertSame(1, $report->fresh()->downloads);
    }

    /** The private disk must not be reachable without the controller. */
    public function test_the_documents_folder_is_not_in_the_public_web_root(): void
    {
        $this->assertFileDoesNotExist(public_path('documents'));
        $this->assertFileDoesNotExist(public_path('storage/documents'));
    }
}
