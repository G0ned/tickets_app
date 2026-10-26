<?php

namespace Tests\Feature;

use App\Models\ClientPortfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeletionPortfolioReassignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'John', 'surname' => 'Doe', 'email' => 'admin@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => true, 'is_supervisor' => false,
        ]);
        $this->manager = User::create([
            'name' => 'Mister', 'surname' => 'Manager', 'email' => 'manager@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => false, 'is_supervisor' => false,
        ]);
    }

    public function test_deleting_a_manager_reassigns_their_portfolios_to_the_acting_admin(): void
    {
        $portfolioA = ClientPortfolio::create(['name' => 'Portfolio A', 'user_id' => $this->manager->id]);
        $portfolioB = ClientPortfolio::create(['name' => 'Portfolio B', 'user_id' => $this->manager->id]);

        $this->actingAs($this->admin)
            ->delete(route('user-delete', $this->manager->id))
            ->assertRedirect(route('user-list'));

        $this->assertTrue(session()->has('success'));
        $this->assertStringContainsString('2', session('success'));

        $portfolioA->refresh();
        $portfolioB->refresh();
        $this->assertSame($this->admin->id, $portfolioA->user_id);
        $this->assertSame($this->admin->id, $portfolioB->user_id);

        $this->assertSoftDeleted('users', ['id' => $this->manager->id]);
    }

    public function test_deleting_a_manager_without_portfolios_does_not_mention_reassignment(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('user-delete', $this->manager->id))
            ->assertRedirect(route('user-list'));

        $this->assertStringNotContainsString('reasignad', session('success'));
    }

    public function test_portfolios_list_and_show_render_for_an_orphaned_portfolio(): void
    {
        // Simulate data that predates this fix: a portfolio still pointing at a
        // user_id whose row has since been soft-deleted directly (bypassing the
        // new reassignment logic in destroy()).
        $portfolio = ClientPortfolio::create(['name' => 'Orphan Portfolio', 'user_id' => $this->manager->id]);
        $this->manager->delete();

        $this->actingAs($this->admin)
            ->get(route('portfolios-list'))
            ->assertOk()
            ->assertSee('Sin gestor asignado');

        $this->actingAs($this->admin)
            ->get(route('portfolios-show', $portfolio->id))
            ->assertOk()
            ->assertSee('Sin gestor asignado');
    }
}
