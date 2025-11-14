<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            'username' => 'admin',
            'email' => 'admin@cafejob.com',
            'password_hash' => Hash::make('admin123'),
            'role' => 'super_admin',
            'status' => 'active',
            'created_at' => now(),
        ]);
    }
}

