<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Rapports annuels d'activité — one PDF per year. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->year('year');
            $table->text('description')->nullable();

            $table->string('cover')->nullable();

            // Private disk, as for the guides.
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->default(0);

            $table->string('status', 16)->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->unsignedInteger('downloads')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
