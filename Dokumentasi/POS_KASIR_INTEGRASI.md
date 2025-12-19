# DOKUMEN INTEGRASI POS & KASIR DENGAN SISTEM EXISTING

## 📋 DAFTAR ISI
1. [Overview Integrasi](#overview-integrasi)
2. [Integrasi Inventory System](#integrasi-inventory-system)
3. [Integrasi Stock Movement](#integrasi-stock-movement)
4. [Integrasi Loyalty Points](#integrasi-loyalty-points)
5. [Integrasi Coupon System](#integrasi-coupon-system)
6. [Integrasi Order System](#integrasi-order-system)
7. [Integrasi User & Role Management](#integrasi-user--role-management)
8. [Integrasi Payment Methods](#integrasi-payment-methods)
9. [Integrasi Reporting](#integrasi-reporting)
10. [Perubahan yang Diperlukan](#perubahan-yang-diperlukan)
11. [Testing Integrasi](#testing-integrasi)

---

## OVERVIEW INTEGRASI

Sistem POS dan Kasir perlu terintegrasi dengan beberapa sistem yang sudah ada untuk memastikan:
- **Data Consistency**: Data konsisten antara online dan offline
- **Unified Reporting**: Laporan terpusat untuk semua penjualan
- **Shared Resources**: Produk, customer, dan inventory yang sama
- **Seamless Experience**: Customer experience yang seamless

### Sistem yang Perlu Diintegrasikan

1. ✅ **Inventory Management** - Stok per outlet - **SELESAI 100%**
2. ✅ **Stock Movement Tracking** - Audit trail pergerakan stok - **SELESAI 100%**
3. ✅ **Loyalty Points** - Poin reward untuk customer - **SELESAI 100%**
4. ✅ **Coupon System** - Voucher dan diskon - **SELESAI 100%**
5. ⚠️ **Order System** - (Optional) Tracking POS sebagai order - **TIDAK DIIMPLEMENTASIKAN** (Opsi 2: POS transaction terpisah)
6. ✅ **User Management** - Role dan permission - **SELESAI 100%**
7. ✅ **Product Management** - Produk yang sama - **SELESAI 100%**
8. ✅ **Customer Management** - Database customer terpusat - **SELESAI 100%**

**Status:** ✅ **SEMUA INTEGRASI HIGH & MEDIUM PRIORITY SUDAH SELESAI 100%**

---

## INTEGRASI INVENTORY SYSTEM

### Masalah yang Ditemukan

Sistem existing memiliki **DUA** sistem inventory:

1. **Global Stock** (`products.stock_qty`)
   - Digunakan oleh order system online
   - Di-update saat order dibuat/dibatalkan
   - Tidak per-outlet

2. **Outlet Inventory** (`outlet_product_inventories`)
   - Stok per outlet
   - Sudah ada tapi belum digunakan secara konsisten
   - Perlu digunakan untuk POS

### Solusi Integrasi

#### 1. **POS Harus Menggunakan Outlet Inventory**

**❌ SALAH:**
```php
// Jangan update Product.stock_qty langsung
$product->decrement('stock_qty', $quantity);
```

**✅ BENAR:**
```php
// Update OutletProductInventory
$inventory = OutletProductInventory::where('outlet_id', $outletId)
    ->where('product_id', $productId)
    ->firstOrFail();

$inventory->stock -= $quantity;
$inventory->save();

// Optional: Sync ke global stock jika diperlukan
// $product->decrement('stock_qty', $quantity);
```

#### 2. **Sinkronisasi Global Stock (Optional)**

Jika ingin global stock tetap sinkron:

```php
// Setelah update outlet inventory
$product = Product::find($productId);

// Calculate total stock dari semua outlet
$totalStock = OutletProductInventory::where('product_id', $productId)
    ->sum('stock');

// Update global stock
$product->stock_qty = $totalStock;
$product->save();
```

**⚠️ Catatan:** Sinkronisasi ini bisa dilakukan:
- **Real-time**: Setiap transaksi (lebih akurat, tapi lebih berat)
- **Scheduled**: Via cron job setiap X menit (lebih ringan)
- **On-demand**: Manual sync saat diperlukan

#### 3. **Stock Validation di POS**

```php
// Validasi stok sebelum transaksi
$inventory = OutletProductInventory::where('outlet_id', $outletId)
    ->where('product_id', $productId)
    ->first();

if (!$inventory || $inventory->stock < $quantity) {
    throw new \Exception("Stok tidak mencukupi. Tersedia: {$inventory->stock}");
}
```

### Implementasi

**File:** `app/Services/PosInventoryService.php` (NEW)

```php
<?php

namespace App\Services;

use App\Models\OutletProductInventory;
use App\Models\Product;
use App\Models\StockMovement;

class PosInventoryService
{
    /**
     * Decrease stock for POS transaction
     */
    public static function decreaseStock($outletId, $productId, $quantity, $transactionId, $transactionNumber)
    {
        // Get outlet inventory
        $inventory = OutletProductInventory::where('outlet_id', $outletId)
            ->where('product_id', $productId)
            ->firstOrFail();

        $stockBefore = $inventory->stock;

        // Validate stock
        if ($inventory->stock < $quantity) {
            throw new \Exception("Stok tidak mencukupi. Tersedia: {$inventory->stock}, Diminta: {$quantity}");
        }

        // Update outlet inventory
        $inventory->stock -= $quantity;
        $inventory->save();

        $stockAfter = $inventory->stock;

        // Get product for stock movement
        $product = Product::find($productId);

        // Create stock movement
        StockMovementService::logDecrease(
            $product,
            $quantity,
            'App\Models\PosTransaction',
            $transactionId,
            $transactionNumber,
            "POS Sale - Transaction #{$transactionNumber} (Outlet: {$outletId})"
        );

        // Optional: Sync global stock
        self::syncGlobalStock($productId);

        return [
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
        ];
    }

    /**
     * Restore stock for cancelled POS transaction
     */
    public static function restoreStock($outletId, $productId, $quantity, $transactionId, $transactionNumber)
    {
        // Get outlet inventory
        $inventory = OutletProductInventory::where('outlet_id', $outletId)
            ->where('product_id', $productId)
            ->firstOrFail();

        $stockBefore = $inventory->stock;

        // Restore stock
        $inventory->stock += $quantity;
        $inventory->save();

        $stockAfter = $inventory->stock;

        // Get product for stock movement
        $product = Product::find($productId);

        // Create stock movement (restore)
        StockMovementService::logRestore(
            $product,
            $quantity,
            'App\Models\PosTransaction',
            $transactionId,
            $transactionNumber,
            "POS Return - Transaction #{$transactionNumber} (Outlet: {$outletId})"
        );

        // Optional: Sync global stock
        self::syncGlobalStock($productId);

        return [
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
        ];
    }

    /**
     * Sync global stock from outlet inventories
     */
    public static function syncGlobalStock($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        // Calculate total stock from all outlets
        $totalStock = OutletProductInventory::where('product_id', $productId)
            ->sum('stock');

        // Update global stock
        $product->stock_qty = $totalStock;
        $product->save();
    }

    /**
     * Get available stock at outlet
     */
    public static function getStock($outletId, $productId)
    {
        $inventory = OutletProductInventory::where('outlet_id', $outletId)
            ->where('product_id', $productId)
            ->first();

        return $inventory ? $inventory->stock : 0;
    }
}
```

---

## INTEGRASI STOCK MOVEMENT

### Perubahan yang Diperlukan

#### 1. **Tambahkan Type Baru di StockMovement**

**Migration:** `add_pos_types_to_stock_movements.php`

```php
// Tidak perlu migration, cukup gunakan type yang sudah ada:
// - 'out' untuk POS sale
// - 'restore' untuk POS return/cancel

// Tapi bisa tambahkan type khusus jika diperlukan:
// - 'pos_sale' (optional)
// - 'pos_return' (optional)
```

**Update Model:** `app/Models/StockMovement.php`

```php
public function getTypeLabelAttribute()
{
    return match($this->type) {
        'in' => 'Stock Masuk',
        'out' => 'Stock Keluar',
        'adjustment' => 'Penyesuaian Manual',
        'restore' => 'Restore Stock',
        'pos_sale' => 'Penjualan POS',      // NEW
        'pos_return' => 'Return POS',        // NEW
        default => $this->type
    };
}

public function getTypeColorAttribute()
{
    return match($this->type) {
        'in' => 'success',
        'out' => 'danger',
        'adjustment' => 'info',
        'restore' => 'warning',
        'pos_sale' => 'primary',             // NEW
        'pos_return' => 'warning',           // NEW
        default => 'secondary'
    };
}
```

#### 2. **Tambahkan Field outlet_id (Jika Belum Ada)**

**Migration:** `add_outlet_id_to_stock_movements.php`

```php
Schema::table('stock_movements', function (Blueprint $table) {
    $table->foreignId('outlet_id')->nullable()->after('product_id')
        ->constrained('outlets')->onDelete('set null');
    
    $table->index('outlet_id');
});
```

**Update Model:** `app/Models/StockMovement.php`

```php
protected $fillable = [
    'product_id',
    'outlet_id',        // NEW
    'type',
    // ... rest
];

public function outlet()
{
    return $this->belongsTo(Outlet::class);
}
```

#### 3. **Update StockMovementService**

**File:** `app/Services/StockMovementService.php`

```php
public static function logDecrease(
    Product $product,
    int $quantity,
    ?string $referenceType = null,
    ?int $referenceId = null,
    ?string $referenceNumber = null,
    ?string $notes = null,
    ?int $outletId = null  // NEW parameter
): StockMovement {
    $oldStock = $product->stock_qty ?? 0;
    $newStock = max(0, $oldStock - abs($quantity));

    $movement = StockMovement::create([
        'product_id' => $product->id,
        'outlet_id' => $outletId,  // NEW
        'type' => 'out',
        'quantity' => -abs($quantity),
        'old_stock' => $oldStock,
        'new_stock' => $newStock,
        'reference_type' => $referenceType,
        'reference_id' => $referenceId,
        'reference_number' => $referenceNumber,
        'notes' => $notes ?? "Stock keluar: {$quantity} unit",
        'user_id' => auth()->id(),
    ]);

    return $movement;
}
```

---

## INTEGRASI LOYALTY POINTS

### Masalah yang Ditemukan

- Model `LoyaltyPoint` sudah ada
- Logic untuk **earn** points dari transaction **BELUM** ada
- Logic untuk **redeem** points sudah ada di mobile app

### Solusi Integrasi

#### 1. **Award Points dari POS Transaction**

**File:** `app/Services/PosLoyaltyService.php` (NEW)

```php
<?php

namespace App\Services;

use App\Models\LoyaltyPoint;
use App\Models\PosTransaction;
use App\Models\User;

class PosLoyaltyService
{
    /**
     * Award loyalty points from POS transaction
     */
    public static function awardPoints(PosTransaction $transaction)
    {
        // Only award if customer is linked
        if (!$transaction->customer_id) {
            return null;
        }

        $customer = User::find($transaction->customer_id);
        if (!$customer) {
            return null;
        }

        // Check if customer is member (you might have a field like is_member)
        // For now, we'll award to all customers with user account

        // Calculate points (1% of total amount, or customize)
        $pointsEarned = floor($transaction->total_amount * 0.01); // 1 point per Rp100

        if ($pointsEarned <= 0) {
            return null;
        }

        // Create loyalty point record
        $loyaltyPoint = LoyaltyPoint::create([
            'user_id' => $transaction->customer_id,
            'type' => 'earn',
            'points' => $pointsEarned,
            'description' => "Poin dari transaksi POS #{$transaction->transaction_number}",
            'reference_type' => 'App\Models\PosTransaction',
            'reference_id' => $transaction->id,
            'expires_at' => now()->addYear(), // Points expire in 1 year
        ]);

        return $loyaltyPoint;
    }

    /**
     * Redeem loyalty points in POS transaction
     */
    public static function redeemPoints($userId, $points, $transactionId)
    {
        $balance = LoyaltyPoint::getUserBalance($userId);

        if ($points > $balance) {
            throw new \Exception("Poin tidak mencukupi. Saldo: {$balance}");
        }

        // Create redeem record
        $loyaltyPoint = LoyaltyPoint::create([
            'user_id' => $userId,
            'type' => 'redeem',
            'points' => $points,
            'description' => "Redeem poin untuk transaksi POS",
            'reference_type' => 'App\Models\PosTransaction',
            'reference_id' => $transactionId,
        ]);

        // Calculate discount (1 point = Rp1, or customize)
        $discount = $points;

        return [
            'loyalty_point' => $loyaltyPoint,
            'discount' => $discount,
            'remaining_points' => $balance - $points,
        ];
    }

    /**
     * Get customer loyalty balance
     */
    public static function getBalance($userId)
    {
        return LoyaltyPoint::getUserBalance($userId);
    }
}
```

#### 2. **Integrasi ke PosService**

**File:** `app/Services/PosService.php` (UPDATE)

```php
// Di method createTransaction, setelah transaction created:

// Award loyalty points
if ($transaction->customer_id) {
    try {
        PosLoyaltyService::awardPoints($transaction);
    } catch (\Exception $e) {
        // Log error but don't fail transaction
        \Log::warning('Failed to award loyalty points', [
            'transaction_id' => $transaction->id,
            'error' => $e->getMessage()
        ]);
    }
}
```

#### 3. **UI untuk Redeem Points di POS**

Tambahkan di payment interface:
- Input field untuk redeem points
- Display current balance
- Calculate discount dari points

---

## INTEGRASI COUPON SYSTEM

### Masalah yang Ditemukan

- Model `Coupon` sudah ada dengan validation logic
- Tabel `user_coupons` untuk tracking usage
- Logic validation sudah ada di `Coupon::canBeUsedByUser()`

### Solusi Integrasi

#### 1. **Service untuk Apply Coupon di POS**

**File:** `app/Services/PosCouponService.php` (NEW)

```php
<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class PosCouponService
{
    /**
     * Validate and apply coupon
     */
    public static function applyCoupon($code, $subtotal, $userId = null)
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (!$coupon) {
            throw new \Exception('Kode kupon tidak ditemukan');
        }

        if (!$coupon->isValid()) {
            throw new \Exception('Kupon tidak valid atau sudah kadaluarsa');
        }

        // Check user usage limit
        if ($userId && !$coupon->canBeUsedByUser($userId, $subtotal)) {
            throw new \Exception('Kupon tidak dapat digunakan. Cek syarat dan ketentuan.');
        }

        // Check minimum purchase
        if ($coupon->min_purchase && $subtotal < $coupon->min_purchase) {
            throw new \Exception('Minimum pembelian: Rp' . number_format($coupon->min_purchase, 0, ',', '.'));
        }

        // Calculate discount
        $discount = $coupon->calculateDiscount($subtotal);

        return [
            'coupon' => $coupon,
            'discount' => $discount,
        ];
    }

    /**
     * Mark coupon as used
     */
    public static function markAsUsed($couponId, $userId, $transactionId)
    {
        DB::beginTransaction();
        try {
            // Increment usage count
            $coupon = Coupon::findOrFail($couponId);
            $coupon->increment('usage_count');

            // Record user usage
            if ($userId) {
                DB::table('user_coupons')->insert([
                    'user_id' => $userId,
                    'coupon_id' => $couponId,
                    'used_at' => now(),
                    'reference_type' => 'App\Models\PosTransaction',
                    'reference_id' => $transactionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

#### 2. **Integrasi ke PosService**

```php
// Di method createTransaction, jika ada coupon:

if (isset($data['coupon_code'])) {
    try {
        $couponResult = PosCouponService::applyCoupon(
            $data['coupon_code'],
            $data['subtotal'],
            $data['customer_id'] ?? null
        );

        // Apply discount
        $transaction->discount_amount += $couponResult['discount'];
        $transaction->total_amount -= $couponResult['discount'];
        $transaction->save();

        // Mark coupon as used
        PosCouponService::markAsUsed(
            $couponResult['coupon']->id,
            $data['customer_id'] ?? null,
            $transaction->id
        );
    } catch (\Exception $e) {
        throw new \Exception("Coupon error: " . $e->getMessage());
    }
}
```

---

## INTEGRASI ORDER SYSTEM

### Opsi Integrasi

#### **Opsi 1: POS Transaction = Order (Recommended)**

Buat Order dari POS transaction untuk unified reporting:

**File:** `app/Services/PosOrderService.php` (NEW)

```php
<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PosTransaction;

class PosOrderService
{
    /**
     * Create order from POS transaction
     */
    public static function createOrderFromTransaction(PosTransaction $transaction)
    {
        $order = Order::create([
            'store_id' => $transaction->outlet->store_id,
            'outlet_id' => $transaction->outlet_id,
            'order_number' => $transaction->transaction_number,
            'user_id' => $transaction->customer_id,
            'subtotal' => $transaction->subtotal,
            'discount' => $transaction->discount_amount,
            'tax_amount' => $transaction->tax_amount ?? 0,
            'total_amount' => $transaction->total_amount,
            'status' => 'completed', // POS transactions are immediately completed
            'payment_type' => $transaction->payment_method,
            'payment_method' => $transaction->payment_method,
            'payment_details' => $transaction->payment_details,
            'paid_at' => $transaction->created_at,
            'processed_at' => $transaction->created_at,
            'shipping_address' => null, // POS is walk-in
            'billing_address' => null,
        ]);

        // Create order items
        foreach ($transaction->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'price' => $item->unit_price,
                'quantity' => $item->quantity,
                'total' => $item->total_amount,
                'product_details' => [
                    'sku' => $item->product_sku,
                ],
            ]);
        }

        return $order;
    }
}
```

**Keuntungan:**
- ✅ Unified reporting (semua penjualan di satu tempat)
- ✅ Customer bisa lihat history POS di order history
- ✅ Analytics lebih mudah

**Kekurangan:**
- ⚠️ Order table akan lebih besar
- ⚠️ Perlu handle duplicate order_number (atau gunakan prefix)

#### **Opsi 2: POS Transaction Terpisah (Current Design)**

POS transaction tetap terpisah dari Order.

**Keuntungan:**
- ✅ Separation of concerns
- ✅ Order table tidak membesar
- ✅ Lebih simple

**Kekurangan:**
- ⚠️ Reporting perlu gabungkan dua sumber
- ⚠️ Customer tidak lihat POS transaction di order history

### Rekomendasi

**Gunakan Opsi 1** dengan modifikasi:
- Order number untuk POS: `POS-{OUTLET}-{DATE}-{SEQ}`
- Order number untuk online: `ORD-{DATE}-{SEQ}`
- Atau gunakan prefix di order_number

---

## INTEGRASI USER & ROLE MANAGEMENT

### Perubahan yang Diperlukan

#### 1. **Tambahkan Role Baru**

**Migration:** `add_pos_roles_to_users.php` (Optional, jika menggunakan enum)

Atau cukup gunakan string role:
- `'cashier'` - Bisa akses POS, buka/tutup shift
- `'staff'` - Bisa akses POS, tidak bisa tutup shift
- `'manager'` - Full access termasuk reports

#### 2. **Update User Model**

**File:** `app/Models/User.php` (UPDATE)

```php
public function isCashier(): bool
{
    return $this->role === 'cashier';
}

public function isStaff(): bool
{
    return $this->role === 'staff';
}

public function isManager(): bool
{
    return $this->role === 'manager';
}

public function canAccessPos(): bool
{
    return in_array($this->role, ['admin', 'manager', 'cashier', 'staff']);
}

public function canCloseShift(): bool
{
    return in_array($this->role, ['admin', 'manager', 'cashier']);
}
```

#### 3. **Tambahkan Permissions**

**Seeder:** `PosPermissionsSeeder.php` (NEW)

```php
use Spatie\Permission\Models\Permission;

$permissions = [
    'pos.view',
    'pos.transaction',
    'pos.cancel',
    'pos.refund',
    'pos.shift.open',
    'pos.shift.close',
    'pos.report',
    'pos.setting',
];

foreach ($permissions as $permission) {
    Permission::firstOrCreate(['name' => $permission]);
}
```

#### 4. **Middleware: PosAccess**

**File:** `app/Http/Middleware/PosAccess.php` (NEW)

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PosAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->canAccessPos()) {
            abort(403, 'Anda tidak memiliki akses ke POS');
        }

        return $next($request);
    }
}
```

---

## INTEGRASI PAYMENT METHODS

### Payment Methods yang Perlu Didukung

1. **Cash** - ✅ Sudah di rancangan
2. **Card** - ✅ Sudah di rancangan
3. **E-Wallet** - ✅ Sudah di rancangan
4. **QRIS** - ✅ Sudah di rancangan
5. **Midtrans** - ⚠️ Perlu pertimbangan

### Integrasi dengan Midtrans (Optional)

Jika ingin POS bisa terima payment via Midtrans (untuk online payment di store):

**File:** `app/Services/PosPaymentService.php` (UPDATE)

```php
public function processMidtransPayment($transaction, $paymentDetails)
{
    // Use existing MidtransService
    $midtransService = new \App\Services\MidtransService();
    
    // Create payment
    $payment = $midtransService->createTransaction([
        'transaction_details' => [
            'order_id' => $transaction->transaction_number,
            'gross_amount' => $transaction->total_amount,
        ],
        // ... other details
    ]);

    return $payment;
}
```

**Catatan:** Untuk POS, biasanya payment langsung (cash/card), jadi Midtrans mungkin tidak diperlukan.

---

## INTEGRASI REPORTING

### Unified Sales Report

Gabungkan data dari:
- **Online Orders** (`orders` table)
- **POS Transactions** (`pos_transactions` table)

**File:** `app/Services/UnifiedSalesReportService.php` (NEW)

```php
<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PosTransaction;
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

        // POS sales
        $posQuery = PosTransaction::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $posQuery->where('outlet_id', $outletId);
        }

        $posSales = $posQuery->sum('total_amount');

        return [
            'online' => $onlineSales,
            'pos' => $posSales,
            'total' => $onlineSales + $posSales,
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
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as quantity'), DB::raw('SUM(order_items.total) as total'))
            ->groupBy('order_items.product_id')
            ->get();

        // POS sales
        $posItems = PosTransactionItem::join('pos_transactions', 'pos_transaction_items.transaction_id', '=', 'pos_transactions.id')
            ->where('pos_transactions.status', 'completed')
            ->whereBetween('pos_transactions.created_at', [$dateFrom, $dateTo]);

        if ($outletId) {
            $posItems->where('pos_transactions.outlet_id', $outletId);
        }

        $posSales = $posItems
            ->select('pos_transaction_items.product_id', DB::raw('SUM(pos_transaction_items.quantity) as quantity'), DB::raw('SUM(pos_transaction_items.total_amount) as total'))
            ->groupBy('pos_transaction_items.product_id')
            ->get();

        // Merge results
        $merged = [];
        foreach ($onlineSales as $item) {
            $merged[$item->product_id] = [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'total' => $item->total,
            ];
        }

        foreach ($posSales as $item) {
            if (isset($merged[$item->product_id])) {
                $merged[$item->product_id]['quantity'] += $item->quantity;
                $merged[$item->product_id]['total'] += $item->total;
            } else {
                $merged[$item->product_id] = [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'total' => $item->total,
                ];
            }
        }

        return collect($merged)->values();
    }
}
```

---

## PERUBAHAN YANG DIPERLUAN

### 1. Database Changes

- [x] ✅ **Migration:** Add `outlet_id` to `stock_movements` table - **SELESAI**
- [x] ✅ **Migration:** (Optional) Add `pos_sale` and `pos_return` types support - **SELESAI**
- [ ] ⚠️ **Migration:** (Optional) Add `order_id` to `pos_transactions` for linking - **TIDAK DIIMPLEMENTASIKAN** (Opsi 2 dipilih: POS transaction terpisah)

### 2. Model Updates

- [x] ✅ **StockMovement:** Add `outlet_id` field and relationship - **SELESAI**
- [x] ✅ **User:** Add `isCashier()`, `isStaff()`, `canAccessPos()` methods - **SELESAI**
- [x] ✅ **Product:** (Optional) Add method to sync global stock from outlets - **SELESAI** (via PosInventoryService)

### 3. Service Layer (NEW)

- [x] ✅ **PosInventoryService:** Handle outlet inventory updates - **SELESAI**
- [x] ✅ **PosLoyaltyService:** Award/redeem loyalty points - **SELESAI**
- [x] ✅ **PosCouponService:** Apply coupons in POS - **SELESAI**
- [ ] ⚠️ **PosOrderService:** (Optional) Create orders from POS transactions - **TIDAK DIIMPLEMENTASIKAN** (Opsi 2 dipilih)
- [x] ✅ **UnifiedSalesReportService:** Combined reporting - **SELESAI** (via PosReportController)

### 4. Middleware (NEW)

- [x] ✅ **PosAccess:** Check user can access POS - **SELESAI**
- [x] ✅ **PosShiftOpen:** Check shift is open - **SELESAI**
- [x] ✅ **PosOutletAccess:** Check user can access outlet - **SELESAI** (via PosAccess middleware)

### 5. StockMovementService Updates

- [x] ✅ Add `outlet_id` parameter to methods - **SELESAI**
- [x] ✅ Support POS transaction references - **SELESAI**

---

## TESTING INTEGRASI

### ⚠️ CRITICAL: Database Safety for Testing

**IMPORTANT:** Semua tests menggunakan **SQLite in-memory database** (`:memory:`) dan **TIDAK AKAN PERNAH** menghapus data dari database production/local Anda.

**Safety Checks:**
- ✅ Tests dipaksa menggunakan `DB_CONNECTION=sqlite` dan `DB_DATABASE=:memory:` via `phpunit.xml`
- ✅ Multiple safety checks di `tests/TestCase.php` memastikan tests tidak pernah mengakses MySQL/PostgreSQL
- ✅ Jika test mencoba menggunakan database yang salah, test akan **STOP** dengan error jelas
- ✅ Semua test data dibuat di memory dan dihapus setelah test selesai

**Lihat dokumentasi lengkap:** `tests/SAFETY_WARNING.md`

### Test Cases

#### 1. **Inventory Integration**
- [x] ✅ POS sale decreases outlet inventory - **SELESAI & TESTED**
- [x] ✅ Stock movement created correctly - **SELESAI & TESTED**
- [x] ✅ Global stock sync (if enabled) - **SELESAI & TESTED**
- [x] ✅ POS cancel restores outlet inventory - **SELESAI & TESTED**
- [x] ✅ Stock validation works - **SELESAI & TESTED**

#### 2. **Loyalty Points**
- [x] ✅ Points awarded from POS transaction - **SELESAI & TESTED**
- [x] ✅ Points redeemed in POS transaction - **SELESAI & TESTED**
- [x] ✅ Balance calculation correct - **SELESAI & TESTED**
- [x] ✅ Points expire correctly - **SELESAI & TESTED**

#### 3. **Coupon System**
- [x] ✅ Coupon validation works - **SELESAI & TESTED**
- [x] ✅ Discount calculated correctly - **SELESAI & TESTED**
- [x] ✅ Usage limit enforced - **SELESAI & TESTED**
- [x] ✅ User usage limit enforced - **SELESAI & TESTED**
- [x] ✅ Coupon marked as used - **SELESAI & TESTED**

#### 4. **Order Integration (if implemented)**
- [ ] ⚠️ Order created from POS transaction - **TIDAK DIIMPLEMENTASIKAN** (Opsi 2 dipilih)
- [ ] ⚠️ Order items match transaction items - **TIDAK DIIMPLEMENTASIKAN**
- [ ] ⚠️ Order number unique - **TIDAK DIIMPLEMENTASIKAN**
- [ ] ⚠️ Order status correct - **TIDAK DIIMPLEMENTASIKAN**

#### 5. **Reporting**
- [x] ✅ Unified sales report combines online + POS - **SELESAI & TESTED**
- [x] ✅ Product sales report accurate - **SELESAI & TESTED**
- [x] ✅ Date range filtering works - **SELESAI & TESTED**
- [x] ✅ Outlet filtering works - **SELESAI & TESTED**

---

## KESIMPULAN

### Prioritas Integrasi

**HIGH PRIORITY:**
1. ✅ **Inventory System (Outlet Inventory)** - **SELESAI 100%**
2. ✅ **Stock Movement Tracking** - **SELESAI 100%**
3. ✅ **User & Role Management** - **SELESAI 100%**

**MEDIUM PRIORITY:**
4. ✅ **Loyalty Points** - **SELESAI 100%**
5. ✅ **Coupon System** - **SELESAI 100%**

**LOW PRIORITY:**
6. ⚠️ **Order Integration (Optional)** - **TIDAK DIIMPLEMENTASIKAN** (Opsi 2 dipilih: POS transaction terpisah dari Order)
7. ⚠️ **Payment Methods (Midtrans)** - **TIDAK DIIMPLEMENTASIKAN** (POS menggunakan payment langsung: Cash, Card, E-Wallet, QRIS)

### Status Implementasi

**✅ SEMUA INTEGRASI HIGH & MEDIUM PRIORITY SUDAH SELESAI 100%**

**Yang Sudah Diimplementasikan:**
- ✅ Inventory System dengan outlet inventory
- ✅ Stock Movement tracking dengan outlet_id
- ✅ User & Role Management dengan permissions
- ✅ Loyalty Points (award & redeem)
- ✅ Coupon System (validation & usage tracking)
- ✅ Unified Reporting (online + POS)
- ✅ Comprehensive Testing (Unit + Integration + Feature)
- ✅ Database Safety Checks untuk Testing

**Yang Tidak Diimplementasikan (Keputusan Design):**
- ⚠️ Order Integration - Diputuskan untuk memisahkan POS transaction dari Order system
- ⚠️ Midtrans Payment - POS menggunakan payment langsung, tidak perlu Midtrans

### Testing Status

**✅ SEMUA TESTS SUDAH SELESAI 100%**

- ✅ Unit Tests (PosShift, PosTransaction, PosService)
- ✅ Integration Tests (Transaction, Shift, Payment, Inventory, Refund flows)
- ✅ Feature Tests (Reports, Receipts, Customer, Settings, Cash Movement, Receipt Templates)
- ✅ Database Safety Checks (Tests menggunakan SQLite in-memory, tidak pernah menghapus production database)

**Lihat dokumentasi lengkap:**
- `Dokumentasi/POS_KASIR_FINAL_STATUS.md` - Status implementasi lengkap
- `tests/SAFETY_WARNING.md` - Database safety untuk testing
- `tests/README.md` - Panduan testing

---

**Dokumen ini dibuat pada:** 19 Desember 2025  
**Versi:** 2.0  
**Status:** ✅ **COMPLETE - All High & Medium Priority Integrations Implemented**
