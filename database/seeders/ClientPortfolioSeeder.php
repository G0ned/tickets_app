<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ClientPortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $adminId    = DB::table('users')->where('email', 'admin@example.test')->value('id');
        $notAdminId = DB::table('users')->where('email', 'notadmin@example.test')->value('id');

        DB::table('client_portfolio')->insert([
            ['name' => 'Portfolio Corporativo', 'user_id' => $adminId,    'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Portfolio VIP',         'user_id' => $notAdminId, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
