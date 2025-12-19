@extends('mobile.layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Detail Pesanan')

@section('content')
<!-- Order Header -->
<div style="background: linear-gradient(135deg, #147440 0%, #1a9c52 100%); padding: 1.5rem; color: white; margin-bottom: 0.5rem;">
  <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Pesanan</div>
  <div style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $order->order_number }}</div>
  <div style="display: flex; align-items: center; gap: 0.5rem;">
    <span style="padding: 0.25rem 0.75rem; background: rgba(255,255,255,0.2); border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
      {{ ucfirst(str_replace('_', ' ', $order->status)) }}
    </span>
    <span style="font-size: 0.875rem; opacity: 0.9;">
      {{ $order->created_at->format('d M Y, H:i') }}
    </span>
  </div>
</div>

<!-- Order Items -->
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
    <i class="bx bx-package"></i> Item Pesanan
  </h5>
  
  @foreach($order->items as $item)
    <div style="display: flex; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid #f0f0f0;">
      <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
        @if($item->product && $item->product->main_image_path)
          <img src="{{ Storage::url($item->product->main_image_path) }}" 
               alt="{{ $item->product_name }}"
               style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;"
               onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        @endif
        <div style="display: {{ $item->product && $item->product->main_image_path ? 'none' : 'flex' }}; align-items: center; justify-content: center; width: 100%; height: 100%; color: #999;">
          <i class="bx bx-package" style="font-size: 1.5rem;"></i>
        </div>
      </div>
      <div style="flex: 1;">
        <div style="font-size: 0.875rem; font-weight: 500; color: #333; margin-bottom: 0.25rem;">
          {{ $item->product_name }}
        </div>
        <div style="font-size: 0.75rem; color: #666; margin-bottom: 0.5rem;">
          {{ $item->quantity }}x Rp{{ number_format($item->price, 0, ',', '.') }}
        </div>
        <div style="font-size: 0.875rem; font-weight: 600; color: #147440;">
          Rp{{ number_format($item->total, 0, ',', '.') }}
        </div>
      </div>
    </div>
  @endforeach
</div>

<!-- Order Summary -->
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
    <i class="bx bx-receipt"></i> Ringkasan Pesanan
  </h5>
  
  <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
    <span style="color: #666;">Subtotal</span>
    <span style="font-weight: 600;">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
  </div>
  
  @if($order->discount > 0)
    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
      <span style="color: #666;">Diskon</span>
      <span style="font-weight: 600; color: #28a745;">-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
    </div>
  @endif
  
  <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
    <span style="color: #666;">Ongkir</span>
    <span style="font-weight: 600;">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
  </div>
  
  <div style="border-top: 2px solid #147440; padding-top: 0.75rem; margin-top: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
    <span style="font-size: 1.125rem; font-weight: 700; color: #333;">Total</span>
    <span style="font-size: 1.25rem; font-weight: 700; color: #147440;">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
  </div>
</div>

<!-- Shipping Address -->
@if($order->shipping_address)
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
    <i class="bx bx-map"></i> Alamat Pengiriman
  </h5>
  
  <div style="font-size: 0.875rem; color: #666; line-height: 1.6;">
    <div style="font-weight: 600; color: #333; margin-bottom: 0.5rem;">
      {{ $order->shipping_address['first_name'] ?? $order->shipping_address['recipient_name'] ?? 'N/A' }}
    </div>
    <div>{{ $order->shipping_address['address'] ?? '' }}</div>
    <div>{{ $order->shipping_address['city'] ?? '' }}</div>
    @if(isset($order->shipping_address['postal_code']))
      <div>{{ $order->shipping_address['postal_code'] }}</div>
    @endif
    <div style="margin-top: 0.5rem; color: #999;">
      <i class="bx bx-phone"></i> {{ $order->shipping_address['phone'] ?? $order->shipping_address['recipient_phone'] ?? '-' }}
    </div>
  </div>
</div>
@endif

<!-- Payment Info -->
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
    <i class="bx bx-credit-card"></i> Informasi Pembayaran
  </h5>
  
  @if($order->paymentTransactions->isNotEmpty())
    @php($paymentTransaction = $order->paymentTransactions->first())
    <div style="font-size: 0.875rem; color: #666;">
      <div style="margin-bottom: 0.5rem;">
        <span style="color: #333; font-weight: 500;">Metode:</span> 
        {{ $paymentTransaction->payment_method ?? '-' }}
      </div>
      <div>
        <span style="color: #333; font-weight: 500;">Status:</span>
        <span style="padding: 0.25rem 0.75rem; background: #d4edda; color: #155724; border-radius: 12px; font-size: 0.75rem; font-weight: 600; margin-left: 0.5rem;">
          {{ ucfirst($paymentTransaction->status ?? $order->status) }}
        </span>
      </div>
    </div>
  @else
    <div style="font-size: 0.875rem; color: #666;">
      <div style="margin-bottom: 0.5rem;">
        <span style="color: #333; font-weight: 500;">Metode:</span> 
        {{ $order->payment_method ?? '-' }}
      </div>
      <div>
        <span style="color: #333; font-weight: 500;">Status:</span>
        <span style="padding: 0.25rem 0.75rem; background: {{ $order->status == 'paid' ? '#d4edda' : '#fff3cd' }}; color: {{ $order->status == 'paid' ? '#155724' : '#856404' }}; border-radius: 12px; font-size: 0.75rem; font-weight: 600; margin-left: 0.5rem;">
          {{ ucfirst(str_replace('_', ' ', $order->status)) }}
        </span>
      </div>
    </div>
  @endif
</div>

<!-- Order Tracking Timeline -->
@if(in_array($order->status, ['paid', 'processing', 'shipped', 'delivered', 'completed']))
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
    <i class="bx bx-map"></i> Lacak Pesanan
  </h5>
  
  <div style="position: relative; padding-left: 1.5rem;">
    <!-- Timeline Line -->
    <div style="position: absolute; left: 0.5rem; top: 0; bottom: 0; width: 2px; background: #e0e0e0;"></div>
    
    <!-- Order Created -->
    <div style="position: relative; margin-bottom: 1.5rem;">
      <div style="position: absolute; left: -1.25rem; top: 0.25rem; width: 12px; height: 12px; background: #147440; border-radius: 50%; border: 2px solid white; z-index: 1;"></div>
      <div style="font-size: 0.875rem; font-weight: 600; color: #333; margin-bottom: 0.25rem;">
        Pesanan Dibuat
      </div>
      <div style="font-size: 0.75rem; color: #666;">
        {{ $order->created_at->format('d M Y, H:i') }}
      </div>
    </div>
    
    <!-- Payment -->
    @if($order->paid_at)
      <div style="position: relative; margin-bottom: 1.5rem;">
        <div style="position: absolute; left: -1.25rem; top: 0.25rem; width: 12px; height: 12px; background: #147440; border-radius: 50%; border: 2px solid white; z-index: 1;"></div>
        <div style="font-size: 0.875rem; font-weight: 600; color: #333; margin-bottom: 0.25rem;">
          Pembayaran Diterima
        </div>
        <div style="font-size: 0.75rem; color: #666;">
          {{ $order->paid_at->format('d M Y, H:i') }}
        </div>
      </div>
    @elseif($order->status == 'pending')
      <div style="position: relative; margin-bottom: 1.5rem; opacity: 0.5;">
        <div style="position: absolute; left: -1.25rem; top: 0.25rem; width: 12px; height: 12px; background: #ddd; border-radius: 50%; border: 2px solid white; z-index: 1;"></div>
        <div style="font-size: 0.875rem; font-weight: 600; color: #666; margin-bottom: 0.25rem;">
          Menunggu Pembayaran
        </div>
      </div>
    @endif
    
    <!-- Processing -->
    @if($order->processed_at)
      <div style="position: relative; margin-bottom: 1.5rem;">
        <div style="position: absolute; left: -1.25rem; top: 0.25rem; width: 12px; height: 12px; background: #147440; border-radius: 50%; border: 2px solid white; z-index: 1;"></div>
        <div style="font-size: 0.875rem; font-weight: 600; color: #333; margin-bottom: 0.25rem;">
          Pesanan Diproses
        </div>
        <div style="font-size: 0.75rem; color: #666;">
          {{ $order->processed_at->format('d M Y, H:i') }}
        </div>
      </div>
    @elseif(in_array($order->status, ['paid', 'processing']))
      <div style="position: relative; margin-bottom: 1.5rem; opacity: 0.5;">
        <div style="position: absolute; left: -1.25rem; top: 0.25rem; width: 12px; height: 12px; background: #ddd; border-radius: 50%; border: 2px solid white; z-index: 1;"></div>
        <div style="font-size: 0.875rem; font-weight: 600; color: #666; margin-bottom: 0.25rem;">
          Menunggu Diproses
        </div>
      </div>
    @endif
    
    <!-- Shipped -->
    @if($order->shipped_at)
      <div style="position: relative; margin-bottom: 1.5rem;">
        <div style="position: absolute; left: -1.25rem; top: 0.25rem; width: 12px; height: 12px; background: #147440; border-radius: 50%; border: 2px solid white; z-index: 1;"></div>
        <div style="font-size: 0.875rem; font-weight: 600; color: #333; margin-bottom: 0.25rem;">
          Pesanan Dikirim
        </div>
        <div style="font-size: 0.75rem; color: #666; margin-bottom: 0.25rem;">
          {{ $order->shipped_at->format('d M Y, H:i') }}
        </div>
        @if($order->tracking_number)
          <div style="font-size: 0.75rem; color: #147440; font-weight: 600;">
            <i class="bx bx-package"></i> Resi: {{ $order->tracking_number }}
          </div>
        @endif
      </div>
    @elseif(in_array($order->status, ['paid', 'processing']))
      <div style="position: relative; margin-bottom: 1.5rem; opacity: 0.5;">
        <div style="position: absolute; left: -1.25rem; top: 0.25rem; width: 12px; height: 12px; background: #ddd; border-radius: 50%; border: 2px solid white; z-index: 1;"></div>
        <div style="font-size: 0.875rem; font-weight: 600; color: #666; margin-bottom: 0.25rem;">
          Menunggu Pengiriman
        </div>
      </div>
    @endif
    
    <!-- Delivered -->
    @if($order->delivered_at)
      <div style="position: relative;">
        <div style="position: absolute; left: -1.25rem; top: 0.25rem; width: 12px; height: 12px; background: #147440; border-radius: 50%; border: 2px solid white; z-index: 1;"></div>
        <div style="font-size: 0.875rem; font-weight: 600; color: #333; margin-bottom: 0.25rem;">
          Pesanan Diterima
        </div>
        <div style="font-size: 0.75rem; color: #666;">
          {{ $order->delivered_at->format('d M Y, H:i') }}
        </div>
      </div>
    @elseif($order->status == 'shipped')
      <div style="position: relative; opacity: 0.5;">
        <div style="position: absolute; left: -1.25rem; top: 0.25rem; width: 12px; height: 12px; background: #ddd; border-radius: 50%; border: 2px solid white; z-index: 1;"></div>
        <div style="font-size: 0.875rem; font-weight: 600; color: #666; margin-bottom: 0.25rem;">
          Dalam Perjalanan
        </div>
      </div>
    @endif
  </div>
</div>
@endif

<!-- Action Buttons -->
<div style="padding: 1rem; background: white; margin-bottom: 0.5rem; border-radius: 12px;">
  @if($order->status == 'pending')
    <div style="background: #fff3cd; padding: 1rem; border-radius: 10px; margin-bottom: 0.75rem;">
      <p style="font-size: 0.875rem; color: #856404; margin: 0; text-align: center;">
        <i class="bx bx-info-circle"></i> Silakan selesaikan pembayaran melalui link yang dikirim ke email atau WhatsApp Anda.
      </p>
    </div>
  @endif
  
  @if(in_array($order->status, ['paid', 'processing']))
    <a href="{{ route('orders.track', base64_encode($order->order_number)) }}" 
       style="display: block; background: #17a2b8; color: white; text-align: center; padding: 0.875rem; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.75rem;">
      <i class="bx bx-map"></i> Lacak Pesanan
    </a>
  @endif
  
  @if($order->status == 'paid')
    <a href="{{ route('orders.invoice', base64_encode($order->order_number)) }}" 
       target="_blank"
       style="display: block; background: #6c757d; color: white; text-align: center; padding: 0.875rem; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.75rem;">
      <i class="bx bx-download"></i> Download Invoice
    </a>
  @endif
  
  <a href="{{ route('mobile.transactions') }}" 
     style="display: block; background: #f0f0f0; color: #333; text-align: center; padding: 0.875rem; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.875rem;">
    <i class="bx bx-arrow-back"></i> Kembali ke Daftar Pesanan
  </a>
</div>
@endsection
