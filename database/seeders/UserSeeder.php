<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        DB::table('users')->insert([
            [
                'name' => 'Ettiene',
                'surname' => 'Surname',
                'email' => 'smithettiene@yahoo.com',
                'password' => Hash::make('12345'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'GoldenBoy',
                'surname' => 'HasParking',
                'email' => 'goldenboy@yahoo.com',
                'password' => Hash::make('12345'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'BossesDaughter',
                'surname' => 'HasAstonMartin',
                'email' => 'bossesdaughter@yahoo.com',
                'password' => Hash::make('12345'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Titaniam',
                'surname' => 'Expert++',
                'email' => 'titanium@yahoo.com',
                'password' => Hash::make('12345'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'BTK',
                'surname' => 'Koningvandie',
                'email' => 'btk@yahoo.com',
                'password' => Hash::make('12345'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
