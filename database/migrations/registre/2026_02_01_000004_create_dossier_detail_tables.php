<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * What hangs off a dossier: who works on it, what is attached to it,
 * what is due on it, and how its status has moved.
 */
return new class extends Migration
{
    public function getConnection(): string
    {
        return 'registre';
    }

    public function up(): void
    {
        /*
         * "Affecter un ou plusieurs étudiants cliniciens et un
         * superviseur/encadrant" — several rows per dossier, so the
         * assignment is a table rather than two columns.
         */
        Schema::connection('registre')->create('dossier_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossiers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('registre_users')->restrictOnDelete();

            // 'etudiant' or 'encadrant': the part played on this dossier,
            // which is not necessarily the account's profile.
            $table->string('role', 20);

            $table->foreignId('assigned_by')->nullable()->constrained('registre_users')->nullOnDelete();
            $table->timestamp('assigned_at');

            // Ended rather than deleted: who worked on a dossier and when
            // is part of its history.
            $table->timestamp('ended_at')->nullable();

            $table->timestamps();

            $table->index(['dossier_id', 'role']);
            $table->index(['user_id', 'ended_at']);
        });

        /*
         * Document metadata only. The files themselves are written to a
         * private disk outside the web root, and are served by a
         * controller that checks the reader's rights first — "toute
         * consultation ou tout téléchargement exige une autorisation".
         */
        Schema::connection('registre')->create('dossier_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossiers')->cascadeOnDelete();

            // mandat_signe, piece_jointe, analyse, autre.
            $table->string('kind', 30);

            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size');

            // Arrived with an online pré-saisine rather than uploaded by
            // a member of the Clinic.
            $table->boolean('from_presaisine')->default(false);

            $table->foreignId('uploaded_by')->nullable()->constrained('registre_users')->nullOnDelete();
            $table->timestamps();

            $table->index(['dossier_id', 'kind']);
        });

        /*
         * "Suivre l'urgence, les échéances et les retards." Several per
         * dossier: a hearing, a filing, a call back.
         */
        Schema::connection('registre')->create('dossier_deadlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossiers')->cascadeOnDelete();
            $table->string('label');
            $table->timestamp('due_at');
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('registre_users')->nullOnDelete();
            $table->timestamps();

            $table->index(['due_at', 'completed_at']);
        });

        /*
         * "Enregistrer les changements de statut avec horodatage et
         * identité de l'utilisateur." Append-only: a row is never updated
         * or removed, which is what makes it evidence.
         */
        Schema::connection('registre')->create('dossier_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossiers')->cascadeOnDelete();
            $table->foreignId('from_status_id')->nullable()->constrained('dossier_statuses')->nullOnDelete();
            $table->foreignId('to_status_id')->constrained('dossier_statuses')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('registre_users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('created_at');

            $table->index(['dossier_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::connection('registre')->dropIfExists('dossier_status_history');
        Schema::connection('registre')->dropIfExists('dossier_deadlines');
        Schema::connection('registre')->dropIfExists('dossier_documents');
        Schema::connection('registre')->dropIfExists('dossier_assignments');
    }
};
