<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A membre may only edit what they submitted themselves, so an activity has
 * to remember who that was — articles already carry author_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->after('category_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('author_id');
        });
    }
};
