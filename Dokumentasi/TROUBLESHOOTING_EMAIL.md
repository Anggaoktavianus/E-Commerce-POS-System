# Troubleshooting Email SMTP

## Error: 535 Incorrect authentication data

### Solusi 1: Quote Password di .env
Password yang mengandung karakter khusus harus di-quote:

```env
# SALAH:
MAIL_PASSWORD=password-with-special-chars

# BENAR:
MAIL_PASSWORD="password-with-special-chars"
```

### Solusi 2: Coba Port 587 dengan TLS
Beberapa server email lebih kompatibel dengan port 587:

```env
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

### Solusi 3: Pastikan Username Benar
Beberapa server memerlukan full email, beberapa hanya username:

```env
# Coba full email:
MAIL_USERNAME=your-email@yourdomain.com

# Atau coba hanya username (tanpa @domain):
MAIL_USERNAME=info
```

### Solusi 4: Clear Config Cache
Setelah mengubah .env:

```bash
php artisan config:clear
php artisan cache:clear
```

### Solusi 5: Test dengan Tinker
Test koneksi SMTP:

```bash
php artisan tinker
```

Kemudian jalankan:
```php
use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('Test email', function($message) {
        $message->to('your-email@example.com')
                ->subject('Test Email');
    });
    echo "Email sent successfully!";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Solusi 6: Cek Log
Cek error detail di:
- `storage/logs/laravel.log`
- Browser console (F12) untuk error JavaScript

### Solusi 7: Verifikasi Kredensial
Pastikan:
1. Email `your-email@yourdomain.com` sudah aktif
2. Password benar (copy-paste untuk menghindari typo)
3. Server SMTP `mail.yourdomain.com` dapat diakses dari server aplikasi
4. Firewall tidak memblokir port 465 atau 587

### Solusi 8: Gunakan Mailtrap untuk Testing
Untuk development, gunakan Mailtrap:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
```

### Catatan
- Password dengan karakter khusus seperti `=`, `^`, `{`, `}` harus selalu di-quote
- Setelah perubahan .env, selalu clear config cache
- Beberapa hosting memerlukan whitelist IP untuk SMTP
