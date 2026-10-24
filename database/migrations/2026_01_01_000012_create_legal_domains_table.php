<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Domaines du droit. The plan asks the Clinic to keep this list up to
 * date because it feeds both the pré-saisine form and the register, so
 * it is a table rather than a hard-coded list.
 *
 * `plain_language` carries the everyday restatement the plan requires
 * beside each academic heading.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_domains', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('plain_language')->nullable();
            $table->string('icon', 32)->nullable();
            $table->string('tone', 16)->default('blue');

            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('position')->default(0);

            $table->timestamps();

            $table->index(['is_active', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_domains');
    }
};
