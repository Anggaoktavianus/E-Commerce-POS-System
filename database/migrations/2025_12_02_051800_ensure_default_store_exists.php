<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure a default store with id=1 exists as requested
        $exists = DB::table('stores')->where('id', 1)->exists();
        if (!$exists) {
            // Insert minimal required fields; ensure unique columns are unique
            DB::table('stores')->insert([
                'id' => 1,
                'name' => 'Default Store',
                'code' => 'default',
                'domain' => null,
                'owner_name' => 'Owner',
                'email' => 'default-store@example.com',
                'phone' => '0000000000',
                'address' => 'Default Address',
                'province' => 'N/A',
                'city' => 'N/A',
                'postal_code' => '00000',
                'tax_id' => null,
                'business_license' => null,
                'logo_url' => null,
                'theme' => 'default',
                'settings' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Do not delete the store on rollback to avoid cascading deletes; leave as is
    }
};
