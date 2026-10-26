<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Shared taxonomy. One table rather than one per section: the Clinic
 * manages "catégories" the same way everywhere, and `type` keeps each
 * section's list separate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // article | guide | video | activity | partner | call
            $table->string('type', 32);
            $table->string('name');
            $table->string('slug');

            // Badge colour on the public site and in the admin tables.
            $table->string('tone', 16)->default('blue');
            $table->unsignedSmallInteger('position')->default(0);

            $table->timestamps();

            // A slug is unique within its own section, not across all of them.
            $table->unique(['type', 'slug']);
            $table->index(['type', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
