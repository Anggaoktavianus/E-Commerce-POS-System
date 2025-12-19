<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PosShift;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PosShiftFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_open_shift()
    {
        $outlet = Outlet::factory()->create();
        $user = User::factory()->create(['role' => 'cashier']);

        $this->actingAs($user);

        $response = $this->postJson(route('admin.pos.shifts.open'), [
            'outlet_id' => $outlet->id,
            'shift_number' => 1,
            'opening_balance' => 100000,
            'notes' => 'Test shift'
        ]);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('pos_shifts', [
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'shift_number' => 1,
            'opening_balance' => 100000,
            'status' => 'open'
        ]);
    }

    /** @test */
    public function it_cannot_open_shift_if_one_already_open()
    {
        $outlet = Outlet::factory()->create();
        $user = User::factory()->create(['role' => 'cashier']);

        // Open first shift
        PosShift::create([
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'shift_date' => today(),
            'shift_number' => 1,
            'opening_balance' => 100000,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $this->actingAs($user);

        // Try to open another shift
        $response = $this->postJson(route('admin.pos.shifts.open'), [
            'outlet_id' => $outlet->id,
            'shift_number' => 2,
            'opening_balance' => 100000,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function it_can_close_shift()
    {
        $outlet = Outlet::factory()->create();
        $user = User::factory()->create(['role' => 'cashier']);

        $shift = PosShift::create([
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'shift_date' => today(),
            'shift_number' => 1,
            'opening_balance' => 100000,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('admin.pos.shifts.close', $shift->id), [
            'actual_cash' => 150000,
            'notes' => 'Closing shift'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $shift->refresh();
        $this->assertEquals('closed', $shift->status);
        $this->assertEquals(150000, $shift->actual_cash);
        $this->assertNotNull($shift->closed_at);
    }

    /** @test */
    public function it_cannot_close_shift_without_transactions()
    {
        $outlet = Outlet::factory()->create();
        $user = User::factory()->create(['role' => 'cashier']);

        $shift = PosShift::create([
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'shift_date' => today(),
            'shift_number' => 1,
            'opening_balance' => 100000,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('admin.pos.shifts.close', $shift->id), [
            'actual_cash' => 100000,
        ]);

        // Should fail because canClose() requires transactions
        $response->assertStatus(400);
    }

    /** @test */
    public function it_calculates_expected_cash_correctly()
    {
        $outlet = Outlet::factory()->create();
        $user = User::factory()->create(['role' => 'cashier']);

        $shift = PosShift::create([
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'shift_date' => today(),
            'shift_number' => 1,
            'opening_balance' => 100000,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        // Create cash transactions
        \App\Models\PosTransaction::create([
            'transaction_number' => 'POS-001',
            'outlet_id' => $outlet->id,
            'shift_id' => $shift->id,
            'user_id' => $user->id,
            'total_amount' => 50000,
            'payment_method' => 'cash',
            'cash_received' => 50000,
            'status' => 'completed',
        ]);

        \App\Models\PosTransaction::create([
            'transaction_number' => 'POS-002',
            'outlet_id' => $outlet->id,
            'shift_id' => $shift->id,
            'user_id' => $user->id,
            'total_amount' => 30000,
            'payment_method' => 'cash',
            'cash_received' => 30000,
            'status' => 'completed',
        ]);

        $expectedCash = $shift->calculateExpectedCash();
        
        // Expected = opening_balance + cash transactions
        $this->assertEquals(180000, $expectedCash); // 100000 + 50000 + 30000
    }
}
