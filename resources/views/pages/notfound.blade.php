@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan - ' . config('app.name'))

@section('meta_description', 'Halaman yang Anda cari tidak ditemukan di ' . config('app.name') . '. Kembali ke beranda untuk melihat produk kami.')

@section('meta_keywords', '404, halaman tidak ditemukan, error, not found, ' . config('app.name') . ', toko online')

@section('og_image', asset('storage/defaults/og-404.jpg'))

@section('content')
<div class="container py-5 text-center" style="margin-top:120px;">
    <h1 class="display-4">404</h1>
    <p class="lead mb-4">Halaman yang Anda cari tidak ditemukan.</p>
    <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4">Kembali ke Beranda</a>
</div>
@endsection
