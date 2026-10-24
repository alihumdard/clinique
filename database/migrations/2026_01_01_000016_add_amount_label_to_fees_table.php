<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Not every administrative fee is a single figure. The Clinic's own grid
 * has "5 000 - 15 000 FCFA", "Selon tarif en vigueur" and "Sur décision de
 * la Clinique" alongside the fixed amounts, and none of those fit an
 * unsigned integer. A label, when present, is shown instead of the figure.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->string('amount_label', 80)->nullable()->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropColumn('amount_label');
        });
    }
};
