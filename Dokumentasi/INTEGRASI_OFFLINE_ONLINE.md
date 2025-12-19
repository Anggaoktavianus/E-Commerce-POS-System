# INTEGRASI PENJUALAN OFFLINE (POS) DAN ONLINE (ORDER)

## 📋 DAFTAR ISI
1. [Overview Integrasi](#overview-integrasi)
2. [Arsitektur Integrasi](#arsitektur-integrasi)
3. [Integrasi Data yang Sudah Ada](#integrasi-data-yang-sudah-ada)
4. [Unified Reporting](#unified-reporting)
5. [Perbedaan dan Persamaan](#perbedaan-dan-persamaan)
6. [Skenario Penggunaan](#skenario-penggunaan)
7. [Implementasi yang Diperlukan](#implementasi-yang-diperlukan)

---

## OVERVIEW INTEGRASI

Sistem ini mendukung **dua channel penjualan** yang terintegrasi:

### 1. **Penjualan Online (E-Commerce)**
- **Tabel:** `orders` dan `order_items`
- **Karakteristik:**
  - Customer order via website/mobile app
  - Payment via Midtrans (online payment gateway)
  - Shipping ke alamat customer
  - Status: `pending` → `paid` → `processing` → `shipped` → `completed`
  - Order number: `ORD-{DATE}-{SEQ}`

### 2. **Penjualan Offline (POS)**
- **Tabel:** `pos_transactions` dan `pos_transaction_items`
- **Karakteristik:**
  - Customer datang langsung ke outlet/store
  - Payment langsung (Cash, Card, E-Wallet, QRIS)
  - Tidak ada shipping (walk-in)
  - Status: `completed` (langsung selesai)
  - Transaction number: `POS-{OUTLET}-{DATE}-{SEQ}`

---

## ARSITEKTUR INTEGRASI

### Data yang Dibagikan (Shared Resources)

```
┌─────────────────────────────────────────────────────────┐
│              SHARED RESOURCES                            │
├─────────────────────────────────────────────────────────┤
│                                                           │
│  ┌──────────────┐    ┌──────────────┐    ┌────────────┐ │
│  │   Products   │    │  Customers   │    │  Inventory │ │
│  │              │    │  (Users)     │    │  (Outlet)  │ │
│  └──────────────┘    └──────────────┘    └────────────┘ │
│         │                    │                   │        │
│         └────────────────────┴───────────────────┘        │
│                            │                              │
│         ┌──────────────────┴──────────────────┐          │
│         │                                      │          │
│  ┌──────▼──────┐                    ┌─────────▼──────┐  │
│  │   ORDERS    │                    │ POS TRANSACTIONS│  │
│  │  (Online)   │                    │    (Offline)   │  │
│  └─────────────┘                    └─────────────────┘  │
│                                                           │
└─────────────────────────────────────────────────────────┘
```

### Integrasi yang Sudah Ada

1. ✅ **Product Management** - Produk yang sama digunakan untuk online dan offline
2. ✅ **Customer Management** - Database customer terpusat (table `users`)
3. ✅ **Inventory Management** - Stok per outlet (`outlet_product_inventories`)
4. ✅ **Stock Movement** - Tracking pergerakan stok dari kedua channel
5. ✅ **Loyalty Points** - Poin reward dari kedua channel
6. ✅ **Coupon System** - Voucher bisa digunakan di kedua channel

---

## INTEGRASI DATA YANG SUDAH ADA

### 1. **Product Management**

**Sumber Data:** `products` table

**Digunakan oleh:**
- ✅ Online Orders (`order_items.product_id`)
- ✅ POS Transactions (`pos_transaction_items.product_id`)

**Integrasi:**
- Produk yang sama bisa dijual online dan offline
- Harga bisa berbeda per outlet (via `outlet_product_inventories.price`)
- Stok terpisah per outlet (via `outlet_product_inventories.stock`)

### 2. **Customer Management**

**Sumber Data:** `users` table (dengan `role = 'customer'`)

**Digunakan oleh:**
- ✅ Online Orders (`orders.user_id`)
- ✅ POS Transactions (`pos_transactions.customer_id`)

**Integrasi:**
- Customer yang sama bisa belanja online dan offline
- History transaksi terpisah (orders vs pos_transactions)
- Loyalty points terpusat (dari kedua channel)

### 3. **Inventory Management**

**Sumber Data:** `outlet_product_inventories` table

**Digunakan oleh:**
- ✅ Online Orders - Stok dikurangi saat order `processing`
- ✅ POS Transactions - Stok dikurangi saat transaksi `completed`

**Integrasi:**
- Stok per outlet dikurangi dari kedua channel
- Stock movement tracking untuk audit trail
- Global stock (`products.stock_qty`) bisa di-sync dari outlet inventories

### 4. **Loyalty Points**

**Sumber Data:** `loyalty_points` table

**Digunakan oleh:**
- ✅ Online Orders - Poin diberikan saat order `completed`
- ✅ POS Transactions - Poin diberikan saat transaksi `completed`

**Integrasi:**
- Poin dari kedua channel diakumulasi di balance customer
- Poin bisa digunakan untuk redeem di kedua channel
- History poin terpusat

### 5. **Coupon System**

**Sumber Data:** `coupons` dan `user_coupons` tables

**Digunakan oleh:**
- ✅ Online Orders - Kupon bisa digunakan saat checkout
- ✅ POS Transactions - Kupon bisa digunakan saat transaksi

**Integrasi:**
- Kupon yang sama bisa digunakan di kedua channel
- Usage limit terpusat (per user dan global)
- Validation logic sama untuk kedua channel

---

## UNIFIED REPORTING

### Status Saat Ini

**✅ Sudah Diimplementasikan:**
- POS Reports (hanya POS transactions)
  - Daily Sales Report
  - Product Sales Report
  - Category Sales Report
  - Payment Method Report
  - Cashier Performance Report

**⚠️ Belum Diimplementasikan:**
- Unified Sales Report (gabungan online + POS)
- Unified Product Sales Report
- Unified Category Sales Report
- Comparison Report (online vs offline)

### Implementasi yang Diperlukan

#### 1. **Unified Sales Report Service**

**File:** `app/Services/UnifiedSalesReportService.php` (NEW)

```php
<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use Illuminate\Support\Facades\DB;

class UnifiedSalesReportService
{
    /**
     * Get total sales (online + POS)
     */
    public static function getTotalSales($dateFrom, $dateTo, $outletId = null)
    {
        // Online sales
        $onlineQuery = Order::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $onlineQuery->where('outlet_id', $outletId);
        }

        $onlineSales = $onlineQuery->sum('total_amount');
        $onlineCount = $onlineQuery->count();

        // POS sales
        $posQuery = PosTransaction::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $posQuery->where('outlet_id', $outletId);
        }

        $posSales = $posQuery->sum('total_amount');
        $posCount = $posQuery->count();

        return [
            'online' => [
                'total' => $onlineSales,
                'count' => $onlineCount,
                'percentage' => $onlineSales + $posSales > 0 
                    ? ($onlineSales / ($onlineSales + $posSales)) * 100 
                    : 0
            ],
            'pos' => [
                'total' => $posSales,
                'count' => $posCount,
                'percentage' => $onlineSales + $posSales > 0 
                    ? ($posSales / ($onlineSales + $posSales)) * 100 
                    : 0
            ],
            'total' => $onlineSales + $posSales,
            'total_count' => $onlineCount + $posCount
        ];
    }

    /**
     * Get sales by product (online + POS)
     */
    public static function getProductSales($dateFrom, $dateTo, $outletId = null)
    {
        // Online sales
        $onlineItems = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $onlineItems->where('orders.outlet_id', $outletId);
        }

        $onlineSales = $onlineItems
            ->select(
                'order_items.product_id',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.total) as total'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('order_items.product_id')
            ->get()
            ->keyBy('product_id');

        // POS sales
        $posItems = PosTransactionItem::join('pos_transactions', 'pos_transaction_items.transaction_id', '=', 'pos_transactions.id')
            ->where('pos_transactions.status', 'completed')
            ->whereBetween('pos_transactions.created_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $posItems->where('pos_transactions.outlet_id', $outletId);
        }

        $posSales = $posItems
            ->select(
                'pos_transaction_items.product_id',
                DB::raw('SUM(pos_transaction_items.quantity) as quantity'),
                DB::raw('SUM(pos_transaction_items.total_amount) as total'),
                DB::raw('COUNT(DISTINCT pos_transactions.id) as transaction_count')
            )
            ->groupBy('pos_transaction_items.product_id')
            ->get()
            ->keyBy('product_id');

        // Merge results
        $allProductIds = $onlineSales->keys()->merge($posSales->keys())->unique();
        
        $merged = $allProductIds->map(function($productId) use ($onlineSales, $posSales) {
            $online = $onlineSales->get($productId);
            $pos = $posSales->get($productId);
            
            return [
                'product_id' => $productId,
                'online_quantity' => $online->quantity ?? 0,
                'online_total' => $online->total ?? 0,
                'online_count' => $online->order_count ?? 0,
                'pos_quantity' => $pos->quantity ?? 0,
                'pos_total' => $pos->total ?? 0,
                'pos_count' => $pos->transaction_count ?? 0,
                'total_quantity' => ($online->quantity ?? 0) + ($pos->quantity ?? 0),
                'total_sales' => ($online->total ?? 0) + ($pos->total ?? 0),
                'total_count' => ($online->order_count ?? 0) + ($pos->transaction_count ?? 0),
            ];
        })->values()->sortByDesc('total_sales');

        return $merged;
    }

    /**
     * Get sales by category (online + POS)
     */
    public static function getCategorySales($dateFrom, $dateTo, $outletId = null)
    {
        // Online sales by category
        $onlineItems = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $onlineItems->where('orders.outlet_id', $outletId);
        }

        $onlineSales = $onlineItems
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.total) as total'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->keyBy('id');

        // POS sales by category
        $posItems = PosTransactionItem::join('pos_transactions', 'pos_transaction_items.transaction_id', '=', 'pos_transactions.id')
            ->join('products', 'pos_transaction_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('pos_transactions.status', 'completed')
            ->whereBetween('pos_transactions.created_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $posItems->where('pos_transactions.outlet_id', $outletId);
        }

        $posSales = $posItems
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(pos_transaction_items.quantity) as quantity'),
                DB::raw('SUM(pos_transaction_items.total_amount) as total'),
                DB::raw('COUNT(DISTINCT pos_transactions.id) as transaction_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->keyBy('id');

        // Merge results
        $allCategoryIds = $onlineSales->keys()->merge($posSales->keys())->unique();
        
        $merged = $allCategoryIds->map(function($categoryId) use ($onlineSales, $posSales) {
            $online = $onlineSales->get($categoryId);
            $pos = $posSales->get($categoryId);
            
            return [
                'category_id' => $categoryId,
                'category_name' => $online->name ?? $pos->name,
                'online_quantity' => $online->quantity ?? 0,
                'online_total' => $online->total ?? 0,
                'online_count' => $online->order_count ?? 0,
                'pos_quantity' => $pos->quantity ?? 0,
                'pos_total' => $pos->total ?? 0,
                'pos_count' => $pos->transaction_count ?? 0,
                'total_quantity' => ($online->quantity ?? 0) + ($pos->quantity ?? 0),
                'total_sales' => ($online->total ?? 0) + ($pos->total ?? 0),
                'total_count' => ($online->order_count ?? 0) + ($pos->transaction_count ?? 0),
            ];
        })->values()->sortByDesc('total_sales');

        return $merged;
    }

    /**
     * Get sales comparison (online vs POS)
     */
    public static function getSalesComparison($dateFrom, $dateTo, $outletId = null)
    {
        $totalSales = self::getTotalSales($dateFrom, $dateTo, $outletId);
        
        // Daily breakdown
        $dates = [];
        $currentDate = Carbon::parse($dateFrom);
        $endDate = Carbon::parse($dateTo);
        
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            
            $dailyOnline = Order::where('status', 'completed')
                ->whereDate('created_at', $dateStr);
            if ($outletId) {
                $dailyOnline->where('outlet_id', $outletId);
            }
            $dailyOnline = $dailyOnline->sum('total_amount');
            
            $dailyPos = PosTransaction::where('status', 'completed')
                ->whereDate('created_at', $dateStr);
            if ($outletId) {
                $dailyPos->where('outlet_id', $outletId);
            }
            $dailyPos = $dailyPos->sum('total_amount');
            
            $dates[] = [
                'date' => $dateStr,
                'online' => $dailyOnline,
                'pos' => $dailyPos,
                'total' => $dailyOnline + $dailyPos
            ];
            
            $currentDate->addDay();
        }
        
        return [
            'summary' => $totalSales,
            'daily' => $dates
        ];
    }
}
```

#### 2. **Unified Report Controller**

**File:** `app/Http/Controllers/Admin/UnifiedReportController.php` (NEW)

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UnifiedSalesReportService;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UnifiedReportController extends Controller
{
    /**
     * Unified sales dashboard
     */
    public function index(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $dateFrom = $request->get('date_from', today()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', today()->format('Y-m-d'));
        $outlets = Outlet::where('is_active', true)->get();

        $totalSales = UnifiedSalesReportService::getTotalSales($dateFrom, $dateTo, $outletId);
        $comparison = UnifiedSalesReportService::getSalesComparison($dateFrom, $dateTo, $outletId);

        $selectedOutlet = $outletId ? Outlet::find($outletId) : null;

        return view('admin.reports.unified.index', compact(
            'outlets',
            'outletId',
            'dateFrom',
            'dateTo',
            'totalSales',
            'comparison',
            'selectedOutlet'
        ));
    }

    /**
     * Unified product sales
     */
    public function products(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $dateFrom = $request->get('date_from', today()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', today()->format('Y-m-d'));
        $outlets = Outlet::where('is_active', true)->get();

        $productSales = UnifiedSalesReportService::getProductSales($dateFrom, $dateTo, $outletId)
            ->load('product');

        $selectedOutlet = $outletId ? Outlet::find($outletId) : null;

        return view('admin.reports.unified.products', compact(
            'outlets',
            'outletId',
            'dateFrom',
            'dateTo',
            'productSales',
            'selectedOutlet'
        ));
    }

    /**
     * Unified category sales
     */
    public function categories(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $dateFrom = $request->get('date_from', today()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', today()->format('Y-m-d'));
        $outlets = Outlet::where('is_active', true)->get();

        $categorySales = UnifiedSalesReportService::getCategorySales($dateFrom, $dateTo, $outletId);

        $selectedOutlet = $outletId ? Outlet::find($outletId) : null;

        return view('admin.reports.unified.categories', compact(
            'outlets',
            'outletId',
            'dateFrom',
            'dateTo',
            'categorySales',
            'selectedOutlet'
        ));
    }
}
```

---

## PERBEDAAN DAN PERSAMAAN

### Perbedaan

| Aspek | Online (Orders) | Offline (POS) |
|-------|-----------------|---------------|
| **Payment** | Midtrans (online gateway) | Cash, Card, E-Wallet, QRIS (langsung) |
| **Shipping** | ✅ Ada (ke alamat customer) | ❌ Tidak ada (walk-in) |
| **Status Flow** | `pending` → `paid` → `processing` → `shipped` → `completed` | `completed` (langsung) |
| **Customer** | Wajib login/register | Bisa walk-in (tidak wajib login) |
| **Inventory Update** | Saat order `processing` | Saat transaksi `completed` |
| **Receipt** | Email invoice | Thermal printer receipt |
| **Location** | Dari mana saja | Di outlet/store |

### Persamaan

| Aspek | Online & Offline |
|-------|------------------|
| **Products** | ✅ Produk yang sama |
| **Customers** | ✅ Database customer terpusat |
| **Inventory** | ✅ Stok per outlet |
| **Loyalty Points** | ✅ Poin dari kedua channel |
| **Coupons** | ✅ Kupon bisa digunakan di kedua channel |
| **Stock Movement** | ✅ Tracking pergerakan stok |
| **Reporting** | ✅ Laporan terpusat (unified) |

---

## SKENARIO PENGGUNAAN

### Skenario 1: Customer Belanja Online dan Offline

1. **Customer register** di website → Data masuk ke `users` table
2. **Customer belanja online** → Order masuk ke `orders` table
3. **Customer datang ke outlet** → Transaksi POS masuk ke `pos_transactions` table
4. **Loyalty points** diakumulasi dari kedua transaksi
5. **History** bisa dilihat terpisah (orders vs pos_transactions)

### Skenario 2: Stok Terintegrasi

1. **Produk A** memiliki stok 100 di Outlet 1
2. **Order online** untuk Outlet 1 → Stok berkurang menjadi 95
3. **Transaksi POS** di Outlet 1 → Stok berkurang menjadi 90
4. **Stock movement** tercatat untuk kedua transaksi
5. **Global stock** di-sync dari total outlet inventories

### Skenario 3: Unified Reporting

1. **Manager** ingin melihat total penjualan hari ini
2. **System** menggabungkan:
   - Online orders: Rp 5.000.000
   - POS transactions: Rp 3.000.000
   - **Total: Rp 8.000.000**
3. **Report** menampilkan breakdown per channel
4. **Analytics** bisa melihat trend online vs offline

---

## IMPLEMENTASI YANG DIPERLUAN

### Prioritas HIGH

1. ✅ **UnifiedSalesReportService** - Service untuk gabungkan data online + POS
2. ✅ **UnifiedReportController** - Controller untuk unified reports
3. ✅ **Views untuk Unified Reports** - UI untuk menampilkan laporan gabungan
4. ✅ **Routes untuk Unified Reports** - Routing untuk akses reports

### Prioritas MEDIUM

5. ⚠️ **Customer History Integration** - Tampilkan history online + offline di customer dashboard
6. ⚠️ **Real-time Stock Sync** - Sinkronisasi global stock dari outlet inventories
7. ⚠️ **Notification Integration** - Notifikasi untuk manager tentang penjualan gabungan

### Prioritas LOW

8. ⚠️ **Order Creation from POS** - Opsi untuk membuat Order dari POS transaction (untuk unified history)
9. ⚠️ **Advanced Analytics** - Predictive analytics untuk online vs offline trends

---

## KESIMPULAN

### Status Saat Ini

**✅ Sudah Terintegrasi:**
- Product Management
- Customer Management
- Inventory Management (per outlet)
- Stock Movement Tracking
- Loyalty Points System
- Coupon System

**⚠️ Perlu Diimplementasikan:**
- Unified Sales Report Service
- Unified Report Controller & Views
- Customer History Integration (optional)
- Real-time Stock Sync (optional)

### Rekomendasi

1. **Implementasikan Unified Reporting** untuk memberikan insight lengkap tentang penjualan gabungan
2. **Pertahankan Separation** antara Orders dan POS Transactions untuk separation of concerns
3. **Gunakan Shared Resources** (products, customers, inventory) untuk konsistensi data
4. **Monitor Performance** karena unified reporting akan query dua tabel besar

---

**Dokumen ini dibuat pada:** 19 Desember 2025  
**Versi:** 1.0  
**Status:** ✅ **Dokumentasi Lengkap - Ready for Implementation**
