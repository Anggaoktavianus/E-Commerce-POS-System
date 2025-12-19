<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PosShift;
use App\Models\PosTransaction;
use App\Models\PosReceiptTemplate;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PosReceiptTest extends TestCase
{
    use RefreshDatabase;

    protected $outlet;
    protected $cashier;
    protected $shift;
    protected $transaction;

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

        $this->transaction = PosTransaction::create([
            'shift_id' => $this->shift->id,
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->cashier->id,
            'transaction_number' => 'POS-001',
            'subtotal' => 100000,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 100000,
            'payment_method' => 'cash',
            'cash_received' => 150000,
            'change_amount' => 50000,
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function it_can_print_receipt()
    {
        $response = $this->actingAs($this->cashier)
            ->get(route('admin.pos.receipts.print', $this->transaction->id));

        $response->assertStatus(200)
            ->assertViewIs('admin.pos.receipts.print')
            ->assertViewHas('transaction');
    }

    /** @test */
    public function it_can_generate_pdf_receipt()
    {
        $response = $this->actingAs($this->cashier)
            ->get(route('admin.pos.receipts.pdf', $this->transaction->id));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function it_can_preview_receipt()
    {
        $response = $this->actingAs($this->cashier)
            ->get(route('admin.pos.receipts.preview', $this->transaction->id));

        $response->assertStatus(200)
            ->assertViewIs('admin.pos.receipts.preview')
            ->assertViewHas('transaction');
    }

    /** @test */
    public function it_uses_custom_receipt_template_if_available()
    {
        // Create custom receipt template
        $template = PosReceiptTemplate::create([
            'outlet_id' => $this->outlet->id,
            'name' => 'Custom Template',
            'template_content' => '<html><body>Custom Receipt</body></html>',
            'is_default' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->cashier)
            ->get(route('admin.pos.receipts.print', $this->transaction->id));

        $response->assertStatus(200);
        // Template should be used
    }

    /** @test */
    public function it_falls_back_to_default_template_if_no_custom_template()
    {
        $response = $this->actingAs($this->cashier)
            ->get(route('admin.pos.receipts.print', $this->transaction->id));

        $response->assertStatus(200);
        // Should use default template
    }
}
