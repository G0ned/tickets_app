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
        User::create([
            'name' => 'Admin',
            'surname' => 'Admin',
            'email' => 'admin@example.test',
            'is_admin' => true,
            'is_supervisor' => true,
            'password' => bcrypt('admin1234') 
        ]);

        User::create([
            'name' => 'NotAdmin',
            'surname' => 'Not',
            'email' => 'notadmin@example.test',
            'is_admin' => false,
            'is_supervisor' => true,
            'password' => bcrypt('notadmin1234') 
        ]);
        
        //Seed the users table with some sample data
    }
}
