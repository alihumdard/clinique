<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Actualités. The plan asks for categories and archives by year, so the
 * publication date is indexed and the status carries the four states the
 * admin screen shows: publié, brouillon, programmée, corbeille.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();

            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('status', 16)->default('draft');

            // Null while a draft; set when published or scheduled. The admin
            // "Programmée" state is simply a published_at in the future.
            $table->timestamp('published_at')->nullable();

            $table->unsignedInteger('views')->default(0);

            $table->timestamps();
            // The plan speaks of a corbeille rather than deletion.
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
