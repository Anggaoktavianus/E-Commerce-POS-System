@extends('mobile.layouts.app')

@section('title', 'Produk')

@section('content')
<div class="empty-state">
  <i class="bx bx-shopping-bag"></i>
  <p>Halaman produk akan segera hadir</p>
  <a href="{{ route('mobile.shop') }}" 
     style="display: inline-block; background: #147440; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 1rem;">
    <i class="bx bx-shopping-bag"></i> Lihat Semua Produk
  </a>
</div>
@endsection
