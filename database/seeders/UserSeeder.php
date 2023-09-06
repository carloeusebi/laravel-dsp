<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = ['email' => 'admin', 'password' => password_hash('admin', PASSWORD_BCRYPT, ['cost' => 12])];
        User::create($user);
    }
}
