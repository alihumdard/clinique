<?php

namespace App\Support\Registre;

use Illuminate\Support\Facades\DB;

/**
 * Hands out the unique dossier reference the plan requires
 * ("générer des numéros de dossier uniques").
 *
 * Shape: CJ-2026-0001 — the Clinic's initials, the year the dossier was
 * opened, then a counter that restarts each year.
 *
 * The counter is a row that is locked and incremented inside a
 * transaction, not a count of existing dossiers: two consultations opened
 * in the same second would otherwise be handed the same number, and a
 * dossier removed from the list would hand its number to the next one —
 * two files quoting the same reference to the same applicant.
 */
class DossierNumber
{
    public const PREFIX = 'CJ';

    public static function next(?int $year = null): string
    {
        $year ??= (int) now()->year;

        $number = DB::connection('registre')->transaction(function () use ($year) {
            $table = DB::connection('registre')->table('dossier_number_sequences');

            // lockForUpdate so a second request waits here rather than
            // reading the same last_number.
            $row = $table->where('year', $year)->lockForUpdate()->first();

            if ($row === null) {
                $table->insert([
                    'year' => $year,
                    'last_number' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return 1;
            }

            $next = $row->last_number + 1;
            $table->where('year', $year)->update([
                'last_number' => $next,
                'updated_at' => now(),
            ]);

            return $next;
        });

        return sprintf('%s-%d-%04d', self::PREFIX, $year, $number);
    }
}
