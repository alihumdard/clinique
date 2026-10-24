<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Web TV capsules. The plan is explicit that video files are not hosted
 * on this server: a capsule stores the address of a video that stays on
 * the Clinic's external channel, which is why there is a url and no file.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();

            // The address the Clinic pastes in, and what was parsed from it.
            $table->string('video_url');
            $table->string('provider', 32)->default('youtube');
            $table->string('provider_id', 64)->nullable();

            $table->string('cover')->nullable();
            $table->string('duration', 16)->nullable();

            // "transcription écrite lorsqu'elle existe"
            $table->longText('transcript')->nullable();

            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            $table->string('status', 16)->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->unsignedInteger('views')->default(0);
            $table->boolean('allow_comments')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
