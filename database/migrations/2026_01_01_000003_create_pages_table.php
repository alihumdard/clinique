<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Editable site pages. The slug is the public path the page answers on,
 * which is why the admin screen shows it as a column of its own.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('body')->nullable();
            $table->string('image')->nullable();

            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            $table->string('status', 16)->default('draft');
            $table->timestamp('published_at')->nullable();

            // Pages such as Accueil or Contact are rendered by their own
            // template and must not be deletable from the CMS.
            $table->boolean('is_system')->default(false);

            $table->string('seo_title')->nullable();
            $table->string('seo_description', 320)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
