<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Throwable;

/**
 * Bridges the CMS settings table onto config('clinique.*').
 *
 * config/clinique.php was the single source of truth for the public site
 * through the design phase, and 20-odd views read it directly. Rather than
 * rewrite every one of them, the stored settings are layered over that
 * config once per request: a key the Clinic has filled in wins, and the
 * config value remains the default for one they have not. The views do not
 * change, and a fresh install still renders.
 */
class SiteSettings
{
    /**
     * Setting key => config key. Only these are editable from the CMS; the
     * rest of config/clinique.php stays in the file.
     *
     * @var array<string, string>
     */
    public const MAP = [
        'site.name' => 'clinique.name',
        'site.faculty' => 'clinique.faculty',
        'site.university' => 'clinique.university',
        'site.description' => 'clinique.description',

        'contact.phone' => 'clinique.contact.phone.display',
        'contact.phone_href' => 'clinique.contact.phone.tel',
        'contact.email' => 'clinique.contact.email',
        'contact.address_line1' => 'clinique.contact.address.line1',
        'contact.address_line2' => 'clinique.contact.address.line2',
        'contact.address_city' => 'clinique.contact.address.city',
        'contact.address_country' => 'clinique.contact.address.country',

        'hours.days' => 'clinique.hours.days',
        'hours.time' => 'clinique.hours.time',
        'hours.note' => 'clinique.hours.note',

        'social.facebook' => 'clinique.social.facebook',
        'social.x' => 'clinique.social.x',
        'social.linkedin' => 'clinique.social.linkedin',
        'social.youtube' => 'clinique.social.youtube',
        'social.instagram' => 'clinique.social.instagram',

        'seo.title' => 'clinique.seo.title',
        'seo.description' => 'clinique.seo.description',
    ];

    /**
     * Image settings and the bundled file each falls back to, so a fresh
     * install renders before the Clinic has uploaded anything.
     *
     * @var array<string, string>
     */
    public const IMAGES = [
        'site.logo' => 'images/brand/logo.png',
        'site.favicon' => 'images/brand/logo.png',
        'seo.image' => 'images/brand/logo.png',
    ];

    /**
     * The logo for the header, which shows it at 48px.
     *
     * The bundled 96px copy exists because the main file is also the
     * favicon and the Open Graph image and needs the resolution for those;
     * at 57 Ko on every page it was the heaviest thing the site served
     * twice over. An uploaded logo has no small copy, so it is used as it
     * is — the Clinic's own file, at whatever size they chose.
     */
    public static function headerLogoUrl(): string
    {
        if (filled(self::values()['site.logo'] ?? null)) {
            return self::imageUrl('site.logo');
        }

        return asset('images/brand/logo-96.png');
    }

    /** A URL for one of the image settings, uploaded or bundled. */
    public static function imageUrl(string $key): string
    {
        $stored = self::values()[$key] ?? null;

        return blank($stored)
            ? asset(self::IMAGES[$key] ?? 'images/brand/logo.png')
            : Media::url($stored, 'brand/logo');
    }

    /** Lays the stored settings over the config defaults. */
    public static function applyToConfig(): void
    {
        foreach (self::values() as $key => $value) {
            if (! isset(self::MAP[$key]) || blank($value)) {
                continue;
            }

            Config::set(self::MAP[$key], $value);
        }
    }

    /**
     * The site-wide notice the plan calls a bandeau d'état: a closure or a
     * change of opening hours the Clinic needs every visitor to see.
     *
     * @return array{message: string, tone: string}|null
     */
    public static function banner(): ?array
    {
        $values = self::values();

        if (empty($values['banner.enabled']) || blank($values['banner.message'] ?? null)) {
            return null;
        }

        return [
            'message' => $values['banner.message'],
            'tone' => $values['banner.tone'] ?? 'info',
        ];
    }

    /**
     * Every stored setting, or an empty set when the table is not there
     * yet — during the very first migrate, for one, where a provider that
     * threw would stop the command it is being run by.
     *
     * @return array<string, mixed>
     */
    public static function values(): array
    {
        try {
            return Setting::allValues();
        } catch (Throwable) {
            return [];
        }
    }
}
