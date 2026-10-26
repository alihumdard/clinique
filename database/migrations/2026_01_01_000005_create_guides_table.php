<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fiches pratiques — a PDF per guide, published by legal domain.
 *
 * `file_path` points inside a private disk, never the public web root:
 * the plan requires stored documents to sit outside it and be served
 * through a controller that checks the request first.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guides', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('body')->nullable();

            $table->string('cover')->nullable();

            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->default(0);

            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            $table->string('status', 16)->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->unsignedInteger('downloads')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guides');
    }
};
