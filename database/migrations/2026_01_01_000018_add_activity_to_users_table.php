<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * An account is disabled rather than deleted.
 *
 * Deleting one would set author_id to null on everything that account
 * ever published, and the Clinic would lose the answer to "who wrote
 * this". Someone who leaves keeps their name on their work and loses
 * their way in.
 *
 * last_login_at is what makes an account nobody uses visible, which is
 * the first step in closing it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('role');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'last_login_at']);
        });
    }
};
