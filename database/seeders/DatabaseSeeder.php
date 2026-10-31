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
        /*
         * The content seeders, and not only the two accounts.
         *
         * Without them a fresh checkout follows the README and arrives at
         * a site whose pré-saisine cannot be sent: `legal_domain` is
         * required and its select is built from `legal_domains`, which
         * `ReferenceSeeder` fills. The form was unusable rather than
         * merely empty, and nothing said so.
         *
         * Every one of them is written with `updateOrCreate`, so running
         * `migrate --seed` again updates the fixtures instead of
         * duplicating them.
         */
        $this->call([
            ReferenceSeeder::class,
            PageSeeder::class,
            ContentSeeder::class,
            PeopleSeeder::class,
            ActivitySeeder::class,
            ResourceSeeder::class,
        ]);

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
