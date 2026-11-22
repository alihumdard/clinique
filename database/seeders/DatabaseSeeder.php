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

        // One account per approved access level, so the three can be
        // compared during the review.
        User::updateOrCreate(
            ['email' => 'editeurclinique@gmail.com'],
            [
                'name' => 'Coordinateur de la Clinique',
                'password' => Hash::make('password'),
                'role' => User::ROLE_COORDINATEUR,
            ]
        );

        User::updateOrCreate(
            ['email' => 'membreclinique@gmail.com'],
            [
                'name' => 'Membre de la Clinique',
                'password' => Hash::make('password'),
                'role' => User::ROLE_MEMBRE,
            ]
        );
    }
}
