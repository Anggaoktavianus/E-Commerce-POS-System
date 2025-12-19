@extends('admin.layouts.app')

@section('title', 'Riwayat Stok - ' . $product->name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-1">
        <i class="bx bx-history me-2"></i>Riwayat Stok
      </h4>
      <p class="text-muted mb-0">
        <strong>{{ $product->name }}</strong> - Stok saat ini: <strong>{{ $product->stock_qty ?? 0 }} {{ $product->unit ?? 'pcs' }}</strong>
      </p>
    </div>
    <div>
      <a href="{{ route('admin.products.edit', encode_id($product->id)) }}" class="btn btn-primary">
        <i class="bx bx-edit me-2"></i>Edit Produk
      </a>
      <a href="{{ route('admin.stock_movements.index') }}" class="btn btn-secondary">
        <i class="bx bx-arrow-back me-2"></i>Semua Riwayat
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h5 class="card-title mb-0">Daftar Perubahan Stok</h5>
    </div>
    <div class="card-body">
      @if($movements->count() > 0)
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Stok Lama</th>
                <th>Stok Baru</th>
                <th>Referensi</th>
                <th>User</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              @foreach($movements as $movement)
              <tr>
                <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                <td>
                  <span class="badge bg-{{ $movement->type_color }}">
                    {{ $movement->type_label }}
                  </span>
                </td>
                <td>
                  @if($movement->type === 'out')
                    <span class="text-danger">-{{ abs($movement->quantity) }}</span>
                  @else
                    <span class="text-success">+{{ abs($movement->quantity) }}</span>
                  @endif
                </td>
                <td>{{ number_format($movement->old_stock) }}</td>
                <td><strong>{{ number_format($movement->new_stock) }}</strong></td>
                <td>
                  @if($movement->reference_number)
                    <a href="{{ route('admin.orders.index', ['search' => $movement->reference_number]) }}" class="text-primary">
                      {{ $movement->reference_number }}
                    </a>
                  @else
                    -
                  @endif
                </td>
                <td>{{ $movement->user ? $movement->user->name : 'System' }}</td>
                <td>
                  <small class="text-muted">{{ $movement->notes ?? '-' }}</small>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-3">
          {{ $movements->links() }}
        </div>
      @else
        <div class="text-center py-5">
          <i class="bx bx-history text-muted" style="font-size: 3rem;"></i>
          <h5 class="mt-3 text-muted">Belum ada riwayat perubahan stok</h5>
          <p class="text-muted">Riwayat perubahan stok akan muncul di sini setelah ada transaksi atau penyesuaian stok.</p>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
