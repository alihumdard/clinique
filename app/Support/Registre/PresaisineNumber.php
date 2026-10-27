<?php

namespace App\Support\Registre;

/**
 * The reference handed to someone who submits an online pré-saisine
 * ("générer un numéro de référence unique, afficher une confirmation").
 *
 * Shape: PS-2026-0001. Deliberately not the same prefix as a dossier: a
 * submission is not yet a case, and the applicant should not be given a
 * number that looks like one before the Clinic has accepted it.
 */
class PresaisineNumber
{
    public const PREFIX = 'PS';

    private const SEQUENCE_TABLE = 'presaisine_number_sequences';

    public static function next(?int $year = null): string
    {
        return YearlySequence::next(self::SEQUENCE_TABLE, self::PREFIX, $year);
    }
}
