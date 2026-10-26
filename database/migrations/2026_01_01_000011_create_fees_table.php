<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Grille tarifaire — the plan calls for "un tableau administrable", so
 * each line is a row the Clinic can edit rather than text in a page.
 *
 * The amount is stored in whole francs CFA: the currency has no minor
 * unit, and an integer cannot drift the way a float can.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->id();

            $table->string('label');
            $table->text('note')->nullable();

            $table->unsignedInteger('amount')->nullable();
            $table->string('currency', 8)->default('FCFA');

            // A line that is free of charge, which the plan treats as a
            // case of its own rather than an amount of zero.
            $table->boolean('is_free')->default(false);

            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('is_visible')->default(true);

            $table->timestamps();

            $table->index('position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
