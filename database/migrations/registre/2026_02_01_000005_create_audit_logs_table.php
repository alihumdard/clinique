<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The access and change log.
 *
 * "Tenir des journaux d'accès et de modification pour les activités
 * sensibles liées aux dossiers", and "les actions sensibles, transitions
 * de statut et événements d'accès/modification doivent pouvoir être
 * audités".
 *
 * Append-only by intent: the application writes rows and never updates or
 * deletes one. Module 4 adds the twelve-month retention sweep.
 */
return new class extends Migration
{
    public function getConnection(): string
    {
        return 'registre';
    }

    public function up(): void
    {
        Schema::connection('registre')->create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Nullable so a failed sign-in is logged too, where there is
            // no account to point at.
            $table->foreignId('user_id')->nullable()->constrained('registre_users')->nullOnDelete();

            // dossier.viewed, dossier.updated, document.downloaded,
            // status.changed, assignment.created, export.generated…
            $table->string('action', 60);

            // What it happened to, kept as type + id rather than a foreign
            // key: the row must survive the deletion of its subject, or
            // the log would erase itself exactly where it matters.
            $table->string('subject_type', 60)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            // Anything else worth keeping about the event — never the
            // confidential content itself, only what identifies it.
            $table->json('context')->nullable();

            $table->timestamp('created_at');

            $table->index(['subject_type', 'subject_id']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::connection('registre')->dropIfExists('audit_logs');
    }
};
