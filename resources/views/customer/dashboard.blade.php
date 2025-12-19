@extends('layouts.app')

@section('title', 'Dashboard Customer')

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

/* Chart Container */
.chart-container {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

/* Product List */
.product-item {
    padding: 1rem;
    border-radius: 10px;
    background: rgba(19, 116, 64, 0.05);
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.product-item:hover {
    background: rgba(19, 116, 64, 0.1);
    transform: translateX(5px);
}

/* Status Badges */
.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Table Styles */
.custom-table {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.custom-table thead {
    background: linear-gradient(135deg, #137440, #0f5d33);
    color: white;
}

.custom-table thead th {
    border: none;
    padding: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.custom-table tbody tr {
    transition: all 0.3s ease;
}

.custom-table tbody tr:hover {
    background: rgba(19, 116, 64, 0.05);
}

/* Action Buttons */
.btn-action {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 2px;
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: scale(1.1);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem;
    color: #6b7280;
}

.empty-state i {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
        border-radius: 15px;
    }

    .dashboard-header {
        padding: 1.5rem;
    }

    .stat-card {
        margin-bottom: 1rem;
    }
}

/* Animations */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.slide-in {
    animation: slideIn 0.6s ease-out;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #137440, #0f5d33);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #0f5d33, #0a4528);
}

/* Action Buttons Styling */
.btn-action {
    padding: 0.375rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 6px;
    transition: all 0.2s ease;
    border: 1px solid;
    background: transparent;
    color: inherit;
    min-width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-action.btn-outline-primary:hover {
    background: #137440;
    border-color: #137440;
    color: white;
}

.btn-action.btn-outline-success:hover {
    background: #198754;
    border-color: #198754;
    color: white;
}

.btn-action.btn-outline-warning:hover {
    background: #ffc107;
    border-color: #ffc107;
    color: #212529;
}

.btn-action.btn-outline-danger:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: white;
}

.btn-group .btn-action {
    margin: 0 1px;
}

.btn-group .btn-action:first-child {
    margin-left: 0;
}

.btn-group .btn-action:last-child {
    margin-right: 0;
}

/* DataTables Pagination Styling - Override Bootstrap and DataTables defaults */
.dataTables_wrapper .dataTables_paginate {
    margin-top: 1rem !important;
    margin: 0 !important;
    white-space: normal !important;
    text-align: center !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 4px !important;
}

/* Override Bootstrap pagination styles */
.dataTables_wrapper .dataTables_paginate .pagination {
    margin: 0 !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 4px !important;
    flex-wrap: wrap !important;
}

.dataTables_wrapper .dataTables_paginate .pagination .page-link {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 40px !important;
    height: 40px !important;
    padding: 8px 12px !important;
    margin: 0 2px !important;
    border-radius: 8px !important;
    border: none !important;
    background: transparent !important;
    color: #137440 !important;
    font-weight: 500 !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
    box-shadow: none !important;
    cursor: pointer !important;
    line-height: 1 !important;
    text-align: center !important;
    vertical-align: middle !important;
    font-size: 14px !important;
    white-space: nowrap !important;
}

.dataTables_wrapper .dataTables_paginate .pagination .page-link:hover {
    background: #137440 !important;
    color: #ffffff !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(19,116,64,0.2) !important;
    border: none !important;
}

.dataTables_wrapper .dataTables_paginate .pagination .page-item.active .page-link {
    background: #137440 !important;
    color: #ffffff !important;
    border: none !important;
    box-shadow: 0 4px 8px rgba(19,116,64,0.2) !important;
}

.dataTables_wrapper .dataTables_paginate .pagination .page-item.active .page-link:hover {
    transform: none !important;
}

.dataTables_wrapper .dataTables_paginate .pagination .page-item.disabled .page-link {
    opacity: 0.5 !important;
    pointer-events: none !important;
    background: transparent !important;
    color: #6c757d !important;
    border: none !important;
    cursor: not-allowed !important;
    transform: none !important;
}

.dataTables_wrapper .dataTables_paginate .pagination .page-item.disabled .page-link:hover {
    transform: none !important;
    box-shadow: none !important;
    background: transparent !important;
    color: #6c757d !important;
    border: none !important;
}

/* Fallback for direct paginate_button targeting */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 40px !important;
    height: 40px !important;
    padding: 8px 12px !important;
    margin: 0 2px !important;
    border-radius: 8px !important;
    border: none !important;
    background: transparent !important;
    color: #137440 !important;
    font-weight: 500 !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
    box-shadow: none !important;
    cursor: pointer !important;
    float: none !important;
    position: relative !important;
    line-height: 1 !important;
    text-align: center !important;
    vertical-align: middle !important;
    font-size: 14px !important;
    white-space: nowrap !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #137440 !important;
    color: #ffffff !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(19,116,64,0.2) !important;
    border: none !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #137440 !important;
    color: #ffffff !important;
    border: none !important;
    box-shadow: 0 4px 8px rgba(19,116,64,0.2) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    transform: none !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    opacity: 0.5 !important;
    pointer-events: none !important;
    background: transparent !important;
    color: #6c757d !important;
    border: none !important;
    cursor: not-allowed !important;
    transform: none !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
    transform: none !important;
    box-shadow: none !important;
    background: transparent !important;
    color: #6c757d !important;
    border: none !important;
}

.dataTables_wrapper .dataTables_info {
    margin-top: 1rem;
    color: #6b7280;
    font-size: 0.875rem;
}

/* Processing indicator */
.dataTables_processing {
    position: absolute !important;
    top: 50% !important;
    left: 50% !important;
    width: auto !important;
    height: auto !important;
    margin-left: -50px !important;
    margin-top: -15px !important;
    padding: 10px 20px !important;
    background: rgba(19, 116, 64, 0.9) !important;
    color: white !important;
    border-radius: 8px !important;
    font-weight: 500 !important;
    z-index: 1000 !important;
}
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <!-- Single Page Header Start -->
    <div class="container-fluid page-header py-5" style="background: linear-gradient(135deg, #137440 0%, #0f5d33 100%);">
        <h1 class="text-center text-white display-6">Dashboard Pelanggan</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Beranda</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-white">Halaman</a></li>
            <li class="breadcrumb-item active text-white">Dashboard Pelanggan</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <div class="dashboard-container">
        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="stat-card slide-in" style="animation-delay: 0.1s;">
                    <div class="stat-icon primary">
                        <i class="bx bx-shopping-bag"></i>
                    </div>
                    <h6 class="text-white mb-2">Pembelian Bulan Ini</h6>
                    <h4 class="mb-2">{{ $monthlyOrderCount }}</h4>
                    <p class="text-white small mb-0">
                        {{ $monthlyOrderCount }} pesanan •
                        <strong>Rp {{ number_format($monthlySpent, 0, ',', '.') }}</strong>
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="stat-card slide-in" style="animation-delay: 0.2s;">
                    <div class="stat-icon success">
                        <i class="bx bx-package"></i>
                    </div>
                    <h6 class="text-white mb-2">Total Pesanan</h6>
                    <h4 class="mb-2">{{ $totalOrders ?? 0 }}</h4>
                    <p class="text-white small mb-0">Total: Rp {{ number_format($totalSpent ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="stat-card slide-in" style="animation-delay: 0.3s;">
                    <div class="stat-icon info">
                        <i class="bx bx-map-pin"></i>
                    </div>
                    <h6 class="text-white mb-2">Alamat Tersimpan</h6>
                    <h4 class="mb-2">{{ $addressCount ?? 0 }}</h4>
                    <p class="text-white small mb-0">Alamat pengiriman tersimpan</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm fade-in" style="animation-delay: 0.4s;">
                    <div class="card-body p-4">
                        <h6 class="card-title mb-3 fw-bold">
                            <i class="bx bx-bolt me-2 text-success"></i>Aksi Cepat
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <a href="{{ route('shop') }}" class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-3">
                                    <i class="bx bx-shopping-bag fs-4 mb-2"></i>
                                    <span>Belanja</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="{{ route('cart') }}" class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-3">
                                    <i class="bx bx-cart fs-4 mb-2"></i>
                                    <span>Keranjang</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="{{ route('customer.dashboard') }}#orders" class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-3">
                                    <i class="bx bx-package fs-4 mb-2"></i>
                                    <span>Pesanan Saya</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="{{ route('user.addresses.index') }}" class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-3">
                                    <i class="bx bx-map-pin fs-4 mb-2"></i>
                                    <span>Alamat Saya</span>
                                </a>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6 col-6">
                                <a href="{{ route('customer.profile.index') }}" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="bx bx-user fs-4 mb-2"></i>
                                    <span>Profil Saya</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Products -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="chart-container fade-in" style="animation-delay: 0.5s;">
                    <h6 class="card-title mb-4 fw-bold">
                        <i class="bx bx-line-chart me-2 text-primary"></i>
                        Grafik Pengeluaran 6 Bulan Terakhir
                    </h6>
                    <div style="height: 350px; position: relative;">
                        <canvas id="spendingChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="chart-container fade-in" style="animation-delay: 0.6s;">
                    <h6 class="card-title mb-4 fw-bold">
                        <i class="bx bx-star me-2 text-warning"></i>
                        Produk Favorit
                    </h6>
                    @if($favoriteProducts->count() > 0)
                        <div>
                            @foreach($favoriteProducts as $product)
                                <div class="product-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-medium text-dark">{{ $product->product_name }}</div>
                                            <div class="text-white small">{{ $product->total_quantity }} pcs</div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary rounded-pill">{{ $product->order_count }}x</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bx bx-package"></i>
                            <h6 class="mt-3">Belum Ada Data</h6>
                            <p class="text-white small">Belum ada data pembelian</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Status Breakdown -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm fade-in" style="animation-delay: 0.7s;">
                    <div class="card-body p-4">
                        <h6 class="card-title mb-3 fw-bold">
                            <i class="bx bx-bar-chart-alt-2 me-2 text-info"></i>Ringkasan Status Pesanan
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-2 col-6">
                                <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                    <div class="fw-bold text-white fs-4">{{ $orderStats['pending'] ?? 0 }}</div>
                                    <small class="text-white">Pending</small>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                    <div class="fw-bold text-white fs-4">{{ $orderStats['paid'] ?? 0 }}</div>
                                    <small class="text-white">Dibayar</small>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="text-center p-3 bg-danger bg-opacity-10 rounded">
                                    <div class="fw-bold text-white fs-4">{{ $orderStats['failed'] ?? 0 }}</div>
                                    <small class="text-white">Gagal</small>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="text-center p-3 bg-secondary bg-opacity-10 rounded">
                                    <div class="fw-bold text-white fs-4">{{ $orderStats['cancelled'] ?? 0 }}</div>
                                    <small class="text-white">Dibatalkan</small>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="text-center p-3 bg-dark bg-opacity-10 rounded">
                                    <div class="fw-bold text-white fs-4">{{ $orderStats['expired'] ?? 0 }}</div>
                                    <small class="text-white">Kedaluwarsa</small>
                                </div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                    <div class="fw-bold text-white fs-4">{{ $totalOrders ?? 0 }}</div>
                                    <small class="text-white">Total</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="custom-table fade-in mb-4" id="orders" style="animation-delay: 0.8s; margin: 2rem 0; padding: 1.5rem; background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid #e5e7eb;">
            <div class="p-3 border-bottom bg-light">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="mb-0 fw-bold">
                            <i class="bx bx-history me-2 text-primary"></i>
                            Pesanan Terbaru
                        </h6>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <small class="text-white">
                            <i class="bx bx-user me-1"></i>
                            {{ auth()->user()->name }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table mb-0" id="ordersTable">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via server-side processing -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<script>
// Action button functions
function showOrderDetail(orderNumber) {
    // Show order detail modal or navigate to detail page
    window.location.href = `/orders/${orderNumber}`;
}

function trackOrder(orderNumber) {
    // Show tracking modal or navigate to tracking page
    window.location.href = `/orders/${orderNumber}/track`;
}

function downloadInvoice(orderNumber) {
    // Download invoice PDF
    window.location.href = `/orders/${orderNumber}/invoice`;
}

function cancelOrder(orderNumber) {
    Swal.fire({
        title: 'Batalkan Pesanan?',
        text: 'Apakah Anda yakin ingin membatalkan pesanan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bx bx-x-circle me-1"></i>Ya, Batalkan',
        cancelButtonText: '<i class="bx bx-x me-1"></i>Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to cancel order
            fetch(`/orders/${orderNumber}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pesanan berhasil dibatalkan',
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        // Refresh the table
                        $('#ordersTable').DataTable().ajax.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal membatalkan pesanan: ' + (data.message || 'Terjadi kesalahan'),
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat membatalkan pesanan',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

function reorderItems(orderNumber) {
    Swal.fire({
        title: 'Pesan Lagi?',
        text: 'Apakah Anda ingin memesan kembali item dari pesanan ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bx bx-refresh me-1"></i>Ya, Pesan Lagi',
        cancelButtonText: '<i class="bx bx-x me-1"></i>Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to reorder items
            fetch(`/orders/${orderNumber}/reorder`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Item berhasil ditambahkan ke keranjang',
                        confirmButtonColor: '#28a745',
                        showCancelButton: true,
                        cancelButtonText: 'Lanjut Belanja',
                        confirmButtonText: 'Lihat Keranjang'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/cart';
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            // Stay on page
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal menambahkan item ke keranjang: ' + (data.message || 'Terjadi kesalahan'),
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menambahkan item ke keranjang',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables with server-side processing
    $('#ordersTable').DataTable({
        responsive: true,
        pageLength: 5,
        lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "Semua"]],
        ordering: true,
        searching: true,
        info: true,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("customer.orders.datatables") }}',
            type: 'GET',
            data: function (d) {
                // Add any additional parameters if needed
                return d;
            },
            error: function(xhr, error, code) {
                console.error('DataTables error:', error);
                // Show error message to user
                $('#ordersTable tbody').html(
                    '<tr><td colspan="5" class="text-center">Kesalahan memuat data. Silakan coba lagi.</td></tr>'
                );
            }
        },
        columns: [
            { data: 'order_number', name: 'order_number' },
            { data: 'created_at', name: 'created_at' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ pesanan",
            info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ pesanan",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            },
            processing: "Sedang memuat...",
            emptyTable: "Tidak ada data pesanan",
            zeroRecords: "Tidak ditemukan pesanan yang cocok"
        },
        order: [[1, 'desc']] // Sort by date descending
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Chart initialization
    const ctx = document.getElementById('spendingChart').getContext('2d');
    const spendingData = @json($monthlySpending);

    // Responsive configuration
    const config = {
        type: 'bar',
        data: {
            labels: spendingData.map(item => {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const [year, month] = item.month.split('-');
                return months[parseInt(month) - 1] + ' ' + year;
            }),
            datasets: [{
                label: 'Pengeluaran (Rp)',
                data: spendingData.map(item => item.spent),
                backgroundColor: 'rgba(19, 116, 64, 0.2)',
                borderColor: 'rgba(19, 116, 64, 1)',
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 10,
                    right: 10,
                    bottom: 10,
                    left: 10
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 11
                        },
                        usePointStyle: true,
                        padding: 10
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Pengeluaran: Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        maxRotation: 45,
                        minRotation: 0
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                            }
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    };

    const spendingChart = new Chart(ctx, config);

    // Make chart responsive on window resize
    window.addEventListener('resize', function() {
        spendingChart.resize();
    });
});
</script>
@endpush
@endsection
