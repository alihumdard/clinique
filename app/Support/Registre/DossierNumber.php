<?php

namespace App\Support\Registre;

/**
 * The unique dossier reference the plan requires ("générer des numéros de
 * dossier uniques").
 *
 * Shape: CJ-2026-0001 — the Clinic's initials, the year the dossier was
 * opened, then a counter that restarts each year. The counter itself is
 * handled by YearlySequence, which the pré-saisine references share.
 */
class DossierNumber
{
    public const PREFIX = 'CJ';

    private const SEQUENCE_TABLE = 'dossier_number_sequences';

    public static function next(?int $year = null): string
    {
        return YearlySequence::next(self::SEQUENCE_TABLE, self::PREFIX, $year);
    }
}
