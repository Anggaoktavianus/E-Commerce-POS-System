# RANCANGAN ROLE & PERMISSION UNTUK POS & KASIR

## 📋 DAFTAR ISI
1. [Overview](#overview)
2. [Rekomendasi Role](#rekomendasi-role)
3. [Permission Breakdown](#permission-breakdown)
4. [Role Hierarchy](#role-hierarchy)
5. [Implementasi](#implementasi)
6. [Best Practices](#best-practices)

---

## OVERVIEW

Berdasarkan analisis kebutuhan POS dan best practices, sistem membutuhkan **4 role utama** untuk POS dengan level akses yang berbeda.

---

## REKOMENDASI ROLE

### **Rekomendasi: 4 Role untuk POS**

#### 1. **Admin** (Sudah Ada)
**Level:** Super Admin  
**Akses:** Full access ke semua fitur termasuk POS

**Fitur POS yang bisa diakses:**
- ✅ Semua fitur POS
- ✅ Buka/tutup shift
- ✅ Create/edit/cancel transactions
- ✅ View semua reports
- ✅ Manage settings
- ✅ Access semua outlet

---

#### 2. **Manager** (Baru)
**Level:** Store/Outlet Manager  
**Akses:** Full access untuk outlet mereka

**Fitur POS yang bisa diakses:**
- ✅ Semua fitur POS untuk outlet mereka
- ✅ Buka/tutup shift
- ✅ Create/edit/cancel transactions
- ✅ View reports (outlet mereka)
- ✅ Manage settings (outlet mereka)
- ✅ View shift reports
- ✅ Access multiple outlets (jika assigned)

**Tugas:**
- Oversee operations outlet
- Review shift reports
- Handle variance issues
- Manage staff & cashier

---

#### 3. **Cashier** (Baru)
**Level:** Kasir Utama  
**Akses:** Transaction processing + shift management

**Fitur POS yang bisa diakses:**
- ✅ Buka/tutup shift
- ✅ Create transactions
- ✅ View transactions (shift mereka)
- ✅ Cancel transactions (shift mereka, dengan limit waktu)
- ✅ View basic reports (shift mereka)
- ❌ Tidak bisa edit settings
- ❌ Tidak bisa akses outlet lain

**Tugas:**
- Buka shift di pagi hari
- Proses transaksi sepanjang hari
- Tutup shift di akhir hari
- Handle cash management

**Keterbatasan:**
- Hanya bisa akses outlet yang di-assign
- Tidak bisa cancel transaction > 24 jam
- Tidak bisa refund (perlu manager approval)

---

#### 4. **Staff** (Baru)
**Level:** Staff/Kasir Junior  
**Akses:** Transaction processing only

**Fitur POS yang bisa diakses:**
- ✅ Create transactions
- ✅ View transactions (shift aktif)
- ❌ Tidak bisa buka shift
- ❌ Tidak bisa tutup shift
- ❌ Tidak bisa cancel transactions
- ❌ Tidak bisa view reports
- ❌ Tidak bisa akses settings

**Tugas:**
- Proses transaksi
- Assist customers
- Scan products
- Handle payments

**Keterbatasan:**
- Hanya bisa transaksi saat shift sudah dibuka
- Tidak bisa manage shift
- Tidak bisa cancel/refund

---

## PERMISSION BREAKDOWN

### Matrix Permission per Role

| Permission | Admin | Manager | Cashier | Staff |
|------------|-------|---------|---------|-------|
| **POS Access** |
| `pos.view` (Dashboard) | ✅ | ✅ | ✅ | ✅ |
| `pos.dashboard` | ✅ | ✅ | ✅ | ✅ |
| **Shift Management** |
| `pos.shift.open` | ✅ | ✅ | ✅ | ❌ |
| `pos.shift.close` | ✅ | ✅ | ✅ | ❌ |
| `pos.shift.view` | ✅ | ✅ | ✅ | ✅ (current shift only) |
| `pos.shift.report` | ✅ | ✅ | ✅ | ❌ |
| **Transaction** |
| `pos.transaction.create` | ✅ | ✅ | ✅ | ✅ |
| `pos.transaction.view` | ✅ | ✅ | ✅ | ✅ (own shift) |
| `pos.transaction.cancel` | ✅ | ✅ | ✅ (limited) | ❌ |
| `pos.transaction.refund` | ✅ | ✅ | ❌ | ❌ |
| **Product & Customer** |
| `pos.product.search` | ✅ | ✅ | ✅ | ✅ |
| `pos.customer.search` | ✅ | ✅ | ✅ | ✅ |
| `pos.customer.create` | ✅ | ✅ | ✅ | ✅ |
| **Reports** |
| `pos.report.daily` | ✅ | ✅ | ✅ (own outlet) | ❌ |
| `pos.report.shift` | ✅ | ✅ | ✅ (own shift) | ❌ |
| `pos.report.product` | ✅ | ✅ | ❌ | ❌ |
| `pos.report.export` | ✅ | ✅ | ❌ | ❌ |
| **Settings** |
| `pos.setting.view` | ✅ | ✅ | ❌ | ❌ |
| `pos.setting.edit` | ✅ | ✅ (own outlet) | ❌ | ❌ |
| **Cash Management** |
| `pos.cash.deposit` | ✅ | ✅ | ✅ | ❌ |
| `pos.cash.withdrawal` | ✅ | ✅ | ❌ | ❌ |
| `pos.cash.transfer` | ✅ | ✅ | ❌ | ❌ |

---

## ROLE HIERARCHY

```
Admin (Super Admin)
  └── Full access semua fitur & semua outlet

Manager (Store Manager)
  └── Full access untuk outlet mereka
      ├── Shift management
      ├── Transaction management
      ├── Reports
      └── Settings

Cashier (Kasir Utama)
  └── Transaction + Shift management
      ├── Buka/tutup shift
      ├── Create transactions
      ├── Cancel transactions (limited)
      └── View basic reports

Staff (Kasir Junior)
  └── Transaction only
      ├── Create transactions
      └── View transactions (current shift)
```

---

## IMPLEMENTASI

### 1. **Update User Model** (Sudah Done ✅)

Methods yang sudah ada:
- `isCashier()`
- `isStaff()`
- `isManager()`
- `canAccessPos()`
- `canCloseShift()`

### 2. **Tambahkan Methods untuk Permission Granular**

**File:** `app/Models/User.php` (UPDATE)

```php
// Permission checks untuk POS
public function canOpenShift(): bool
{
    return in_array($this->role, ['admin', 'manager', 'cashier']);
}

public function canCloseShift(): bool
{
    return in_array($this->role, ['admin', 'manager', 'cashier']);
}

public function canCancelTransaction(): bool
{
    return in_array($this->role, ['admin', 'manager', 'cashier']);
}

public function canRefundTransaction(): bool
{
    return in_array($this->role, ['admin', 'manager']);
}

public function canViewReports(): bool
{
    return in_array($this->role, ['admin', 'manager', 'cashier']);
}

public function canExportReports(): bool
{
    return in_array($this->role, ['admin', 'manager']);
}

public function canManageSettings(): bool
{
    return in_array($this->role, ['admin', 'manager']);
}

public function canManageCash(): bool
{
    return in_array($this->role, ['admin', 'manager', 'cashier']);
}

public function canWithdrawCash(): bool
{
    return in_array($this->role, ['admin', 'manager']);
}

public function canTransferCash(): bool
{
    return in_array($this->role, ['admin', 'manager']);
}
```

### 3. **Update Middleware untuk Granular Permission**

**File:** `app/Http/Middleware/PosAccess.php` (UPDATE)

```php
public function handle(Request $request, Closure $next, $permission = null): Response
{
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    // Basic POS access
    if (!$user->canAccessPos()) {
        abort(403, 'Anda tidak memiliki akses ke POS');
    }

    // Check specific permission if provided
    if ($permission) {
        $method = 'can' . str_replace(' ', '', ucwords(str_replace('.', ' ', $permission)));
        if (method_exists($user, $method) && !$user->$method()) {
            abort(403, 'Anda tidak memiliki permission untuk aksi ini');
        }
    }

    return $next($request);
}
```

### 4. **Update Controllers untuk Permission Checks**

**Contoh di PosTransactionController:**

```php
public function cancel(Request $request, $id)
{
    // Check permission
    if (!auth()->user()->canCancelTransaction()) {
        return response()->json([
            'success' => false,
            'message' => 'Anda tidak memiliki permission untuk cancel transaction'
        ], 403);
    }

    // ... rest of code
}
```

---

## ALTERNATIF: Menggunakan Permission Package (Spatie)

Jika ingin lebih fleksibel, bisa menggunakan **Spatie Laravel Permission**:

### Keuntungan:
- ✅ Granular permission control
- ✅ Role bisa punya multiple permissions
- ✅ User bisa punya multiple roles
- ✅ Permission bisa di-assign per user
- ✅ Lebih flexible untuk future needs

### Implementasi dengan Spatie:

```php
// Install: composer require spatie/laravel-permission

// Seeder
$roles = [
    'admin' => [
        'pos.*' // All POS permissions
    ],
    'manager' => [
        'pos.view',
        'pos.shift.*',
        'pos.transaction.*',
        'pos.report.*',
        'pos.setting.*'
    ],
    'cashier' => [
        'pos.view',
        'pos.shift.open',
        'pos.shift.close',
        'pos.transaction.create',
        'pos.transaction.view',
        'pos.transaction.cancel',
        'pos.report.shift'
    ],
    'staff' => [
        'pos.view',
        'pos.transaction.create',
        'pos.transaction.view'
    ]
];
```

---

## REKOMENDASI FINAL

### **Opsi 1: Simple Role-Based (Current - Recommended untuk Start)**

**4 Role:**
1. **Admin** - Full access
2. **Manager** - Full access untuk outlet mereka
3. **Cashier** - Transaction + Shift management
4. **Staff** - Transaction only

**Keuntungan:**
- ✅ Simple dan mudah di-manage
- ✅ Cukup untuk kebutuhan awal
- ✅ Tidak perlu package tambahan
- ✅ Fast implementation

**Kekurangan:**
- ⚠️ Kurang flexible untuk custom permission
- ⚠️ Perlu update code jika ada permission baru

---

### **Opsi 2: Permission-Based dengan Spatie (Recommended untuk Scale)**

**4 Role + Multiple Permissions:**
- Same roles, tapi dengan granular permissions
- Permission bisa di-assign per user
- Lebih flexible untuk future needs

**Keuntungan:**
- ✅ Sangat flexible
- ✅ Bisa custom permission per user
- ✅ Easy to add new permissions
- ✅ Industry standard

**Kekurangan:**
- ⚠️ Perlu install package
- ⚠️ Lebih complex setup
- ⚠️ Perlu migration untuk permissions table

---

## REKOMENDASI SAYA

### **Gunakan Opsi 1 (Simple Role-Based) untuk Start**

**Alasan:**
1. **Cukup untuk kebutuhan awal** - 4 role sudah cover semua use case
2. **Simple implementation** - Tidak perlu package tambahan
3. **Easy to understand** - Role jelas dan mudah dipahami
4. **Fast to implement** - Sudah sebagian besar done
5. **Bisa upgrade later** - Jika perlu, bisa migrate ke Spatie

### **4 Role yang Disarankan:**

1. **`admin`** - Full access (sudah ada)
2. **`manager`** - Store/Outlet manager
3. **`cashier`** - Kasir utama (bisa buka/tutup shift)
4. **`staff`** - Staff/kasir junior (hanya transaksi)

### **Jika Perlu Lebih Detail:**

Bisa tambahkan 1 role lagi:
5. **`supervisor`** - Level antara manager dan cashier
   - Bisa buka/tutup shift
   - Bisa cancel/refund
   - Bisa view reports
   - Tidak bisa manage settings

**Tapi untuk start, 4 role sudah cukup!**

---

## IMPLEMENTASI YANG DISARANKAN

### Step 1: Update User Model (Tambahkan Methods)

```php
// Tambahkan methods untuk granular permission
public function canOpenShift(): bool
{
    return in_array($this->role, ['admin', 'manager', 'cashier']);
}

public function canCancelTransaction(): bool
{
    return in_array($this->role, ['admin', 'manager', 'cashier']);
}

public function canRefundTransaction(): bool
{
    return in_array($this->role, ['admin', 'manager']);
}

public function canViewReports(): bool
{
    return in_array($this->role, ['admin', 'manager', 'cashier']);
}

public function canManageSettings(): bool
{
    return in_array($this->role, ['admin', 'manager']);
}
```

### Step 2: Update Controllers

Tambahkan permission checks di controllers untuk actions yang sensitive.

### Step 3: Update Views

Hide/show buttons berdasarkan permission di views.

---

## KESIMPULAN

### **Rekomendasi: 4 Role**

1. **Admin** - Full access
2. **Manager** - Full access untuk outlet mereka
3. **Cashier** - Transaction + Shift management
4. **Staff** - Transaction only

**Ini sudah cukup untuk:**
- ✅ Separation of duties
- ✅ Security
- ✅ Business needs
- ✅ Scalability

**Jika di masa depan perlu lebih granular, bisa:**
- Upgrade ke Spatie Permission
- Atau tambahkan role baru (supervisor, dll)

---

**Dokumen ini dibuat pada:** 18 Desember 2025  
**Versi:** 1.0  
**Status:** Recommendation
