<?php

namespace Tests;

/**
 * Migrates the Registre's own connection for a test that needs it.
 *
 * RefreshDatabase only prepares the default connection, and the Registre
 * is deliberately not on it. A test that wants Registre tables says so by
 * using this trait, which keeps the separation honest: nothing in the
 * Registre schema exists in a test that did not ask for it.
 */
trait RefreshesRegistreDatabase
{
    protected function setUpRefreshesRegistreDatabase(): void
    {
        $this->artisan('migrate', [
            '--database' => 'registre',
            '--path' => 'database/migrations/registre',
            '--realpath' => false,
        ]);
    }
}
