<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The dossier statuses, and the moves allowed between them.
 *
 * The plan requires thirteen of them and is explicit about the labels:
 * "Utiliser la liste des 13 statuts de dossier approuvée par la Clinique.
 * Les libellés exacts doivent être confirmés ; ne pas les inventer."
 *
 * They are therefore rows, not an enum and not constants in the code: the
 * Clinic's wording goes in without a migration, and the transitions they
 * approve go in beside it. Until then the seeded rows are marked
 * provisional and every screen that shows one says so.
 */
return new class extends Migration
{
    public function getConnection(): string
    {
        return 'registre';
    }

    public function up(): void
    {
        Schema::connection('registre')->create('dossier_statuses', function (Blueprint $table) {
            $table->id();

            // Stable handle for code that must name one status — the
            // label is the Clinic's to change, the code is not.
            $table->string('code', 40)->unique();
            $table->string('label');
            $table->unsignedSmallInteger('position');

            // A dossier in a terminal status is closed: the retention
            // rules of module 4 count from there.
            $table->boolean('is_terminal')->default(false);

            /*
             * The gate the plan demands: "Empêcher le passage au statut
             * « analyse retournée au demandeur » tant que la validation du
             * superviseur n'est pas terminée." Which status that is cannot
             * be known before the Clinic names it, so the rule is carried
             * by a flag on the row rather than by a hard-coded id.
             */
            $table->boolean('requires_supervisor_validation')->default(false);

            // True while the label is ours and not the Clinic's.
            $table->boolean('is_provisional')->default(true);

            $table->timestamps();
        });

        Schema::connection('registre')->create('dossier_status_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_status_id')->constrained('dossier_statuses')->cascadeOnDelete();
            $table->foreignId('to_status_id')->constrained('dossier_statuses')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['from_status_id', 'to_status_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('registre')->dropIfExists('dossier_status_transitions');
        Schema::connection('registre')->dropIfExists('dossier_statuses');
    }
};
