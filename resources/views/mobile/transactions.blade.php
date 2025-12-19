@extends('mobile.layouts.app')

@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Transaksi')

@section('content')
@auth
<!-- Stats Cards -->
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;">
    <div style="text-align: center; padding: 0.75rem; background: #f5f5f5; border-radius: 8px;">
      <div style="font-size: 1.25rem; font-weight: 700; color: #147440;">{{ $stats['all'] ?? 0 }}</div>
      <div style="font-size: 0.75rem; color: #666; margin-top: 0.25rem;">Semua</div>
    </div>
    <div style="text-align: center; padding: 0.75rem; background: #fff3cd; border-radius: 8px;">
      <div style="font-size: 1.25rem; font-weight: 700; color: #856404;">{{ $stats['pending'] ?? 0 }}</div>
      <div style="font-size: 0.75rem; color: #666; margin-top: 0.25rem;">Pending</div>
    </div>
    <div style="text-align: center; padding: 0.75rem; background: #d1ecf1; border-radius: 8px;">
      <div style="font-size: 1.25rem; font-weight: 700; color: #0c5460;">{{ $stats['paid'] ?? 0 }}</div>
      <div style="font-size: 0.75rem; color: #666; margin-top: 0.25rem;">Dibayar</div>
    </div>
  </div>
</div>

<!-- Filter Tabs -->
<div style="background: white; padding: 0.75rem 1rem; margin-bottom: 0.5rem; overflow-x: auto; -webkit-overflow-scrolling: touch;">
  <div style="display: flex; gap: 0.5rem; min-width: max-content;">
    <a href="{{ route('mobile.transactions') }}" 
       style="padding: 0.5rem 1rem; border-radius: 20px; background: {{ !request('status') ? '#147440' : '#f0f0f0' }}; color: {{ !request('status') ? 'white' : '#666' }}; text-decoration: none; font-size: 0.875rem; font-weight: 500; white-space: nowrap;">
      Semua
    </a>
    <a href="{{ route('mobile.transactions', ['status' => 'pending']) }}" 
       style="padding: 0.5rem 1rem; border-radius: 20px; background: {{ request('status') == 'pending' ? '#ffc107' : '#f0f0f0' }}; color: {{ request('status') == 'pending' ? 'white' : '#666' }}; text-decoration: none; font-size: 0.875rem; font-weight: 500; white-space: nowrap;">
      Pending
    </a>
    <a href="{{ route('mobile.transactions', ['status' => 'paid']) }}" 
       style="padding: 0.5rem 1rem; border-radius: 20px; background: {{ request('status') == 'paid' ? '#28a745' : '#f0f0f0' }}; color: {{ request('status') == 'paid' ? 'white' : '#666' }}; text-decoration: none; font-size: 0.875rem; font-weight: 500; white-space: nowrap;">
      Dibayar
    </a>
    <a href="{{ route('mobile.transactions', ['status' => 'processing']) }}" 
       style="padding: 0.5rem 1rem; border-radius: 20px; background: {{ request('status') == 'processing' ? '#17a2b8' : '#f0f0f0' }}; color: {{ request('status') == 'processing' ? 'white' : '#666' }}; text-decoration: none; font-size: 0.875rem; font-weight: 500; white-space: nowrap;">
      Diproses
    </a>
    <a href="{{ route('mobile.transactions', ['status' => 'delivered']) }}" 
       style="padding: 0.5rem 1rem; border-radius: 20px; background: {{ request('status') == 'delivered' ? '#28a745' : '#f0f0f0' }}; color: {{ request('status') == 'delivered' ? 'white' : '#666' }}; text-decoration: none; font-size: 0.875rem; font-weight: 500; white-space: nowrap;">
      Selesai
    </a>
  </div>
</div>

<!-- Orders List -->
@if($orders->count() > 0)
  @foreach($orders as $order)
    <a href="{{ route('mobile.order.detail', base64_encode($order->order_number)) }}" 
       style="display: block; background: white; padding: 1rem; margin-bottom: 0.5rem; text-decoration: none; color: inherit; border-radius: 12px;">
      <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
        <div>
          <div style="font-size: 0.875rem; font-weight: 600; color: #333; margin-bottom: 0.25rem;">
            {{ $order->order_number }}
          </div>
          <div style="font-size: 0.75rem; color: #666;">
            {{ $order->created_at->format('d M Y, H:i') }}
          </div>
        </div>
        @php
          $statusColors = [
            'paid' => ['bg' => '#d4edda', 'text' => '#155724'],
            'pending' => ['bg' => '#fff3cd', 'text' => '#856404'],
            'processing' => ['bg' => '#d1ecf1', 'text' => '#0c5460'],
            'cancelled' => ['bg' => '#f8d7da', 'text' => '#721c24'],
            'failed' => ['bg' => '#f8d7da', 'text' => '#721c24'],
            'expired' => ['bg' => '#e2e3e5', 'text' => '#383d41'],
          ];
          $color = $statusColors[$order->status] ?? ['bg' => '#e2e3e5', 'text' => '#383d41'];
        @endphp
        <div style="padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
          {{ ucfirst(str_replace('_', ' ', $order->status)) }}
        </div>
      </div>
      
      <div style="margin-bottom: 0.75rem;">
        @foreach($order->items->take(2) as $item)
          <div style="display: flex; gap: 0.75rem; margin-bottom: 0.5rem;">
            @php
              $product = $item->product ?? \App\Models\Product::find($item->product_id);
              $imagePath = $product ? $product->main_image_path : null;
            @endphp
            <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 6px; flex-shrink: 0; overflow: hidden;">
              @if($imagePath)
                <img src="{{ Storage::url($imagePath) }}" 
                     alt="{{ $item->product_name }}"
                     style="width: 100%; height: 100%; object-fit: cover;"
                     onerror="this.style.display='none';">
              @endif
            </div>
            <div style="flex: 1;">
              <div style="font-size: 0.875rem; color: #333; margin-bottom: 0.25rem;">
                {{ $item->product_name }}
              </div>
              <div style="font-size: 0.75rem; color: #666;">
                {{ $item->quantity }}x Rp{{ number_format($item->price, 0, ',', '.') }}
              </div>
            </div>
          </div>
        @endforeach
        @if($order->items->count() > 2)
          <div style="font-size: 0.75rem; color: #999; margin-top: 0.5rem;">
            +{{ $order->items->count() - 2 }} produk lainnya
          </div>
        @endif
      </div>
      
      <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.75rem; border-top: 1px solid #f0f0f0;">
        <div style="font-size: 0.875rem; color: #666;">
          Total
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
          <div style="font-size: 1rem; font-weight: 700; color: #147440;">
            Rp{{ number_format($order->total_amount, 0, ',', '.') }}
          </div>
          @if(in_array($order->status, ['delivered', 'completed', 'cancelled']))
            <button type="button" 
                    onclick="event.preventDefault(); reorderItems('{{ base64_encode($order->order_number) }}')"
                    style="background: #28a745; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.75rem; font-weight: 600; cursor: pointer; white-space: nowrap;">
              <i class="bx bx-refresh"></i> Pesan Lagi
            </button>
          @endif
        </div>
      </div>
    </a>
  @endforeach

  <!-- Pagination -->
  @if($orders->hasPages())
    <div style="padding: 1rem; text-align: center; background: white; margin-top: 0.5rem; border-radius: 12px;">
      {{ $orders->links() }}
    </div>
  @endif

@else
  <div class="empty-state">
    <i class="bx bx-package"></i>
    <p>Tidak ada pesanan ditemukan</p>
    <a href="{{ route('mobile.shop') }}" 
       style="display: inline-block; background: #147440; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 1rem;">
      <i class="bx bx-shopping-bag"></i> Mulai Belanja
    </a>
  </div>
@endif

@else
<div class="empty-state">
  <i class="bx bx-user"></i>
  <p>Silakan login untuk melihat transaksi</p>
  <a href="{{ route('mobile.login') }}" 
     style="display: inline-block; background: #147440; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 1rem;">
    <i class="bx bx-log-in"></i> Login
  </a>
</div>
@endauth
@endsection

@push('scripts')
<script>
  function reorderItems(orderNumber) {
    Swal.fire({
      title: 'Pesan Lagi?',
      text: 'Apakah Anda ingin memesan kembali item dari pesanan ini?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Pesan Lagi',
      cancelButtonText: 'Batal',
      width: '90%'
    }).then((result) => {
      if (result.isConfirmed) {
        MobileLoading.show('Menambahkan ke keranjang...');
        
        fetch(`/orders/${orderNumber}/reorder`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          MobileLoading.hide();
          if (data.success) {
            Swal.fire({
              title: 'Berhasil!',
              text: data.message || 'Item berhasil ditambahkan ke keranjang',
              icon: 'success',
              confirmButtonText: 'Lihat Keranjang',
              showCancelButton: true,
              cancelButtonText: 'Tutup',
              width: '90%'
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = '{{ route("mobile.cart") }}';
              }
            });
            // Update cart badge
            updateCartBadge();
          } else {
            MobileNotification.error(data.message || 'Gagal menambahkan item ke keranjang');
          }
        })
        .catch(error => {
          MobileLoading.hide();
          MobileErrorHandler.handle(error);
        });
      }
    });
  }
  
  function updateCartBadge() {
    fetch('{{ route("api.cart.count") }}', {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      const badge = document.getElementById('cartBadge');
      if (badge) {
        if (data.count > 0) {
          badge.textContent = data.count > 99 ? '99+' : data.count;
          badge.style.display = 'flex';
        } else {
          badge.style.display = 'none';
        }
      }
    })
    .catch(() => {});
  }
</script>
@endpush
