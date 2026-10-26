<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Équipe. The plan notes the student list is replaced every three months,
 * so `cohort` records which session a member belongs to — that is what
 * lets a past list be retired without losing the record of who served.
 *
 * These rows describe identifiable people. The retention rules agreed
 * with the Clinic apply to them, and a member who leaves is hidden
 * rather than kept on the public page.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('role');
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();

            $table->string('department', 64)->nullable();
            $table->string('cohort', 32)->nullable();

            $table->string('email')->nullable();
            $table->string('linkedin')->nullable();

            $table->string('status', 16)->default('draft');
            $table->boolean('is_visible')->default(true);
            $table->unsignedSmallInteger('position')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'position']);
            $table->index('cohort');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
