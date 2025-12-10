<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Logo & Branding
            [
                'key' => 'site_logo',
                'value' => 'fruitables/img/logo/logo-samsae.png',
                'type' => 'image',
                'description' => 'Website logo image'
            ],
            [
                'key' => 'site_name_logo',
                'value' => 'fruitables/img/logo/name-store-logo.png',
                'type' => 'image',
                'description' => 'Website name logo image'
            ],
            [
                'key' => 'site_name',
                'value' => 'Samsae Store',
                'type' => 'text',
                'description' => 'Website name for SEO and branding'
            ],
            [
                'key' => 'site_title',
                'value' => 'Samsae',
                'type' => 'text',
                'description' => 'Website title'
            ],
            [
                'key' => 'site_description',
                'value' => 'Samsae - Your trusted online store',
                'type' => 'textarea',
                'description' => 'Website description'
            ],
            
            // Brand Information
            [
                'key' => 'brand_name',
                'value' => 'Samsae Store',
                'type' => 'text',
                'description' => 'Brand name displayed on website'
            ],
            [
                'key' => 'brand_tagline',
                'value' => 'Oleh-Oleh',
                'type' => 'text',
                'description' => 'Brand tagline'
            ],
            
            // Contact Information
            [
                'key' => 'address',
                'value' => '1429 Netus Rd, NY 48247',
                'type' => 'text',
                'description' => 'Company address'
            ],
            [
                'key' => 'email',
                'value' => 'info@samsae.com',
                'type' => 'text',
                'description' => 'Company email'
            ],
            [
                'key' => 'phone',
                'value' => '+0123 4567 8910',
                'type' => 'text',
                'description' => 'Company phone number'
            ],
            [
                'key' => 'payment_image_path',
                'value' => 'fruitables/img/payment.png',
                'type' => 'image',
                'description' => 'Payment methods image'
            ],
            
            // Hero Section
            [
                'key' => 'hero_bg',
                'value' => 'fruitables/img/hero-img.jpg',
                'type' => 'image',
                'description' => 'Hero section background image'
            ],
            
            // Homepage Section Titles
            [
                'key' => 'homepage_products_title',
                'value' => 'Our Products',
                'type' => 'text',
                'description' => 'Homepage products section title'
            ],
            [
                'key' => 'homepage_products_subtitle',
                'value' => 'Browse our wide selection of fresh products',
                'type' => 'textarea',
                'description' => 'Homepage products section subtitle'
            ],
            [
                'key' => 'homepage_vegetables_title',
                'value' => 'Fresh Organic Vegetables',
                'type' => 'text',
                'description' => 'Homepage vegetables section title'
            ],
            [
                'key' => 'homepage_vegetables_subtitle',
                'value' => 'Premium quality organic vegetables',
                'type' => 'textarea',
                'description' => 'Homepage vegetables section subtitle'
            ],
            [
                'key' => 'homepage_bestseller_title',
                'value' => 'Bestseller Products',
                'type' => 'text',
                'description' => 'Homepage bestseller section title'
            ],
            [
                'key' => 'homepage_bestseller_subtitle',
                'value' => 'Most popular products chosen by our customers',
                'type' => 'textarea',
                'description' => 'Homepage bestseller section subtitle'
            ],
            
            // Testimonial Section
            [
                'key' => 'homepage_testimonial_title',
                'value' => 'Our Testimonial',
                'type' => 'text',
                'description' => 'Homepage testimonial section title'
            ],
            [
                'key' => 'homepage_testimonial_subtitle',
                'value' => 'Our Client Saying!',
                'type' => 'text',
                'description' => 'Homepage testimonial section subtitle'
            ],
            
            // Search & Subscribe
            [
                'key' => 'search_placeholder',
                'value' => 'Search for products...',
                'type' => 'text',
                'description' => 'Search input placeholder text'
            ],
            [
                'key' => 'search_button_text',
                'value' => 'Search',
                'type' => 'text',
                'description' => 'Search button text'
            ],
            [
                'key' => 'subscribe_placeholder',
                'value' => 'Your Email',
                'type' => 'text',
                'description' => 'Subscribe email placeholder'
            ],
            [
                'key' => 'subscribe_button_text',
                'value' => 'Subscribe Now',
                'type' => 'text',
                'description' => 'Subscribe button text'
            ],
            
            // Footer
            [
                'key' => 'footer_about_title',
                'value' => 'Why People Like us!',
                'type' => 'text',
                'description' => 'Footer about section title'
            ],
            [
                'key' => 'footer_about_text',
                'value' => 'typesetting, remaining essentially unchanged. It was popularised in the 1960s with the like Aldus PageMaker including of Lorem Ipsum.',
                'type' => 'textarea',
                'description' => 'Footer about section content'
            ],
            [
                'key' => 'footer_about_link_text',
                'value' => 'Read More',
                'type' => 'text',
                'description' => 'Footer about link text'
            ],
            [
                'key' => 'footer_about_link_url',
                'value' => '/about',
                'type' => 'text',
                'description' => 'Footer about link URL'
            ],
            
            // Copyright
            [
                'key' => 'copyright_text',
                'value' => 'Samsae Store',
                'type' => 'text',
                'description' => 'Copyright text'
            ],
            [
                'key' => 'copyright_year',
                'value' => '2024',
                'type' => 'text',
                'description' => 'Copyright year'
            ],
            [
                'key' => 'copyright_designer_text',
                'value' => 'Designed By HTML Codex',
                'type' => 'text',
                'description' => 'Designer credit text'
            ],
            [
                'key' => 'copyright_designer_url',
                'value' => 'https://htmlcodex.com',
                'type' => 'text',
                'description' => 'Designer credit URL'
            ],
            [
                'key' => 'copyright_distributor_text',
                'value' => 'Distributed By Samsaestore',
                'type' => 'text',
                'description' => 'Distributor credit text'
            ],
            [
                'key' => 'copyright_distributor_url',
                'value' => 'https://themewagon.com',
                'type' => 'text',
                'description' => 'Distributor credit URL'
            ],
            
            // Contact Section
            [
                'key' => 'contact_section_title',
                'value' => 'Contact',
                'type' => 'text',
                'description' => 'Contact section title'
            ],
            [
                'key' => 'contact_payment_title',
                'value' => 'Payment Accepted',
                'type' => 'text',
                'description' => 'Payment methods title'
            ],
            
            // Footer Menu Configuration
            [
                'key' => 'footer_menu_columns',
                'value' => '2',
                'type' => 'number',
                'description' => 'Number of footer menu columns to display'
            ],
            [
                'key' => 'footer_menu_show_all',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Show all footer menus (true) or limit to specific number (false)'
            ],
            
            // Product & E-commerce
            [
                'key' => 'currency_symbol',
                'value' => 'Rp.',
                'type' => 'text',
                'description' => 'Currency symbol for prices'
            ],
            [
                'key' => 'product_default_description',
                'value' => 'Fresh product from Samsae.',
                'type' => 'textarea',
                'description' => 'Default product description when none is provided'
            ],
            [
                'key' => 'product_badge_text',
                'value' => 'Product',
                'type' => 'text',
                'description' => 'Text displayed on product badges'
            ],
            [
                'key' => 'add_to_cart_text',
                'value' => 'Add to cart',
                'type' => 'text',
                'description' => 'Add to cart button text'
            ],
            [
                'key' => 'quantity_placeholder',
                'value' => 'Qty',
                'type' => 'text',
                'description' => 'Quantity input placeholder'
            ],
            
            // Product Detail Page
            [
                'key' => 'product_detail_title',
                'value' => 'Shop Detail',
                'type' => 'text',
                'description' => 'Product detail page title'
            ],
            [
                'key' => 'product_category_label',
                'value' => 'Category',
                'type' => 'text',
                'description' => 'Product category label'
            ],
            [
                'key' => 'product_description_tab',
                'value' => 'Description',
                'type' => 'text',
                'description' => 'Product description tab text'
            ],
            [
                'key' => 'product_reviews_tab',
                'value' => 'Reviews',
                'type' => 'text',
                'description' => 'Product reviews tab text'
            ],
            [
                'key' => 'price_label',
                'value' => 'Harga',
                'type' => 'text',
                'description' => 'Price label text'
            ],
            [
                'key' => 'unit_label',
                'value' => 'Unit',
                'type' => 'text',
                'description' => 'Unit label text'
            ],
            [
                'key' => 'stock_label',
                'value' => 'Stock',
                'type' => 'text',
                'description' => 'Stock label text'
            ],
            [
                'key' => 'size_label',
                'value' => 'Size',
                'type' => 'text',
                'description' => 'Size label text'
            ],
            [
                'key' => 'color_label',
                'value' => 'Color',
                'type' => 'text',
                'description' => 'Color label text'
            ],
            [
                'key' => 'weight_label',
                'value' => 'Weight',
                'type' => 'text',
                'description' => 'Weight label text'
            ],
            
            // Breadcrumb Navigation
            [
                'key' => 'breadcrumb_home_text',
                'value' => 'Home',
                'type' => 'text',
                'description' => 'Breadcrumb home text'
            ],
            [
                'key' => 'breadcrumb_pages_text',
                'value' => 'Pages',
                'type' => 'text',
                'description' => 'Breadcrumb pages text'
            ],
            [
                'key' => 'breadcrumb_shop_detail_text',
                'value' => 'Shop Detail',
                'type' => 'text',
                'description' => 'Breadcrumb shop detail text'
            ],
            [
                'key' => 'breadcrumb_contact_text',
                'value' => 'Contact',
                'type' => 'text',
                'description' => 'Breadcrumb contact text'
            ],
            
            // Contact Page
            [
                'key' => 'contact_page_title',
                'value' => 'Contact',
                'type' => 'text',
                'description' => 'Contact page title'
            ],
            [
                'key' => 'contact_form_title',
                'value' => 'Get in touch',
                'type' => 'text',
                'description' => 'Contact form title'
            ],
            [
                'key' => 'contact_form_description',
                'value' => 'The contact form is currently inactive. Get a functional and working contact form with Ajax & PHP in a few minutes. Just copy and paste the files, add a little code and you\'re done.',
                'type' => 'textarea',
                'description' => 'Contact form description'
            ],
            [
                'key' => 'contact_form_link_text',
                'value' => 'Download Now',
                'type' => 'text',
                'description' => 'Contact form link text'
            ],
            [
                'key' => 'contact_form_link_url',
                'value' => 'https://htmlcodex.com/contact-form',
                'type' => 'text',
                'description' => 'Contact form link URL'
            ],
            
            // Empty State Messages
            [
                'key' => 'no_products_found_text',
                'value' => 'No products found.',
                'type' => 'text',
                'description' => 'Message when no products are found'
            ],
            [
                'key' => 'no_category_products_text',
                'value' => 'No products in',
                'type' => 'text',
                'description' => 'Message when no products in category'
            ],
            
            // Contact Form Fields
            [
                'key' => 'contact_name_placeholder',
                'value' => 'Your Name',
                'type' => 'text',
                'description' => 'Contact form name placeholder'
            ],
            [
                'key' => 'contact_email_placeholder',
                'value' => 'Enter Your Email',
                'type' => 'text',
                'description' => 'Contact form email placeholder'
            ],
            [
                'key' => 'contact_message_placeholder',
                'value' => 'Your Message',
                'type' => 'text',
                'description' => 'Contact form message placeholder'
            ],
            [
                'key' => 'contact_submit_button',
                'value' => 'Submit',
                'type' => 'text',
                'description' => 'Contact form submit button text'
            ],
            [
                'key' => 'contact_address_title',
                'value' => 'Address',
                'type' => 'text',
                'description' => 'Contact page address title'
            ],
            [
                'key' => 'contact_email_title',
                'value' => 'Mail Us',
                'type' => 'text',
                'description' => 'Contact page email title'
            ],
            [
                'key' => 'contact_phone_title',
                'value' => 'Telephone',
                'type' => 'text',
                'description' => 'Contact page phone title'
            ],
            
            // Mitra Dashboard
            [
                'key' => 'mitra_dashboard_title',
                'value' => 'Dashboard Mitra',
                'type' => 'text',
                'description' => 'Mitra dashboard page title'
            ],
            [
                'key' => 'mitra_welcome_message',
                'value' => 'Selamat datang',
                'type' => 'text',
                'description' => 'Mitra dashboard welcome message'
            ],
            [
                'key' => 'mitra_dashboard_subtitle',
                'value' => 'Berikut ringkasan aktivitas akun mitra Anda.',
                'type' => 'text',
                'description' => 'Mitra dashboard subtitle'
            ],
            [
                'key' => 'mitra_account_status_title',
                'value' => 'Status Akun',
                'type' => 'text',
                'description' => 'Mitra account status title'
            ],
            [
                'key' => 'mitra_verified_text',
                'value' => 'Terverifikasi',
                'type' => 'text',
                'description' => 'Mitra verified status text'
            ],
            [
                'key' => 'mitra_verified_description',
                'value' => 'Akun Anda sudah diverifikasi admin. Anda bisa melakukan pemesanan grosir.',
                'type' => 'textarea',
                'description' => 'Mitra verified status description'
            ],
            [
                'key' => 'mitra_pending_text',
                'value' => 'Menunggu Verifikasi',
                'type' => 'text',
                'description' => 'Mitra pending verification text'
            ],
            [
                'key' => 'mitra_pending_description',
                'value' => 'Akun Anda sedang menunggu verifikasi admin sebelum dapat mengakses harga grosir.',
                'type' => 'textarea',
                'description' => 'Mitra pending verification description'
            ],
            [
                'key' => 'mitra_orders_title',
                'value' => 'Pesanan Besar Bulan Ini',
                'type' => 'text',
                'description' => 'Mitra orders section title'
            ],
            [
                'key' => 'mitra_orders_description',
                'value' => 'Integrasi dengan data pesanan akan ditambahkan kemudian.',
                'type' => 'text',
                'description' => 'Mitra orders description'
            ],
            [
                'key' => 'mitra_sales_target_title',
                'value' => 'Target Penjualan',
                'type' => 'text',
                'description' => 'Mitra sales target title'
            ],
            [
                'key' => 'mitra_target_label',
                'value' => 'Target bulan ini',
                'type' => 'text',
                'description' => 'Mitra target label'
            ],
            [
                'key' => 'mitra_no_target_set',
                'value' => 'Belum ditetapkan',
                'type' => 'text',
                'description' => 'Mitra no target set text'
            ],
            [
                'key' => 'mitra_target_description',
                'value' => 'Fitur kuota/target penjualan akan dihubungkan ke data penjualan di tahap berikutnya.',
                'type' => 'textarea',
                'description' => 'Mitra target description'
            ],
            [
                'key' => 'mitra_order_history_title',
                'value' => 'Riwayat Pemesanan',
                'type' => 'text',
                'description' => 'Mitra order history title'
            ],
            [
                'key' => 'mitra_order_history_description',
                'value' => 'Tabel riwayat pemesanan mitra akan ditampilkan di sini setelah modul pesanan selesai dibuat.',
                'type' => 'textarea',
                'description' => 'Mitra order history description'
            ],
            
            // Navigation
            [
                'key' => 'nav_home_text',
                'value' => 'Beranda',
                'type' => 'text',
                'description' => 'Navigation home text'
            ],
            [
                'key' => 'nav_admin_dashboard_text',
                'value' => 'Dashboard Admin',
                'type' => 'text',
                'description' => 'Navigation admin dashboard text'
            ],
            [
                'key' => 'nav_mitra_dashboard_text',
                'value' => 'Dashboard Mitra',
                'type' => 'text',
                'description' => 'Navigation mitra dashboard text'
            ],
            [
                'key' => 'nav_logout_text',
                'value' => 'Logout',
                'type' => 'text',
                'description' => 'Navigation logout text'
            ],
            [
                'key' => 'nav_login_text',
                'value' => 'Login',
                'type' => 'text',
                'description' => 'Navigation login text'
            ],
            [
                'key' => 'nav_register_text',
                'value' => 'Daftar',
                'type' => 'text',
                'description' => 'Navigation register text'
            ],
            [
                'key' => 'nav_mitra_register_text',
                'value' => 'Daftar Mitra',
                'type' => 'text',
                'description' => 'Navigation mitra register text'
            ]
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'description' => $setting['description']
                ]
            );
        }
    }
}
