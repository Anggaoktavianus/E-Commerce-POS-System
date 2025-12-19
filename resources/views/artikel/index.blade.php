@extends('layouts.app')

@section('title', 'Artikel - ' . config('app.name'))

@section('meta_description', 'Baca artikel menarik dan informasi bermanfaat di ' . config('app.name'))

@section('meta_keywords', 'artikel, blog, informasi, tips, berita, ' . config('app.name'))

@section('content')
@include('partials.modern-page-header', [
    'pageTitle' => 'Artikel & Blog',
    'breadcrumbItems' => [
        ['label' => 'Beranda', 'url' => url('/')],
        ['label' => 'Artikel', 'url' => null]
    ]
])

<!-- Search and Filter Section -->
<section class="py-4" style="background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari artikel...">
                </div>
            </div>
            <div class="col-md-3">
                <select id="kategoriFilter" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="sortFilter" class="form-select">
                    <option value="latest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="popular">Terpopuler</option>
                    <option value="title">Judul A-Z</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Articles Grid -->
<section class="py-5" style="background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);">
    <div class="container">
        <div class="row" id="artikelContainer">
            @forelse($artikel as $item)
                <div class="col-lg-4 col-md-6 mb-4 artikel-item" data-kategori="{{ $item->kategori_artikel_id }}" data-judul="{{ strtolower($item->judul) }}" data-views="{{ $item->views }}" data-tanggal="{{ $item->created_at->timestamp }}">
                    <div class="card h-100 shadow-sm article-card">
                        @if($item->gambar_thumbnail)
                            <img src="{{ Storage::url($item->gambar_thumbnail) }}" class="card-img-top article-thumbnail" alt="{{ $item->judul }}">
                        @elseif($item->gambar_utama)
                            <img src="{{ Storage::url($item->gambar_utama) }}" class="card-img-top article-thumbnail" alt="{{ $item->judul }}">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bx bx-image bx-3x text-muted"></i>
                            </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <!-- Category Badge -->
                            <div class="mb-2">
                                <span class="badge bg-primary">{{ $item->kategoriArtikel->nama }}</span>
                            </div>
                            
                            <!-- Title -->
                            <h5 class="card-title fw-bold">
                                <a href="{{ route('artikel.show', $item->slug) }}" class="text-decoration-none text-dark article-title">
                                    {{ Str::limit($item->judul, 60) }}
                                </a>
                            </h5>
                            
                            <!-- Excerpt -->
                            <p class="card-text text-muted flex-grow-1">
                                {{ Str::limit(strip_tags($item->konten), 120) }}
                            </p>
                            
                            <!-- Meta Info -->
                            <div class="d-flex justify-content-between align-items-center text-muted small mb-3">
                                <span><i class="bx bx-calendar me-1"></i> {{ $item->created_at->format('d M Y') }}</span>
                                <span><i class="bx bx-time me-1"></i> {{ $item->reading_time }} menit</span>
                            </div>
                            
                            <!-- Stats -->
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">
                                    <i class="bx bx-show me-1"></i> {{ number_format($item->views) }} views
                                </span>
                                <a href="{{ route('artikel.show', $item->slug) }}" class="btn btn-sm btn-outline-primary">
                                    Baca Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bx bx-file bx-5x text-muted mb-3"></i>
                        <h4 class="text-muted">Belum ada artikel</h4>
                        <p class="text-muted">Belum ada artikel yang dipublish saat ini.</p>
                    </div>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($artikel->hasPages())
            <div class="row mt-4">
                <div class="col-12">
                    <div class="pagination d-flex justify-content-center">
                        {{-- Previous Page Link --}}
                        @if ($artikel->onFirstPage())
                            <span class="rounded px-3 py-1 me-1 disabled">&laquo;</span>
                        @else
                            <a href="{{ $artikel->previousPageUrl() }}" class="rounded px-3 py-1 me-1">&laquo;</a>
                        @endif

                        {{-- Page numbers --}}
                        @for ($page = 1; $page <= $artikel->lastPage(); $page++)
                            @if ($page == $artikel->currentPage())
                                <span class="active rounded px-3 py-1 me-1">{{ $page }}</span>
                            @else
                                <a href="{{ $artikel->url($page) }}" class="rounded px-3 py-1 me-1">{{ $page }}</a>
                            @endif
                        @endfor

                        {{-- Next Page Link --}}
                        @if ($artikel->hasMorePages())
                            <a href="{{ $artikel->nextPageUrl() }}" class="rounded px-3 py-1">&raquo;</a>
                        @else
                            <span class="rounded px-3 py-1 disabled">&raquo;</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- No Results Message (Hidden by default) -->
<div id="noResults" class="container py-5" style="display: none;">
    <div class="text-center">
        <i class="bx bx-search bx-5x text-muted mb-3"></i>
        <h4 class="text-muted">Tidak ada artikel ditemukan</h4>
        <p class="text-muted">Coba ubah kata kunci atau filter pencarian Anda.</p>
        <button class="btn btn-primary" onclick="resetFilters()">Reset Filter</button>
    </div>
</div>
@endsection

@push('styles')
<style>
.article-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.article-thumbnail {
    height: 200px;
    object-fit: cover;
}

.article-title {
    transition: color 0.3s ease;
}

.article-title:hover {
    color: #0d6efd !important;
}

.article-item {
    display: block;
}

.article-item.hidden {
    display: none;
}

/* Custom pagination */
.pagination .rounded {
    color: #6c757d;
    text-decoration: none;
    border: 1px solid #dee2e6;
    background-color: #fff;
    transition: all 0.3s ease;
}

.pagination .rounded:hover {
    color: #0d6efd;
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

.pagination .active {
    color: #fff;
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
}

.pagination .disabled {
    color: #6c757d;
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Search functionality
    $('#searchInput').on('keyup', function() {
        filterArticles();
    });
    
    // Category filter
    $('#kategoriFilter').on('change', function() {
        filterArticles();
    });
    
    // Sort filter
    $('#sortFilter').on('change', function() {
        sortArticles();
    });
});

function filterArticles() {
    const searchTerm = $('#searchInput').val().toLowerCase();
    const kategoriFilter = $('#kategoriFilter').val();
    
    let visibleCount = 0;
    
    $('.artikel-item').each(function() {
        const judul = $(this).data('judul');
        const kategori = $(this).data('kategori');
        
        const matchesSearch = judul.includes(searchTerm);
        const matchesKategori = !kategoriFilter || kategori == kategoriFilter;
        
        if (matchesSearch && matchesKategori) {
            $(this).removeClass('hidden');
            visibleCount++;
        } else {
            $(this).addClass('hidden');
        }
    });
    
    // Show/hide no results message
    if (visibleCount === 0) {
        $('#noResults').show();
        $('.row[id="artikelContainer"]').hide();
    } else {
        $('#noResults').hide();
        $('.row[id="artikelContainer"]').show();
    }
}

function sortArticles() {
    const sortBy = $('#sortFilter').val();
    const container = $('#artikelContainer');
    const items = container.find('.artikel-item');
    
    items.sort(function(a, b) {
        let aVal, bVal;
        
        switch(sortBy) {
            case 'latest':
                aVal = parseInt($(b).data('tanggal'));
                bVal = parseInt($(a).data('tanggal'));
                break;
            case 'oldest':
                aVal = parseInt($(a).data('tanggal'));
                bVal = parseInt($(b).data('tanggal'));
                break;
            case 'popular':
                aVal = parseInt($(b).data('views'));
                bVal = parseInt($(a).data('views'));
                break;
            case 'title':
                aVal = $(a).find('.article-title').text().toLowerCase();
                bVal = $(b).find('.article-title').text().toLowerCase();
                break;
            default:
                return 0;
        }
        
        if (aVal < bVal) return -1;
        if (aVal > bVal) return 1;
        return 0;
    });
    
    container.html(items);
}

function resetFilters() {
    $('#searchInput').val('');
    $('#kategoriFilter').val('');
    $('#sortFilter').val('latest');
    
    $('.artikel-item').removeClass('hidden');
    $('#noResults').hide();
    $('.row[id="artikelContainer"]').show();
    
    // Reset to original order (latest)
    sortArticles();
}
</script>
@endpush
