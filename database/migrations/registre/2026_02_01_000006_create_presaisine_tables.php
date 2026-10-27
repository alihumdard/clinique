<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Online pré-saisine submissions.
 *
 * They are kept in the Registre's database, not the public site's, even
 * though it is the public site that receives them. A submission carries
 * someone's name, telephone number and account of their problem: exactly
 * what "les informations confidentielles des dossiers ne doivent pas être
 * stockées dans la base de données du site public" is about. The public
 * controller writes here through one narrow path and reads nothing back.
 *
 * A submission is not a dossier. It stays a submission until someone at
 * the Clinic accepts it, which is the "transfert" of the plan; the
 * dossier is created at that moment and the intake attachments are
 * removed once they have been copied.
 */
return new class extends Migration
{
    public function getConnection(): string
    {
        return 'registre';
    }

    public function up(): void
    {
        Schema::connection('registre')->create('presaisine_number_sequences', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
        });

        Schema::connection('registre')->create('presaisine_submissions', function (Blueprint $table) {
            $table->id();

            // What the applicant is shown and told to quote.
            $table->string('reference', 30)->unique();

            // The plan's minimum: name, phone, domain, subject.
            $table->string('last_name');
            $table->string('first_name')->nullable();
            $table->string('phone', 40);
            $table->string('email')->nullable();
            $table->string('commune')->nullable();

            $table->boolean('is_uam_student')->default(false);
            $table->string('student_card_number', 40)->nullable();

            $table->string('legal_domain');
            $table->string('subject');
            $table->text('narrative')->nullable();

            // "jusqu'à trois questions"
            $table->text('question_1')->nullable();
            $table->text('question_2')->nullable();
            $table->text('question_3')->nullable();

            /*
             * The orientation answers that led here. Kept only for a
             * submission that was actually made: the plan forbids keeping
             * them for a visitor who was not routed to the form, so for
             * everyone else they never leave the session.
             */
            $table->json('orientation')->nullable();

            // en_attente, acceptee, refusee. The submission's own state,
            // which is not one of the thirteen dossier statuses: those
            // begin once a dossier exists.
            $table->string('status', 20)->default('en_attente');

            // Explicit consent to processing, and when it was given.
            $table->timestamp('consent_at');

            $table->foreignId('reviewed_by')->nullable()->constrained('registre_users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();

            // Set when the submission became a dossier.
            $table->foreignId('dossier_id')->nullable()->constrained('dossiers')->nullOnDelete();

            // Kept to rate-limit and to investigate abuse, not to profile.
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('phone');
        });

        Schema::connection('registre')->create('presaisine_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('presaisine_submissions')->cascadeOnDelete();

            // Private disk, outside the web root. Nothing links to it.
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size');

            /*
             * "Pièces jointes en ligne : supprimer du serveur de réception
             * après transfert sécurisé." The row stays as a record of what
             * arrived; this is when its file was removed from intake.
             */
            $table->timestamp('purged_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('registre')->dropIfExists('presaisine_attachments');
        Schema::connection('registre')->dropIfExists('presaisine_submissions');
        Schema::connection('registre')->dropIfExists('presaisine_number_sequences');
    }
};
