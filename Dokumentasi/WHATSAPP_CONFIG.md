# Konfigurasi WhatsApp API

## Pengaturan di file .env

Tambahkan konfigurasi berikut di file `.env`:

```env
WHATSAPP_TOKEN=VMoffahoDaBaO6DNvn4biBwIjSKtIHlvUUUR1TAYKMeQmz48E9
WHATSAPP_API_URL=http://nusagateway.com/api/send-message.php
```

## Penjelasan

- **WHATSAPP_TOKEN**: Token API untuk NusaGateway WhatsApp
- **WHATSAPP_API_URL**: URL endpoint API NusaGateway (default: http://nusagateway.com/api/send-message.php)

## Fitur

Fitur reset password via WhatsApp memungkinkan pengguna untuk:
1. Memilih metode pengiriman: Email atau WhatsApp
2. Jika memilih WhatsApp, masukkan nomor telepon yang terdaftar
3. Menerima link reset password via WhatsApp
4. Link reset password berlaku selama 60 menit

## Catatan

- Nomor telepon harus terdaftar di tabel `users` (kolom `phone`)
- Format nomor telepon akan dinormalisasi (menghapus karakter non-numeric, menambahkan 0 di depan jika perlu)
- Token reset password disimpan dengan hash untuk keamanan
- Setelah mengubah .env, jalankan: `php artisan config:clear`
