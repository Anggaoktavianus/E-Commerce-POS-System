@extends('mobile.layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Bandingkan Produk')

@section('content')
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <div style="display: flex; justify-content: space-between; align-items: center;">
    <h5 style="font-size: 1rem; font-weight: 700; margin: 0; color: #333;">
      <i class="bx bx-layer"></i> Perbandingan Produk
    </h5>
    <button type="button" 
            onclick="clearComparison()"
            style="background: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; cursor: pointer;">
      <i class="bx bx-trash"></i> Hapus Semua
    </button>
  </div>
</div>

@if($products->count() > 0)
  <div style="background: white; padding: 1rem; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
      <thead>
        <tr>
          <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid #f0f0f0; font-size: 0.875rem; color: #666; width: 120px;">Fitur</th>
          @foreach($products as $product)
            <th style="padding: 0.75rem; text-align: center; border-bottom: 2px solid #f0f0f0; font-size: 0.875rem; color: #333; min-width: 150px;">
              <a href="{{ route('mobile.shop.detail', $product->slug) }}" style="text-decoration: none; color: inherit;">
                <img src="{{ $product->main_image_path ? Storage::url($product->main_image_path) : asset('sneat/assets/img/placeholder.png') }}" 
                     alt="{{ $product->name }}"
                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-bottom: 0.5rem;"
                     onerror="this.src='{{ asset('sneat/assets/img/placeholder.png') }}'">
                <div style="font-weight: 600; font-size: 0.75rem; margin-bottom: 0.25rem;">{{ $product->name }}</div>
                <button type="button" 
                        onclick="event.preventDefault(); removeFromComparison({{ $product->id }})"
                        style="background: #dc3545; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.7rem; cursor: pointer; margin-top: 0.25rem;">
                  <i class="bx bx-x"></i> Hapus
                </button>
              </a>
            </th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="padding: 0.75rem; border-bottom: 1px solid #f0f0f0; font-weight: 600; font-size: 0.875rem;">Harga</td>
          @foreach($products as $product)
            <td style="padding: 0.75rem; border-bottom: 1px solid #f0f0f0; text-align: center; font-size: 0.875rem;">
              <div style="font-weight: 700; color: #147440;">Rp{{ number_format($product->price, 0, ',', '.') }}</div>
              @if($product->unit)
                <div style="font-size: 0.75rem; color: #666;">/ {{ $product->unit }}</div>
              @endif
            </td>
          @endforeach
        </tr>
        <tr>
          <td style="padding: 0.75rem; border-bottom: 1px solid #f0f0f0; font-weight: 600; font-size: 0.875rem;">Rating</td>
          @foreach($products as $product)
            <td style="padding: 0.75rem; border-bottom: 1px solid #f0f0f0; text-align: center;">
              @if(isset($product->average_rating) && $product->average_rating > 0)
                <div style="display: flex; align-items: center; justify-content: center; gap: 0.25rem; margin-bottom: 0.25rem;">
                  @for($i = 1; $i <= 5; $i++)
                    <i class="bx {{ $i <= round($product->average_rating) ? 'bxs-star' : 'bx-star' }}" 
                       style="color: {{ $i <= round($product->average_rating) ? '#ffc107' : '#ddd' }}; font-size: 0.875rem;"></i>
                  @endfor
                </div>
                <div style="font-size: 0.75rem; color: #666;">{{ number_format($product->average_rating, 1) }} ({{ $product->total_reviews ?? 0 }})</div>
              @else
                <div style="font-size: 0.75rem; color: #999;">Belum ada rating</div>
              @endif
            </td>
          @endforeach
        </tr>
        <tr>
          <td style="padding: 0.75rem; border-bottom: 1px solid #f0f0f0; font-weight: 600; font-size: 0.875rem;">Terjual</td>
          @foreach($products as $product)
            <td style="padding: 0.75rem; border-bottom: 1px solid #f0f0f0; text-align: center; font-size: 0.875rem;">
              @if(isset($product->total_sold) && $product->total_sold > 0)
                @if($product->total_sold >= 1000)
                  {{ number_format($product->total_sold / 1000, 1) }}rb+
                @else
                  {{ $product->total_sold }}+
                @endif
              @else
                <span style="color: #999;">Belum terjual</span>
              @endif
            </td>
          @endforeach
        </tr>
        <tr>
          <td style="padding: 0.75rem; border-bottom: 1px solid #f0f0f0; font-weight: 600; font-size: 0.875rem;">Stok</td>
          @foreach($products as $product)
            <td style="padding: 0.75rem; border-bottom: 1px solid #f0f0f0; text-align: center; font-size: 0.875rem;">
              @if($product->stock_qty > 0)
                <span style="color: #147440; font-weight: 600;">Tersedia ({{ $product->stock_qty }})</span>
              @else
                <span style="color: #dc3545;">Habis</span>
              @endif
            </td>
          @endforeach
        </tr>
        <tr>
          <td style="padding: 0.75rem; border-bottom: 1px solid #f0f0f0; font-weight: 600; font-size: 0.875rem;">Aksi</td>
          @foreach($products as $product)
            <td style="padding: 0.75rem; border-bottom: 1px solid #f0f0f0; text-align: center;">
              <a href="{{ route('mobile.shop.detail', $product->slug) }}" 
                 style="display: inline-block; padding: 0.5rem 1rem; background: #147440; color: white; border-radius: 6px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
                Lihat Detail
              </a>
            </td>
          @endforeach
        </tr>
      </tbody>
    </table>
  </div>
@else
  <div class="empty-state">
    <i class="bx bx-layer" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
    <p>Tidak ada produk untuk dibandingkan</p>
    <a href="{{ route('mobile.shop') }}" 
       style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: #147440; color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
      Pilih Produk
    </a>
  </div>
@endif
@endsection

@push('scripts')
<script>
  function removeFromComparison(productId) {
    fetch('{{ route("mobile.comparison.remove") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        MobileNotification.success(data.message);
        setTimeout(() => {
          location.reload();
        }, 500);
      }
    })
    .catch(error => {
      MobileErrorHandler.handle(error);
    });
  }
  
  function clearComparison() {
    Swal.fire({
      title: 'Hapus Semua?',
      text: 'Semua produk akan dihapus dari perbandingan',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('{{ route("mobile.comparison.clear") }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            MobileNotification.success(data.message);
            setTimeout(() => {
              window.location.href = '{{ route("mobile.shop") }}';
            }, 500);
          }
        });
      }
    });
  }
</script>
@endpush
