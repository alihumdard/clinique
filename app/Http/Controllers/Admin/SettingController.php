<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsRequest;
use App\Models\Setting;
use App\Services\MediaStorage;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private readonly MediaStorage $media)
    {
    }

    /**
     * Form field => [setting key, stored type].
     *
     * The field names are flat because an HTML form is flat; the setting
     * keys keep the dotted grouping the table is indexed on.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const FIELDS = [
        'site_name' => ['site.name', 'string'],
        'site_faculty' => ['site.faculty', 'string'],
        'site_university' => ['site.university', 'string'],
        'site_description' => ['site.description', 'string'],

        'contact_phone' => ['contact.phone', 'string'],
        'contact_phone_href' => ['contact.phone_href', 'string'],
        'contact_email' => ['contact.email', 'string'],
        'contact_address_line1' => ['contact.address_line1', 'string'],
        'contact_address_line2' => ['contact.address_line2', 'string'],
        'contact_address_city' => ['contact.address_city', 'string'],
        'contact_address_country' => ['contact.address_country', 'string'],

        'hours_days' => ['hours.days', 'string'],
        'hours_time' => ['hours.time', 'string'],
        'hours_note' => ['hours.note', 'string'],

        'social_facebook' => ['social.facebook', 'string'],
        'social_x' => ['social.x', 'string'],
        'social_linkedin' => ['social.linkedin', 'string'],
        'social_youtube' => ['social.youtube', 'string'],
        'social_instagram' => ['social.instagram', 'string'],

        'seo_title' => ['seo.title', 'string'],
        'seo_description' => ['seo.description', 'string'],

        'banner_enabled' => ['banner.enabled', 'boolean'],
        'banner_message' => ['banner.message', 'string'],
        'banner_tone' => ['banner.tone', 'string'],

        // The bandeau d'état on the home page said "Permanences ouvertes"
        // whatever the day: the state was written into the template. It is
        // a claim about something real — whether anyone is there to receive
        // a visitor — so the Clinic sets it.
        'service_state' => ['service.state', 'string'],
    ];

    /** Which card each field belongs to, used for the setting's group. */
    private const GROUPS = [
        'site' => 'general',
        'contact' => 'contact',
        'hours' => 'contact',
        'social' => 'social',
        'seo' => 'seo',
        'banner' => 'banner',
        'service' => 'banner',
    ];

    public function index(): View
    {
        return view('admin.settings', [
            // config carries the stored value where there is one and the
            // file's default where there is not, so most fields read from
            // config. The ones with no config default — the bandeau and
            // the uploaded images — read from here.
            'values' => SiteSettings::values(),
        ]);
    }

    public function update(SettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->storeImages($request);

        foreach (self::FIELDS as $field => [$key, $type]) {
            $value = $type === 'boolean'
                ? $request->boolean($field)
                : ($data[$field] ?? null);

            /*
             * An emptied field is removed rather than stored as an empty
             * string, so config/clinique.php becomes the default again
             * instead of the site showing a blank where a value belongs.
             */
            if ($type !== 'boolean' && blank($value)) {
                Setting::where('key', $key)->delete();

                continue;
            }

            Setting::put($key, $value, $type, self::group($key));
        }

        // A mass delete above fires no model events, so the cached set is
        // dropped explicitly.
        Setting::flushCache();

        /*
         * The config was laid over at boot, before any of this was saved.
         * Re-applying it here means anything read later in this same
         * request — a flash message, a preview link — already sees the new
         * values, rather than only the next request doing so.
         */
        SiteSettings::applyToConfig();

        return redirect()
            ->route('admin.settings')
            ->with('status', 'Les paramètres du site ont été enregistrés.');
    }

    /**
     * The three uploaded images, and the checkboxes that put each back to
     * the bundled default.
     */
    private function storeImages(SettingsRequest $request): void
    {
        $fields = [
            'logo' => 'site.logo',
            'favicon' => 'site.favicon',
            'seo_image' => 'seo.image',
        ];

        $remove = (array) $request->input('remove', []);

        foreach ($fields as $field => $key) {
            $previous = SiteSettings::values()[$key] ?? null;

            if ($request->hasFile($field)) {
                // Replace, then drop the old file — not the other way
                // round, so a failed upload cannot leave a blank logo.
                Setting::put($key, $this->media->storeImage($request->file($field), 'site'), 'string', 'general');
                $this->media->deleteImage($previous);

                continue;
            }

            if (in_array($field, $remove, true)) {
                Setting::where('key', $key)->delete();
                $this->media->deleteImage($previous);
            }
        }

        Setting::flushCache();
    }

    private static function group(string $key): string
    {
        $prefix = explode('.', $key)[0];

        return self::GROUPS[$prefix] ?? 'general';
    }
}
