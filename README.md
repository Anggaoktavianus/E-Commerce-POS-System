# Samsae - E-Commerce & POS System

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Samsae adalah sistem e-commerce lengkap dengan fitur Point of Sales (POS) dan Kasir yang terintegrasi. Sistem ini dirancang untuk mendukung penjualan online dan offline dalam satu platform terpusat.

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [API Documentation](#-api-documentation)
- [Testing](#-testing)
- [Documentation](#-documentation)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Fitur Utama

### 🛒 E-Commerce
- **Product Management**: Manajemen produk dengan kategori, gambar, dan variasi
- **Shopping Cart**: Keranjang belanja dengan session management
- **Checkout System**: Proses checkout dengan validasi lengkap
- **Payment Gateway**: Integrasi Midtrans untuk pembayaran online
- **Order Management**: Manajemen pesanan dengan tracking status
- **Customer Dashboard**: Dashboard pelanggan dengan riwayat pesanan
- **Wishlist**: Fitur wishlist untuk pelanggan
- **Coupon System**: Sistem kupon dan diskon
- **Loyalty Points**: Program poin loyalitas
- **Product Reviews**: Sistem review produk
- **Search & Filter**: Pencarian dan filter produk

### 🏪 Multi-Store & Multi-Outlet
- **Store Management**: Manajemen multiple store
- **Outlet Management**: Manajemen multiple outlet per store
- **Store Themes**: Template berbeda per store
- **Inventory per Outlet**: Stok terpisah per outlet

### 💰 Point of Sales (POS) & Kasir
- **Shift Management**: Buka/tutup shift dengan opening/closing balance
- **Transaction Processing**: Proses transaksi cepat dengan keyboard shortcuts
- **Multiple Payment Methods**: Cash, Card, E-Wallet, QRIS, Split Payment
- **Barcode Scanner**: Scan barcode produk (hardware & camera)
- **Customer Lookup**: Pencarian pelanggan cepat
- **Discount System**: Diskon item, transaksi, kupon, dan member
- **Loyalty Points Redemption**: Tukar poin loyalitas saat transaksi
- **Receipt Printing**: Print struk thermal printer & PDF
- **Receipt Template Editor**: Editor template struk custom
- **Cash Movement**: Deposit, withdrawal, dan transfer kas
- **Auto-save Draft**: Auto-save draft transaksi ke localStorage
- **Real-time Inventory**: Update stok real-time saat transaksi

### 📊 Reporting & Analytics
- **Sales Reports**: Laporan penjualan harian, produk, kategori
- **Payment Reports**: Laporan metode pembayaran
- **Cashier Performance**: Laporan performa kasir
- **Export Features**: Export ke CSV dan PDF
- **Dashboard Analytics**: Dashboard dengan statistik real-time

### 📦 Inventory Management
- **Stock Management**: Manajemen stok global dan per outlet
- **Stock Movement**: Tracking pergerakan stok
- **Low Stock Alerts**: Notifikasi stok rendah
- **Stock History**: Riwayat perubahan stok

### 🚚 Shipping & Delivery
- **Multiple Shipping Methods**: Berbagai metode pengiriman
- **Distance-based Shipping**: Ongkir berdasarkan jarak
- **Delivery Tracking**: Tracking pengiriman real-time
- **Smart Shipping Service**: Kalkulasi ongkir otomatis

### 📱 Mobile API
- **RESTful API**: API untuk aplikasi mobile
- **Authentication**: JWT authentication
- **Mobile-optimized**: Endpoint khusus untuk mobile

### 🔔 Notifications
- **Email Notifications**: Notifikasi via email
- **WhatsApp Integration**: Notifikasi via WhatsApp (opsional)
- **Order Notifications**: Notifikasi status pesanan

### 🎨 Content Management
- **Dynamic Homepage**: Homepage dinamis dengan CMS
- **Banner Management**: Manajemen banner
- **Carousel/Slider**: Manajemen carousel
- **Features Section**: Manajemen fitur unggulan
- **Testimonials**: Manajemen testimoni
- **Articles/Blog**: Sistem artikel dan blog
- **Pages**: Halaman statis custom

## 🛠 Tech Stack

### Backend
- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Database**: MySQL/MariaDB
- **Cache**: Redis (optional)
- **Queue**: Database/Redis

### Frontend
- **Template Engine**: Blade
- **CSS Framework**: Bootstrap 5, Tailwind CSS 4
- **JavaScript**: Vanilla JS, jQuery
- **UI Library**: Sneat Admin Template
- **Icons**: Boxicons

### Third-party Services
- **Payment**: Midtrans
- **PDF Generation**: Dompdf
- **Image Processing**: Intervention Image
- **DataTables**: Yajra DataTables

### Development Tools
- **Testing**: PHPUnit
- **Code Style**: Laravel Pint
- **Package Manager**: Composer, NPM

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18.x & NPM
- MySQL >= 8.0 atau MariaDB >= 10.5
- Redis (optional, untuk cache & queue)
- Web Server (Apache/Nginx)
- Extension PHP:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML
  - GD atau Imagick (untuk image processing)

## 🚀 Installation

### 1. Clone Repository

```bash
git clone https://github.com/Anggaoktavianus/E-Commerce-POS-System.git
cd E-Commerce-POS-System
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment

Edit `.env` file dan sesuaikan konfigurasi:

```env
APP_NAME=Samsae
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=samsae
DB_USERNAME=root
DB_PASSWORD=

# Cache & Queue (optional)
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"

# Midtrans Configuration
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false

# WhatsApp Configuration (optional)
WHATSAPP_API_URL=
WHATSAPP_API_KEY=
```

### 5. Database Setup

```bash
# Run migrations
php artisan migrate

# Run seeders (optional)
php artisan db:seed
```

### 6. Storage Link

```bash
# Create symbolic link for storage
php artisan storage:link
```

### 7. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Start Development Server

```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

## ⚙️ Configuration

### Admin Access

Setelah instalasi, buat user admin pertama:

```bash
php artisan tinker
```

```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@example.com'; // Ganti dengan email admin Anda
$user->password = Hash::make('your-secure-password'); // Ganti dengan password yang aman
$user->role = 'admin';
$user->is_verified = true;
$user->save();
```

### POS Configuration

1. Login sebagai admin
2. Buat Store dan Outlet
3. Konfigurasi POS Settings di: `Admin > POS & Kasir > Settings`
4. Buka shift pertama di: `Admin > POS & Kasir > Shift Management`

### Payment Gateway

1. Daftar di [Midtrans](https://midtrans.com)
2. Dapatkan Server Key dan Client Key
3. Update di `.env`:
   ```env
   MIDTRANS_SERVER_KEY=your_midtrans_server_key
   MIDTRANS_CLIENT_KEY=your_midtrans_client_key
   MIDTRANS_IS_PRODUCTION=false
   ```

### Email Configuration

Konfigurasi SMTP di `.env` untuk email notifications.

## 📖 Usage

### E-Commerce

1. **Customer Registration**: Daftar sebagai customer
2. **Browse Products**: Jelajahi produk dan kategori
3. **Add to Cart**: Tambah produk ke keranjang
4. **Checkout**: Proses checkout dengan pilih shipping
5. **Payment**: Bayar via Midtrans
6. **Track Order**: Track pesanan di dashboard

### POS System

1. **Open Shift**: Buka shift dengan opening balance
2. **Create Transaction**: Buat transaksi baru
3. **Add Products**: Scan/tambah produk ke cart
4. **Apply Discounts**: Terapkan diskon jika ada
5. **Process Payment**: Proses pembayaran
6. **Print Receipt**: Print struk
7. **Close Shift**: Tutup shift dengan closing balance

### Admin Panel

- **Dashboard**: Overview statistik
- **Products**: Kelola produk
- **Orders**: Kelola pesanan
- **POS & Kasir**: Manajemen POS
- **Reports**: Lihat laporan
- **Settings**: Konfigurasi sistem

## 📚 API Documentation

### Mobile API Endpoints

Base URL: `/api/mobile`

#### Authentication
- `POST /auth/login` - Login
- `POST /auth/register` - Register
- `POST /auth/logout` - Logout

#### Products
- `GET /products` - List products
- `GET /products/{id}` - Product detail
- `GET /products/search` - Search products

#### Cart
- `GET /cart` - Get cart
- `POST /cart/add` - Add to cart
- `PUT /cart/update` - Update cart
- `DELETE /cart/remove/{id}` - Remove from cart

#### Orders
- `POST /orders` - Create order
- `GET /orders` - List orders
- `GET /orders/{id}` - Order detail
- `GET /orders/{id}/track` - Track order

Lihat dokumentasi lengkap di: `Dokumentasi/API_DOCUMENTATION.md`

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter PosTransactionFlowTest

# Run with coverage
php artisan test --coverage
```

### Test Files

- `tests/Unit/` - Unit tests
- `tests/Feature/` - Feature/Integration tests

## 📄 Documentation

Dokumentasi lengkap tersedia di folder `Dokumentasi/`:

- [POS & Kasir Rancangan](./Dokumentasi/POS_KASIR_RANCANGAN.md)
- [POS & Kasir Integrasi](./Dokumentasi/POS_KASIR_INTEGRASI.md)
- [POS & Kasir Checklist](./Dokumentasi/POS_KASIR_CHECKLIST.md)
- [POS & Kasir Final Status](./Dokumentasi/POS_KASIR_FINAL_STATUS.md)
- [Database Diagram](./Dokumentasi/POS_KASIR_DATABASE_DIAGRAM.md)
- [Implementation Examples](./Dokumentasi/POS_KASIR_IMPLEMENTATION_EXAMPLES.md)
- [Shipping Methods](./Dokumentasi/SHIPPING_METHODS_SUMMARY.md)
- [Email Configuration](./Dokumentasi/EMAIL_CONFIG.md)
- [WhatsApp Configuration](./Dokumentasi/WHATSAPP_CONFIG.md)

Lihat [Dokumentasi/README.md](./Dokumentasi/README.md) untuk daftar lengkap.

## 🏗 Project Structure

```
samsae/
├── app/
│   ├── Console/Commands/     # Artisan commands
│   ├── Http/
│   │   ├── Controllers/      # Controllers
│   │   │   ├── Admin/        # Admin controllers
│   │   │   ├── Auth/         # Auth controllers
│   │   │   └── Mobile/       # Mobile API controllers
│   │   ├── Middleware/       # Middleware
│   │   └── Requests/         # Form requests
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic services
│   └── Jobs/                  # Queue jobs
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/             # Database seeders
├── resources/
│   ├── views/                # Blade templates
│   │   ├── admin/           # Admin views
│   │   ├── pages/           # Frontend views
│   │   └── mobile/          # Mobile views
│   ├── css/                 # CSS files
│   └── js/                  # JavaScript files
├── routes/
│   └── web.php              # Web routes
├── tests/                    # Tests
├── Dokumentasi/             # Documentation
└── public/                  # Public assets
```

## 🔒 Security

- **CSRF Protection**: Semua form dilindungi CSRF
- **XSS Protection**: Input validation dan sanitization
- **SQL Injection**: Menggunakan Eloquent ORM
- **Authentication**: Laravel authentication
- **Authorization**: Role-based access control
- **Password Hashing**: Bcrypt hashing
- **Security Headers**: Middleware untuk security headers

## 🤝 Contributing

Kontribusi sangat diterima! Silakan:

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

### Coding Standards

- Ikuti [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard
- Gunakan Laravel Pint untuk code formatting
- Tulis tests untuk fitur baru
- Update dokumentasi jika perlu

## 📝 Changelog

Lihat [CHANGELOG.md](CHANGELOG.md) untuk daftar perubahan.

## 🐛 Known Issues

- [ ] SQLite compatibility issue pada beberapa migrations (untuk testing)
- [ ] Barcode scanner hardware integration perlu konfigurasi tambahan

Lihat [Issues](https://github.com/Anggaoktavianus/E-Commerce-POS-System/issues) untuk daftar lengkap.

## 📞 Support

Untuk support dan pertanyaan:

- **Email**: support@example.com (Ganti dengan email support Anda)
- **Issues**: [GitHub Issues](https://github.com/Anggaoktavianus/E-Commerce-POS-System/issues)
- **Documentation**: [Dokumentasi](./Dokumentasi/README.md)

## 👥 Authors

- **Angga Oktavianus** - *Initial work* - [Anggaoktavianus](https://github.com/Anggaoktavianus)

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Sneat](https://themeselection.com/products/sneat-free-bootstrap-html-admin-template/) - Admin Template
- [Midtrans](https://midtrans.com) - Payment Gateway
- [Dompdf](https://github.com/dompdf/dompdf) - PDF Generation
- [Intervention Image](https://image.intervention.io/) - Image Processing

---

**Made with ❤️ using Laravel**
