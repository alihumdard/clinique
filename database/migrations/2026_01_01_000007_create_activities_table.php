<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Activités et caravanes juridiques, with their photo gallery. The place
 * and the date are columns of their own because the admin table sorts and
 * filters on them, and the public page shows them beside each activity.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();

            $table->string('location')->nullable();
            $table->date('held_on')->nullable();

            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            $table->string('status', 16)->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'held_on']);
        });

        Schema::create('activity_photos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();

            $table->string('path');
            // Carries the alt text; an information-bearing image needs one.
            $table->string('caption')->nullable();
            $table->unsignedSmallInteger('position')->default(0);

            $table->timestamps();

            $table->index(['activity_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_photos');
        Schema::dropIfExists('activities');
    }
};
