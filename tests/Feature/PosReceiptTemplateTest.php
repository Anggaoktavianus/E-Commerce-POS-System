<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PosReceiptTemplate;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PosReceiptTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected $outlet;
    protected $admin;

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
        $this->admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
    }

    /** @test */
    public function it_can_view_receipt_templates_index()
    {
        PosReceiptTemplate::create([
            'outlet_id' => $this->outlet->id,
            'name' => 'Test Template',
            'template_content' => '<html><body>Test</body></html>',
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pos.receipt-templates.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.pos.receipt-templates.index')
            ->assertViewHas('templates');
    }

    /** @test */
    public function it_can_create_receipt_template()
    {
        $templateData = [
            'outlet_id' => $this->outlet->id,
            'name' => 'New Template',
            'template_content' => '<html><body>New Receipt Template</body></html>',
            'is_default' => true
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pos.receipt-templates.store'), $templateData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('pos_receipt_templates', [
            'outlet_id' => $this->outlet->id,
            'name' => 'New Template',
            'is_default' => true
        ]);
    }

    /** @test */
    public function it_can_update_receipt_template()
    {
        $template = PosReceiptTemplate::create([
            'outlet_id' => $this->outlet->id,
            'name' => 'Original Template',
            'template_content' => '<html><body>Original</body></html>',
            'is_active' => true
        ]);

        $updateData = [
            'outlet_id' => $this->outlet->id,
            'name' => 'Updated Template',
            'template_content' => '<html><body>Updated</body></html>',
            'is_default' => true
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.pos.receipt-templates.update', $template->id), $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('pos_receipt_templates', [
            'id' => $template->id,
            'name' => 'Updated Template'
        ]);
    }

    /** @test */
    public function it_can_preview_receipt_template()
    {
        $template = PosReceiptTemplate::create([
            'outlet_id' => $this->outlet->id,
            'name' => 'Preview Template',
            'template_content' => '<html><body>{{transaction_number}}</body></html>',
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pos.receipt-templates.preview', $template->id));

        $response->assertStatus(200)
            ->assertViewIs('admin.pos.receipt-templates.preview')
            ->assertViewHas('template')
            ->assertViewHas('sampleTransaction');
    }

    /** @test */
    public function it_can_delete_receipt_template()
    {
        $template = PosReceiptTemplate::create([
            'outlet_id' => $this->outlet->id,
            'name' => 'To Delete',
            'template_content' => '<html><body>Delete</body></html>',
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.pos.receipt-templates.destroy', $template->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseMissing('pos_receipt_templates', [
            'id' => $template->id
        ]);
    }

    /** @test */
    public function it_validates_template_creation_data()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.pos.receipt-templates.store'), [
                'name' => '', // Empty name
                'template_content' => '' // Empty content
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'template_content']);
    }

    /** @test */
    public function it_sets_only_one_default_template_per_outlet()
    {
        // Create first default template
        $template1 = PosReceiptTemplate::create([
            'outlet_id' => $this->outlet->id,
            'name' => 'Template 1',
            'template_content' => '<html><body>1</body></html>',
            'is_default' => true,
            'is_active' => true
        ]);

        // Create second template and set as default
        $template2 = PosReceiptTemplate::create([
            'outlet_id' => $this->outlet->id,
            'name' => 'Template 2',
            'template_content' => '<html><body>2</body></html>',
            'is_default' => false,
            'is_active' => true
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.pos.receipt-templates.update', $template2->id), [
                'outlet_id' => $this->outlet->id,
                'name' => 'Template 2',
                'template_content' => '<html><body>2</body></html>',
                'is_default' => true
            ]);

        // Template 1 should no longer be default
        $this->assertDatabaseHas('pos_receipt_templates', [
            'id' => $template1->id,
            'is_default' => false
        ]);

        // Template 2 should be default
        $this->assertDatabaseHas('pos_receipt_templates', [
            'id' => $template2->id,
            'is_default' => true
        ]);
    }
}
