# Konfigurasi Email SMTP

## Pengaturan di file .env

Tambahkan atau perbarui konfigurasi berikut di file `.env`:

**OPSI 1: Port 465 dengan SSL (Recommended)**
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD="your-secure-password"
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

**OPSI 2: Port 587 dengan TLS (Alternatif jika OPSI 1 tidak berhasil)**
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD="your-secure-password"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

**PENTING:**
- Password harus di-quote dengan tanda kutip ganda `"` karena mengandung karakter khusus
- Setelah mengubah .env, jalankan: `php artisan config:clear`

## Penjelasan Konfigurasi

- **MAIL_MAILER**: Menggunakan `smtp` untuk mengirim email melalui SMTP server
- **MAIL_HOST**: Server SMTP (contoh: `mail.yourdomain.com` atau `smtp.gmail.com`)
- **MAIL_PORT**: Port SMTP (`465` untuk SSL, `587` untuk TLS)
- **MAIL_USERNAME**: Username untuk autentikasi (email atau username SMTP Anda)
- **MAIL_PASSWORD**: Password SMTP Anda (di-quote jika mengandung karakter khusus)
- **MAIL_ENCRYPTION**: Enkripsi SSL untuk port 465, atau TLS untuk port 587
- **MAIL_FROM_ADDRESS**: Alamat email pengirim (bisa berbeda dengan username)
- **MAIL_FROM_NAME**: Nama pengirim (menggunakan APP_NAME dari .env)

## Informasi Server (Contoh untuk cPanel)

- **Server Masuk (IMAP)**: mail.yourdomain.com (Port: 993)
- **Server Masuk (POP3)**: mail.yourdomain.com (Port: 995)
- **Server Keluar (SMTP)**: mail.yourdomain.com (Port: 465 untuk SSL)
- **Nama Pengguna**: your-email@yourdomain.com
- **Kata Sandi**: Gunakan kata sandi email atau cPanel Anda
- **IMAP, POP3, dan SMTP requires authentication**: Ya

**PENTING KEAMANAN:**
- Jangan pernah commit file `.env` ke repository
- Jangan sertakan password real di dokumentasi
- Simpan credentials di environment variables atau secret management

## Testing Email

Setelah mengatur konfigurasi, test dengan:

1. Jalankan `php artisan tinker`
2. Ketik: `Mail::raw('Test email', function($message) { $message->to('your-email@example.com')->subject('Test'); });`
3. Atau gunakan fitur "Lupa Password" di halaman login

## Catatan

- Pastikan password tidak mengandung karakter khusus yang perlu di-escape di .env
- Jika ada masalah, cek log di `storage/logs/laravel.log`
- Untuk development, bisa menggunakan `MAIL_MAILER=log` untuk melihat email di log file
