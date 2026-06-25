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
            ['name' => 'Carlos',    'surname' => 'García López',      'email' => 'carlos.garcia@example.com',    'phone' => '600111001', 'passport' => '12345678A'],
            ['name' => 'María',     'surname' => 'Martínez Ruiz',     'email' => 'maria.martinez@example.com',   'phone' => '600111002', 'passport' => '23456789B'],
            ['name' => 'Javier',    'surname' => 'Fernández Sánchez', 'email' => 'javier.fernandez@example.com', 'phone' => '600111003', 'passport' => '34567890C'],
            ['name' => 'Laura',     'surname' => 'González Pérez',    'email' => 'laura.gonzalez@example.com',   'phone' => '600111004', 'passport' => '45678901D'],
            ['name' => 'Antonio',   'surname' => 'Rodríguez Jiménez', 'email' => 'antonio.rodriguez@example.com','phone' => '600111005', 'passport' => '56789012E'],
            ['name' => 'Ana',       'surname' => 'López Moreno',      'email' => 'ana.lopez@example.com',        'phone' => '600111006', 'passport' => '67890123F'],
            ['name' => 'Miguel',    'surname' => 'Sánchez Díaz',      'email' => 'miguel.sanchez@example.com',   'phone' => '600111007', 'passport' => '78901234G'],
            ['name' => 'Elena',     'surname' => 'Díaz Álvarez',      'email' => 'elena.diaz@example.com',       'phone' => '600111008', 'passport' => '89012345H'],
            ['name' => 'Pablo',     'surname' => 'Pérez Romero',      'email' => 'pablo.perez@example.com',      'phone' => '600111009', 'passport' => '90123456J'],
            ['name' => 'Sofía',     'surname' => 'Álvarez Torres',    'email' => 'sofia.alvarez@example.com',    'phone' => '600111010', 'passport' => '01234567K'],
            ['name' => 'David',     'surname' => 'Torres Navarro',    'email' => 'david.torres@example.com',     'phone' => '600111011', 'passport' => '11234567L'],
            ['name' => 'Carmen',    'surname' => 'Navarro Domínguez', 'email' => 'carmen.navarro@example.com',   'phone' => '600111012', 'passport' => '21234567M'],
            ['name' => 'Sergio',    'surname' => 'Domínguez Vázquez', 'email' => 'sergio.dominguez@example.com', 'phone' => '600111013', 'passport' => '31234567N'],
            ['name' => 'Lucía',     'surname' => 'Vázquez Ramos',     'email' => 'lucia.vazquez@example.com',    'phone' => '600111014', 'passport' => '41234567P'],
            ['name' => 'Alejandro', 'surname' => 'Ramos Gil',         'email' => 'alejandro.ramos@example.com',  'phone' => '600111015', 'passport' => '51234567Q'],
            ['name' => 'Isabel',    'surname' => 'Gil Serrano',       'email' => 'isabel.gil@example.com',       'phone' => '600111016', 'passport' => '61234567R'],
            ['name' => 'Roberto',   'surname' => 'Serrano Molina',    'email' => 'roberto.serrano@example.com',  'phone' => '600111017', 'passport' => '71234567S'],
            ['name' => 'Natalia',   'surname' => 'Molina Blanco',     'email' => 'natalia.molina@example.com',   'phone' => '600111018', 'passport' => '81234567T'],
            ['name' => 'Francisco', 'surname' => 'Blanco Herrera',    'email' => 'francisco.blanco@example.com', 'phone' => '600111019', 'passport' => '91234567V'],
            ['name' => 'Cristina',  'surname' => 'Herrera Castro',    'email' => 'cristina.herrera@example.com', 'phone' => '600111020', 'passport' => '01234568W'],
        ];

        foreach ($people as $person) {
            $person['client_portfolio_id'] = rand(0, 1) ? rand(1, 2) : null;
            Person::create($person);
        }
    }
}
