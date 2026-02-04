<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'identification' => '12345678Z',
            'firstname' => 'admin',
            'surname' => 'admin',
            'email' => 'admin@mail.test',
            'password' => bcrypt('Administrador1'),
            'role' => 'admin',
        ]);

        DB::table('events')->insert([
            'name' => 'test',
            'date' => '2024-12-31 20:00:00',
            'user_id' => '12345678Z',
            'location' => 'Auditorio Principal',
            'is_active' => true,
            'capacity' => 120,
            'number_of_attendees' => 0,
        ]);
    }
}
