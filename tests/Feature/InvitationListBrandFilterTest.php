<?php

namespace Tests\Feature;

use App\Enums\Type;
use App\Models\ClientPortfolio;
use App\Models\Edition;
use App\Models\Event;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationListBrandFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_brand_dropdown_lists_every_brand_in_the_database_not_only_the_managers_own(): void
    {
        $admin = User::create([
            'name' => 'John', 'surname' => 'Doe', 'email' => 'admin@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => true, 'is_supervisor' => false,
        ]);

        $manager = User::create([
            'name' => 'Mister', 'surname' => 'Manager', 'email' => 'manager@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => false, 'is_supervisor' => false,
        ]);
        $otherManager = User::create([
            'name' => 'Other', 'surname' => 'Manager', 'email' => 'other@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => false, 'is_supervisor' => false,
        ]);

        $event = Event::create([
            'name' => 'DemoName', 'description' => 'DemoDescription', 'public' => false,
            'poster_path' => 'path/to/poster.jpeg', 'created_by' => $admin->id,
            'updated_by' => null, 'deleted_by' => null,
        ]);
        $edition = Edition::create([
            'event_id' => $event->id, 'date' => now()->addDay(), 'location' => 'DemoPlace',
            'duration' => 2, 'capacity' => 50, 'status' => false,
        ]);
        $edition->managers()->attach($manager->id, [
            'is_supervisor' => false, 'is_doorman' => false, 'invitations_capacity' => null,
        ]);

        $portfolio = ClientPortfolio::create(['name' => 'My Portfolio', 'user_id' => $manager->id]);
        Person::create([
            'name' => 'Own', 'surname' => 'Client', 'email' => 'own@example.test',
            'phone' => '600000000', 'passport' => '22222222J', 'type' => Type::Client,
            'client_portfolio_id' => $portfolio->id, 'brand' => 'Nike',
        ]);

        // A client in a different manager's portfolio - never selectable by $manager,
        // but its brand should still show up as a filter option (brands are a global
        // vocabulary, not scoped to one manager's own clients).
        $otherPortfolio = ClientPortfolio::create(['name' => 'Other Portfolio', 'user_id' => $otherManager->id]);
        Person::create([
            'name' => 'Other', 'surname' => 'Client', 'email' => 'other-client@example.test',
            'phone' => '600000001', 'passport' => '11111111H', 'type' => Type::Client,
            'client_portfolio_id' => $otherPortfolio->id, 'brand' => 'Adidas',
        ]);

        $response = $this->actingAs($manager)
            ->get(route('edition-invitation-list', $edition->id));

        $response->assertOk();
        $response->assertSee('Nike');
        $response->assertSee('Adidas');
    }

    public function test_brand_dropdown_does_not_repeat_a_brand_shared_by_several_persons(): void
    {
        $admin = User::create([
            'name' => 'John', 'surname' => 'Doe', 'email' => 'admin@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => true, 'is_supervisor' => false,
        ]);
        $manager = User::create([
            'name' => 'Mister', 'surname' => 'Manager', 'email' => 'manager@example.test',
            'password' => bcrypt('secret1234'), 'is_admin' => false, 'is_supervisor' => false,
        ]);

        $event = Event::create([
            'name' => 'DemoName', 'description' => 'DemoDescription', 'public' => false,
            'poster_path' => 'path/to/poster.jpeg', 'created_by' => $admin->id,
            'updated_by' => null, 'deleted_by' => null,
        ]);
        $edition = Edition::create([
            'event_id' => $event->id, 'date' => now()->addDay(), 'location' => 'DemoPlace',
            'duration' => 2, 'capacity' => 50, 'status' => false,
        ]);
        $edition->managers()->attach($manager->id, [
            'is_supervisor' => false, 'is_doorman' => false, 'invitations_capacity' => null,
        ]);

        $portfolio = ClientPortfolio::create(['name' => 'My Portfolio', 'user_id' => $manager->id]);

        // Three different persons sharing the same brand: this is exactly the case
        // where a naive distinct() (over every column, id included) fails to collapse
        // the repeats, because `id` is already unique on every row.
        $passports = ['22222222J', '11111111H', '33333333P'];
        foreach ($passports as $i => $passport) {
            Person::create([
                'name' => "Client{$i}", 'surname' => 'Nike', 'email' => "client{$i}@example.test",
                'phone' => "60000000{$i}", 'passport' => $passport, 'type' => Type::Client,
                'client_portfolio_id' => $portfolio->id, 'brand' => 'Nike',
            ]);
        }

        $response = $this->actingAs($manager)
            ->get(route('edition-invitation-list', $edition->id));

        $response->assertOk();
        $this->assertSame(
            1,
            substr_count($response->getContent(), '<option value="Nike">Nike</option>'),
            'The "Nike" brand option should appear exactly once in the dropdown, not once per person.'
        );
    }
}
