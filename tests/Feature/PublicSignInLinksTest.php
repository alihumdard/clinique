<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The way in, from the public site.
 *
 * Neither sign-in was linked from anywhere: both areas are noindex, so the
 * only route to them was to know the URL by heart. They are now a
 * "Connexion" control in the header, beside the Pré-saisine call to
 * action — one control with two destinations, since the platform has two
 * and an account from one is refused at the other.
 *
 * There is deliberately no registration. Accounts in the CMS are created
 * by an administrator and accounts in the registre by a coordinator, which
 * is what the specification asks of a platform whose other database holds
 * confidential case files. `/rejoindre` is the public path to joining the
 * Clinic, and it is a call for applications, not an account form.
 */
class PublicSignInLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_public_site_links_to_both_sign_ins(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString(route('admin.login'), $html);
        $this->assertStringContainsString(route('registre.login'), $html);
    }

    public function test_the_links_are_on_every_public_page(): void
    {
        // They are in the shared header, so they are reachable from
        // wherever someone happens to be.
        foreach (['/a-propos', '/actualites', '/contact', '/services'] as $uri) {
            $html = $this->get($uri)->assertOk()->getContent();

            $this->assertStringContainsString(route('admin.login'), $html, "{$uri} has no sign-in link.");
        }
    }

    public function test_the_sign_in_links_are_not_offered_to_crawlers(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        // Both destinations send noindex of their own; rel=nofollow keeps
        // the public site from pointing a crawler at them in the first place.
        foreach ([route('admin.login'), route('registre.login')] as $href) {
            $this->assertMatchesRegularExpression(
                '~<a[^>]*href="'.preg_quote($href, '~').'"[^>]*rel="nofollow"~',
                $html,
                "The link to {$href} does not carry rel=nofollow."
            );
        }
    }

    public function test_both_links_lead_to_a_working_sign_in_page(): void
    {
        $this->get(route('admin.login'))->assertOk()->assertSee('Se connecter', escape: false);
        $this->get(route('registre.login'))->assertOk();
    }

    public function test_the_two_areas_are_still_separate_doors(): void
    {
        // One link each, because they are two systems with their own
        // accounts. A single door would turn half the staff away without
        // telling them why.
        $this->assertNotSame(route('admin.login'), route('registre.login'));
    }

    public function test_there_is_no_public_registration_to_link_to(): void
    {
        // If this ever starts failing, someone has added self-registration
        // to a platform whose sibling database holds confidential case
        // files. That is a decision for the Clinic, not a convenience.
        foreach (['/inscription', '/register', '/signup', '/creer-un-compte'] as $uri) {
            $this->get($uri)->assertNotFound();
        }
    }

    public function test_joining_the_clinic_goes_through_the_call_for_applications(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        // This is what the public "register" actually is here.
        $this->assertStringContainsString(route('join'), $html);
        $this->get(route('join'))->assertOk();
    }
}
