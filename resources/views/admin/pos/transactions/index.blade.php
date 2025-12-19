@extends('admin.layouts.app')

@section('title', 'Transaksi POS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-receipt me-2 text-primary"></i>Transaksi POS
          </h4>
          <p class="text-muted mb-0">Riwayat semua transaksi POS</p>
        </div>
        <div>
          <a href="{{ route('admin.pos.dashboard') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>Transaksi Baru
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.pos.transactions.index') }}">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Outlet</label>
            <select name="outlet_id" class="form-select">
              <option value="">Semua Outlet</option>
              @foreach(\App\Models\Outlet::where('is_active', true)->get() as $outlet)
                <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>
                  {{ $outlet->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">Semua Status</option>
              <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
              <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
          </div>
          <div class="col-md-12">
            <button type="submit" class="btn btn-primary">
              <i class="bx bx-search me-1"></i>Filter
            </button>
            <a href="{{ route('admin.pos.transactions.index') }}" class="btn btn-secondary">
              <i class="bx bx-refresh me-1"></i>Reset
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Transactions Table -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>No. Transaksi</th>
              <th>Outlet</th>
              <th>Kasir</th>
              <th>Customer</th>
              <th>Total</th>
              <th>Payment</th>
              <th>Status</th>
              <th>Waktu</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($transactions as $transaction)
              <tr>
                <td><strong>{{ $transaction->transaction_number }}</strong></td>
                <td>{{ $transaction->outlet->name }}</td>
                <td>{{ $transaction->user->name }}</td>
                <td>{{ $transaction->customer ? $transaction->customer->name : 'Walk-in' }}</td>
                <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                <td>
                  <span class="badge bg-{{ $transaction->payment_method == 'cash' ? 'success' : 'info' }}">
                    {{ strtoupper($transaction->payment_method) }}
                  </span>
                </td>
                <td>
                  @if($transaction->status == 'completed')
                    <span class="badge bg-success">Completed</span>
                  @elseif($transaction->status == 'cancelled')
                    <span class="badge bg-danger">Cancelled</span>
                  @else
                    <span class="badge bg-warning">{{ ucfirst($transaction->status) }}</span>
                  @endif
                </td>
                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                <td>
                  <a href="{{ route('admin.pos.transactions.show', $transaction->id) }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-show"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center py-4">
                  <p class="text-muted">Tidak ada transaksi ditemukan</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">
        {{ $transactions->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
