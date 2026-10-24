<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Newsletter subscribers.
 *
 * The retention rule for this list is "jusqu'à désinscription", so an
 * unsubscribe is recorded rather than the row being deleted outright —
 * that is what stops a later import silently re-subscribing someone who
 * asked to leave. `token` is what an unsubscribe link carries, so a
 * person can leave without signing in anywhere.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();

            $table->string('email')->unique();
            $table->string('name')->nullable();

            $table->string('token', 64)->unique();

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();

            $table->timestamps();

            $table->index('unsubscribed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
