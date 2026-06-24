<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $people = [
            ['name' => 'Carlos',    'surname' => 'García López',      'email' => 'carlos.garcia@example.com',    'phone' => '600111001'],
            ['name' => 'María',     'surname' => 'Martínez Ruiz',     'email' => 'maria.martinez@example.com',   'phone' => '600111002'],
            ['name' => 'Javier',    'surname' => 'Fernández Sánchez', 'email' => 'javier.fernandez@example.com', 'phone' => '600111003'],
            ['name' => 'Laura',     'surname' => 'González Pérez',    'email' => 'laura.gonzalez@example.com',   'phone' => '600111004'],
            ['name' => 'Antonio',   'surname' => 'Rodríguez Jiménez', 'email' => 'antonio.rodriguez@example.com','phone' => '600111005'],
            ['name' => 'Ana',       'surname' => 'López Moreno',      'email' => 'ana.lopez@example.com',        'phone' => '600111006'],
            ['name' => 'Miguel',    'surname' => 'Sánchez Díaz',      'email' => 'miguel.sanchez@example.com',   'phone' => '600111007'],
            ['name' => 'Elena',     'surname' => 'Díaz Álvarez',      'email' => 'elena.diaz@example.com',       'phone' => '600111008'],
            ['name' => 'Pablo',     'surname' => 'Pérez Romero',      'email' => 'pablo.perez@example.com',      'phone' => '600111009'],
            ['name' => 'Sofía',     'surname' => 'Álvarez Torres',    'email' => 'sofia.alvarez@example.com',    'phone' => '600111010'],
            ['name' => 'David',     'surname' => 'Torres Navarro',    'email' => 'david.torres@example.com',     'phone' => '600111011'],
            ['name' => 'Carmen',    'surname' => 'Navarro Domínguez', 'email' => 'carmen.navarro@example.com',   'phone' => '600111012'],
            ['name' => 'Sergio',    'surname' => 'Domínguez Vázquez', 'email' => 'sergio.dominguez@example.com', 'phone' => '600111013'],
            ['name' => 'Lucía',     'surname' => 'Vázquez Ramos',     'email' => 'lucia.vazquez@example.com',    'phone' => '600111014'],
            ['name' => 'Alejandro', 'surname' => 'Ramos Gil',         'email' => 'alejandro.ramos@example.com',  'phone' => '600111015'],
            ['name' => 'Isabel',    'surname' => 'Gil Serrano',       'email' => 'isabel.gil@example.com',       'phone' => '600111016'],
            ['name' => 'Roberto',   'surname' => 'Serrano Molina',    'email' => 'roberto.serrano@example.com',  'phone' => '600111017'],
            ['name' => 'Natalia',   'surname' => 'Molina Blanco',     'email' => 'natalia.molina@example.com',   'phone' => '600111018'],
            ['name' => 'Francisco', 'surname' => 'Blanco Herrera',    'email' => 'francisco.blanco@example.com', 'phone' => '600111019'],
            ['name' => 'Cristina',  'surname' => 'Herrera Castro',    'email' => 'cristina.herrera@example.com', 'phone' => '600111020'],
        ];

        foreach ($people as $person) {
            $person['client_portfolio_id'] = rand(0, 1) ? rand(1, 2) : null;
            Person::create($person);
        }
    }
}
