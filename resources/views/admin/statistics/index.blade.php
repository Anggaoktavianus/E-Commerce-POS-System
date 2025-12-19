@extends('admin.layouts.app')

@section('title', 'Statistik')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card page-header-card">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
              <h4 class="mb-2 fw-bold">
                <i class="bx bx-chart me-2 text-info"></i>Statistik & Analitik
              </h4>
              <p class="text-muted mb-0">
                <i class="bx bx-time me-1"></i>
                {{ now()->format('l, d F Y') }} - Ringkasan data dan performa sistem
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Overall Statistics -->
  <div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
      <div class="card stat-card bg-primary text-white">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <p class="stat-label mb-2">Total Produk</p>
              <h3 class="stat-value mb-0 text-white">{{ number_format($stats['total_products'], 0, ',', '.') }}</h3>
              <small class="stat-change d-block mt-2">
                <i class="bx bx-package"></i> {{ $stats['active_products'] }} aktif
              </small>
            </div>
            <div class="stat-icon">
              <i class="bx bx-package"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card stat-card bg-success text-white">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <p class="stat-label mb-2">Total Pesanan</p>
              <h3 class="stat-value mb-0 text-white">{{ number_format($stats['total_orders'], 0, ',', '.') }}</h3>
              <small class="stat-change d-block mt-2">
                <i class="bx bx-shopping-bag"></i> {{ $stats['paid_orders'] }} dibayar
              </small>
            </div>
            <div class="stat-icon">
              <i class="bx bx-shopping-bag"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card stat-card bg-info text-white">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <p class="stat-label mb-2">Total Revenue</p>
              <h3 class="stat-value mb-0 text-white">Rp{{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
              <small class="stat-change d-block mt-2">
                <i class="bx bx-dollar-circle"></i> Bulan ini: Rp{{ number_format($stats['month_revenue'], 0, ',', '.') }}
              </small>
            </div>
            <div class="stat-icon">
              <i class="bx bx-dollar-circle"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card stat-card bg-warning text-white">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <p class="stat-label mb-2">Total Users</p>
              <h3 class="stat-value mb-0 text-white">{{ number_format($stats['total_users'], 0, ',', '.') }}</h3>
              <small class="stat-change d-block mt-2">
                <i class="bx bx-user"></i> Pengguna terdaftar
              </small>
            </div>
            <div class="stat-icon">
              <i class="bx bx-user"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Order Status Breakdown -->
  <div class="row g-4 mb-4">
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bx bx-receipt me-2"></i>Status Pesanan</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-6">
              <div class="d-flex align-items-center p-3 bg-light rounded">
                <div class="flex-shrink-0">
                  <i class="bx bx-time text-warning fs-4"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                  <p class="mb-0 small text-muted">Pending</p>
                  <h5 class="mb-0">{{ $stats['pending_orders'] }}</h5>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="d-flex align-items-center p-3 bg-light rounded">
                <div class="flex-shrink-0">
                  <i class="bx bx-check-circle text-success fs-4"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                  <p class="mb-0 small text-muted">Paid</p>
                  <h5 class="mb-0">{{ $stats['paid_orders'] }}</h5>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="d-flex align-items-center p-3 bg-light rounded">
                <div class="flex-shrink-0">
                  <i class="bx bx-cog text-info fs-4"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                  <p class="mb-0 small text-muted">Processing</p>
                  <h5 class="mb-0">{{ $stats['processing_orders'] }}</h5>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="d-flex align-items-center p-3 bg-light rounded">
                <div class="flex-shrink-0">
                  <i class="bx bx-check-double text-primary fs-4"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                  <p class="mb-0 small text-muted">Completed</p>
                  <h5 class="mb-0">{{ $stats['completed_orders'] }}</h5>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="d-flex align-items-center p-3 bg-light rounded">
                <div class="flex-shrink-0">
                  <i class="bx bx-truck text-success fs-4"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                  <p class="mb-0 small text-muted">Delivered</p>
                  <h5 class="mb-0">{{ $stats['delivered_orders'] }}</h5>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="d-flex align-items-center p-3 bg-light rounded">
                <div class="flex-shrink-0">
                  <i class="bx bx-x-circle text-danger fs-4"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                  <p class="mb-0 small text-muted">Cancelled</p>
                  <h5 class="mb-0">{{ $stats['cancelled_orders'] }}</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bx bx-line-chart me-2"></i>Revenue Hari Ini</h5>
        </div>
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-center" style="min-height: 200px;">
            <div class="text-center">
              <h2 class="text-success mb-2">Rp{{ number_format($stats['today_revenue'], 0, ',', '.') }}</h2>
              <p class="text-muted mb-0">Pendapatan hari ini</p>
              <div class="mt-3">
                <span class="badge bg-label-success">+{{ number_format(($stats['today_revenue'] / max($stats['month_revenue'], 1)) * 100, 1) }}% dari bulan ini</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Revenue Chart -->
  <div class="row g-4 mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
          <h5 class="mb-0">
            <i class="bx bx-bar-chart-alt me-2"></i>Revenue 
            <span class="text-muted small">
              @if($filter === 'daily')
                (30 Hari Terakhir)
              @elseif($filter === 'weekly')
                (12 Minggu Terakhir)
              @elseif($filter === 'monthly')
                (12 Bulan Terakhir)
              @elseif($filter === 'yearly')
                (5 Tahun Terakhir)
              @endif
            </span>
          </h5>
          <div class="mt-2 mt-md-0">
            <div class="btn-group" role="group">
              <a href="{{ route('admin.statistics', ['filter' => 'daily']) }}" 
                 class="btn btn-sm {{ $filter === 'daily' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="bx bx-calendar"></i> Harian
              </a>
              <a href="{{ route('admin.statistics', ['filter' => 'weekly']) }}" 
                 class="btn btn-sm {{ $filter === 'weekly' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="bx bx-calendar-week"></i> Mingguan
              </a>
              <a href="{{ route('admin.statistics', ['filter' => 'monthly']) }}" 
                 class="btn btn-sm {{ $filter === 'monthly' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="bx bx-calendar-month"></i> Bulanan
              </a>
              <a href="{{ route('admin.statistics', ['filter' => 'yearly']) }}" 
                 class="btn btn-sm {{ $filter === 'yearly' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="bx bx-calendar"></i> Tahunan
              </a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div style="position: relative; height: 400px;">
            <canvas id="revenueChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Top Products -->
  <div class="row g-4 mb-4">
    <div class="col-12 col-md-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bx bx-package me-2"></i>Produk Terlaris</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th>Terjual</th>
                  <th>Revenue</th>
                </tr>
              </thead>
              <tbody>
                @forelse($topProducts as $product)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="flex-shrink-0 me-3">
                        <i class="bx bx-package text-primary"></i>
                      </div>
                      <div>
                        <strong>{{ $product->name }}</strong>
                        <br>
                        <small class="text-muted">Rp{{ number_format($product->price, 0, ',', '.') }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-label-primary">{{ number_format($product->total_sold, 0, ',', '.') }} unit</span>
                  </td>
                  <td>
                    <strong class="text-success">Rp{{ number_format($product->total_revenue, 0, ',', '.') }}</strong>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="3" class="text-center text-muted">Belum ada data produk terlaris</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bx bx-time me-2"></i>Pesanan Terbaru</h5>
        </div>
        <div class="card-body">
          <div class="list-group list-group-flush">
            @forelse($recentOrders->take(5) as $order)
            <div class="list-group-item px-0">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h6 class="mb-1">{{ $order->order_number }}</h6>
                  <p class="mb-1 small text-muted">{{ $order->user->name ?? 'Guest' }}</p>
                  <small class="text-muted">{{ $order->created_at->format('d M Y H:i') }}</small>
                </div>
                <div class="text-end">
                  <span class="badge bg-label-{{ $order->status === 'paid' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                    {{ ucfirst($order->status) }}
                  </span>
                  <p class="mb-0 mt-1 small"><strong>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</strong></p>
                </div>
              </div>
            </div>
            @empty
            <div class="list-group-item text-center text-muted">Belum ada pesanan</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueChartEl = document.getElementById('revenueChart');
    if (revenueChartEl) {
      @php
        $revenueDataJson = json_encode($revenueData ?? []);
        $filterType = $filter ?? 'monthly';
      @endphp
      const revenueData = {!! $revenueDataJson !!};
      const filterType = '{{ $filterType }}';
      
      // Generate labels based on filter type
      const revenueLabels = revenueData.length > 0 ? revenueData.map(function(item) {
        if (filterType === 'daily') {
          const date = new Date(item.date);
          const day = date.getDate();
          const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
          return day + ' ' + monthNames[date.getMonth()];
        } else if (filterType === 'weekly') {
          const date = new Date(item.date);
          const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
          return 'Minggu ' + item.week + ' - ' + monthNames[date.getMonth()] + ' ' + item.year;
        } else if (filterType === 'monthly') {
          const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
          return monthNames[item.month - 1] + ' ' + item.year;
        } else if (filterType === 'yearly') {
          return item.year;
        }
        return '';
      }) : [];
      
      const revenueValues = revenueData.length > 0 ? revenueData.map(function(item) {
        return parseFloat(item.revenue) || 0;
      }) : [];
      const orderCounts = revenueData.length > 0 ? revenueData.map(function(item) {
        return parseInt(item.order_count) || 0;
      }) : [];

      // Destroy existing chart if any
      if (window.revenueChartInstance) {
        window.revenueChartInstance.destroy();
      }

      const ctx = revenueChartEl.getContext('2d');
      window.revenueChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
          labels: revenueLabels,
          datasets: [{
            label: 'Revenue (Rp)',
            data: revenueValues,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.4,
            fill: true,
            yAxisID: 'y',
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: 'rgb(75, 192, 192)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
          }, {
            label: 'Jumlah Pesanan',
            data: orderCounts,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.4,
            fill: true,
            yAxisID: 'y1',
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: 'rgb(255, 99, 132)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'top',
              labels: {
                usePointStyle: true,
                padding: 15,
                font: {
                  size: 12,
                  weight: '500'
                }
              }
            },
            tooltip: {
              mode: 'index',
              intersect: false,
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              titleFont: {
                size: 14,
                weight: 'bold'
              },
              bodyFont: {
                size: 12
              },
              callbacks: {
                label: function(context) {
                  let label = context.dataset.label || '';
                  if (label) {
                    label += ': ';
                  }
                  if (context.parsed.y !== null) {
                    if (context.dataset.yAxisID === 'y') {
                      label += 'Rp' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                    } else {
                      label += new Intl.NumberFormat('id-ID').format(context.parsed.y) + ' pesanan';
                    }
                  }
                  return label;
                }
              }
            }
          },
          interaction: {
            mode: 'index',
            intersect: false,
          },
          scales: {
            x: {
              display: true,
              grid: {
                display: true,
                color: 'rgba(0, 0, 0, 0.05)'
              },
              ticks: {
                maxRotation: 45,
                minRotation: 0,
                font: {
                  size: 11
                }
              }
            },
            y: {
              type: 'linear',
              display: true,
              position: 'left',
              beginAtZero: true,
              grid: {
                display: true,
                color: 'rgba(0, 0, 0, 0.05)'
              },
              ticks: {
                callback: function(value) {
                  return 'Rp' + new Intl.NumberFormat('id-ID').format(value);
                },
                font: {
                  size: 11
                }
              },
              title: {
                display: true,
                text: 'Revenue (Rp)',
                font: {
                  size: 12,
                  weight: 'bold'
                }
              }
            },
            y1: {
              type: 'linear',
              display: true,
              position: 'right',
              beginAtZero: true,
              grid: {
                drawOnChartArea: false,
              },
              ticks: {
                callback: function(value) {
                  return new Intl.NumberFormat('id-ID').format(value);
                },
                font: {
                  size: 11
                }
              },
              title: {
                display: true,
                text: 'Jumlah Pesanan',
                font: {
                  size: 12,
                  weight: 'bold'
                }
              }
            }
          }
        }
      });
    }
  });
</script>
@endpush
@endsection
