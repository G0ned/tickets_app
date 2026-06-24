<?php

namespace Database\Seeders;

use App\Models\Edition;
use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EditionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Edition::create([
        'event_id' => Event::find(1)->id,
        'date' => '2000-01-01 09:00:00',
        'duration' => 2,
        'location' => 'Far Far Away',
        'capacity' => 120,
        'status' => 1
        ]);

        Edition::create([
        'event_id' => Event::find(1)->id,
        'date' => '2000-01-01 11:00:00',
        'duration' => 2,
        'location' => 'Far Far Away',
        'capacity' => 120,
        'status' => 1
        ]);

        Edition::create([
        'event_id' => Event::find(2)->id,
        'date' => '2000-03-01 09:00:00',
        'duration' => 1.5,
        'location' => 'Knowhere',
        'capacity' => 80,
        'status' => 1
        ]);

        Edition::create([
        'event_id' => Event::find(2)->id,
        'date' => '2000-03-02 09:00:00',
        'duration' => 1.5,
        'location' => 'Knowhere',
        'capacity' => 80,
        'status' => 1
        ]);
    }
}
