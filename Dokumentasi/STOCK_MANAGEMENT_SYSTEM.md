# Dokumentasi Sistem Manajemen Stok

## 📋 Daftar Isi
1. [Overview](#overview)
2. [Fitur yang Diimplementasikan](#fitur-yang-diimplementasikan)
3. [Database Schema](#database-schema)
4. [API Endpoints](#api-endpoints)
5. [Cara Penggunaan](#cara-penggunaan)
6. [Technical Details](#technical-details)
7. [Troubleshooting](#troubleshooting)

---

## Overview

Sistem Manajemen Stok adalah modul lengkap untuk mengelola stok produk dalam aplikasi e-commerce. Sistem ini mencakup tracking otomatis, history logging, manual adjustment, export laporan, dan real-time validation.

### Teknologi yang Digunakan
- **Framework**: Laravel 12
- **Database**: MySQL
- **Frontend**: Bootstrap 5, DataTables, jQuery
- **Backend**: PHP 8.2+

---

## Fitur yang Diimplementasikan

### ✅ Prioritas Tinggi (High Priority)

#### 1. Disable Add to Cart saat Stok Habis
- **Lokasi**: Semua halaman produk (shop-detail, shop, home, store theme)
- **Fitur**:
  - Button "Add to Cart" otomatis disabled saat stok = 0
  - Menampilkan pesan "Stok Habis"
  - Input quantity juga disabled

#### 2. Max Quantity Validation di Frontend
- **Lokasi**: Form add to cart di semua halaman produk
- **Fitur**:
  - Input quantity memiliki atribut `max` sesuai stok tersedia
  - Mencegah user input melebihi stok yang ada
  - Validasi real-time

#### 3. Stock Restore on Payment Failed/Expired
- **Lokasi**: `app/Services/MidtransService.php`, `app/Http/Controllers/OrderController.php`
- **Fitur**:
  - Otomatis restore stok saat order status: `failed`, `expired`, `cancelled`
  - Hanya restore jika stok sudah pernah dikurangi (`processed_at` tidak null)
  - Logging otomatis ke stock_movements

#### 4. Stock Display di Cart Page
- **Lokasi**: `resources/views/pages/cart.blade.php`
- **Fitur**:
  - Menampilkan stok tersedia per item
  - Badge warning untuk stok menipis (≤10) dan habis (≤0)
  - Max quantity di input update sesuai stok tersedia

---

### ✅ Prioritas Sedang (Medium Priority)

#### 5. Low Stock Alert untuk Admin
- **Lokasi**: `resources/views/admin/dashboard.blade.php`
- **Fitur**:
  - Alert warning di dashboard admin
  - Menampilkan jumlah produk dengan stok menipis (≤10)
  - Menampilkan jumlah produk habis
  - Tabel produk dengan stok menipis
  - Link langsung ke halaman produk

#### 6. Stock History/Logging
- **Lokasi**: `app/Models/StockMovement.php`, `app/Services/StockMovementService.php`
- **Fitur**:
  - Tabel `stock_movements` untuk tracking semua perubahan stok
  - 4 tipe perubahan: `in`, `out`, `adjustment`, `restore`
  - Tracking reference (order_id, order_number)
  - Tracking user yang melakukan perubahan
  - Catatan/keterangan untuk setiap perubahan

---

### ✅ Prioritas Rendah (Low Priority)

#### 7. Stock Adjustment Manual
- **Lokasi**: `app/Http/Controllers/Admin/ProductController.php`, `resources/views/admin/products/index.blade.php`
- **Fitur**:
  - Modal form untuk adjust stok
  - 3 tipe adjustment:
    - **Set Stok**: Tentukan jumlah stok langsung
    - **Tambah Stok**: Tambahkan stok
    - **Kurangi Stok**: Kurangi stok
  - Preview stok baru sebelum simpan
  - Validasi dan error handling
  - Logging otomatis ke stock_movements

#### 8. Export Stock Report
- **Lokasi**: `app/Http/Controllers/Admin/StockMovementController.php`
- **Fitur**:
  - Export riwayat perubahan stok (CSV)
  - Export ringkasan stok produk (CSV)
  - Filter by produk dan tipe perubahan
  - Format CSV dengan BOM untuk Excel UTF-8
  - Download langsung dari browser

#### 9. Real-time Stock Check
- **Lokasi**: `app/Http/Controllers/CartController.php`, `resources/views/pages/shop-detail.blade.php`
- **Fitur**:
  - Endpoint AJAX untuk check stock
  - Validasi sebelum submit form add to cart
  - Status indicator (tersedia/terbatas/habis)
  - Auto-check saat quantity berubah
  - Mencegah submit jika stok tidak cukup

---

## Database Schema

### Tabel: `stock_movements`

```sql
CREATE TABLE `stock_movements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `type` enum('in','out','adjustment','restore') NOT NULL COMMENT 'in: stock masuk, out: stock keluar, adjustment: manual adjustment, restore: restore dari order cancel',
  `quantity` int(11) NOT NULL COMMENT 'Jumlah perubahan stok (positif untuk in/adjustment/restore, negatif untuk out)',
  `old_stock` int(11) NOT NULL COMMENT 'Stok sebelum perubahan',
  `new_stock` int(11) NOT NULL COMMENT 'Stok setelah perubahan',
  `reference_type` varchar(255) DEFAULT NULL COMMENT 'Model class (Order, OrderItem, dll)',
  `reference_id` bigint(20) unsigned DEFAULT NULL COMMENT 'ID dari reference (order_id, dll)',
  `reference_number` varchar(255) DEFAULT NULL COMMENT 'Nomor referensi (order_number, dll)',
  `notes` text DEFAULT NULL COMMENT 'Catatan/keterangan perubahan stok',
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'User yang melakukan perubahan (untuk manual adjustment)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_product_id_created_at_index` (`product_id`,`created_at`),
  KEY `stock_movements_type_created_at_index` (`type`,`created_at`),
  KEY `stock_movements_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tabel: `products` (Field Terkait)

```sql
ALTER TABLE `products` ADD COLUMN `stock_qty` int(11) DEFAULT 0;
```

---

## API Endpoints

### Admin Endpoints

#### 1. Stock Movements Index
```
GET /admin/stock-movements
```
Menampilkan halaman daftar riwayat perubahan stok.

**Query Parameters:**
- `product_id` (optional): Filter by product ID
- `type` (optional): Filter by type (in, out, adjustment, restore)

#### 2. Stock Movements Data (DataTables)
```
GET /admin/stock-movements/data
```
Mengembalikan data JSON untuk DataTables.

**Query Parameters:**
- `product_id` (optional): Filter by product ID
- `type` (optional): Filter by type

#### 3. Stock History per Product
```
GET /admin/products/{product}/stock-history
```
Menampilkan riwayat stok untuk produk tertentu.

**Parameters:**
- `product`: Encoded product ID

#### 4. Export Stock History
```
GET /admin/stock-movements/export
```
Export riwayat perubahan stok ke CSV.

**Query Parameters:**
- `product_id` (optional): Filter by product ID
- `type` (optional): Filter by type
- `start_date` (optional): Filter by start date
- `end_date` (optional): Filter by end date

#### 5. Export Stock Summary
```
GET /admin/stock-movements/export-summary
```
Export ringkasan stok semua produk ke CSV.

#### 6. Adjust Stock
```
POST /admin/products/{product}/adjust-stock
```
Sesuaikan stok produk secara manual.

**Request Body:**
```json
{
  "adjustment_type": "set|increase|decrease",
  "quantity": 10,
  "notes": "Catatan penyesuaian"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Stok berhasil disesuaikan",
  "old_stock": 50,
  "new_stock": 60
}
```

### Frontend Endpoints

#### 7. Check Stock (AJAX)
```
GET /cart/check-stock
```
Cek ketersediaan stok untuk produk.

**Query Parameters:**
- `product_id`: Product ID
- `quantity`: Quantity yang diminta

**Response:**
```json
{
  "available": true,
  "stock": 100,
  "requested": 5,
  "current_cart_qty": 2,
  "max_can_add": 98,
  "message": "Stok tersedia",
  "is_out_of_stock": false,
  "is_low_stock": false
}
```

---

## Cara Penggunaan

### Untuk Admin

#### 1. Melihat Riwayat Perubahan Stok

1. Login ke admin panel
2. Navigasi ke **Manajemen Toko** → **Riwayat Stok**
3. Gunakan filter untuk mencari:
   - Filter by produk
   - Filter by tipe perubahan
4. Klik **Export Riwayat** untuk download CSV

#### 2. Melihat Riwayat Stok per Produk

1. Buka halaman **Produk**
2. Klik tombol **Riwayat Stok** (ikon history) pada produk yang diinginkan
3. Akan menampilkan semua perubahan stok untuk produk tersebut

#### 3. Menyesuaikan Stok Manual

1. Buka halaman **Produk**
2. Klik tombol **Sesuaikan Stok** (ikon adjust) pada produk yang diinginkan
3. Pilih tipe penyesuaian:
   - **Set Stok**: Tentukan jumlah stok langsung
   - **Tambah Stok**: Tambahkan stok
   - **Kurangi Stok**: Kurangi stok
4. Masukkan jumlah
5. (Opsional) Tambahkan catatan
6. Lihat preview stok baru
7. Klik **Simpan Perubahan**

#### 4. Export Laporan Stok

**Export Ringkasan Stok:**
1. Buka halaman **Riwayat Stok**
2. Klik **Export Ringkasan Stok**
3. File CSV akan terdownload dengan ringkasan semua produk

**Export Riwayat:**
1. Buka halaman **Riwayat Stok**
2. (Opsional) Gunakan filter untuk memfilter data
3. Klik **Export Riwayat**
4. File CSV akan terdownload dengan data yang sudah difilter

### Untuk Customer

#### 1. Menambahkan Produk ke Cart

1. Buka halaman produk
2. Sistem akan otomatis:
   - Menampilkan stok tersedia
   - Men-disable button jika stok habis
   - Membatasi input quantity sesuai stok
   - Menampilkan status stok (tersedia/terbatas/habis)
3. Pilih quantity
4. Klik **Add to Cart**

#### 2. Melihat Stok di Cart

1. Buka halaman **Keranjang**
2. Setiap item akan menampilkan:
   - Stok tersedia
   - Badge warning jika stok menipis/habis
   - Max quantity untuk update

---

## Technical Details

### Models

#### StockMovement Model
```php
namespace App\Models;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id', 'type', 'quantity', 'old_stock', 'new_stock',
        'reference_type', 'reference_id', 'reference_number',
        'notes', 'user_id'
    ];

    // Relationships
    public function product() { return $this->belongsTo(Product::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function reference() { return $this->morphTo('reference', 'reference_type', 'reference_id'); }
}
```

#### Product Model (Updates)
```php
// Added relationship
public function stockMovements()
{
    return $this->hasMany(StockMovement::class);
}

// Stock helpers
public function hasStock($quantity) { ... }
public function getRemainingStock($quantity) { ... }
```

### Services

#### StockMovementService
```php
namespace App\Services;

class StockMovementService
{
    // Log stock movement
    public static function log(...) { ... }
    
    // Log stock decrease
    public static function logDecrease(...) { ... }
    
    // Log stock increase
    public static function logIncrease(...) { ... }
    
    // Log stock restore
    public static function logRestore(...) { ... }
    
    // Log manual adjustment
    public static function logAdjustment(...) { ... }
}
```

### Controllers

#### StockMovementController
- `index()`: Menampilkan halaman daftar riwayat
- `data()`: Data untuk DataTables
- `show()`: Menampilkan riwayat per produk
- `export()`: Export riwayat ke CSV
- `exportSummary()`: Export ringkasan stok ke CSV

#### ProductController (Updates)
- `adjustStock()`: Handle stock adjustment manual

#### CartController (Updates)
- `checkStock()`: AJAX endpoint untuk check stock

### Jobs

#### ProcessOrderJob (Updates)
- Otomatis mengurangi stok saat order paid
- Logging ke stock_movements
- Mencegah double processing dengan `processed_at`

### Services

#### MidtransService (Updates)
- Restore stock saat payment failed/expired/cancelled
- Logging restore ke stock_movements
- Hanya restore jika stok sudah pernah dikurangi

---

## Troubleshooting

### Error: "Target class [StockMovementController] does not exist"
**Solusi**: Pastikan import statement ada di `routes/web.php`:
```php
use App\Http\Controllers\Admin\StockMovementController;
```

### Error: "DataTables warning: i18n file loading error"
**Solusi**: Sudah diperbaiki dengan menggunakan konfigurasi bahasa inline. Jika masih muncul, clear browser cache.

### Stok tidak berkurang setelah order paid
**Checklist**:
1. Pastikan `ProcessOrderJob` di-dispatch dari `MidtransService`
2. Pastikan order status adalah `paid`
3. Pastikan `processed_at` masih null (belum diproses)
4. Check log untuk error

### Stok tidak restore saat order cancelled
**Checklist**:
1. Pastikan order memiliki `processed_at` (stok sudah pernah dikurangi)
2. Pastikan order status adalah `failed`, `expired`, atau `cancelled`
3. Check log untuk error

### Export CSV tidak bisa dibuka di Excel
**Solusi**: File sudah menggunakan BOM UTF-8. Pastikan Excel mendukung UTF-8 atau buka dengan Google Sheets.

### Real-time stock check tidak bekerja
**Checklist**:
1. Pastikan JavaScript enabled
2. Check browser console untuk error
3. Pastikan route `cart.check_stock` terdaftar
4. Pastikan endpoint mengembalikan JSON response

---

## Changelog

### Version 1.0.0 (2025-12-11)
- ✅ Implementasi semua fitur prioritas tinggi
- ✅ Implementasi semua fitur prioritas sedang
- ✅ Implementasi semua fitur prioritas rendah
- ✅ Stock tracking dan history logging
- ✅ Stock adjustment manual
- ✅ Export laporan stok
- ✅ Real-time stock validation

---

## Support

Untuk pertanyaan atau masalah, silakan hubungi tim development atau buat issue di repository.

---

**Dokumentasi ini dibuat pada**: 2025-12-11  
**Versi Sistem**: 1.0.0  
**Framework**: Laravel 12
