<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        Setting::flushCache();
    }

    /** @return array<string, mixed> */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'site_name' => 'Clinique Juridique FSJP-UAM',
            'site_description' => "Accès au droit pour tous au Niger.",
            'contact_email' => 'contact@clinique-fsjp.ne',
        ], $overrides);
    }

    public function test_the_screen_requires_authentication(): void
    {
        $this->get('/admin/parametres')->assertRedirect(route('admin.login'));
        $this->put('/admin/parametres', $this->payload())->assertRedirect(route('admin.login'));
    }

    /**
     * A stored setting has to reach the public site, which reads
     * config('clinique.*') in twenty-odd views.
     */
    public function test_a_saved_setting_reaches_the_public_site(): void
    {
        $this->actingAs($this->admin)
            ->put('/admin/parametres', $this->payload([
                'contact_email' => 'nouvelle-adresse@clinique-fsjp.ne',
                'contact_phone' => '+227 20 00 00 00',
            ]))
            ->assertRedirect(route('admin.settings'));

        // A fresh request, as a visitor would make.
        $this->get('/contact')
            ->assertOk()
            ->assertSee('nouvelle-adresse@clinique-fsjp.ne', escape: false)
            ->assertSee('+227 20 00 00 00', escape: false);
    }

    /** An emptied field restores the file default rather than blanking the site. */
    public function test_emptying_a_field_restores_the_default(): void
    {
        Setting::put('hours.note', 'Fermé le vendredi après-midi', 'string', 'contact');

        $this->actingAs($this->admin)->put('/admin/parametres', $this->payload([
            'hours_note' => '',
        ]));

        // The row is gone, so config/clinique.php is the default again
        // rather than the site showing a blank where a value belongs.
        $this->assertDatabaseMissing('settings', ['key' => 'hours.note']);
        $this->assertArrayNotHasKey('hours.note', SiteSettings::values());
    }

    public function test_an_invalid_email_is_refused(): void
    {
        $this->actingAs($this->admin)
            ->put('/admin/parametres', $this->payload(['contact_email' => 'pas-une-adresse']))
            ->assertSessionHasErrors('contact_email');
    }

    /** A social link goes into an href, so http and other schemes are out. */
    public function test_a_social_link_must_be_https(): void
    {
        $this->actingAs($this->admin)
            ->put('/admin/parametres', $this->payload(['social_facebook' => 'http://facebook.com/x']))
            ->assertSessionHasErrors('social_facebook');

        $this->actingAs($this->admin)
            ->put('/admin/parametres', $this->payload(['social_facebook' => 'javascript:alert(1)']))
            ->assertSessionHasErrors('social_facebook');
    }

    public function test_the_phone_used_in_links_must_be_digits(): void
    {
        $this->actingAs($this->admin)
            ->put('/admin/parametres', $this->payload(['contact_phone_href' => '+227 20 31 52 63']))
            ->assertSessionHasErrors('contact_phone_href');

        $this->actingAs($this->admin)
            ->put('/admin/parametres', $this->payload(['contact_phone_href' => '+22720315263']))
            ->assertSessionHasNoErrors();
    }

    /*
    |--------------------------------------------------------------------------
    | Bandeau d'état
    |--------------------------------------------------------------------------
    */

    public function test_the_banner_appears_on_every_page_once_enabled(): void
    {
        $this->actingAs($this->admin)->put('/admin/parametres', $this->payload([
            'banner_enabled' => '1',
            'banner_message' => 'La Clinique sera fermée le 31 octobre.',
            'banner_tone' => 'closure',
        ]));

        foreach (['/', '/contact', '/actualites'] as $uri) {
            $this->get($uri)
                ->assertOk()
                ->assertSee('La Clinique sera fermée le 31 octobre.', escape: false);
        }
    }

    public function test_the_banner_is_absent_from_the_markup_when_switched_off(): void
    {
        Setting::put('banner.message', 'Un message resté en base', 'string', 'banner');
        Setting::put('banner.enabled', '0', 'boolean', 'banner');

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Un message resté en base', escape: false);
    }

    public function test_an_enabled_banner_needs_a_message(): void
    {
        $this->actingAs($this->admin)
            ->put('/admin/parametres', $this->payload([
                'banner_enabled' => '1',
                'banner_message' => '',
            ]))
            ->assertSessionHasErrors('banner_message');
    }

    public function test_an_unknown_banner_tone_is_refused(): void
    {
        $this->actingAs($this->admin)
            ->put('/admin/parametres', $this->payload([
                'banner_enabled' => '1',
                'banner_message' => 'Message',
                'banner_tone' => 'rainbow',
            ]))
            ->assertSessionHasErrors('banner_tone');
    }

    /*
    |--------------------------------------------------------------------------
    | Images
    |--------------------------------------------------------------------------
    */

    public function test_an_uploaded_logo_replaces_the_bundled_one(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->put('/admin/parametres', $this->payload([
            'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
        ]));

        $stored = SiteSettings::values()['site.logo'] ?? null;

        $this->assertNotNull($stored);
        Storage::disk('public')->assertExists($stored);

        // The header reads through the same helper.
        $this->get('/')->assertOk()->assertSee($stored, escape: false);
    }

    public function test_an_svg_logo_is_refused(): void
    {
        $svg = UploadedFile::fake()->createWithContent(
            'logo.svg',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'
        );

        $this->actingAs($this->admin)
            ->put('/admin/parametres', $this->payload(['logo' => $svg]))
            ->assertSessionHasErrors('logo');
    }

    public function test_the_default_image_can_be_restored(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->put('/admin/parametres', $this->payload([
            'logo' => UploadedFile::fake()->image('logo.png'),
        ]));

        $stored = SiteSettings::values()['site.logo'];

        $this->actingAs($this->admin)->put('/admin/parametres', $this->payload([
            'remove' => ['logo'],
        ]));

        $this->assertDatabaseMissing('settings', ['key' => 'site.logo']);
        Storage::disk('public')->assertMissing($stored);
    }

    /** The whole table is cached, so a write has to drop that cache. */
    public function test_the_cache_is_dropped_on_save(): void
    {
        Setting::put('site.name', 'Premier nom');
        $this->assertSame('Premier nom', SiteSettings::values()['site.name']);

        $this->actingAs($this->admin)->put('/admin/parametres', $this->payload([
            'site_name' => 'Deuxième nom',
        ]));

        $this->assertSame('Deuxième nom', SiteSettings::values()['site.name']);
    }
}
