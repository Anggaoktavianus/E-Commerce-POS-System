<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavigationMenusPagesSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama (urutkan dari child ke parent agar aman FK)
        DB::table('navigation_links')->delete();
        DB::table('navigation_menus')->delete();
        DB::table('pages')->delete();

        // Seed pages baru (created_by = 4)
        $now = now();

        $aboutPageId = DB::table('pages')->insertGetId([
            'title' => 'Tentang Kami',
            'slug' => 'tentang-kami',
            'content' => '<h1>Tentang Samsae</h1><p>Informasi tentang toko Samsae.</p>',
            'featured_image' => null,
            'attachments' => null,
            'video_url' => null,
            'meta_title' => 'Tentang Samsae',
            'meta_description' => 'Informasi tentang toko Samsae.',
            'is_published' => true,
            'created_by' => 4,
            'created_at' => $now,
            'updated_at' => $now,
            'deleted_at' => null,
        ]);

        $contactPageId = DB::table('pages')->insertGetId([
            'title' => 'Kontak',
            'slug' => 'kontak',
            'content' => '<h1>Kontak</h1><p>Hubungi kami untuk informasi lebih lanjut.</p>',
            'featured_image' => null,
            'attachments' => null,
            'video_url' => null,
            'meta_title' => 'Kontak Samsae',
            'meta_description' => 'Halaman kontak Samsae.',
            'is_published' => true,
            'created_by' => 4,
            'created_at' => $now,
            'updated_at' => $now,
            'deleted_at' => null,
        ]);

        $faqPageId = DB::table('pages')->insertGetId([
            'title' => 'FAQ',
            'slug' => 'faq',
            'content' => '<h1>Pertanyaan yang Sering Diajukan</h1><p>Beberapa FAQ tentang layanan kami.</p>',
            'featured_image' => null,
            'attachments' => null,
            'video_url' => null,
            'meta_title' => 'FAQ Samsae',
            'meta_description' => 'Pertanyaan umum tentang Samsae.',
            'is_published' => true,
            'created_by' => 4,
            'created_at' => $now,
            'updated_at' => $now,
            'deleted_at' => null,
        ]);

        // Seed navigation_menus
        $headerMenuId = DB::table('navigation_menus')->insertGetId([
            'name' => 'Header',
            'location' => 'header',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $footerMenuId = DB::table('navigation_menus')->insertGetId([
            'name' => 'Footer Column 1',
            'location' => 'footer_column_1',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Seed navigation_links yang saling terhubung dengan pages, termasuk contoh sub-menu
        // Header menu - top level links
        $homeLinkId = DB::table('navigation_links')->insertGetId([
            'navigation_menu_id' => $headerMenuId,
            'parent_id' => null,
            'label' => 'Beranda',
            'url' => '/',
            'route_name' => null,
            'page_id' => null,
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $productsLinkId = DB::table('navigation_links')->insertGetId([
            'navigation_menu_id' => $headerMenuId,
            'parent_id' => null,
            'label' => 'Produk',
            'url' => null,
            'route_name' => 'shop',
            'page_id' => null,
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Parent "Tentang" dengan beberapa sub-menu di bawahnya
        $aboutParentId = DB::table('navigation_links')->insertGetId([
            'navigation_menu_id' => $headerMenuId,
            'parent_id' => null,
            'label' => 'Tentang',
            'url' => null,
            'route_name' => null,
            'page_id' => null,
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Child links di bawah "Tentang"
        DB::table('navigation_links')->insert([
            [
                'navigation_menu_id' => $headerMenuId,
                'parent_id' => $aboutParentId,
                'label' => 'Tentang Kami',
                'url' => null,
                'route_name' => null,
                'page_id' => $aboutPageId,
                'target' => '_self',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'navigation_menu_id' => $headerMenuId,
                'parent_id' => $aboutParentId,
                'label' => 'FAQ',
                'url' => null,
                'route_name' => null,
                'page_id' => $faqPageId,
                'target' => '_self',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Link kontak tetap sebagai top level
        DB::table('navigation_links')->insert([
            [
                'navigation_menu_id' => $headerMenuId,
                'parent_id' => null,
                'label' => 'Kontak',
                'url' => null,
                'route_name' => null,
                'page_id' => $contactPageId,
                'target' => '_self',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Footer menu (tanpa sub-menu, langsung ke pages)
        DB::table('navigation_links')->insert([
            [
                'navigation_menu_id' => $footerMenuId,
                'parent_id' => null,
                'label' => 'Tentang Samsae',
                'url' => null,
                'route_name' => null,
                'page_id' => $aboutPageId,
                'target' => '_self',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'navigation_menu_id' => $footerMenuId,
                'parent_id' => null,
                'label' => 'FAQ',
                'url' => null,
                'route_name' => null,
                'page_id' => $faqPageId,
                'target' => '_self',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
