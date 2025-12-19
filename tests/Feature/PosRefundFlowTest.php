<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PosShift;
use App\Models\PosTransaction;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Product;
use App\Models\OutletProductInventory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PosRefundFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_refund_transaction()
    {
        $outlet = Outlet::factory()->create();
        $manager = User::factory()->create(['role' => 'manager']);
        $product = Product::factory()->create(['price' => 50000]);

        $inventory = OutletProductInventory::create([
            'outlet_id' => $outlet->id,
            'product_id' => $product->id,
            'stock' => 10
        ]);

        $shift = PosShift::create([
            'outlet_id' => $outlet->id,
            'user_id' => $manager->id,
            'shift_date' => today(),
            'shift_number' => 1,
            'opening_balance' => 100000,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        // Create completed transaction
        $transaction = PosTransaction::create([
            'transaction_number' => 'POS-REFUND-001',
            'outlet_id' => $outlet->id,
            'shift_id' => $shift->id,
            'user_id' => $manager->id,
            'total_amount' => 100000,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        \App\Models\PosTransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 50000,
            'total_amount' => 100000,
        ]);

        // Decrease inventory
        $inventory->stock = 8;
        $inventory->save();

        $this->actingAs($manager);

        // Refund transaction
        $response = $this->postJson(route('admin.pos.transactions.refund', $transaction->id), [
            'reason' => 'Customer request',
            'refund_amount' => null // Full refund
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $transaction->refresh();
        $this->assertEquals('refunded', $transaction->status);
        $this->assertNotNull($transaction->cancelled_at);
    }

    /** @test */
    public function it_can_partial_refund_transaction()
    {
        $outlet = Outlet::factory()->create();
        $manager = User::factory()->create(['role' => 'manager']);

        $shift = PosShift::create([
            'outlet_id' => $outlet->id,
            'user_id' => $manager->id,
            'shift_date' => today(),
            'shift_number' => 1,
            'opening_balance' => 100000,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $transaction = PosTransaction::create([
            'transaction_number' => 'POS-REFUND-002',
            'outlet_id' => $outlet->id,
            'shift_id' => $shift->id,
            'user_id' => $manager->id,
            'total_amount' => 100000,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $this->actingAs($manager);

        // Partial refund
        $response = $this->postJson(route('admin.pos.transactions.refund', $transaction->id), [
            'reason' => 'Partial refund',
            'refund_amount' => 50000
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $transaction->refresh();
        $this->assertEquals('refunded', $transaction->status);
    }

    /** @test */
    public function it_prevents_refund_by_non_manager()
    {
        $outlet = Outlet::factory()->create();
        $cashier = User::factory()->create(['role' => 'cashier']);

        $shift = PosShift::create([
            'outlet_id' => $outlet->id,
            'user_id' => $cashier->id,
            'shift_date' => today(),
            'shift_number' => 1,
            'opening_balance' => 100000,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $transaction = PosTransaction::create([
            'transaction_number' => 'POS-REFUND-003',
            'outlet_id' => $outlet->id,
            'shift_id' => $shift->id,
            'user_id' => $cashier->id,
            'total_amount' => 100000,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $this->actingAs($cashier);

        // Try to refund (should fail - no permission)
        $response = $this->postJson(route('admin.pos.transactions.refund', $transaction->id), [
            'reason' => 'Test',
        ]);

        $response->assertStatus(403); // Forbidden
    }
}
