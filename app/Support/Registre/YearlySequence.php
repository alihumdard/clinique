<?php

namespace App\Support\Registre;

use Illuminate\Support\Facades\DB;

/**
 * A counter per year, locked while it is read and incremented.
 *
 * Both the dossier numbers and the pré-saisine references are handed out
 * this way. The counter is a row rather than a count of existing records:
 * two requests arriving in the same second would otherwise read the same
 * value, and a deleted record would hand its number to the next one —
 * two files quoting the same reference to the same person.
 */
class YearlySequence
{
    public static function next(string $table, string $prefix, ?int $year = null): string
    {
        $year ??= (int) now()->year;

        $number = DB::connection('registre')->transaction(function () use ($table, $year) {
            $sequence = DB::connection('registre')->table($table);

            // lockForUpdate: a second request waits here instead of
            // reading the same last_number.
            $row = $sequence->where('year', $year)->lockForUpdate()->first();

            if ($row === null) {
                $sequence->insert([
                    'year' => $year,
                    'last_number' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return 1;
            }

            $next = $row->last_number + 1;
            $sequence->where('year', $year)->update([
                'last_number' => $next,
                'updated_at' => now(),
            ]);

            return $next;
        });

        return sprintf('%s-%d-%04d', $prefix, $year, $number);
    }
}
