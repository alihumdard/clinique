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
            ['email' => 'admin@clinique-fsjp.ne'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('ClinicAdmin2025!'),
            ]
        );
    }
}
