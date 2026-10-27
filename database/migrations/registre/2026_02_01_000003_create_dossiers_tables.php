<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The applicants and their dossiers.
 *
 * Both live in the Registre's own database: this is the confidential
 * material the plan keeps out of the public site entirely.
 */
return new class extends Migration
{
    public function getConnection(): string
    {
        return 'registre';
    }

    public function up(): void
    {
        Schema::connection('registre')->create('applicants', function (Blueprint $table) {
            $table->id();

            $table->string('last_name');
            $table->string('first_name')->nullable();

            // The plan's minimum is name, phone, domain and subject, so
            // the phone is the one contact detail that is never absent.
            $table->string('phone', 40);
            $table->string('email')->nullable();
            $table->string('commune')->nullable();
            $table->string('address')->nullable();

            // Asked by the pré-saisine form "le cas échéant".
            $table->boolean('is_uam_student')->default(false);
            $table->string('student_card_number', 40)->nullable();

            /*
             * The full field list is still to be confirmed by the Clinic
             * ("champs définitifs du dossier"), so what is here is what
             * the plan names and nothing more. An applicant is never
             * created from unconfirmed fields.
             */
            $table->timestamps();

            $table->index('phone');
            $table->index('last_name');
        });

        /*
         * Dossier numbering. A counter row per year rather than a count of
         * existing dossiers: two consultations opened in the same second
         * must not be handed the same number, and a deleted dossier must
         * not hand its number to the next one.
         */
        Schema::connection('registre')->create('dossier_number_sequences', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
        });

        Schema::connection('registre')->create('dossiers', function (Blueprint $table) {
            $table->id();

            // What the Clinic and the applicant quote to each other.
            $table->string('reference', 30)->unique();

            $table->foreignId('applicant_id')->constrained('applicants')->restrictOnDelete();

            /*
             * The legal domain is copied as text, not joined: the list the
             * public site offers lives in the other database, and a
             * cross-database foreign key is not possible — nor desirable,
             * since a dossier must keep the wording it was opened under
             * even if the public list is later reworded.
             */
            $table->string('legal_domain');

            $table->string('subject');
            $table->text('narrative')->nullable();

            // Up to three questions, as the pré-saisine form allows.
            $table->text('question_1')->nullable();
            $table->text('question_2')->nullable();
            $table->text('question_3')->nullable();

            $table->foreignId('status_id')->constrained('dossier_statuses')->restrictOnDelete();

            // Walk-in or transferred from an online pré-saisine.
            $table->string('source', 20)->default('sur_place');

            // The reference the applicant was given online, kept so a
            // submission can be traced to the dossier it became.
            $table->string('presaisine_reference', 30)->nullable()->index();

            $table->string('urgency', 20)->default('normale');

            // The analysis, and the supervisor gate that stands in front
            // of returning it to the applicant.
            $table->text('analysis')->nullable();
            $table->timestamp('analysis_submitted_at')->nullable();
            $table->foreignId('analysis_validated_by')->nullable()->constrained('registre_users')->nullOnDelete();
            $table->timestamp('analysis_validated_at')->nullable();
            $table->timestamp('returned_to_applicant_at')->nullable();

            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('registre_users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status_id', 'opened_at']);
            $table->index('urgency');
        });
    }

    public function down(): void
    {
        Schema::connection('registre')->dropIfExists('dossiers');
        Schema::connection('registre')->dropIfExists('dossier_number_sequences');
        Schema::connection('registre')->dropIfExists('applicants');
    }
};
