<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Module 2 asks for "l'authentification administrateur et les
 * autorisations des éditeurs de contenu": a CMS account that writes and
 * publishes content is not the same thing as one that changes the site's
 * settings, its référentiels, or reads the personal data left by the
 * public.
 *
 * Two roles are enough for the content back-office. The four Register
 * roles of module 3 (Coordinateur, Encadrant, Étudiant, Administrateur
 * technique) belong to a separate application with its own accounts, and
 * their permission matrix is still to be approved by the Clinic.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Editor is the safe default: an account created without a
            // role stated gets the lesser of the two.
            $table->string('role', 20)->default('editor')->after('email');
        });

        // The accounts that exist today are the ones administering the
        // site, so they keep every screen they had before this change.
        DB::table('users')->update(['role' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
