@extends('layouts.app')

@section('title', 'Lacak Pesanan - ' . $order->order_number)

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

/* Timeline Styles */
.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-item.completed .timeline-marker {
    background: #10b981;
}

.timeline-item:not(.completed) .timeline-marker {
    background: #e9ecef;
    color: #6c757d;
}

.timeline-item.cancelled .timeline-marker {
    background: #ef4444;
}

.timeline-content {
    padding-left: 10px;
}

.timeline-content h6 {
    color: #1f2937;
    font-weight: 600;
}

.timeline-content p {
    color: #6b7280;
    font-size: 0.875rem;
}

/* Order tracking styles */
.tracking-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
}

.tracking-card h5 {
    color: #137440;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
}

.tracking-card h5 i {
    margin-right: 0.5rem;
}

.status-alert {
    border-radius: 12px;
    padding: 1.5rem;
    border: none;
    font-weight: 500;
}

.status-alert h6 {
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.summary-table {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.summary-table .table {
    margin-bottom: 0;
}

.summary-table .table td {
    padding: 0.75rem;
    vertical-align: middle;
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
        <h1 class="text-center text-white display-6">Lacak Pesanan</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-white">Pages</a></li>
            <li class="breadcrumb-item active text-white">Lacak Pesanan #{{ $order->order_number }}</li>
        </ol>
    </div>
    <!-- Single Page Header End -->
    
    <div class="dashboard-container">
        <!-- Order Status Timeline -->
        <div class="tracking-card">
            <h5><i class="bx bx-time"></i> Status Pesanan</h5>
            <div class="timeline">
                <div class="timeline-item {{ in_array($order->status, ['pending', 'paid', 'processing', 'shipped', 'delivered', 'completed']) ? 'completed' : '' }}">
                    <div class="timeline-marker">
                        <i class="bx bx-check"></i>
                    </div>
                    <div class="timeline-content">
                        <h6>Pesanan Dibuat</h6>
                        <p>{{ $order->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                @if(in_array($order->status, ['paid', 'processing', 'shipped', 'delivered', 'completed']))
                <div class="timeline-item completed">
                    <div class="timeline-marker">
                        <i class="bx bx-check"></i>
                    </div>
                    <div class="timeline-content">
                        <h6>Pembayaran Berhasil</h6>
                        <p>{{ $order->paid_at?->format('d M Y H:i') ?? '-' }}</p>
                    </div>
                </div>
                @endif

                @if(in_array($order->status, ['processing', 'shipped', 'delivered', 'completed']))
                <div class="timeline-item completed">
                    <div class="timeline-marker">
                        <i class="bx bx-check"></i>
                    </div>
                    <div class="timeline-content">
                        <h6>Sedang Diproses</h6>
                        <p>{{ $order->processed_at?->format('d M Y H:i') ?? '-' }}</p>
                    </div>
                </div>
                @endif

                @if(in_array($order->status, ['shipped', 'delivered', 'completed']))
                <div class="timeline-item completed">
                    <div class="timeline-marker">
                        <i class="bx bx-check"></i>
                    </div>
                    <div class="timeline-content">
                        <h6>Dalam Pengiriman</h6>
                        <p>{{ $order->shipped_at?->format('d M Y H:i') ?? '-' }}</p>
                        @if($order->tracking_number)
                            <p><strong>No. Resi:</strong> {{ $order->tracking_number }}</p>
                        @endif
                    </div>
                </div>
                @endif

                @if(in_array($order->status, ['delivered', 'completed']))
                <div class="timeline-item completed">
                    <div class="timeline-marker">
                        <i class="bx bx-check"></i>
                    </div>
                    <div class="timeline-content">
                        <h6>Terkirim</h6>
                        <p>{{ $order->delivered_at?->format('d M Y H:i') ?? '-' }}</p>
                    </div>
                </div>
                @endif

                @if($order->status === 'cancelled')
                <div class="timeline-item cancelled">
                    <div class="timeline-marker">
                        <i class="bx bx-x"></i>
                    </div>
                    <div class="timeline-content">
                        <h6>Dibatalkan</h6>
                        <p>{{ $order->cancelled_at?->format('d M Y H:i') ?? '-' }}</p>
                        @if($order->cancel_reason)
                            <p><strong>Alasan:</strong> {{ $order->cancel_reason }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Current Status & Estimation -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="tracking-card">
                    <h5><i class="bx bx-info-circle"></i> Status Saat Ini</h5>
                    <div class="status-alert alert-{{ $order->status_color ?? 'secondary' }}">
                        <h6>{{ $order->formatted_status }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="tracking-card">
                    <h5><i class="bx bx-time-five"></i> Estimasi Pengiriman</h5>
                    <div class="d-flex align-items-center">
                        <div class="stat-icon info me-3">
                            <i class="bx bx-truck"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">2-3 hari kerja</h6>
                            <p class="text-muted mb-0">Estimasi waktu pengiriman</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="summary-table">
            <h5 class="mb-4"><i class="bx bx-clipboard"></i> Ringkasan Pesanan</h5>
            <table class="table">
                <tr>
                    <td><strong>No. Pesanan:</strong></td>
                    <td>{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td><strong>Tanggal:</strong></td>
                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <td><strong>Total Item:</strong></td>
                    <td>{{ $order->items->sum('quantity') }} item</td>
                </tr>
                <tr>
                    <td><strong>Total Pembayaran:</strong></td>
                    <td class="fw-bold text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-2"></i>Kembali
            </a>
            
            <a href="{{ route('orders.show', base64_encode($order->order_number)) }}" class="btn btn-primary">
                <i class="bx bx-receipt me-2"></i>Detail Pesanan
            </a>
            
            @if(in_array($order->status, ['delivered', 'completed']))
                <a href="{{ route('orders.invoice', base64_encode($order->order_number)) }}" class="btn btn-info">
                    <i class="bx bx-download me-2"></i>Download Invoice
                </a>
            @endif
        </div>
    </div>
@endsection
