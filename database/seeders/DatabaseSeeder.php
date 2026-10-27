<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Fictitious development account only. The security plan forbids
        // real Clinic data during development, and requires production
        // passwords to be at least 12 characters and changed at first login.
        User::updateOrCreate(
            ['email' => 'adminclinique@gmail.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        // A second account so the two roles can be compared during the
        // review: this one sees the content screens and not the settings,
        // the référentiels or the public's personal data. The screen that
        // creates accounts is a module 4 deliverable (52. Gestion des
        // utilisateurs), so until then they are seeded.
        User::updateOrCreate(
            ['email' => 'editeurclinique@gmail.com'],
            [
                'name' => 'Éditeur de contenu',
                'password' => Hash::make('password'),
                'role' => User::ROLE_EDITOR,
            ]
        );
    }
}
