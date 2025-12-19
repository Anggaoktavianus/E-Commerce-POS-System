# Troubleshooting WhatsApp API

## Error: Operation timed out

### Penyebab
- Server NusaGateway sedang lambat atau sibuk
- Koneksi internet tidak stabil
- Firewall memblokir koneksi ke API

### Solusi

#### 1. Cek Koneksi Internet
Pastikan server aplikasi memiliki koneksi internet yang stabil.

#### 2. Cek Token WhatsApp
Pastikan token di `.env` sudah benar:
```env
WHATSAPP_TOKEN=VMoffahoDaBaO6DNvn4biBwIjSKtIHlvUUUR1TAYKMeQmz48E9
```

#### 3. Test Koneksi ke API
Test dengan curl manual:
```bash
curl -X POST "http://nusagateway.com/api/send-message.php" \
  -d "token=VMoffahoDaBaO6DNvn4biBwIjSKtIHlvUUUR1TAYKMeQmz48E9" \
  -d "phone=082222205204" \
  -d "message=Test"
```

#### 4. Gunakan Alternatif Email
Jika WhatsApp terus timeout, user bisa menggunakan metode email sebagai alternatif.

#### 5. Cek Log
Cek detail error di `storage/logs/laravel.log`:
```bash
tail -f storage/logs/laravel.log | grep WhatsApp
```

#### 6. Konfigurasi Timeout
Timeout sudah diatur menjadi 90 detik. Jika masih timeout, mungkin server API sedang down.

### Konfigurasi yang Sudah Diperbaiki

1. **Timeout ditingkatkan**: 30 detik → 60 detik
2. **Connection timeout**: 20 detik terpisah
3. **Error handling**: Pesan error lebih informatif
4. **Retry mechanism**: Otomatis retry 1x jika timeout
5. **POST data format**: Menggunakan array langsung (sesuai contoh API)
6. **SSL verification**: Dinonaktifkan untuk kompatibilitas

### Alternatif

Jika masalah terus berlanjut:
1. Gunakan metode email sebagai fallback
2. Kontak provider NusaGateway untuk cek status API
3. Pertimbangkan menggunakan queue untuk mengirim WhatsApp (async)
