<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Partenaires — logo, nature du partenariat, lien. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->string('logo')->nullable();
            $table->string('website')->nullable();

            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            $table->string('status', 16)->default('draft');
            $table->boolean('is_visible')->default(true);
            $table->unsignedSmallInteger('position')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
