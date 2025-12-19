# Quick Start Guide - Stock Management System

## 🚀 Panduan Cepat untuk Admin

### 1. Melihat Dashboard Stok

**Langkah:**
1. Login ke admin panel
2. Buka **Dashboard Admin**
3. Lihat card **Stok Menipis** di bagian atas
4. Scroll ke bawah untuk melihat tabel **Produk dengan Stok Menipis**

**Informasi yang Ditampilkan:**
- Jumlah produk dengan stok menipis (≤10)
- Jumlah produk habis (≤0)
- Daftar 10 produk dengan stok terendah

---

### 2. Menyesuaikan Stok Produk

**Langkah:**
1. Buka **Manajemen Toko** → **Produk**
2. Klik tombol **Sesuaikan Stok** (ikon adjust) pada produk
3. Pilih tipe:
   - **Set Stok**: Atur jumlah stok langsung
   - **Tambah Stok**: Tambahkan stok
   - **Kurangi Stok**: Kurangi stok
4. Masukkan jumlah
5. (Opsional) Tambahkan catatan
6. Klik **Simpan Perubahan**

**Contoh:**
- Stok saat ini: 50
- Tipe: Tambah Stok
- Jumlah: 20
- Stok baru: 70

---

### 3. Melihat Riwayat Perubahan Stok

**Cara 1: Semua Riwayat**
1. Buka **Manajemen Toko** → **Riwayat Stok**
2. Gunakan filter untuk mencari:
   - Filter by produk
   - Filter by tipe perubahan
3. Klik **Export Riwayat** untuk download CSV

**Cara 2: Riwayat per Produk**
1. Buka **Manajemen Toko** → **Produk**
2. Klik tombol **Riwayat Stok** (ikon history) pada produk
3. Lihat semua perubahan stok untuk produk tersebut

---

### 4. Export Laporan Stok

**Export Ringkasan Stok:**
1. Buka **Riwayat Stok**
2. Klik **Export Ringkasan Stok**
3. File CSV akan terdownload

**Export Riwayat Perubahan:**
1. Buka **Riwayat Stok**
2. (Opsional) Filter data
3. Klik **Export Riwayat**
4. File CSV akan terdownload

---

## 📊 Tipe Perubahan Stok

| Tipe | Deskripsi | Kapan Terjadi |
|------|-----------|---------------|
| **in** | Stock Masuk | Stock masuk dari supplier (manual) |
| **out** | Stock Keluar | Order dibayar dan diproses |
| **adjustment** | Penyesuaian Manual | Admin melakukan penyesuaian manual |
| **restore** | Restore Stock | Order dibatalkan/gagal dan stok dikembalikan |

---

## ⚠️ Alert Stok

### Low Stock Alert
- **Kondisi**: Stok ≤ 10 dan > 0
- **Tampilan**: Badge kuning "Terbatas"
- **Aksi**: Segera tambah stok

### Out of Stock Alert
- **Kondisi**: Stok ≤ 0
- **Tampilan**: Badge merah "Habis"
- **Aksi**: Tambah stok atau nonaktifkan produk

---

## 🔍 Tips & Best Practices

1. **Cek Dashboard Setiap Hari**
   - Lihat alert stok menipis
   - Prioritaskan produk dengan stok terendah

2. **Gunakan Catatan saat Adjust Stok**
   - Catat alasan penyesuaian
   - Contoh: "Stok masuk dari supplier", "Koreksi stok", dll

3. **Export Laporan Berkala**
   - Export ringkasan stok setiap minggu
   - Export riwayat perubahan setiap bulan
   - Gunakan untuk analisis dan audit

4. **Monitor Riwayat Perubahan**
   - Cek riwayat jika ada ketidaksesuaian stok
   - Gunakan filter untuk mencari perubahan tertentu

5. **Validasi Sebelum Adjust**
   - Pastikan jumlah yang diinput benar
   - Lihat preview stok baru sebelum simpan

---

## 🆘 Troubleshooting Cepat

**Q: Stok tidak berkurang setelah order dibayar?**
A: Check log atau pastikan `ProcessOrderJob` berjalan dengan baik.

**Q: Stok tidak restore saat order dibatalkan?**
A: Pastikan order sudah pernah diproses (ada `processed_at`).

**Q: Export CSV tidak bisa dibuka?**
A: Buka dengan Google Sheets atau pastikan Excel mendukung UTF-8.

**Q: Real-time check tidak bekerja?**
A: Refresh halaman atau clear browser cache.

---

**Last Updated**: 2025-12-11
