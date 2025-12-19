<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PosShift;
use App\Models\PosTransaction;
use App\Models\PosPayment;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\OutletProductInventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PosReportTest extends TestCase
{
    use RefreshDatabase;

    protected $outlet;
    protected $cashier;
    protected $shift;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create store first
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
    public function it_can_generate_daily_sales_report()
    {
        // Create transactions
        $transaction1 = PosTransaction::create([
            'shift_id' => $this->shift->id,
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->cashier->id,
            'transaction_number' => 'POS-001',
            'subtotal' => 100000,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 100000,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $transaction2 = PosTransaction::create([
            'shift_id' => $this->shift->id,
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->cashier->id,
            'transaction_number' => 'POS-002',
            'subtotal' => 50000,
            'discount_amount' => 5000,
            'tax_amount' => 0,
            'total_amount' => 45000,
            'payment_method' => 'card',
            'status' => 'completed',
        ]);

        $this->actingAs($this->cashier)
            ->get(route('admin.pos.reports.daily', [
                'outlet_id' => $this->outlet->id,
                'date' => today()->format('Y-m-d')
            ]))
            ->assertStatus(200)
            ->assertViewIs('admin.pos.reports.daily')
            ->assertViewHas('transactions')
            ->assertViewHas('summary');
    }

    /** @test */
    public function it_can_generate_product_sales_report()
    {
        $product = Product::create([
            'store_id' => 1,
            'name' => 'Test Product',
            'price' => 50000,
            'stock_qty' => 10,
            'is_active' => true
        ]);
        
        $transaction = PosTransaction::create([
            'shift_id' => $this->shift->id,
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->cashier->id,
            'transaction_number' => 'POS-001',
            'subtotal' => 100000,
            'total_amount' => 100000,
            'status' => 'completed',
        ]);

        DB::table('pos_transaction_items')->insert([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 50000,
            'total_amount' => 100000,
        ]);

        $this->actingAs($this->cashier)
            ->get(route('admin.pos.reports.product', [
                'outlet_id' => $this->outlet->id,
                'start_date' => today()->format('Y-m-d'),
                'end_date' => today()->format('Y-m-d')
            ]))
            ->assertStatus(200)
            ->assertViewIs('admin.pos.reports.product');
    }

    /** @test */
    public function it_can_generate_category_sales_report()
    {
        $store = \App\Models\Store::firstOrCreate([
            'name' => 'Test Store'
        ], [
            'address' => 'Test Store Address',
            'phone' => '081234567890',
            'is_active' => true
        ]);
        
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true
        ]);
        $product = Product::create([
            'store_id' => $store->id,
            'name' => 'Test Product',
            'price' => 50000,
            'stock_qty' => 10,
            'is_active' => true
        ]);
        $product->categories()->attach($category->id);

        $transaction = PosTransaction::create([
            'shift_id' => $this->shift->id,
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->cashier->id,
            'transaction_number' => 'POS-001',
            'subtotal' => 50000,
            'total_amount' => 50000,
            'status' => 'completed',
        ]);

        DB::table('pos_transaction_items')->insert([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 50000,
            'total_amount' => 50000,
        ]);

        $this->actingAs($this->cashier)
            ->get(route('admin.pos.reports.category', [
                'outlet_id' => $this->outlet->id,
                'start_date' => today()->format('Y-m-d'),
                'end_date' => today()->format('Y-m-d')
            ]))
            ->assertStatus(200)
            ->assertViewIs('admin.pos.reports.category');
    }

    /** @test */
    public function it_can_generate_payment_method_report()
    {
        PosTransaction::create([
            'shift_id' => $this->shift->id,
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->cashier->id,
            'transaction_number' => 'POS-001',
            'subtotal' => 100000,
            'total_amount' => 100000,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        PosTransaction::create([
            'shift_id' => $this->shift->id,
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->cashier->id,
            'transaction_number' => 'POS-002',
            'subtotal' => 50000,
            'total_amount' => 50000,
            'payment_method' => 'card',
            'status' => 'completed',
        ]);

        $this->actingAs($this->cashier)
            ->get(route('admin.pos.reports.payment', [
                'outlet_id' => $this->outlet->id,
                'start_date' => today()->format('Y-m-d'),
                'end_date' => today()->format('Y-m-d')
            ]))
            ->assertStatus(200)
            ->assertViewIs('admin.pos.reports.payment');
    }

    /** @test */
    public function it_can_generate_cashier_performance_report()
    {
        $cashier2 = User::create([
            'name' => 'Cashier 2',
            'email' => 'cashier2@test.com',
            'password' => bcrypt('password'),
            'role' => 'cashier'
        ]);

        PosTransaction::create([
            'shift_id' => $this->shift->id,
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->cashier->id,
            'transaction_number' => 'POS-001',
            'subtotal' => 100000,
            'total_amount' => 100000,
            'status' => 'completed',
        ]);

        $this->actingAs($this->cashier)
            ->get(route('admin.pos.reports.cashier', [
                'outlet_id' => $this->outlet->id,
                'start_date' => today()->format('Y-m-d'),
                'end_date' => today()->format('Y-m-d')
            ]))
            ->assertStatus(200)
            ->assertViewIs('admin.pos.reports.cashier');
    }

    /** @test */
    public function it_can_export_daily_report_to_csv()
    {
        PosTransaction::create([
            'shift_id' => $this->shift->id,
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->cashier->id,
            'transaction_number' => 'POS-001',
            'subtotal' => 100000,
            'total_amount' => 100000,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->cashier)
            ->get(route('admin.pos.reports.daily', [
                'outlet_id' => $this->outlet->id,
                'date' => today()->format('Y-m-d'),
                'export' => 'csv'
            ]));

        $response->assertStatus(200);
        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function it_can_export_daily_report_to_pdf()
    {
        PosTransaction::create([
            'shift_id' => $this->shift->id,
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->cashier->id,
            'transaction_number' => 'POS-001',
            'subtotal' => 100000,
            'total_amount' => 100000,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->cashier)
            ->get(route('admin.pos.reports.daily', [
                'outlet_id' => $this->outlet->id,
                'date' => today()->format('Y-m-d'),
                'export' => 'pdf'
            ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }
}
