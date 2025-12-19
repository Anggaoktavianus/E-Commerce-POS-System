@extends('mobile.layouts.app')

@section('title', 'Loyalty Points')

@section('content')
<!-- Points Balance Card -->
<div style="background: linear-gradient(135deg, #147440 0%, #1a9c52 100%); padding: 1.5rem; color: white; margin-bottom: 0.5rem; border-radius: 12px;">
  <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Poin Anda</div>
  <div style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">{{ number_format($balance, 0, ',', '.') }}</div>
  @if($expiringSoon > 0)
    <div style="font-size: 0.75rem; opacity: 0.9; background: rgba(255,255,255,0.2); padding: 0.5rem; border-radius: 6px; margin-top: 0.5rem;">
      <i class="bx bx-time"></i> {{ number_format($expiringSoon, 0, ',', '.') }} poin akan kadaluarsa dalam 30 hari
    </div>
  @endif
</div>

<!-- Redeem Points -->
@if($balance >= 100)
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
    <i class="bx bx-gift"></i> Tukar Poin
  </h5>
  <form id="redeemForm" style="display: flex; gap: 0.5rem;">
    <input type="number" 
           id="redeemPoints"
           min="100" 
           max="{{ $balance }}" 
           step="100"
           placeholder="Min. 100 poin"
           required
           style="flex: 1; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
    <button type="submit" 
            style="background: #147440; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
      Tukar
    </button>
  </form>
  <div style="font-size: 0.75rem; color: #666; margin-top: 0.5rem;">
    <i class="bx bx-info-circle"></i> 1 poin = Rp1 discount
  </div>
</div>
@endif

<!-- Points History -->
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
    <i class="bx bx-history"></i> Riwayat Poin
  </h5>
  
  @if($transactions->count() > 0)
    <div>
      @foreach($transactions as $transaction)
        <div style="padding: 1rem; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
          <div style="flex: 1;">
            <div style="font-weight: 600; font-size: 0.875rem; color: #333; margin-bottom: 0.25rem;">
              {{ $transaction->description }}
            </div>
            <div style="font-size: 0.75rem; color: #666;">
              {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }}
            </div>
            @if($transaction->expires_at)
              <div style="font-size: 0.7rem; color: #999; margin-top: 0.25rem;">
                Kadaluarsa: {{ \Carbon\Carbon::parse($transaction->expires_at)->format('d M Y') }}
              </div>
            @endif
          </div>
          <div style="text-align: right;">
            @if($transaction->type === 'earn')
              <div style="font-size: 1rem; font-weight: 700; color: #28a745;">+{{ number_format($transaction->points, 0, ',', '.') }}</div>
            @else
              <div style="font-size: 1rem; font-weight: 700; color: #dc3545;">-{{ number_format($transaction->points, 0, ',', '.') }}</div>
            @endif
          </div>
        </div>
      @endforeach
    </div>
    
    <div style="padding: 1rem; text-align: center;">
      {{ $transactions->links() }}
    </div>
  @else
    <div style="text-align: center; padding: 2rem; color: #999;">
      <i class="bx bx-history" style="font-size: 3rem; margin-bottom: 0.5rem; opacity: 0.3;"></i>
      <p style="font-size: 0.875rem; margin: 0;">Belum ada riwayat poin</p>
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('redeemForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const points = parseInt(document.getElementById('redeemPoints').value);
    
    if (points < 100) {
      MobileNotification.error('Minimum 100 poin untuk ditukar');
      return;
    }
    
    if (points > {{ $balance }}) {
      MobileNotification.error('Poin tidak mencukupi');
      return;
    }
    
    MobileLoading.show('Memproses penukaran poin...');
    
    fetch('{{ route("mobile.loyalty.redeem") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ points: points })
    })
    .then(response => response.json())
    .then(data => {
      MobileLoading.hide();
      if (data.success) {
        Swal.fire({
          title: 'Berhasil!',
          html: `Anda mendapatkan diskon Rp${new Intl.NumberFormat('id-ID').format(data.discount)}<br>Sisa poin: ${new Intl.NumberFormat('id-ID').format(data.remaining_points)}`,
          icon: 'success',
          confirmButtonText: 'OK'
        }).then(() => {
          location.reload();
        });
      } else {
        MobileNotification.error(data.message || 'Gagal menukar poin');
      }
    })
    .catch(error => {
      MobileLoading.hide();
      MobileErrorHandler.handle(error);
    });
  });
</script>
@endpush
