@extends('layouts.app')

@section('title', 'Detail Pesanan - ' . $order->order_number)

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

/* Order detail styles */
.order-info-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
}

.order-info-card h5 {
    color: #137440;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
}

.order-info-card h5 i {
    margin-right: 0.5rem;
}

.order-table {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.order-table .table {
    margin-bottom: 0;
}

.order-table .table th {
    background: #f8fafc;
    border-bottom: 2px solid #137440;
    color: #1f2937;
    font-weight: 600;
    padding: 1rem;
}

.order-table .table td {
    padding: 1rem;
    vertical-align: middle;
}

.product-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
}

.product-image-placeholder {
    width: 50px;
    height: 50px;
    background: #f3f4f6;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
}

.badge {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
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
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <!-- Single Page Header Start -->
    <div class="container-fluid page-header py-5" style="background: linear-gradient(135deg, #137440 0%, #0f5d33 100%);">
        <h1 class="text-center text-white display-6">Detail Pesanan</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-white">Pages</a></li>
            <li class="breadcrumb-item active text-white">Detail Pesanan #{{ $order->order_number }}</li>
        </ol>
    </div>
    <!-- Single Page Header End -->
    
        <div class="dashboard-container">
            <!-- Order Info Cards -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="order-info-card">
                        <h5><i class="bx bx-info-circle"></i> Informasi Pesanan</h5>
                        <div class="row">
                            <div class="col-12">
                                <p><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                                <p><strong>Status:</strong> <span class="badge bg-{{ $order->status_color ?? 'secondary' }}">{{ $order->formatted_status }}</span></p>
                                <p><strong>Total:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="order-info-card">
                        <h5><i class="bx bx-credit-card"></i> Informasi Pembayaran</h5>
                        @if($order->paymentTransactions->isNotEmpty())
                            @php($paymentTransaction = $order->paymentTransactions->first())
                            <p><strong>Metode:</strong> {{ $paymentTransaction->payment_method ?? '-' }}</p>
                            <p><strong>Status Pembayaran:</strong> <span class="badge bg-{{ $paymentTransaction->status_color ?? 'secondary' }}">{{ $paymentTransaction->formatted_status ?? $paymentTransaction->status }}</span></p>
                        @else
                            <p><strong>Metode:</strong> {{ $order->payment_method ?? '-' }}</p>
                            <p><strong>Status Pembayaran:</strong> <span class="badge bg-{{ $order->status_color ?? 'secondary' }}">{{ $order->formatted_status }}</span></p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Items Table -->
            <div class="order-table">
                <h5 class="mb-4"><i class="bx bx-package"></i> Item Pesanan</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($item->product && $item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}" class="product-image">
                                                @else
                                                    <div class="product-image-placeholder">
                                                        <i class="bx bx-package"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-medium text-dark">{{ $item->product_name }}</div>
                                                @if($item->product)
                                                    <small class="text-muted">{{ $item->product->category ?? '' }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-medium">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="fw-bold text-primary">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th class="text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-2"></i>Kembali
                </a>
                
                @if(in_array($order->status, ['paid', 'processing', 'shipped']))
                    <a href="{{ route('orders.track', base64_encode($order->order_number)) }}" class="btn btn-success">
                        <i class="bx bx-map me-2"></i>Lacak Pesanan
                    </a>
                @endif
                
                @if(in_array($order->status, ['delivered', 'completed']))
                    <a href="{{ route('orders.invoice', base64_encode($order->order_number)) }}" class="btn btn-info">
                        <i class="bx bx-download me-2"></i>Download Invoice
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection
