<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Nurse;
use Illuminate\Support\Facades\Hash;


class NurseUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Admin Nurse',
            'email' => 'nurse@clinic.com',
            'password' => Hash::make('securepassword'),
            'role' => 'nurse',
        ]);

        Nurse::create([
            'user_id' => $user->id,
            'license_number' => 'RN-0001',
            'department' => 'General',
            'specialization' => 'General',
            'years_of_experience' => '8 years',
        ]);
    }
}