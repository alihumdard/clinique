<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Error pages.
 *
 * Two faults, found by driving the live site. Only 403 and 404 had views at
 * all, so a 410 — which the registre raises deliberately, with a French
 * sentence of its own — came out as the framework's default page reading
 * "Something is broken. We will fix it as soon as possible." in English: a
 * confidential French tool telling a coordinator it was broken when it had
 * worked exactly as designed. And both existing views extended the public
 * layout, so an error inside the administration answered with the public
 * site's header, the Clinic's telephone number and the language switcher.
 */
class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{int}> */
    public static function statuses(): array
    {
        return [
            '403 refusé' => [403],
            '404 introuvable' => [404],
            '410 retiré' => [410],
            '419 session expirée' => [419],
            '429 trop de tentatives' => [429],
            '500 erreur serveur' => [500],
            '503 maintenance' => [503],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('statuses')]
    public function test_every_status_the_application_raises_has_a_french_page(int $status): void
    {
        $this->assertFileExists(
            resource_path("views/errors/{$status}.blade.php"),
            "A {$status} falls back to the framework's own page, which is in English."
        );

        $html = view("errors.{$status}", ['exception' => null])->render();

        $this->assertStringNotContainsString('Something is broken', $html);
        $this->assertStringNotContainsString('An Error Occurred', $html);
        $this->assertStringContainsString((string) $status, $html);
    }

    public function test_the_registre_410_says_what_actually_happened(): void
    {
        $message = 'Cette pièce jointe a été supprimée du serveur de réception.';

        $html = view('errors.410', [
            'exception' => new \Symfony\Component\HttpKernel\Exception\HttpException(410, $message),
        ])->render();

        // The application already wrote the right sentence at the abort();
        // it was the view that threw it away.
        $this->assertStringContainsString($message, $html);
    }

    public function test_a_404_inside_the_administration_does_not_answer_with_the_public_site(): void
    {
        $html = $this->actingAs(User::factory()->create())
            ->get('/admin/une-page-inexistante')
            ->assertNotFound()
            ->getContent();

        // The telephone number and the language switcher belong to the
        // public header. Finding them here means the administrator was put
        // on the public site by an error inside the CMS.
        $this->assertStringNotContainsString('/langue/en', $html);
        $this->assertStringNotContainsString('Faire une pré-saisine', $html);
        $this->assertStringContainsString('Retour au tableau de bord', $html);
    }

    public function test_a_404_inside_the_registre_offers_the_way_back_to_the_registre(): void
    {
        $html = $this->get('/registre/une-page-inexistante')->assertNotFound()->getContent();

        $this->assertStringNotContainsString('/langue/en', $html);
        $this->assertStringContainsString('Retour au registre', $html);
    }

    public function test_a_404_on_the_public_site_keeps_the_public_layout(): void
    {
        $html = $this->get('/une-adresse-inexistante')->assertNotFound()->getContent();

        // The public 404 is designed, with navigation and an illustration;
        // only the administration and the registre get the plain shell.
        $this->assertStringContainsString('/langue/en', $html);
    }

    public function test_the_administration_error_shell_is_not_indexable(): void
    {
        // The public 404 is a real page of the site and is left to the
        // public layout. The plain shell is only ever an internal screen.
        $html = $this->get('/admin/une-page-inexistante')->assertNotFound()->getContent();

        $this->assertStringContainsString('noindex', $html);
    }
}
