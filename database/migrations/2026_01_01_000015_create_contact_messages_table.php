<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Messages from the public contact form — distinct from the pré-saisine,
 * as the plan requires.
 *
 * These rows hold personal data a visitor typed in, so `consent_at`
 * records the explicit agreement the form asks for, and the retention
 * rules agreed with the Clinic apply from the date received.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject', 64);
            $table->text('message');

            $table->timestamp('consent_at')->nullable();

            $table->string('status', 16)->default('new');
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();

            // Kept for abuse handling only, and cleared with the row.
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
