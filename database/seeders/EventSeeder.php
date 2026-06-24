<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::create([
            'name' => 'EventTest',
            'description' => 'This is a sample event',
            'public' => false,
            'poster_path' => '/my_path_to/poster/1',
            'created_by' => DB::table('users')->first()->id,
        ]);

        Event::create([
            'name' => 'EventTest2',
            'description' => 'This is a sample text for event 2',
            'public' => false,
            'poster_path' => '/my_path_to/poster/2',
            'created_by' => DB::table('users')->first()->id,
        ]);
    }
}
