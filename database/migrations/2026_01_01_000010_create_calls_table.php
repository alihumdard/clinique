<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Appels à candidatures. The closing date is a column of its own because
 * the public page ranks on it and an expired call should drop off the
 * list without anyone having to unpublish it by hand.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();

            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            $table->date('closes_on')->nullable();

            $table->string('status', 16)->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'closes_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
