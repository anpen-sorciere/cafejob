<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenderMstSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genders = [
            ['id' => 1, 'gender' => '女性'],
            ['id' => 2, 'gender' => '男性'],
            ['id' => 3, 'gender' => 'その他'],
        ];

        foreach ($genders as $gender) {
            DB::table('gender_mst')->insert([
                'id' => $gender['id'],
                'gender' => $gender['gender'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
