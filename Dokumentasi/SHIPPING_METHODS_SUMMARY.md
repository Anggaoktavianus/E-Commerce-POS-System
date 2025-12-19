# Summary Shipping Methods & Shipping Costs

## ✅ Status: Data Sudah Disesuaikan

### 📊 Shipping Methods (10 methods)

| ID | Name | Code | Type | Distance-Based | Price/Km | Min Cost | Status |
|----|------|------|------|----------------|----------|----------|--------|
| 1 | Pengiriman Instan (Berdasarkan Jarak) | `instant_delivery` | instant | ✅ Yes | Rp 5.000 | Rp 10.000 | ✅ Active |
| 2 | GoSend Instant | `gosend_instant` | instant | ❌ No | - | - | ✅ Active |
| 3 | GrabExpress Instant | `grab_express` | instant | ❌ No | - | - | ✅ Active |
| 4 | SiCepat Same Day | `sicepat_same_day` | same_day | ❌ No | - | - | ✅ Active |
| 5 | JNE OKE | `jne_oke` | regular | ❌ No | - | - | ✅ Active |
| 6 | JNE REG | `jne_reg` | regular | ❌ No | - | - | ✅ Active |
| 7 | JNE YES | `jne_yes` | express | ❌ No | - | - | ✅ Active |
| 8 | JNT Express | `jnt_reg` | regular | ❌ No | - | - | ✅ Active |
| 9 | SiCepat REG | `sicepat_reg` | regular | ❌ No | - | - | ✅ Active |
| 10 | POS Kilat Khusus | `pos_kilat` | regular | ❌ No | - | - | ✅ Active |

### 📦 Shipping Costs (296 routes)

| Shipping Method | Routes Count | Notes |
|----------------|--------------|-------|
| ID 1 (Distance-based) | **0** | ✅ Correct - tidak perlu shipping_costs (hitung otomatis dari jarak) |
| ID 2 (GoSend) | 24 | ✅ Active - untuk Semarang, Jakarta, Surabaya, Bandung |
| ID 3 (GrabExpress) | 36 | ✅ Active |
| ID 4 (SiCepat Same Day) | 46 | ✅ Active |
| ID 5 (JNE OKE) | 52 | ✅ Active |
| ID 6 (JNE REG) | 36 | ✅ Active |
| ID 7 (JNE YES) | 46 | ✅ Active |
| ID 8 (JNT Express) | 30 | ✅ Active |
| ID 9 (SiCepat REG) | 20 | ✅ Active |
| ID 10 (POS Kilat) | 6 | ✅ Active |

---

## 🔧 Perubahan yang Dilakukan

### 1. ✅ Seeder Diperbaiki
- Seeder sekarang **skip membuat shipping_costs** untuk distance-based methods
- Menggunakan `updateOrCreate` untuk menghindari duplikasi
- Distance-based method (ID 1) tidak akan punya shipping_costs

### 2. ✅ Data Dibersihkan
- Menghapus shipping_costs untuk ID 1 (distance-based) - seharusnya 0
- Menghapus shipping_costs untuk ID 11 (tidak ada di seeder)

### 3. ✅ Verifikasi
- ID 1 adalah instant delivery dengan distance-based calculation ✅
- ID 2 (GoSend) aktif dan punya 24 routes ✅
- Semua methods aktif ✅

---

## 📝 Catatan Penting

### ID 1: Pengiriman Instan (Berdasarkan Jarak)
- **Type**: `instant`
- **Distance-based**: ✅ Yes
- **Price per km**: Rp 5.000
- **Min cost**: Rp 10.000
- **Calculation**: `max(min_cost, distance_km * price_per_km)`
- **Shipping costs**: Tidak perlu (hitung otomatis dari koordinat)

### ID 2: GoSend Instant
- **Type**: `instant`
- **Distance-based**: ❌ No
- **Shipping costs**: 24 routes (Semarang, Jakarta, Surabaya, Bandung)
- **Status**: ✅ Active (masih digunakan)

---

## 🚀 Cara Menggunakan

### Untuk Distance-Based (ID 1):
```php
$shippingMethod = ShippingMethod::find(1);
$distance = calculateDistance($origin, $destination); // in km
$cost = max($shippingMethod->min_cost, $distance * $shippingMethod->price_per_km);
```

### Untuk Fixed Cost Methods (ID 2-10):
```php
$shippingMethod = ShippingMethod::find(2); // GoSend
$shippingCost = $shippingMethod->calculateCost($origin, $destination, $weight);
$cost = $shippingCost->cost ?? null;
```

---

## 🔄 Re-seed Data

Jika perlu mengisi ulang data:

```bash
php artisan db:seed --class=ShippingMethodsSeeder
```

Seeder akan:
- ✅ Update atau create shipping methods
- ✅ Skip shipping_costs untuk distance-based methods
- ✅ Update atau create shipping_costs untuk fixed-cost methods

---

## 📍 Routes Coverage

### GoSend (ID 2) - 24 routes:
- Semarang → Semarang, Jakarta, Surabaya, Bandung
- Jakarta → Jakarta, Surabaya, Bandung
- Surabaya → Surabaya, Jakarta, Bandung
- Bandung → Bandung, Jakarta

### Other Methods:
- Coverage lebih luas (Semarang, Jakarta, Surabaya, Bandung, Medan)
- Berbagai kombinasi origin-destination

---

**Last Updated**: 2025-12-14
**Status**: ✅ All data verified and cleaned
