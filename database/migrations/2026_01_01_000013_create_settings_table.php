<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Site settings, including the bandeau d'état du service the plan asks
 * the Clinic to be able to switch on, off and reword.
 *
 * Key/value rather than one column per setting: the settings screen has
 * eight tabs that will keep growing, and a new field should not need a
 * migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->longText('value')->nullable();

            // string | boolean | integer | json — how to cast on read.
            $table->string('type', 16)->default('string');
            $table->string('group', 32)->default('general');

            $table->timestamps();

            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
