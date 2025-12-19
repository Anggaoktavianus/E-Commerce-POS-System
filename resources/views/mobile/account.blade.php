@extends('mobile.layouts.app')

@php
use Illuminate\Support\Facades\Auth;
@endphp

@section('title', 'Akun')

@section('content')
@auth
<div style="background: white; padding: 1rem; margin-bottom: 0.5rem;">
  <!-- User Profile Header -->
  <div style="display: flex; align-items: center; gap: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e0e0e0;">
    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #147440 0%, #1a9c52 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; font-weight: 700;">
      {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
    </div>
    <div style="flex: 1;">
      <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.25rem; color: #333;">
        {{ Auth::user()->name }}
      </h5>
      <p style="font-size: 0.875rem; color: #666; margin: 0;">
        {{ Auth::user()->email }}
      </p>
    </div>
  </div>
</div>

<!-- Menu Items -->
<div style="background: white; padding: 0.5rem 0; margin-bottom: 0.5rem;">
  <a href="{{ route('mobile.profile') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; text-decoration: none; color: #333; border-bottom: 1px solid #f0f0f0;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <i class="bx bx-user" style="font-size: 1.25rem; color: #147440;"></i>
      <span style="font-size: 0.875rem; font-weight: 500;">Profil Saya</span>
    </div>
    <i class="bx bx-chevron-right" style="color: #999;"></i>
  </a>
  
  <a href="{{ route('mobile.addresses') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; text-decoration: none; color: #333; border-bottom: 1px solid #f0f0f0;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <i class="bx bx-map" style="font-size: 1.25rem; color: #147440;"></i>
      <span style="font-size: 0.875rem; font-weight: 500;">Alamat Saya</span>
    </div>
    <i class="bx bx-chevron-right" style="color: #999;"></i>
  </a>
  
  <a href="{{ route('mobile.transactions') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; text-decoration: none; color: #333; border-bottom: 1px solid #f0f0f0;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <i class="bx bx-list-ul" style="font-size: 1.25rem; color: #147440;"></i>
      <span style="font-size: 0.875rem; font-weight: 500;">Pesanan Saya</span>
    </div>
    <i class="bx bx-chevron-right" style="color: #999;"></i>
  </a>
  
  <a href="{{ route('mobile.notifications') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; text-decoration: none; color: #333; border-bottom: 1px solid #f0f0f0;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <i class="bx bx-bell" style="font-size: 1.25rem; color: #147440;"></i>
      <span style="font-size: 0.875rem; font-weight: 500;">Notifikasi</span>
    </div>
    <i class="bx bx-chevron-right" style="color: #999;"></i>
  </a>
  
  <a href="{{ route('mobile.wishlist') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; text-decoration: none; color: #333; border-bottom: 1px solid #f0f0f0;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <i class="bx bx-heart" style="font-size: 1.25rem; color: #147440;"></i>
      <span style="font-size: 0.875rem; font-weight: 500;">Wishlist</span>
    </div>
    <i class="bx bx-chevron-right" style="color: #999;"></i>
  </a>
  
  <a href="{{ route('mobile.coupons') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; text-decoration: none; color: #333; border-bottom: 1px solid #f0f0f0;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <i class="bx bx-purchase-tag" style="font-size: 1.25rem; color: #147440;"></i>
      <span style="font-size: 0.875rem; font-weight: 500;">Kupon & Promo</span>
    </div>
    <i class="bx bx-chevron-right" style="color: #999;"></i>
  </a>
  
  <a href="{{ route('mobile.loyalty') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; text-decoration: none; color: #333; border-bottom: 1px solid #f0f0f0;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <i class="bx bx-star" style="font-size: 1.25rem; color: #147440;"></i>
      <span style="font-size: 0.875rem; font-weight: 500;">Loyalty Points</span>
    </div>
    <i class="bx bx-chevron-right" style="color: #999;"></i>
  </a>
  
  <a href="{{ route('mobile.comparison') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; text-decoration: none; color: #333; border-bottom: 1px solid #f0f0f0;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <i class="bx bx-layer" style="font-size: 1.25rem; color: #147440;"></i>
      <span style="font-size: 0.875rem; font-weight: 500;">Bandingkan Produk</span>
    </div>
    <i class="bx bx-chevron-right" style="color: #999;"></i>
  </a>
  
  <a href="{{ route('mobile.support') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; text-decoration: none; color: #333; border-bottom: 1px solid #f0f0f0;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <i class="bx bx-support" style="font-size: 1.25rem; color: #147440;"></i>
      <span style="font-size: 0.875rem; font-weight: 500;">Bantuan & Support</span>
    </div>
    <i class="bx bx-chevron-right" style="color: #999;"></i>
  </a>
</div>

<!-- Logout -->
<div style="background: white; padding: 0.5rem 0;">
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.75rem; padding: 1rem; background: none; border: none; color: #dc3545; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
      <i class="bx bx-log-out"></i>
      <span>Keluar</span>
    </button>
  </form>
</div>
@else
<div class="empty-state">
  <i class="bx bx-user"></i>
  <p>Silakan login untuk mengakses akun</p>
  <a href="{{ route('mobile.login') }}" 
     style="display: inline-block; background: #147440; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 1rem;">
    <i class="bx bx-log-in"></i> Login
  </a>
</div>
@endauth
@endsection
