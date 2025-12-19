<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\PosTransaction;
use App\Models\PosShift;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PosTransactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_generate_transaction_number()
    {
        $outlet = Outlet::factory()->create(['code' => 'OUT001']);
        
        $transactionNumber = PosTransaction::generateTransactionNumber($outlet->id);
        
        $this->assertStringStartsWith('POS-OUT001-', $transactionNumber);
        $this->assertStringContainsString(date('Ymd'), $transactionNumber);
    }

    /** @test */
    public function it_can_check_if_transaction_is_completed()
    {
        $outlet = Outlet::factory()->create();
        $user = User::factory()->create(['role' => 'cashier']);
        $shift = PosShift::factory()->create([
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'status' => 'open'
        ]);

        $transaction = PosTransaction::create([
            'transaction_number' => 'POS-TEST-001',
            'outlet_id' => $outlet->id,
            'shift_id' => $shift->id,
            'user_id' => $user->id,
            'total_amount' => 100000,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $this->assertTrue($transaction->isCompleted());
    }

    /** @test */
    public function it_can_check_if_transaction_can_be_cancelled()
    {
        $outlet = Outlet::factory()->create();
        $user = User::factory()->create(['role' => 'cashier']);
        $shift = PosShift::factory()->create([
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'status' => 'open'
        ]);

        $transaction = PosTransaction::create([
            'transaction_number' => 'POS-TEST-001',
            'outlet_id' => $outlet->id,
            'shift_id' => $shift->id,
            'user_id' => $user->id,
            'total_amount' => 100000,
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_at' => now(),
        ]);

        // Transaction can be cancelled if shift is open and less than 24 hours
        $this->assertTrue($transaction->canCancel());
    }
}
