@extends('layouts.app')

@section('title', 'Pembayaran Selesai - ' . config('app.name'))

@section('meta_description', 'Pembayaran Anda telah diproses di ' . config('app.name') . '. Terima kasih atas pesanan Anda.')

@section('meta_keywords', 'pembayaran selesai, sukses, order, ' . config('app.name') . ', terima kasih')

@section('og_image', asset('storage/defaults/og-payment.jpg'))

@push('styles')
<style>
    .status-checking {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
</style>
@endpush

@section('content')
@include('partials.modern-page-header', [
    'pageTitle' => 'Status Pembayaran',
    'breadcrumbItems' => [
        ['label' => 'Beranda', 'url' => url('/')],
        ['label' => 'Toko', 'url' => route('shop')],
        ['label' => 'Keranjang', 'url' => route('cart')],
        ['label' => 'Checkout', 'url' => route('checkout')],
        ['label' => 'Status Pembayaran', 'url' => null]
    ]
])

<!-- Payment Status Page Start -->
<div class="container-fluid py-5" style="background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Status Card -->
                <div class="card shadow-lg">
                    <div class="card-body text-center py-5">
                        @if(isset($payment_status) && $payment_status === 'unfinish')
                            <div class="mb-4">
                                <i class="bx bx-x-circle text-warning" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="text-warning mb-3">Pembayaran Dibatalkan</h2>
                            <p class="text-muted mb-4">{{ $message ?? 'Anda telah membatalkan pembayaran. Silakan coba lagi jika masih berminat.' }}</p>
                        @elseif(isset($payment_status) && $payment_status === 'error')
                            <div class="mb-4">
                                <i class="bx bx-error text-danger" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="text-danger mb-3">Terjadi Kesalahan</h2>
                            <p class="text-muted mb-4">{{ $message ?? 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.' }}</p>
                        @elseif($order->status === 'paid')
                            <div class="mb-4">
                                <i class="bx bx-check-circle text-success" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="text-success mb-3">Pembayaran Berhasil!</h2>
                            <p class="text-muted mb-4">Terima kasih atas pesanan Anda. Pembayaran telah berhasil diproses.</p>
                        @elseif($order->status === 'pending')
                            <div class="mb-4 status-checking">
                                <i class="bx bx-time text-warning" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="text-warning mb-3">Memproses Pembayaran</h2>
                            <p class="text-muted mb-4">Pesanan Anda sedang diproses. Halaman akan diperbarui otomatis.</p>
                            <div class="mb-3">
                                <div class="spinner-border text-warning" role="status">
                                    <span class="visually-hidden">Memuat...</span>
                                </div>
                            </div>
                        @elseif($order->status === 'failed')
                            <div class="mb-4">
                                <i class="bx bx-x-circle text-danger" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="text-danger mb-3">Pembayaran Gagal</h2>
                            <p class="text-muted mb-4">Maaf, pembayaran Anda gagal. Silakan coba lagi.</p>
                        @elseif($order->status === 'expired')
                            <div class="mb-4">
                                <i class="bx bx-x-circle text-dark" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="text-dark mb-3">Pembayaran Kadaluarsa</h2>
                            <p class="text-muted mb-4">Waktu pembayaran telah habis. Silakan buat pesanan baru.</p>
                        @else
                            <div class="mb-4">
                                <i class="bx bx-info-circle text-secondary" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="text-secondary mb-3">{{ $order->formatted_status }}</h2>
                            <p class="text-muted mb-4">Status pesanan Anda: {{ $order->formatted_status }}</p>
                        @endif

                        <!-- Order Information -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="text-start">
                                    <h6 class="text-muted">Nomor Pesanan</h6>
                                    <h5 class="mb-3">{{ $order->order_number }}</h5>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-end">
                                    <h6 class="text-muted">Total Pembayaran</h6>
                                    <h5 class="mb-3">{{ $order->formatted_total }}</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        @if($order->payment_method)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="mb-2">Metode Pembayaran</h6>
                                    <p class="mb-0">{{ $order->payment_method }}</p>
                                    @if($order->paid_at)
                                    <small class="text-muted">Dibayar pada: {{ $order->paid_at->format('d M Y H:i') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                        <i class="bx bx-home me-2"></i>Beranda
                                    </a>
                                    <a href="{{ route('shop') }}" class="btn btn-primary">
                                        <i class="bx bx-shopping-bag me-2"></i>Belanja Lagi
                                    </a>
                                    @if($order->status === 'paid')
                                    <a href="#" class="btn btn-success" onclick="window.print()">
                                        <i class="bx bx-printer me-2"></i>Cetak Invoice
                                    </a>
                                    @elseif(isset($payment_status) && ($payment_status === 'unfinish' || $payment_status === 'error'))
                                    <a href="{{ route('checkout') }}" class="btn btn-warning">
                                        <i class="bx bx-arrow-back me-2"></i>Coba Pembayaran Lagi
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Debug Info (only in local/development) -->
                @if(config('app.env') === 'local')
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">Debug Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Order ID:</strong> {{ $order->id }}</p>
                                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                                <p><strong>Status:</strong> {{ $order->status }}</p>
                                <p><strong>Midtrans Order ID:</strong> {{ $order->midtrans_order_id }}</p>
                            </div>
                            <div class="col-md-6">
                                @if($status)
                                <p><strong>Transaction Status:</strong> {{ $status['transaction_status'] ?? 'N/A' }}</p>
                                <p><strong>Payment Type:</strong> {{ $status['payment_type'] ?? 'N/A' }}</p>
                                <p><strong>Gross Amount:</strong> IDR {{ number_format($status['gross_amount'] ?? 0, 0, ',', '.') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Payment Status Page End -->
@endsection

@push('scripts')
<script>
// Auto-refresh for pending payments
@if($order->status === 'pending')
let refreshCount = 0;
const maxRefresh = 10; // Max 10 attempts (50 seconds)

function checkPaymentStatus() {
    if (refreshCount >= maxRefresh) {
        console.log('Max refresh attempts reached');
        return;
    }
    
    refreshCount++;
    
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Check if status has changed by looking for success indicators
        if (html.includes('Pembayaran Berhasil') || html.includes('text-success')) {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error checking status:', error);
    });
}

// Check status every 5 seconds
const interval = setInterval(checkPaymentStatus, 5000);

// Stop checking after 50 seconds or when page is unloaded
setTimeout(() => {
    clearInterval(interval);
}, 50000);

window.addEventListener('beforeunload', () => {
    clearInterval(interval);
});
@endif

// Manual refresh button
function refreshStatus() {
    window.location.reload();
}
</script>
@endpush