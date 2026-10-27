<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Accounts of the Registre numérique.
 *
 * They live in the Registre's own database, not beside the CMS accounts:
 * the plan asks for the two areas to be separated "en matière de comptes,
 * de contrôles d'accès et de base de données". Someone administering the
 * website therefore has no account here at all, which is also what
 * "l'administrateur technique ne doit pas obtenir l'accès au contenu
 * confidentiel des dossiers" requires.
 *
 * Run with:
 *   php artisan migrate --database=registre --path=database/migrations/registre
 */
return new class extends Migration
{
    public function getConnection(): string
    {
        return 'registre';
    }

    public function up(): void
    {
        Schema::connection('registre')->create('registre_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            /*
             * The four profiles the plan names. Their exact rights are the
             * "matrice des autorisations" the Clinic still has to approve,
             * so this column records which profile an account holds and
             * nothing is decided from it yet.
             */
            $table->string('role', 30);

            // An account is disabled rather than deleted: a dossier keeps
            // its assignments, and the audit trail keeps pointing at a row
            // that still exists.
            $table->boolean('is_active')->default(true);

            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index(['role', 'is_active']);
        });

        Schema::connection('registre')->create('registre_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::connection('registre')->dropIfExists('registre_sessions');
        Schema::connection('registre')->dropIfExists('registre_users');
    }
};
