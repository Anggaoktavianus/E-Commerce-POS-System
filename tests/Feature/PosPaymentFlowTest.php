<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PosShift;
use App\Models\PosTransaction;
use App\Models\PosPayment;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Product;
use App\Models\OutletProductInventory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PosPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_process_cash_payment()
    {
        $outlet = Outlet::factory()->create();
        $user = User::factory()->create(['role' => 'cashier']);
        $product = Product::factory()->create(['price' => 50000]);

        OutletProductInventory::create([
            'outlet_id' => $outlet->id,
            'product_id' => $product->id,
            'stock' => 10
        ]);

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

        $response = $this->postJson(route('admin.pos.transactions.store'), [
            'outlet_id' => $outlet->id,
            'shift_id' => $shift->id,
            'user_id' => $user->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price' => 50000,
                    'discount_amount' => 0,
                    'total_amount' => 50000,
                ]
            ],
            'subtotal' => 50000,
            'total_amount' => 50000,
            'payment_method' => 'cash',
            'cash_received' => 100000,
            'change_amount' => 50000,
        ]);

        $response->assertStatus(201);
        
        $transaction = PosTransaction::where('transaction_number', $response->json('data.transaction_number'))->first();
        $this->assertEquals('cash', $transaction->payment_method);
        $this->assertEquals(100000, $transaction->cash_received);
        $this->assertEquals(50000, $transaction->change_amount);
    }

    /** @test */
    public function it_can_process_split_payment()
    {
        $outlet = Outlet::factory()->create();
        $user = User::factory()->create(['role' => 'cashier']);
        $product = Product::factory()->create(['price' => 100000]);

        OutletProductInventory::create([
            'outlet_id' => $outlet->id,
            'product_id' => $product->id,
            'stock' => 10
        ]);

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

        $response = $this->postJson(route('admin.pos.transactions.store'), [
            'outlet_id' => $outlet->id,
            'shift_id' => $shift->id,
            'user_id' => $user->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price' => 100000,
                    'discount_amount' => 0,
                    'total_amount' => 100000,
                ]
            ],
            'subtotal' => 100000,
            'total_amount' => 100000,
            'payment_method' => 'split',
            'payments' => [
                [
                    'method' => 'cash',
                    'amount' => 50000,
                ],
                [
                    'method' => 'card',
                    'amount' => 50000,
                    'reference_number' => 'CARD-123',
                ]
            ],
        ]);

        $response->assertStatus(201);
        
        $transaction = PosTransaction::where('transaction_number', $response->json('data.transaction_number'))->first();
        $this->assertEquals('split', $transaction->payment_method);
        
        // Check split payments created
        $payments = PosPayment::where('transaction_id', $transaction->id)->get();
        $this->assertCount(2, $payments);
        $this->assertEquals(50000, $payments->where('payment_method', 'cash')->first()->amount);
        $this->assertEquals(50000, $payments->where('payment_method', 'card')->first()->amount);
    }

    /** @test */
    public function it_can_process_card_payment()
    {
        $outlet = Outlet::factory()->create();
        $user = User::factory()->create(['role' => 'cashier']);
        $product = Product::factory()->create(['price' => 50000]);

        OutletProductInventory::create([
            'outlet_id' => $outlet->id,
            'product_id' => $product->id,
            'stock' => 10
        ]);

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

        $response = $this->postJson(route('admin.pos.transactions.store'), [
            'outlet_id' => $outlet->id,
            'shift_id' => $shift->id,
            'user_id' => $user->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price' => 50000,
                    'discount_amount' => 0,
                    'total_amount' => 50000,
                ]
            ],
            'subtotal' => 50000,
            'total_amount' => 50000,
            'payment_method' => 'card',
            'payment_details' => ['reference_number' => 'CARD-456'],
        ]);

        $response->assertStatus(201);
        
        $transaction = PosTransaction::where('transaction_number', $response->json('data.transaction_number'))->first();
        $this->assertEquals('card', $transaction->payment_method);
        $this->assertNotNull($transaction->payment_details);
    }
}
