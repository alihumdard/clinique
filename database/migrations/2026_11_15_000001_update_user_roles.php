<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Client-approved access levels (approved Nov 2026):
 *
 *   admin        → Administrateur IT: full platform access on test data only;
 *                  cannot read real dossier data (enforced at registre level).
 *   coordinateur → Coordinateur de la Clinique: manages all CMS content and
 *                  registre dossiers/assignments; cannot change global settings
 *                  or create admin accounts.
 *   membre       → Membre de la Clinique: submits articles/photos for
 *                  coordinator review before publication; no registre access.
 *
 * The old 'editor' role maps to 'coordinateur' — it had the same intent and
 * the same screen access.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('coordinateur')->change();
        });

        DB::table('users')
            ->where('role', 'editor')
            ->update(['role' => 'coordinateur']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'coordinateur')
            ->update(['role' => 'editor']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('editor')->change();
        });
    }
};
