@extends('layouts.app')

@section('title', $page->meta_title ?? $page->title . ' - ' . config('app.name'))

@section('meta_description', $page->meta_description ?? Str::limit(strip_tags($page->content), 160))

@section('meta_keywords', $page->meta_keywords ?? str_replace(' ', ', ', $page->title) . ', ' . config('app.name') . ', halaman, informasi')

@section('og_image', $page->featured_image ? asset($page->featured_image) : asset('storage/defaults/og-image.jpg'))

@section('content')

  <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">{{ $page->title }}</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="#">Halaman</a></li>
            <li class="breadcrumb-item active text-white">{{ $page->title }}</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

<!-- Content Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-12">
                <!-- Main Content Card -->
                <div class="card shadow-lg border-0 mb-4 hover-lift transition-all duration-300">
                    <div class="card-body p-4 p-lg-5">
                        <!-- Featured Image -->
                        @if($page->featured_image)
                        <div class="mb-4 text-center">
                            <img src="{{ asset('storage/' . $page->featured_image) }}" 
                                 alt="{{ $page->title }}" 
                                 class="img-fluid rounded-3 shadow-md hover-shadow-xl transition-all duration-300"
                                 style="max-height: 400px; object-fit: cover;">
                        </div>
                        @endif

                        <!-- Video Embed -->
                        @if($page->video_url)
                        <div class="ratio ratio-16x9 mb-4 rounded-3 overflow-hidden shadow-md hover-shadow-xl transition-all duration-300">
                            <iframe src="{{ $page->video_url }}" 
                                    title="{{ $page->title }}" 
                                    allowfullscreen
                                    class="rounded-3"></iframe>
                        </div>
                        @endif

                        <!-- Page Content -->
                        <div class="page-content">
                            <div class="content-wrapper">
                                {!! $page->content !!}
                            </div>
                        </div>

                        <!-- Attachments -->
                        @if($page->attachments && count($page->attachments) > 0)
                        <div class="mt-5">
                            <div class="d-flex align-items-center mb-4">
                                <i class="bx bx-paperclip fs-4 me-2 text-primary"></i>
                                <h4 class="mb-0">Dokumen Terkait</h4>
                            </div>
                            <div class="row g-3">
                                @foreach($page->attachments as $attachment)
                                <div class="col-md-6">
                                    <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                       class="card border shadow-sm hover-shadow-xl hover-lift transition-all duration-300 text-decoration-none" 
                                       target="_blank"
                                       download>
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                                                        <i class="bx bx-file text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="card-title mb-1 text-dark">{{ $attachment['name'] }}</h6>
                                                    <div class="d-flex align-items-center text-muted small">
                                                        <small class="me-3">{{ formatFileSize($attachment['size']) }}</small>
                                                        <small>{{ $attachment['type'] }}</small>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <i class="bx bx-download text-primary fs-5"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Additional Info Card -->
                <div class="card shadow-md border-0 hover-lift transition-all duration-300">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bx bx-calendar me-2 text-primary"></i>
                                    <span>Diterbitkan: {{ $page->created_at->format('d F Y') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bx bx-time me-2 text-primary"></i>
                                    <span>Diperbarui: {{ $page->updated_at->format('d F Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-content {
    line-height: 1.8;
    color: #4a5568;
}

.page-content h1,
.page-content h2,
.page-content h3,
.page-content h4,
.page-content h5,
.page-content h6 {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 1rem;
    margin-top: 2rem;
}

.page-content h1:first-child,
.page-content h2:first-child,
.page-content h3:first-child {
    margin-top: 0;
}

.page-content p {
    margin-bottom: 1.5rem;
}

.page-content ul,
.page-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.page-content li {
    margin-bottom: 0.5rem;
}

.page-content blockquote {
    border-left: 4px solid #3b82f6;
    padding-left: 1rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #6b7280;
}

.page-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    margin: 1.5rem 0;
}

.page-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5rem 0;
}

.page-content th,
.page-content td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.page-content th {
    background-color: #f9fafb;
    font-weight: 600;
}

/* Enhanced Hover Effects */
.hover-lift:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.hover-shadow-xl:hover {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.shadow-md {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.content-wrapper {
    max-width: none;
}

/* Enhanced Card Hover */
.card:hover {
    transform: translateY(-4px);
}

.card.border:hover {
    border-color: #3b82f6 !important;
}

/* Avatar Icon Hover */
.avatar:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
}

/* Download Icon Hover */
.bx-download:hover {
    transform: scale(1.2) rotate(10deg);
    color: #1d4ed8 !important;
}

/* Primary Icon Hover */
.text-primary:hover {
    color: #1d4ed8 !important;
}

@media (max-width: 768px) {
    .container-xxl {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
    
    .page-content {
        font-size: 0.95rem;
    }
    
    .hover-lift:hover {
        transform: translateY(-4px) scale(1.01);
    }
}

/* Smooth transitions for all interactive elements */
.card,
img,
.avatar,
.bx-download {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
<!-- Content End -->
@endsection

@push('styles')
<style>
    .page-content {
        line-height: 1.8;
    }
    
    .page-content img {
        max-width: 100%;
        height: auto;
        margin: 1.5rem 0;
        border-radius: 0.5rem;
    }
    
    .page-content iframe {
        max-width: 100%;
        margin: 1.5rem 0;
        border-radius: 0.5rem;
    }
    
    .page-content h2, 
    .page-content h3, 
    .page-content h4, 
    .page-content h5, 
    .page-content h6 {
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    
    .page-content p {
        margin-bottom: 1.2rem;
    }
    
    .page-content ul,
    .page-content ol {
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
    }
    
    .page-content table {
        width: 100%;
        margin-bottom: 1.5rem;
        border-collapse: collapse;
    }
    
    .page-content table th,
    .page-content table td {
        padding: 0.75rem;
        border: 1px solid #dee2e6;
    }
    
    .page-content table th {
        background-color: #f8f9fa;
    }
    
    .page-content blockquote {
        padding: 1rem 1.5rem;
        margin: 0 0 1.5rem;
        border-left: 4px solid #0d6efd;
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    // Add responsive class to all tables
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.page-content table').forEach(function(table) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        });
    });
</script>
@endpush
