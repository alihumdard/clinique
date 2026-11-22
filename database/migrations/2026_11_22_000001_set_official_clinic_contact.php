<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The Clinic's official contact details, supplied in November 2026.
 *
 * A stored setting overrides config/clinique.php, so a row still holding a
 * placeholder would keep the old value on screen. Only those placeholder
 * rows are replaced; anything the Clinic typed in Paramètres is left alone.
 */
return new class extends Migration
{
    /** @var array<string, array{0: list<string>, 1: string}> key => [placeholders, official value] */
    private const VALUES = [
        'contact.phone' => [['+227 20 31 52 63'], '+227 88 75 88 23'],
        'contact.phone_href' => [['+22720315263'], '+22788758823'],
        'contact.email' => [['contact@clinique-fsjp.ne'], 'cliniquejuridiquefsjp@gmail.com'],
        'social.facebook' => [['', '#'], 'https://www.facebook.com/share/1TMEG3vPVN/'],
    ];

    public function up(): void
    {
        foreach (self::VALUES as $key => [$placeholders, $official]) {
            DB::table('settings')
                ->where('key', $key)
                ->where(fn ($q) => $q->whereIn('value', $placeholders)->orWhereNull('value'))
                ->update(['value' => $official, 'updated_at' => now()]);
        }

        Setting::flushCache();
    }

    public function down(): void
    {
        // The placeholders were never real details; nothing to restore.
    }
};
