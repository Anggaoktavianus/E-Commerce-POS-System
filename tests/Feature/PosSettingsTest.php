<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Outlet;
use App\Models\User;
use App\Models\PosSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PosSettingsTest extends TestCase
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
    public function it_can_view_pos_settings_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.pos.settings.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.pos.settings.index');
    }

    /** @test */
    public function it_can_view_specific_outlet_settings()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.pos.settings.show', $this->outlet->id));

        $response->assertStatus(200)
            ->assertViewIs('admin.pos.settings.show')
            ->assertViewHas('outlet')
            ->assertViewHas('defaultSettings');
    }

    /** @test */
    public function it_can_update_pos_settings()
    {
        $settings = [
            'tax_enabled' => true,
            'tax_rate' => 10,
            'discount_enabled' => true,
            'max_discount_percentage' => 50,
            'loyalty_points_enabled' => true,
            'loyalty_points_rate' => 1,
            'member_discount_enabled' => true,
            'member_discount_rate' => 5,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.pos.settings.update', $this->outlet->id), $settings);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        // Verify settings are saved
        $this->assertEquals(10, PosSetting::get($this->outlet->id, 'tax_rate'));
        $this->assertEquals(true, PosSetting::get($this->outlet->id, 'tax_enabled'));
    }

    /** @test */
    public function it_validates_settings_data()
    {
        $response = $this->actingAs($this->admin)
            ->put(route('admin.pos.settings.update', $this->outlet->id), [
                'tax_rate' => 150, // Invalid: > 100
                'max_discount_percentage' => -10 // Invalid: negative
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tax_rate', 'max_discount_percentage']);
    }

    /** @test */
    public function it_returns_default_settings_if_not_set()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.pos.settings.show', $this->outlet->id));

        $response->assertStatus(200);
        $defaultSettings = $response->viewData('defaultSettings');
        
        $this->assertIsArray($defaultSettings);
        $this->assertArrayHasKey('tax_rate', $defaultSettings);
        $this->assertArrayHasKey('discount_enabled', $defaultSettings);
    }
}
