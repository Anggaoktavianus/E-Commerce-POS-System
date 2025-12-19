<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PosService;
use App\Models\PosShift;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PosServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $posService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->posService = new PosService();
    }

    /** @test */
    public function it_can_create_transaction()
    {
        $outlet = Outlet::factory()->create();
        $user = User::factory()->create(['role' => 'cashier']);
        $product = Product::factory()->create(['price' => 50000]);

        $shift = PosShift::create([
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'shift_date' => today(),
            'shift_number' => 1,
            'opening_balance' => 100000,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $data = [
            'outlet_id' => $outlet->id,
            'shift_id' => $shift->id,
            'user_id' => $user->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 50000,
                    'total_amount' => 100000,
                ]
            ],
            'subtotal' => 100000,
            'total_amount' => 100000,
            'payment_method' => 'cash',
            'cash_received' => 100000,
            'change_amount' => 0,
        ];

        $transaction = $this->posService->createTransaction($data);

        $this->assertNotNull($transaction);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals(100000, $transaction->total_amount);
    }

    /** @test */
    public function it_throws_exception_when_shift_is_not_open()
    {
        $outlet = Outlet::factory()->create();
        $user = User::factory()->create(['role' => 'cashier']);

        $shift = PosShift::create([
            'outlet_id' => $outlet->id,
            'user_id' => $user->id,
            'shift_date' => today(),
            'shift_number' => 1,
            'opening_balance' => 100000,
            'status' => 'closed',
            'opened_at' => now(),
            'closed_at' => now(),
        ]);

        $data = [
            'outlet_id' => $outlet->id,
            'shift_id' => $shift->id,
            'user_id' => $user->id,
            'items' => [],
            'subtotal' => 0,
            'total_amount' => 0,
            'payment_method' => 'cash',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Shift is not open');

        $this->posService->createTransaction($data);
    }
}
