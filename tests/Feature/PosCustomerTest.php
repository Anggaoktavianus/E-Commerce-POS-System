<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\LoyaltyPoint;
use App\Models\PosTransaction;
use App\Models\PosShift;
use App\Models\Outlet;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PosCustomerTest extends TestCase
{
    use RefreshDatabase;

    protected $outlet;
    protected $cashier;
    protected $shift;

    protected function setUp(): void
    {
        parent::setUp();
        
        $store = \App\Models\Store::create([
            'name' => 'Test Store',
            'address' => 'Test Store Address',
            'phone' => '081234567890',
            'is_active' => true
        ]);
        
        $this->outlet = Outlet::create([
            'store_id' => $store->id,
            'name' => 'Test Outlet',
            'address' => 'Test Address',
            'phone' => '081234567890',
            'is_active' => true
        ]);
        $this->cashier = User::create([
            'name' => 'Test Cashier',
            'email' => 'cashier@test.com',
            'password' => bcrypt('password'),
            'role' => 'cashier'
        ]);
        $this->shift = PosShift::create([
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->cashier->id,
            'shift_date' => today(),
            'shift_number' => 1,
            'opening_balance' => 100000,
            'status' => 'open',
            'opened_at' => now(),
        ]);
    }

    /** @test */
    public function it_can_search_customers()
    {
        $customer = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer'
        ]);

        $response = $this->actingAs($this->cashier)
            ->get(route('admin.pos.customers.search', [
                'query' => 'John'
            ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'email', 'phone']
                ]
            ]);
    }

    /** @test */
    public function it_can_get_customer_loyalty_balance()
    {
        $customer = User::create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer'
        ]);

        LoyaltyPoint::create([
            'user_id' => $customer->id,
            'points' => 1000,
            'type' => 'earned',
            'description' => 'Test points'
        ]);

        $response = $this->actingAs($this->cashier)
            ->get(route('admin.pos.customers.search', [
                'query' => $customer->email,
                'loyalty_check' => true
            ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'loyalty_balance']
                ]
            ]);
    }

    /** @test */
    public function it_can_create_new_customer()
    {
        $customerData = [
            'name' => 'New Customer',
            'email' => 'newcustomer@example.com',
            'phone' => '081234567890',
            'role' => 'customer'
        ];

        $response = $this->actingAs($this->cashier)
            ->post(route('admin.pos.customers.store'), $customerData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newcustomer@example.com',
            'role' => 'customer'
        ]);
    }

    /** @test */
    public function it_validates_customer_creation_data()
    {
        $response = $this->actingAs($this->cashier)
            ->post(route('admin.pos.customers.store'), [
                'name' => '', // Empty name
                'email' => 'invalid-email' // Invalid email
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    }

    /** @test */
    public function it_prevents_duplicate_customer_email()
    {
        $existingCustomer = User::create([
            'name' => 'Existing Customer',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer'
        ]);

        $response = $this->actingAs($this->cashier)
            ->post(route('admin.pos.customers.store'), [
                'name' => 'New Customer',
                'email' => 'existing@example.com',
                'phone' => '081234567890'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
