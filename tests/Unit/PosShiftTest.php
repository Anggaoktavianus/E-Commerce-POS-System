<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\PosShift;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PosShiftTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create test data
    }

    /** @test */
    public function it_can_check_if_shift_is_open()
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

        $this->assertTrue($shift->isOpen());
        $this->assertEquals('open', $shift->status);
    }

    /** @test */
    public function it_can_calculate_expected_cash()
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

        // Expected cash should equal opening balance if no transactions
        $expectedCash = $shift->calculateExpectedCash();
        $this->assertEquals(100000, $expectedCash);
    }

    /** @test */
    public function it_can_check_if_shift_can_be_closed()
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

        // Shift without transactions cannot be closed
        $this->assertFalse($shift->canClose());
    }
}
