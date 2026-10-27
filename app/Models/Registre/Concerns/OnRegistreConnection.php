<?php

namespace App\Models\Registre\Concerns;

/**
 * Every Registre model reads and writes the Registre's own connection.
 *
 * Stated once, in a trait, so that a model added later cannot quietly
 * default to the public site's database — which is the one thing the
 * plan's separation rule forbids.
 */
trait OnRegistreConnection
{
    public function getConnectionName(): string
    {
        return 'registre';
    }
}
