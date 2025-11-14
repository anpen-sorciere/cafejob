<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'demo_user',
            'email' => 'demo@example.com',
            'password' => Hash::make('demo123'),
            'first_name' => 'デモ',
            'last_name' => 'ユーザー',
            'status' => 'active',
        ]);
    }
}

