# Alur Live Tracking System

## ⚠️ STATUS: SEMENTARA DINONAKTIFKAN
**Fitur live tracking saat ini disembunyikan karena masih menggunakan Gosend (kurir pihak ketiga).**
- Aktifkan kembali dengan mengubah `@if(false && $isInstantDelivery)` menjadi `@if($isInstantDelivery)` di `resources/views/orders/track.blade.php`
- Fitur ini akan berguna ketika sudah menggunakan kurir sendiri

## 📋 Overview
Sistem live tracking memungkinkan customer untuk melihat lokasi kurir secara real-time saat pengiriman instan, mirip dengan Grab/Gojek.

---

## 🔄 Alur Lengkap

### 1️⃣ **SAAT CHECKOUT (Customer Membuat Order)**

**Lokasi**: `CheckoutController@process`

**Alur**:
1. Customer memilih **"Pengiriman Instan"** di halaman checkout
2. Customer mengisi alamat pengiriman (dengan koordinat latitude/longitude)
3. Sistem menghitung ongkir berdasarkan jarak
4. Customer submit form checkout
5. **Order dibuat** di database dengan:
   - `shipping_method_id` = ID metode instant (biasanya 1)
   - `shipping_address` = JSON berisi alamat + koordinat
   - `status` = 'pending'

6. **AUTO-CREATE TRACKING** (jika instant delivery):
   ```php
   if ($shippingMethodId == 1 || $shippingMethod->type === 'instant') {
       DeliveryTracking::create([
           'order_id' => $order->id,
           'status' => 'pending'
       ]);
   }
   ```
   - Record dibuat di tabel `delivery_tracking`
   - Status awal: `pending`
   - Belum ada lokasi kurir (latitude/longitude masih null)

7. Order redirect ke Midtrans untuk pembayaran

---

### 2️⃣ **SAAT PEMBAYARAN BERHASIL**

**Lokasi**: `MidtransService@handleNotification`

**Alur**:
1. Midtrans mengirim webhook notification
2. Sistem update order:
   - `status` = 'paid'
   - `paid_at` = timestamp sekarang

3. **VERIFY/CREATE TRACKING** (jika instant delivery):
   ```php
   if ($order->shipping_method_id == 1 || $shippingMethod->type === 'instant') {
       DeliveryTracking::firstOrCreate(
           ['order_id' => $order->id],
           ['status' => 'pending']
       );
   }
   ```
   - Memastikan tracking record ada
   - Jika belum ada, dibuat sekarang

4. Dispatch `ProcessOrderJob` untuk mengurangi stok

---

### 3️⃣ **ADMIN ASSIGN KURIR** (Manual/Optional)

**Lokasi**: Admin Panel (belum dibuat, bisa ditambahkan)

**Alur**:
1. Admin melihat order dengan status 'paid'
2. Admin assign kurir ke order:
   ```php
   $tracking->update([
       'driver_id' => $driverId,
       'status' => 'assigned'
   ]);
   ```
3. Status tracking berubah: `pending` → `assigned`

---

### 4️⃣ **KURIR MULAI TRACKING** (Update Lokasi)

**Lokasi**: API Endpoint `/api/tracking/{orderId}/location`

**Alur**:
1. Kurir membuka aplikasi/website kurir
2. Kurir melihat order yang di-assign
3. Kurir mulai perjalanan → klik "Mulai Pengiriman"
4. **Update Status**:
   ```php
   POST /api/tracking/{orderId}/status
   {
       "status": "picked"  // atau "on_the_way"
   }
   ```
   - Status: `assigned` → `picked` → `on_the_way`
   - `picked_at` atau `on_the_way_at` diisi

5. **Update Lokasi** (setiap 5-10 detik):
   ```php
   POST /api/tracking/{orderId}/location
   {
       "latitude": -6.2088,
       "longitude": 106.8456,
       "address": "Jl. Contoh No. 123"
   }
   ```
   - Sistem update `latitude`, `longitude`, `address`
   - Sistem **otomatis hitung ETA**:
     - Hitung jarak dari lokasi kurir ke tujuan (Haversine formula)
     - Estimasi waktu: jarak / 30 km/h (kecepatan rata-rata)
     - Update `estimated_minutes` dan `distance_km`

---

### 5️⃣ **CUSTOMER MELIHAT TRACKING** (Real-time)

**Lokasi**: Halaman `/orders/{orderNumber}/track`

**Alur**:
1. Customer buka halaman tracking
2. Sistem cek:
   - Apakah order menggunakan instant delivery? (`shipping_method_id == 1` atau `type === 'instant'`)
   - Jika ya, tampilkan map

3. **Inisialisasi Map**:
   - Load Leaflet.js
   - Tampilkan marker **tujuan** (merah) dari `shipping_address.latitude/longitude`
   - Jika ada lokasi kurir, tampilkan marker **kurir** (hijau)
   - Jika ada kedua marker, gambar garis route

4. **Auto-Refresh** (setiap 5 detik):
   ```javascript
   setInterval(() => {
       fetch(`/api/tracking/${orderNumber}`)
           .then(data => {
               // Update marker kurir jika ada lokasi baru
               // Update ETA
               // Update status badge
           });
   }, 5000);
   ```

5. **Data yang ditampilkan**:
   - Status tracking (Menunggu, Dalam Perjalanan, Sudah Sampai)
   - ETA (Estimasi Waktu Tiba)
   - Jarak tersisa
   - Nama kurir (jika sudah di-assign)
   - Timeline pengiriman

---

### 6️⃣ **KURIR SAMPAI DI TUJUAN**

**Lokasi**: API Endpoint `/api/tracking/{orderId}/status`

**Alur**:
1. Kurir klik "Sudah Sampai" di aplikasi
2. **Update Status**:
   ```php
   POST /api/tracking/{orderId}/status
   {
       "status": "arrived"
   }
   ```
   - Status: `on_the_way` → `arrived`
   - `arrived_at` = timestamp sekarang
   - ETA = 0 menit

3. Customer melihat update di map:
   - Marker kurir sekarang di lokasi tujuan
   - Status badge: "Sudah Sampai"
   - ETA: "0 menit"

---

### 7️⃣ **ORDER SELESAI (DELIVERED)**

**Lokasi**: API Endpoint `/api/tracking/{orderId}/status`

**Alur**:
1. Kurir klik "Pesanan Terkirim" setelah customer terima
2. **Update Status**:
   ```php
   POST /api/tracking/{orderId}/status
   {
       "status": "delivered"
   }
   ```
   - Status tracking: `arrived` → `delivered`
   - **Auto-update Order**:
     ```php
     $order->update([
         'status' => 'delivered',
         'delivered_at' => now()
     ]);
     ```

3. Tracking selesai, map tidak perlu update lagi

---

## 📊 Database Flow

### Tabel `orders`
```
id | shipping_method_id | status | shipping_address (JSON)
16 | 1                  | paid   | {latitude, longitude, address, ...}
```

### Tabel `delivery_tracking`
```
id | order_id | driver_id | status | latitude | longitude | estimated_minutes
1  | 16       | NULL      | pending| NULL     | NULL      | NULL
```

**Saat kurir mulai tracking**:
```
id | order_id | driver_id | status      | latitude  | longitude | estimated_minutes
1  | 16       | 5         | on_the_way  | -6.2088   | 106.8456  | 15
```

---

## 🔄 Update Real-time (Polling Method)

**Cara Kerja**:
1. Customer buka halaman tracking
2. JavaScript `setInterval` setiap 5 detik
3. AJAX call ke `/api/tracking/{orderNumber}`
4. Response berisi:
   - Lokasi kurir terbaru (jika ada)
   - Status tracking
   - ETA terbaru
5. Update map marker dan UI

**Keuntungan**:
- ✅ Mudah diimplementasikan
- ✅ Tidak perlu setup WebSocket
- ✅ Bekerja di semua browser

**Kekurangan**:
- ⚠️ Update setiap 5 detik (bukan real-time instant)
- ⚠️ Lebih banyak request ke server

**Upgrade ke WebSocket** (Future):
- Bisa upgrade ke Laravel Echo + Pusher
- Update real-time tanpa polling
- Lebih efisien untuk banyak user

---

## 🎯 Status Tracking

| Status | Deskripsi | Kapan Terjadi |
|--------|-----------|---------------|
| `pending` | Menunggu | Saat order dibuat, belum ada kurir |
| `assigned` | Kurir Ditetapkan | Admin assign kurir (optional) |
| `picked` | Pesanan Diambil | Kurir ambil pesanan dari store |
| `on_the_way` | Dalam Perjalanan | Kurir mulai perjalanan, lokasi mulai di-update |
| `arrived` | Sudah Sampai | Kurir sampai di alamat tujuan |
| `delivered` | Terkirim | Customer terima pesanan |

---

## 📱 Untuk Aplikasi Kurir (Future)

**Fitur yang perlu dibuat**:
1. **Aplikasi Mobile** (React Native / Flutter)
2. **Background GPS Tracking**:
   - Update lokasi otomatis setiap 5-10 detik
   - Berjalan di background
   - Menggunakan GPS device
3. **Push Notification**:
   - Notifikasi order baru
   - Notifikasi update status
4. **Offline Mode**:
   - Simpan lokasi di local storage
   - Sync saat online kembali

---

## 🔧 API Endpoints

### 1. Get Tracking Data (Customer)
```
GET /api/tracking/{orderNumber}
Response: {
    success: true,
    tracking: {
        status: "on_the_way",
        current_location: {lat, lng, address},
        estimated_minutes: 15,
        formatted_eta: "15 menit",
        driver: {name, phone}
    },
    order: {
        destination: {lat, lng, address}
    }
}
```

### 2. Update Location (Kurir)
```
POST /api/tracking/{orderId}/location
Body: {
    latitude: -6.2088,
    longitude: 106.8456,
    address: "Jl. Contoh"
}
```

### 3. Update Status (Kurir)
```
POST /api/tracking/{orderId}/status
Body: {
    status: "picked" | "on_the_way" | "arrived" | "delivered"
}
```

---

## 🎨 UI/UX Flow

### Halaman Tracking Customer:
1. **Timeline Status** (atas)
   - ✅ Pesanan Dibuat
   - ✅ Pembayaran Berhasil
   - ⏳ Sedang Diproses
   - 🚚 Dalam Pengiriman (jika instant)
   - ✅ Terkirim

2. **Live Map** (tengah)
   - Marker merah: Tujuan
   - Marker hijau: Kurir (muncul saat tracking aktif)
   - Garis route: Jarak antara kurir dan tujuan
   - Auto-refresh setiap 5 detik

3. **Info Box** (bawah)
   - Status: "Dalam Perjalanan"
   - ETA: "15 menit"
   - Jarak: "5.2 km"
   - Kurir: "Nama Kurir" (jika sudah di-assign)

---

## ✅ Checklist Implementasi

- [x] Database migration untuk `delivery_tracking`
- [x] Model `DeliveryTracking`
- [x] Auto-create tracking saat order dibuat (instant delivery)
- [x] Auto-create tracking saat pembayaran berhasil
- [x] API endpoint get tracking (customer)
- [x] API endpoint update location (kurir)
- [x] API endpoint update status (kurir)
- [x] Halaman tracking dengan map
- [x] Auto-refresh lokasi (polling)
- [x] ETA calculation (Haversine formula)
- [x] Timeline pengiriman
- [ ] Admin panel untuk assign kurir (optional)
- [ ] Aplikasi mobile untuk kurir (future)
- [ ] WebSocket untuk real-time update (future)

---

## 🚀 Cara Menggunakan

### Untuk Customer:
1. Buat order dengan **Pengiriman Instan**
2. Bayar order
3. Buka halaman tracking: `/orders/{orderNumber}/track`
4. Lihat map dan status real-time

### Untuk Kurir (Manual via API):
1. Login sebagai kurir
2. Update status: `POST /api/tracking/{orderId}/status` dengan `status: "picked"`
3. Update lokasi setiap 5-10 detik: `POST /api/tracking/{orderId}/location`
4. Update status saat sampai: `status: "arrived"`
5. Update status saat selesai: `status: "delivered"`

---

## 📝 Catatan Penting

1. **Tracking hanya untuk Instant Delivery**: 
   - Hanya order dengan `shipping_method_id = 1` atau `type = 'instant'`
   - Order dengan metode lain tidak akan ada tracking

2. **Koordinat Wajib**:
   - `shipping_address` harus punya `latitude` dan `longitude`
   - Jika tidak ada, map tidak bisa menampilkan tujuan

3. **Fallback untuk ID 1**:
   - Karena shipping_method ID 1 adalah instant, sistem menggunakan fallback
   - Jika `ShippingMethod::find(1)` tidak ditemukan, tetap dianggap instant

4. **Polling Interval**:
   - Default: 5 detik
   - Bisa diubah di JavaScript sesuai kebutuhan
   - Semakin sering = lebih real-time tapi lebih banyak request
