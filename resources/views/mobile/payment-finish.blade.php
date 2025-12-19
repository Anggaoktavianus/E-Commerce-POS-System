@extends('mobile.layouts.app')

@section('title', 'Status Pembayaran')

@section('content')
@if(isset($payment_status) && $payment_status === 'unfinish')
  <!-- Unfinish Payment -->
  <div style="padding: 2rem 1rem; text-align: center;">
    <div style="width: 100px; height: 100px; background: #fff3cd; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
      <i class="bx bx-x-circle" style="font-size: 3rem; color: #856404;"></i>
    </div>
    <h2 style="font-size: 1.5rem; font-weight: 700; color: #856404; margin-bottom: 1rem;">Pembayaran Dibatalkan</h2>
    <p style="color: #666; font-size: 0.875rem; margin-bottom: 2rem; line-height: 1.6;">
      {{ $message ?? 'Anda telah membatalkan pembayaran. Silakan coba lagi jika masih berminat.' }}
    </p>
      <a href="{{ route('mobile.cart') }}" 
         style="display: inline-block; background: #147440; color: white; padding: 0.875rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.875rem; margin-right: 0.5rem;">
        <i class="bx bx-cart"></i> Kembali ke Keranjang
      </a>
    <a href="{{ route('mobile.transactions') }}" 
       style="display: inline-block; background: #f0f0f0; color: #333; padding: 0.875rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.875rem;">
      <i class="bx bx-list-ul"></i> Lihat Pesanan
    </a>
  </div>

@elseif(isset($payment_status) && $payment_status === 'error')
  <!-- Error Payment -->
  <div style="padding: 2rem 1rem; text-align: center;">
    <div style="width: 100px; height: 100px; background: #ffebee; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
      <i class="bx bx-error-circle" style="font-size: 3rem; color: #c62828;"></i>
    </div>
    <h2 style="font-size: 1.5rem; font-weight: 700; color: #c62828; margin-bottom: 1rem;">Terjadi Kesalahan</h2>
    <p style="color: #666; font-size: 0.875rem; margin-bottom: 2rem; line-height: 1.6;">
      {{ $message ?? 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.' }}
    </p>
      <a href="{{ route('mobile.cart') }}" 
         style="display: inline-block; background: #147440; color: white; padding: 0.875rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.875rem; margin-right: 0.5rem;">
        <i class="bx bx-cart"></i> Kembali ke Keranjang
      </a>
    <a href="{{ route('mobile.transactions') }}" 
       style="display: inline-block; background: #f0f0f0; color: #333; padding: 0.875rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.875rem;">
      <i class="bx bx-list-ul"></i> Lihat Pesanan
    </a>
  </div>

@else
  <!-- Success/Finish Payment -->
  @if($order->status === 'paid')
    <div style="padding: 2rem 1rem; text-align: center;">
      <div style="width: 100px; height: 100px; background: #d4edda; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
        <i class="bx bx-check-circle" style="font-size: 3rem; color: #155724;"></i>
      </div>
      <h2 style="font-size: 1.5rem; font-weight: 700; color: #155724; margin-bottom: 1rem;">Pembayaran Berhasil!</h2>
      <p style="color: #666; font-size: 0.875rem; margin-bottom: 2rem; line-height: 1.6;">
        Terima kasih! Pembayaran Anda telah berhasil diproses. Pesanan Anda sedang diproses.
      </p>
    </div>
  @else
    <div style="padding: 2rem 1rem; text-align: center;">
      <div style="width: 100px; height: 100px; background: #fff3cd; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
        <i class="bx bx-time" style="font-size: 3rem; color: #856404;"></i>
      </div>
      <h2 style="font-size: 1.5rem; font-weight: 700; color: #856404; margin-bottom: 1rem;">Menunggu Pembayaran</h2>
      <p style="color: #666; font-size: 0.875rem; margin-bottom: 2rem; line-height: 1.6;">
        Pesanan Anda sedang menunggu konfirmasi pembayaran. Silakan selesaikan pembayaran Anda.
      </p>
    </div>
  @endif

  <!-- Order Info -->
  <div style="background: white; padding: 1rem; margin-bottom: 0.5rem; border-radius: 12px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #f0f0f0;">
      <div>
        <div style="font-size: 0.75rem; color: #666; margin-bottom: 0.25rem;">Nomor Pesanan</div>
        <div style="font-size: 1rem; font-weight: 700; color: #333;">{{ $order->order_number }}</div>
      </div>
      <div style="padding: 0.25rem 0.75rem; background: {{ $order->status == 'paid' ? '#d4edda' : '#fff3cd' }}; color: {{ $order->status == 'paid' ? '#155724' : '#856404' }}; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
      </div>
    </div>
    
    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
      <span style="color: #666;">Tanggal</span>
      <span style="font-weight: 600; color: #333;">{{ $order->created_at->format('d M Y, H:i') }}</span>
    </div>
    
    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
      <span style="color: #666;">Total Pembayaran</span>
      <span style="font-size: 1.125rem; font-weight: 700; color: #147440;">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
    </div>
  </div>

  <!-- Action Buttons -->
  <div style="padding: 1rem;">
    @if($order->status === 'paid')
      <a href="{{ route('mobile.order.detail', base64_encode($order->order_number)) }}" 
         style="display: block; background: #147440; color: white; text-align: center; padding: 1rem; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 1rem; margin-bottom: 0.75rem;">
        <i class="bx bx-show"></i> Lihat Detail Pesanan
      </a>
    @else
      <a href="{{ route('mobile.order.detail', base64_encode($order->order_number)) }}" 
         style="display: block; background: #147440; color: white; text-align: center; padding: 1rem; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 1rem; margin-bottom: 0.75rem;">
        <i class="bx bx-credit-card"></i> Lanjutkan Pembayaran
      </a>
    @endif
    
    <a href="{{ route('mobile.transactions') }}" 
       style="display: block; background: #f0f0f0; color: #333; text-align: center; padding: 1rem; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 0.875rem;">
      <i class="bx bx-list-ul"></i> Lihat Semua Pesanan
    </a>
  </div>
@endif
@endsection
