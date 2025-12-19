# Panduan Restore Database

## ⚠️ PENTING

Jika database Anda terhapus, ikuti langkah-langkah berikut untuk restore dari backup.

## 📋 Langkah-langkah Restore

### Opsi 1: Menggunakan Script Restore (Recommended)

```bash
# Restore dari backup terbaru
./restore_database.sh db_samsae_new_2025-12-12.sql

# Atau backup lain
./restore_database.sh db_samsae_2025-12-01.sql
```

Script akan:
1. Membaca konfigurasi database dari `.env`
2. Membuat database jika belum ada
3. Restore semua data dari file SQL
4. Memberikan konfirmasi sebelum restore

### Opsi 2: Manual Restore via MySQL

```bash
# 1. Buat database (jika belum ada)
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS samsae CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Restore dari backup
mysql -u root -p samsae < db_samsae_new_2025-12-12.sql
```

### Opsi 3: Via phpMyAdmin atau MySQL Workbench

1. Buka phpMyAdmin atau MySQL Workbench
2. Pilih database `samsae` (atau buat baru)
3. Import file SQL: `db_samsae_new_2025-12-12.sql`

## 📁 File Backup yang Tersedia

1. **db_samsae_new_2025-12-18.sql** (Backup TERBARU - 18 Desember 2025) ⭐
   - Backup paling baru dan lengkap
   - Disarankan untuk restore

2. **db_samsae_new_2025-12-12.sql** (Backup - 12 Desember 2025)
   - File besar (90,228 lines)
   - Berisi data lengkap

3. **db_samsae_2025-12-01.sql** (Backup lama - 1 Desember 2025)
   - File lebih kecil (1,158 lines)
   - Mungkin hanya struktur atau data awal

## ⚙️ Setelah Restore

### 1. Jalankan Migrations (jika ada yang baru)

```bash
php artisan migrate
```

Ini akan menambahkan tabel/kolom baru yang belum ada di backup.

### 2. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 3. Verifikasi Database

```bash
# Cek tabel
php artisan tinker
>>> DB::table('users')->count()
>>> DB::table('products')->count()
>>> DB::table('orders')->count()
```

## 🔍 Troubleshooting

### Error: "Access denied"
- Pastikan username dan password MySQL benar di `.env`
- Cek apakah user memiliki permission untuk create/restore database

### Error: "Database already exists"
- Script akan tetap restore ke database yang ada
- Jika ingin fresh start, hapus database dulu:
  ```bash
  mysql -u root -p -e "DROP DATABASE samsae;"
  ```

### Error: "Table already exists"
- File SQL mungkin berisi `CREATE TABLE IF NOT EXISTS`
- Atau hapus semua tabel dulu sebelum restore

### Data tidak lengkap
- Cek apakah file SQL lengkap (tidak terpotong)
- Cek ukuran file SQL
- Coba restore dari backup lain

## 🛡️ Pencegahan di Masa Depan

### 1. Backup Otomatis

Tambahkan ke cron job untuk backup harian:

```bash
# Edit crontab
crontab -e

# Tambahkan (backup setiap hari jam 2 pagi)
0 2 * * * mysqldump -u root -p[password] samsae > /path/to/backup/samsae_$(date +\%Y\%m\%d).sql
```

### 2. Backup Sebelum Migration

```bash
# Backup sebelum migrate
mysqldump -u root -p samsae > backup_before_migrate_$(date +%Y%m%d_%H%M%S).sql

# Lalu migrate
php artisan migrate
```

### 3. Git Ignore untuk SQL Files

File SQL sudah di `.gitignore`, jadi tidak akan ter-commit.

### 4. Test Safety

Tests sekarang sudah aman dengan safety checks:
- Menggunakan SQLite in-memory (`:memory:`)
- Tidak akan menghapus database production/local
- Safety checks di `tests/TestCase.php`

## 📞 Bantuan

Jika restore gagal atau ada masalah:
1. Cek log error
2. Pastikan file SQL tidak corrupt
3. Cek permission database user
4. Coba restore manual via phpMyAdmin

---

**Catatan:** File SQL backup tidak di-commit ke GitHub untuk keamanan. Simpan backup di tempat yang aman!
