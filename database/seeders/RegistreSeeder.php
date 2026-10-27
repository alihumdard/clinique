<?php

namespace Database\Seeders;

use App\Models\Registre\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Fictitious Registre accounts, one per profile, for development and
 * review. The plan allows only fictitious data during development, and
 * the screen that creates real accounts is a module 4 deliverable.
 *
 * Run with: php artisan db:seed --class=RegistreSeeder
 */
class RegistreSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['Aminata Issoufou', 'coordination@registre.test', User::ROLE_COORDINATOR],
            ['Pr. Moussa Harouna', 'encadrant@registre.test', User::ROLE_SUPERVISOR],
            ['Fatima Abdou', 'etudiant@registre.test', User::ROLE_STUDENT],
            ['Service informatique', 'technique@registre.test', User::ROLE_TECHNICAL],
        ];

        foreach ($accounts as [$name, $email, $role]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    // Development password. Production accounts take at
                    // least twelve characters, renewed at first sign-in.
                    'password' => Hash::make('registre-demo-2026'),
                    'role' => $role,
                    'is_active' => true,
                ]
            );
        }

        // A disabled account, so the sign-in rule that refuses one is
        // visible in review rather than only in the tests.
        User::updateOrCreate(
            ['email' => 'compte-desactive@registre.test'],
            [
                'name' => 'Ancien étudiant clinicien',
                'password' => Hash::make('registre-demo-2026'),
                'role' => User::ROLE_STUDENT,
                'is_active' => false,
            ]
        );
    }
}
