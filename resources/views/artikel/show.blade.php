@extends('layouts.app')

@section('title', $artikel->judul . ' - ' . config('app.name'))

@section('meta_description', $artikel->meta_description ?: Str::limit(strip_tags($artikel->konten), 160))

@section('meta_keywords', $artikel->meta_keywords ?: 'artikel, ' . $artikel->kategoriArtikel->nama . ', ' . config('app.name'))

@section('og_image', $artikel->gambar_utama ? Storage::url($artikel->gambar_utama) : asset('storage/uploads/settings/pA4eU0uxj49qQm9buxJyHQVoP8u8u6MyfjDbSNic.png'))

@section('content')
<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">{{ $artikel->judul }}</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('artikel.index') }}">Artikel</a></li>
        <li class="breadcrumb-item active text-white">{{ Str::limit($artikel->judul, 50) }}</li>
    </ol>
    <div class="d-flex justify-content-center align-items-center gap-4 flex-wrap mt-3">
        <span class="badge bg-light text-dark">
            <i class="bx bx-category me-1"></i> {{ $artikel->kategoriArtikel->nama }}
        </span>
        <span class="text-white">
            <i class="bx bx-calendar me-1"></i> {{ $artikel->published_at ? $artikel->published_at->format('d M Y') : $artikel->created_at->format('d M Y') }}
        </span>
        <span class="text-white">
            <i class="bx bx-time me-1"></i> {{ $artikel->reading_time }} menit baca
        </span>
        <span class="text-white">
            <i class="bx bx-user me-1"></i> {{ $artikel->user ? $artikel->user->name : 'Admin' }}
        </span>
    </div>
</div>
<!-- Single Page Header End -->

<!-- Featured Image -->
@if($artikel->gambar_utama)
<section class="py-4 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <img src="{{ Storage::url($artikel->gambar_utama) }}" 
                     class="img-fluid rounded shadow-lg" 
                     alt="{{ $artikel->judul }}"
                     style="width: 100%; height: auto; max-height: 500px; object-fit: cover;">
            </div>
        </div>
    </div>
</section>
@endif

<!-- Article Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <article class="article-content">
                    {!! $artikel->konten !!}
                </article>
                
                <!-- Article Tags -->
                @if($artikel->meta_keywords)
                <div class="mt-5">
                    <h6 class="mb-3 text-white">Tags:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(explode(',', $artikel->meta_keywords) as $keyword)
                            <span class="badge bg-secondary">{{ trim($keyword) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Share Buttons -->
                <div class="mt-5">
                    <h6 class="mb-3 text-white">Bagikan artikel:</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bx bxl-facebook"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $artikel->judul }}" 
                           target="_blank" class="btn btn-outline-info btn-sm">
                            <i class="bx bxl-twitter"></i> Twitter
                        </a>
                        <a href="https://wa.me/?text={{ $artikel->judul }} - {{ url()->current() }}" 
                           target="_blank" class="btn btn-outline-success btn-sm">
                            <i class="bx bxl-whatsapp"></i> WhatsApp
                        </a>
                        <a href="https://t.me/share/url?url={{ url()->current() }}&text={{ $artikel->judul }}" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bx bxl-telegram"></i> Telegram
                        </a>
                        <a href="https://www.instagram.com/" 
                           target="_blank" class="btn btn-outline-danger btn-sm" 
                           onclick="copyToClipboard('{{ url()->current() }}'); alert('Link artikel telah disalin! Buka Instagram dan paste link di story atau postingan Anda.'); return false;">
                            <i class="bx bxl-instagram"></i> Instagram
                        </a>
                        <a href="https://www.tiktok.com/" 
                           target="_blank" class="btn btn-outline-dark btn-sm"
                           onclick="copyToClipboard('{{ url()->current() }}'); alert('Link artikel telah disalin! Buka TikTok dan paste link di video atau postingan Anda.'); return false;">
                            <i class="bx bxl-tiktok"></i> TikTok
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Related Articles -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0 text-white"><i class="bx bx-file me-2"></i>Artikel Terkait</h6>
                    </div>
                    <div class="card-body">
                        @php
                        $relatedArtikel = \App\Models\Artikel::where('status', 'published')
                            ->where('id', '!=', $artikel->id)
                            ->where('kategori_artikel_id', $artikel->kategori_artikel_id)
                            ->orderBy('created_at', 'desc')
                            ->limit(3)
                            ->get();
                        @endphp
                        
                        @forelse($relatedArtikel as $related)
                            <div class="d-flex mb-3">
                                @if($related->gambar_thumbnail)
                                    <img src="{{ Storage::url($related->gambar_thumbnail) }}" 
                                         class="rounded me-3" 
                                         alt="{{ $related->judul }}"
                                         style="width: 80px; height: 60px; object-fit: cover;">
                                @elseif($related->gambar_utama)
                                    <img src="{{ Storage::url($related->gambar_utama) }}" 
                                         class="rounded me-3" 
                                         alt="{{ $related->judul }}"
                                         style="width: 80px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 80px; height: 60px;">
                                        <i class="bx bx-image text-muted"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('artikel.show', $related->slug) }}" 
                                           class="text-decoration-none text-dark">
                                            {{ Str::limit($related->judul, 50) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="bx bx-calendar me-1"></i> {{ $related->created_at->format('d M Y') }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">Belum ada artikel terkait</p>
                        @endforelse
                    </div>
                </div>
                
                <!-- Categories -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0 text-white"><i class="bx bx-category me-2"></i>Kategori</h6>
                    </div>
                    <div class="card-body">
                        @php
                        $kategoriList = \App\Models\KategoriArtikel::withCount(['artikels' => function($query) {
                            $query->where('status', 'published');
                        }])->get();
                        @endphp
                        
                        @foreach($kategoriList as $kat)
                            @if($kat->artikels_count > 0)
                                <a href="{{ route('artikel.index') }}?kategori={{ $kat->id }}" 
                                   class="d-flex justify-content-between align-items-center text-decoration-none text-dark mb-2">
                                    <span>{{ $kat->nama }}</span>
                                    <span class="badge bg-primary">{{ $kat->artikels_count }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
                
                <!-- Popular Articles -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0 text-white"><i class="bx bx-trending-up me-2"></i>Artikel Populer</h6>
                    </div>
                    <div class="card-body">
                        @php
                        $popularArtikel = \App\Models\Artikel::where('status', 'published')
                            ->orderBy('views', 'desc')
                            ->limit(3)
                            ->get();
                        @endphp
                        
                        @forelse($popularArtikel as $popular)
                            <div class="d-flex mb-3">
                                @if($popular->gambar_thumbnail)
                                    <img src="{{ Storage::url($popular->gambar_thumbnail) }}" 
                                         class="rounded me-3" 
                                         alt="{{ $popular->judul }}"
                                         style="width: 80px; height: 60px; object-fit: cover;">
                                @elseif($popular->gambar_utama)
                                    <img src="{{ Storage::url($popular->gambar_utama) }}" 
                                         class="rounded me-3" 
                                         alt="{{ $popular->judul }}"
                                         style="width: 80px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 80px; height: 60px;">
                                        <i class="bx bx-image text-muted"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('artikel.show', $popular->slug) }}" 
                                           class="text-decoration-none text-dark">
                                            {{ Str::limit($popular->judul, 50) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="bx bx-show me-1"></i> {{ number_format($popular->views) }} views
                                    </small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">Belum ada artikel populer</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Navigation -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('artikel.index') }}" class="btn btn-outline-primary">
                        <i class="bx bx-arrow-back me-2"></i> Kembali ke Artikel
                    </a>
                    @php
                    $nextArtikel = \App\Models\Artikel::where('status', 'published')
                        ->where('created_at', '>', $artikel->created_at)
                        ->orderBy('created_at', 'asc')
                        ->first();
                    $prevArtikel = \App\Models\Artikel::where('status', 'published')
                        ->where('created_at', '<', $artikel->created_at)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    @endphp
                    
                    <div>
                        @if($prevArtikel)
                            <a href="{{ route('artikel.show', $prevArtikel->slug) }}" 
                               class="btn btn-outline-secondary me-2" title="Artikel Sebelumnya">
                                <i class="bx bx-chevron-left"></i>
                            </a>
                        @endif
                        @if($nextArtikel)
                            <a href="{{ route('artikel.show', $nextArtikel->slug) }}" 
                               class="btn btn-outline-secondary" title="Artikel Selanjutnya">
                                <i class="bx bx-chevron-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.article-content {
    line-height: 1.8;
    font-size: 1.1rem;
}

.article-content h1,
.article-content h2,
.article-content h3,
.article-content h4,
.article-content h5,
.article-content h6 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.article-content h1 { font-size: 2rem; }
.article-content h2 { font-size: 1.75rem; }
.article-content h3 { font-size: 1.5rem; }
.article-content h4 { font-size: 1.25rem; }
.article-content h5 { font-size: 1.1rem; }
.article-content h6 { font-size: 1rem; }

.article-content p {
    margin-bottom: 1.5rem;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1.5rem 0;
}

.article-content blockquote {
    border-left: 4px solid #0d6efd;
    padding-left: 1.5rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #6c757d;
}

.article-content ul,
.article-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.article-content li {
    margin-bottom: 0.5rem;
}

.article-content pre {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 1rem;
    overflow-x: auto;
    margin: 1.5rem 0;
}

.article-content code {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 0.125rem 0.25rem;
    font-size: 0.875rem;
}

.article-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5rem 0;
}

.article-content th,
.article-content td {
    border: 1px solid #dee2e6;
    padding: 0.75rem;
    text-align: left;
}

.article-content th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endpush

@push('scripts')
<script>
// Increment view count
$(document).ready(function() {
    $.ajax({
        url: '{{ route("artikel.increment-views", $artikel->id) }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            console.log('View incremented');
        },
        error: function(xhr) {
            console.log('Error incrementing view');
        }
    });
});

// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        console.log('Link copied to clipboard');
    }, function(err) {
        console.error('Could not copy text: ', err);
        // Fallback for older browsers
        var textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            console.log('Link copied to clipboard (fallback)');
        } catch (err) {
            console.error('Fallback: Could not copy text: ', err);
        }
        document.body.removeChild(textArea);
    });
}
</script>
@endpush
