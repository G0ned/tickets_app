<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert(
            [  
                'identification' => '12345678Z',
                'firstname' => 'admin',
                'surname' => 'admin',
                'email' => 'admin@test.com',
                'email_verified_at' => NULL,
                'password' => bcrypt('Admin1'),
                'role' => 'admin',
                'remember_token' => NULL
            ]);
    }
}
