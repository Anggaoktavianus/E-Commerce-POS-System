@extends('mobile.layouts.app')

@section('title', 'Kupon & Promo')

@section('content')
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <h5 style="font-size: 1rem; font-weight: 700; margin: 0; color: #333;">
    <i class="bx bx-purchase-tag"></i> Kupon Tersedia
  </h5>
</div>

@if($availableCoupons->count() > 0)
  <div style="background: white; padding: 0.5rem 0;">
    @foreach($availableCoupons as $coupon)
      <div style="padding: 1rem; border-bottom: 1px solid #f0f0f0; background: linear-gradient(135deg, #147440 0%, #1a9c52 100%); border-radius: 8px; margin-bottom: 0.75rem; color: white;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
          <div style="flex: 1;">
            <div style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $coupon->code }}</div>
            <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">{{ $coupon->name }}</div>
            @if($coupon->description)
              <div style="font-size: 0.75rem; opacity: 0.8;">{{ $coupon->description }}</div>
            @endif
          </div>
          <div style="text-align: right;">
            @if($coupon->type === 'percent')
              <div style="font-size: 1.5rem; font-weight: 700;">{{ $coupon->value }}%</div>
              <div style="font-size: 0.75rem; opacity: 0.9;">OFF</div>
            @else
              <div style="font-size: 1.25rem; font-weight: 700;">Rp{{ number_format($coupon->value, 0, ',', '.') }}</div>
              <div style="font-size: 0.75rem; opacity: 0.9;">DISKON</div>
            @endif
          </div>
        </div>
        
        <div style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 6px; font-size: 0.75rem;">
          @if($coupon->min_purchase)
            <div style="margin-bottom: 0.25rem;">
              <i class="bx bx-info-circle"></i> Min. pembelian: Rp{{ number_format($coupon->min_purchase, 0, ',', '.') }}
            </div>
          @endif
          @if($coupon->expires_at)
            <div>
              <i class="bx bx-calendar"></i> Berlaku hingga: {{ \Carbon\Carbon::parse($coupon->expires_at)->format('d M Y') }}
            </div>
          @endif
          @if($coupon->per_user_limit > 1)
            <div style="margin-top: 0.25rem;">
              <i class="bx bx-user"></i> Maks. {{ $coupon->per_user_limit }}x penggunaan
            </div>
          @endif
        </div>
        
        <button type="button" 
                onclick="copyCouponCode('{{ $coupon->code }}')"
                style="width: 100%; margin-top: 0.75rem; padding: 0.75rem; background: white; color: #147440; border: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
          <i class="bx bx-copy"></i> Salin Kode
        </button>
      </div>
    @endforeach
  </div>
@else
  <div class="empty-state">
    <i class="bx bx-purchase-tag" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
    <p>Tidak ada kupon tersedia saat ini</p>
  </div>
@endif

@if(isset($usedCoupons) && $usedCoupons->count() > 0)
<div style="background: white; padding: 1rem; margin-top: 0.5rem;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
    <i class="bx bx-history"></i> Kupon yang Digunakan
  </h5>
  
  @foreach($usedCoupons as $coupon)
    <div style="padding: 1rem; border-bottom: 1px solid #f0f0f0; background: #f8f9fa; border-radius: 8px; margin-bottom: 0.75rem; opacity: 0.7;">
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <div style="font-weight: 600; color: #333;">{{ $coupon->code }}</div>
          <div style="font-size: 0.75rem; color: #666;">Digunakan: {{ \Carbon\Carbon::parse($coupon->used_at)->format('d M Y, H:i') }}</div>
        </div>
        <i class="bx bx-check-circle" style="font-size: 1.5rem; color: #28a745;"></i>
      </div>
    </div>
  @endforeach
</div>
@endif
@endsection

@push('scripts')
<script>
  function copyCouponCode(code) {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(code).then(() => {
        MobileNotification.success('Kode kupon berhasil disalin: ' + code);
      });
    } else {
      // Fallback
      const input = document.createElement('input');
      input.value = code;
      document.body.appendChild(input);
      input.select();
      document.execCommand('copy');
      document.body.removeChild(input);
      MobileNotification.success('Kode kupon berhasil disalin: ' + code);
    }
  }
</script>
@endpush
