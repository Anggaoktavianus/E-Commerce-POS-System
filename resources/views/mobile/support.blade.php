@extends('mobile.layouts.app')

@section('title', 'Bantuan & Support')

@section('content')
<!-- Support Header -->
<div style="background: linear-gradient(135deg, #147440 0%, #1a9c52 100%); padding: 1.5rem; color: white; margin-bottom: 0.5rem;">
  <h4 style="font-size: 1.25rem; font-weight: 700; margin: 0;">
    <i class="bx bx-support"></i> Bantuan & Support
  </h4>
  <p style="font-size: 0.875rem; opacity: 0.9; margin: 0.5rem 0 0 0;">
    Kami siap membantu Anda
  </p>
</div>

<!-- Quick Contact -->
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
    <i class="bx bx-phone"></i> Hubungi Kami
  </h5>
  
  @if($whatsappNumber || $supportPhone)
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber ?? $supportPhone ?? '6282222205204') }}?text={{ urlencode('Halo, saya butuh bantuan') }}" 
       target="_blank"
       style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: #25D366; color: white; border-radius: 8px; text-decoration: none; margin-bottom: 0.75rem;">
      <div style="display: flex; align-items: center; gap: 1rem;">
        <i class="bx bxl-whatsapp" style="font-size: 1.5rem;"></i>
        <div>
          <div style="font-weight: 600; font-size: 0.875rem;">Chat via WhatsApp</div>
          <div style="font-size: 0.75rem; opacity: 0.9;">Klik untuk chat langsung</div>
        </div>
      </div>
      <i class="bx bx-chevron-right"></i>
    </a>
  @endif
  
  @if($supportPhone)
    <a href="tel:{{ $supportPhone }}" 
       style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: #f0f0f0; border-radius: 8px; text-decoration: none; margin-bottom: 0.75rem;">
      <div style="display: flex; align-items: center; gap: 1rem;">
        <i class="bx bx-phone" style="font-size: 1.5rem; color: #147440;"></i>
        <div>
          <div style="font-weight: 600; font-size: 0.875rem; color: #333;">Telepon</div>
          <div style="font-size: 0.75rem; color: #666;">{{ $supportPhone }}</div>
        </div>
      </div>
      <i class="bx bx-chevron-right" style="color: #999;"></i>
    </a>
  @endif
  
  @if($supportEmail)
    <a href="mailto:{{ $supportEmail }}" 
       style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: #f0f0f0; border-radius: 8px; text-decoration: none;">
      <div style="display: flex; align-items: center; gap: 1rem;">
        <i class="bx bx-envelope" style="font-size: 1.5rem; color: #147440;"></i>
        <div>
          <div style="font-weight: 600; font-size: 0.875rem; color: #333;">Email</div>
          <div style="font-size: 0.75rem; color: #666;">{{ $supportEmail }}</div>
        </div>
      </div>
      <i class="bx bx-chevron-right" style="color: #999;"></i>
    </a>
  @endif
</div>

<!-- FAQ Section -->
@if($faqs && $faqs->count() > 0)
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
    <i class="bx bx-help-circle"></i> Pertanyaan Umum (FAQ)
  </h5>
  
  @foreach($faqs as $faq)
    <div style="margin-bottom: 0.75rem; border-bottom: 1px solid #f0f0f0; padding-bottom: 0.75rem;">
      <a href="{{ route('pages.show', $faq->slug) }}" 
         style="text-decoration: none; color: inherit;">
        <div style="font-weight: 600; font-size: 0.875rem; color: #333; margin-bottom: 0.25rem;">
          {{ $faq->title }}
        </div>
        <div style="font-size: 0.75rem; color: #666;">
          {{ \Illuminate\Support\Str::limit(strip_tags($faq->content ?? ''), 100) }}
        </div>
      </a>
    </div>
  @endforeach
</div>
@endif

<!-- Help Topics -->
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #333;">
    <i class="bx bx-info-circle"></i> Topik Bantuan
  </h5>
  
  <a href="{{ route('mobile.transactions') }}" 
     style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: #f8f9fa; border-radius: 8px; text-decoration: none; margin-bottom: 0.75rem;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <i class="bx bx-package" style="font-size: 1.25rem; color: #147440;"></i>
      <span style="font-size: 0.875rem; color: #333;">Cek Status Pesanan</span>
    </div>
    <i class="bx bx-chevron-right" style="color: #999;"></i>
  </a>
  
  <a href="{{ route('mobile.addresses') }}" 
     style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: #f8f9fa; border-radius: 8px; text-decoration: none; margin-bottom: 0.75rem;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <i class="bx bx-map" style="font-size: 1.25rem; color: #147440;"></i>
      <span style="font-size: 0.875rem; color: #333;">Kelola Alamat</span>
    </div>
    <i class="bx bx-chevron-right" style="color: #999;"></i>
  </a>
  
  <a href="{{ route('mobile.cart') }}" 
     style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: #f8f9fa; border-radius: 8px; text-decoration: none;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <i class="bx bx-cart" style="font-size: 1.25rem; color: #147440;"></i>
      <span style="font-size: 0.875rem; color: #333;">Keranjang Belanja</span>
    </div>
    <i class="bx bx-chevron-right" style="color: #999;"></i>
  </a>
</div>
@endsection
