@extends('layouts.app')

@section('title', 'Invoice - ' . $order->order_number)

@push('styles')
<style>
/* Custom Dashboard Styles */
.dashboard-wrapper {
    min-height: 100vh;
    padding: 0;
    margin-top: 0;
}

/* Fix navbar overlap - follow fruitables pattern */
body {
    padding-top: 0 !important;
}

/* Responsive dashboard wrapper */
@media (max-width: 992px) {
    .dashboard-wrapper {
        margin-top: 0;
    }
}

@media (max-width: 576px) {
    .dashboard-wrapper {
        margin-top: 0;
        padding: 0;
    }
}

.dashboard-container {
    background: white;
    border-radius: 0;
    padding: 2rem;
    box-shadow: none;
    border: none;
    min-height: calc(100vh - 80px);
}

/* Responsive container padding */
@media (max-width: 576px) {
    .dashboard-container {
        padding: 1rem;
        border-radius: 0;
    }
}

/* Card Styles */
.stat-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.05);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #137440, #0f5d33);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(19, 116, 64, 0.2);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.stat-icon.primary {
    background: linear-gradient(135deg, #137440, #0f5d33);
    color: white;
}

.stat-icon.success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.stat-icon.info {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
}

.stat-icon.warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.stat-icon.danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

/* Invoice Styles */
.invoice-container {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
}

.invoice-header {
    border-bottom: 2px solid #137440;
    padding-bottom: 2rem;
    margin-bottom: 2rem;
}

.invoice-header h3 {
    color: #137440;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.invoice-header p {
    color: #6b7280;
    margin-bottom: 0;
}

.invoice-section {
    margin-bottom: 2rem;
}

.invoice-section h3 {
    color: #137440;
    font-weight: 600;
    margin-bottom: 1.5rem;
    font-size: 1.25rem;
}

.info-section {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
}

.info-section h3 {
    color: #137440;
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.info-section p {
    margin-bottom: 0.5rem;
    color: #374151;
}

.invoice-table {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
}

.invoice-table .table {
    margin-bottom: 0;
}

.invoice-table .table th {
    background: #f8fafc;
    border-bottom: 2px solid #137440;
    color: #1f2937;
    font-weight: 600;
    padding: 1rem;
}

.invoice-table .table td {
    padding: 1rem;
    vertical-align: middle;
}

.invoice-footer {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    text-align: center;
    color: #6b7280;
}

.action-buttons {
    margin-top: 2rem;
}

.action-buttons .btn {
    margin-right: 1rem;
    margin-bottom: 0.5rem;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Print styles */
@media print {
    .dashboard-wrapper {
        padding: 0;
        margin: 0;
    }
    
    .page-header {
        display: none !important;
    }
    
    .action-buttons {
        display: none !important;
    }
    
    .invoice-container {
        box-shadow: none;
        border: 1px solid #000;
    }
}
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <!-- Single Page Header Start -->
    <div class="container-fluid page-header py-5" style="background: linear-gradient(135deg, #137440 0%, #0f5d33 100%);">
        <h1 class="text-center text-white display-6">Invoice</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-white">Pages</a></li>
            <li class="breadcrumb-item active text-white">Invoice #{{ $order->order_number }}</li>
        </ol>
    </div>
    <!-- Single Page Header End -->
    
    <div class="dashboard-container">
        <div class="invoice-container">
            <!-- Invoice Header -->
            <div class="invoice-header">
                <div class="row">
                    <div class="col-md-6">
                        <h3>INVOICE</h3>
                        <p><strong>No. Invoice:</strong> {{ $order->order_number }}</p>
                        <p><strong>Tanggal:</strong> {{ $order->created_at->format('d F Y') }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h3>SAMSAE STORE</h3>
                        <p>Jl. Majapahit No. 123</p>
                        <p>Surabaya, Jawa Timur</p>
                        <p>Indonesia</p>
                    </div>
                </div>
            </div>

            <!-- Customer & Order Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="info-section">
                        <h3>Informasi Pelanggan</h3>
                        <p><strong>Nama:</strong> {{ $order->user->name ?? '-' }}</p>
                        <p><strong>Email:</strong> {{ $order->user->email ?? '-' }}</p>
                        <p><strong>Telepon:</strong> {{ $order->user->phone ?? '-' }}</p>
                        <p><strong>Alamat:</strong> {{ is_array($order->shipping_address) ? $order->shipping_address['address'] ?? '-' : $order->shipping_address ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-section">
                        <h3>Informasi Pesanan</h3>
                        <p><strong>Status:</strong> <span class="badge bg-{{ $order->status_color ?? 'secondary' }}">{{ $order->formatted_status }}</span></p>
                        <p><strong>Tanggal Pesanan:</strong> {{ $order->created_at->format('d F Y H:i') }}</p>
                        @if($order->paymentTransactions->isNotEmpty())
                            @php($paymentTransaction = $order->paymentTransactions->first())
                            <p><strong>Metode Pembayaran:</strong> {{ $paymentTransaction->payment_method ?? '-' }}</p>
                            <p><strong>Status Pembayaran:</strong> <span class="badge bg-{{ $paymentTransaction->status_color ?? 'secondary' }}">{{ $paymentTransaction->formatted_status ?? $paymentTransaction->status }}</span></p>
                        @else
                            <p><strong>Metode Pembayaran:</strong> {{ $order->payment_method ?? '-' }}</p>
                            <p><strong>Status Pembayaran:</strong> <span class="badge bg-{{ $order->status_color ?? 'secondary' }}">{{ $order->formatted_status }}</span></p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="invoice-table">
                <h3 class="mb-4">Detail Item</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $item->product_name }}</strong>
                                    @if($item->product && $item->product->category)
                                        <br><small class="text-muted">{{ $item->product->category }}</small>
                                    @endif
                                </td>
                                <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-warning">
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td class="text-end"><strong class="text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Footer -->
            <div class="invoice-footer">
                <p><strong>Terima kasih atas pesanan Anda!</strong></p>
                <p>Invoice ini adalah bukti pembayaran yang sah. Harap simpan untuk dokumentasi Anda.</p>
                <p class="mb-0">Jika ada pertanyaan, silakan hubungi kami di info@samsae.com atau +62 31 1234 5678</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-2"></i>Kembali
            </a>
            
            <a href="{{ route('orders.show', base64_encode($order->order_number)) }}" class="btn btn-primary">
                <i class="bx bx-receipt me-2"></i>Detail Pesanan
            </a>
            
            <button onclick="window.print()" class="btn btn-success">
                <i class="bx bx-printer me-2"></i>Cetak Invoice
            </button>
        </div>
    </div>
@endsection
