<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PosShift;
use App\Models\PosCashMovement;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PosCashMovementTest extends TestCase
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
    public function it_can_view_cash_movements_for_shift()
    {
        PosCashMovement::create([
            'shift_id' => $this->shift->id,
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->cashier->id,
            'type' => 'deposit',
            'amount' => 50000,
            'reason' => 'Additional cash'
        ]);

        $response = $this->actingAs($this->cashier)
            ->get(route('admin.pos.cash-movements.index', $this->shift->id));

        $response->assertStatus(200)
            ->assertViewIs('admin.pos.cash-movements.index')
            ->assertViewHas('shift')
            ->assertViewHas('cashMovements');
    }

    /** @test */
    public function it_can_create_deposit_cash_movement()
    {
        $data = [
            'type' => 'deposit',
            'amount' => 50000,
            'reason' => 'Additional cash deposit'
        ];

        $response = $this->actingAs($this->cashier)
            ->post(route('admin.pos.cash-movements.store', $this->shift->id), $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('pos_cash_movements', [
            'shift_id' => $this->shift->id,
            'type' => 'deposit',
            'amount' => 50000
        ]);
    }

    /** @test */
    public function it_can_create_withdrawal_cash_movement()
    {
        $data = [
            'type' => 'withdrawal',
            'amount' => 20000,
            'reason' => 'Petty cash'
        ];

        $response = $this->actingAs($this->cashier)
            ->post(route('admin.pos.cash-movements.store', $this->shift->id), $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('pos_cash_movements', [
            'shift_id' => $this->shift->id,
            'type' => 'withdrawal',
            'amount' => 20000
        ]);
    }

    /** @test */
    public function it_can_create_transfer_cash_movement()
    {
        $store = \App\Models\Store::first();
        $targetOutlet = Outlet::create([
            'store_id' => $store->id,
            'name' => 'Target Outlet',
            'address' => 'Target Address',
            'phone' => '081234567891',
            'is_active' => true
        ]);

        $data = [
            'type' => 'transfer',
            'amount' => 30000,
            'reason' => 'Transfer to other outlet',
            'reference_number' => 'TRF-001'
        ];

        $response = $this->actingAs($this->cashier)
            ->post(route('admin.pos.cash-movements.store', $this->shift->id), $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('pos_cash_movements', [
            'shift_id' => $this->shift->id,
            'type' => 'transfer',
            'amount' => 30000
        ]);
    }

    /** @test */
    public function it_validates_cash_movement_data()
    {
        $response = $this->actingAs($this->cashier)
            ->post(route('admin.pos.cash-movements.store', $this->shift->id), [
                'type' => 'invalid_type',
                'amount' => -1000 // Invalid: negative
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type', 'amount']);
    }

    /** @test */
    public function it_can_delete_cash_movement()
    {
        $movement = PosCashMovement::create([
            'shift_id' => $this->shift->id,
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->cashier->id,
            'type' => 'deposit',
            'amount' => 50000,
            'reason' => 'Test deposit'
        ]);

        $response = $this->actingAs($this->cashier)
            ->delete(route('admin.pos.cash-movements.destroy', [
                'shift_id' => $this->shift->id,
                'id' => $movement->id
            ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseMissing('pos_cash_movements', [
            'id' => $movement->id
        ]);
    }

    /** @test */
    public function it_prevents_cash_movement_on_closed_shift()
    {
        $this->shift->update([
            'status' => 'closed',
            'closed_at' => now()
        ]);

        $response = $this->actingAs($this->cashier)
            ->post(route('admin.pos.cash-movements.store', $this->shift->id), [
                'type' => 'deposit',
                'amount' => 50000,
                'reason' => 'Test'
            ]);

        $response->assertStatus(403);
    }
}
