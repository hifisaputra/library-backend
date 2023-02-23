<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['student', 'staff'];

        foreach ($roles as $role) {
            User::firstOrCreate([
                'name' => $role,
                'email' => "$role@app.com",
                'password' => 'password'
            ]);
        }
    }
}
