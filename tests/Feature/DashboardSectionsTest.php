<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_plain_manager_sees_only_gestion_and_mi_cuenta(): void
    {
        $manager = User::create([
            'name' => 'Mister', 'surname' => 'Manager', 'email' => 'manager@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => false, 'is_supervisor' => false,
        ]);

        $response = $this->actingAs($manager)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Gestión');
        $response->assertSee('Mi cuenta');
        $response->assertDontSee('Administración');
        $response->assertDontSee('Supervisión');
    }

    public function test_admin_sees_the_administracion_section(): void
    {
        $admin = User::create([
            'name' => 'John', 'surname' => 'Doe', 'email' => 'admin@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => true, 'is_supervisor' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Administración')
            ->assertSee('Usuarios y roles')
            ->assertSee('Todas las carteras');
    }

    public function test_supervisor_sees_the_supervision_section(): void
    {
        $supervisor = User::create([
            'name' => 'Super', 'surname' => 'Visor', 'email' => 'supervisor@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => false, 'is_supervisor' => true,
        ]);

        $this->actingAs($supervisor)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Supervisión');
    }
}
