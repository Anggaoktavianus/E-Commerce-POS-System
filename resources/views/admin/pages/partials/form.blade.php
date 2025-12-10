@csrf

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bx bx-edit me-2"></i>Konten Utama
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="title" class="form-label">
                        <i class="bx bx-heading text-primary me-1"></i>
                        Judul Halaman
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" 
                           value="{{ old('title', $page->title ?? '') }}" required
                           placeholder="Masukkan judul halaman yang menarik">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="bx bx-info-circle"></i>
                        Judul akan muncul di browser tab dan sebagai heading utama halaman
                    </div>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">
                        <i class="bx bx-link text-info me-1"></i>
                        URL Slug
                    </label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" 
                           value="{{ old('slug', $page->slug ?? '') }}"
                           placeholder="url-halaman-otomatis">
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="bx bx-info-circle"></i>
                        URL-friendly version dari judul (akan otomatis di-generate)
                    </div>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">
                        <i class="bx bx-file-text text-success me-1"></i>
                        Konten Halaman
                        <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" 
                              rows="15" required placeholder="Tulis konten halaman di sini...">{{ old('content', $page->content ?? '') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="bx bx-info-circle"></i>
                        Gunakan toolbar editor untuk format teks, gambar, link, tabel, dll
                    </div>
                </div>
                
                <!-- Summernote Editor -->
                @push('scripts')
                <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
                <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
                <script>
                  document.addEventListener('DOMContentLoaded', function(){
                    const el = document.querySelector('#content');
                    if (!el) return;

                    $(el).summernote({
                      height: 350,
                      placeholder: 'Tulis konten halaman yang menarik dan informatif...',
                      codemirror: {
                        mode: 'text/html',
                        htmlMode: true,
                        lineNumbers: true,
                        theme: 'monokai'
                      },
                      toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph', 'height']],
                        ['insert', ['link', 'picture', 'video', 'table', 'hr']],
                        ['view', ['fullscreen', 'codeview', 'help', 'undo', 'redo']]
                      ],
                      callbacks: {
                        onInit: function() {
                          console.log('Summernote initialized successfully');
                        },
                        onImageUpload: function(files) {
                          // Handle image upload if needed
                          for (let i = 0; i < files.length; i++) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                              $(el).summernote('insertImage', e.target.result, files[i].name);
                            };
                            reader.readAsDataURL(files[i]);
                          }
                        }
                      }
                    });
                  });
                </script>
                @endpush

                <div class="mb-3">
                    <label for="video_url" class="form-label">
                        <i class="bx bx-video text-warning me-1"></i>
                        URL Video (YouTube/Vimeo)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-link"></i></span>
                        <input type="url" class="form-control @error('video_url') is-invalid @enderror" id="video_url" 
                               name="video_url" value="{{ old('video_url', $page->video_url ?? '') }}"
                               placeholder="https://youtube.com/watch?v=...">
                    </div>
                    @error('video_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="bx bx-info-circle"></i>
                        Masukkan URL video YouTube atau Vimeo untuk menampilkan video di halaman
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Status Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bx bx-cog me-2"></i>Status & Publikasi
                </h6>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" 
                           value="1" @checked(old('is_published', $page->is_published ?? false))>
                    <label class="form-check-label" for="is_published">
                        <i class="bx bx-show me-1"></i>Publikasikan Halaman
                    </label>
                </div>
                <div class="alert alert-info mb-0">
                    <i class="bx bx-info-circle me-2"></i>
                    <small>
                        <strong>Published:</strong> Halaman muncul di website<br>
                        <strong>Draft:</strong> Disimpan tapi belum muncul
                    </small>
                </div>
            </div>
        </div>

        <!-- Media Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bx bx-image me-2"></i>Media & Lampiran
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="featured_image" class="form-label">
                        <i class="bx bx-image text-primary me-1"></i>
                        Gambar Unggulan
                    </label>
                    <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                           id="featured_image" name="featured_image" accept="image/*">
                    @error('featured_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="bx bx-info-circle"></i>
                        Format: JPG, PNG, WebP. Maks: 2MB
                    </div>
                    
                    @if(isset($page) && $page->featured_image)
                        <div class="mt-3">
                            <small class="text-muted">Gambar saat ini:</small>
                            <div class="border rounded p-2 mt-1 bg-light">
                                <img src="{{ asset($page->featured_image) }}" 
                                     alt="{{ $page->title }}" class="img-fluid" style="max-height: 150px;">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="remove_featured_image" 
                                           name="remove_featured_image" value="1">
                                    <label class="form-check-label" for="remove_featured_image">
                                        <i class="bx bx-trash me-1"></i>Hapus gambar
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="attachments" class="form-label">
                        <i class="bx bx-paperclip text-warning me-1"></i>
                        File Lampiran
                    </label>
                    <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                           id="attachments" name="attachments[]" multiple>
                    @error('attachments.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="bx bx-info-circle"></i>
                        Format: PDF, DOC, ZIP, dll. Maks: 5MB per file
                    </div>
                    
                    @if(isset($page) && $page->attachments && count($page->attachments) > 0)
                        <div class="mt-3">
                            <small class="text-muted">File Terlampir:</small>
                            <ul class="list-unstyled">
                                @foreach($page->attachments as $index => $attachment)
                                    <li class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                        <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="text-decoration-none">
                                            <i class="bx bx-file me-1"></i> {{ $attachment['name'] }}
                                            <small class="text-muted">({{ number_format($attachment['size']/1024, 2) }} KB)</small>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-icon btn-danger remove-attachment" 
                                                data-index="{{ $index }}" title="Hapus lampiran">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        <input type="hidden" name="existing_attachments[]" value="{{ $attachment['path'] }}">
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- SEO Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bx bx-search-alt me-2"></i>SEO Settings
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="meta_title" class="form-label">
                        <i class="bx bx-heading text-info me-1"></i>
                        Meta Title (SEO)
                    </label>
                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                           id="meta_title" name="meta_title" 
                           value="{{ old('meta_title', $page->meta_title ?? '') }}"
                           placeholder="Judul untuk search engine"
                           maxlength="60">
                    @error('meta_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="bx bx-info-circle"></i>
                        Maks 60 karakter. Akan muncul di Google search results
                    </div>
                </div>

                <div class="mb-3">
                    <label for="meta_description" class="form-label">
                        <i class="bx bx-file-text text-success me-1"></i>
                        Meta Description (SEO)
                    </label>
                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                              id="meta_description" name="meta_description" rows="3"
                              placeholder="Deskripsi singkat halaman untuk search engine"
                              maxlength="160">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                    @error('meta_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="bx bx-info-circle"></i>
                        Maks 160 karakter. Akan muncul di Google search results
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bx bx-save me-1"></i> Simpan Halaman
            </button>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
                <i class="bx bx-x me-1"></i> Batal
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
  .note-editor.note-frame .note-editing-area {
    min-height: 350px;
  }
  .note-editor.note-frame {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
  }
</style>
@endpush

@push('scripts')
<script>
    // Handle remove attachment
    document.querySelectorAll('.remove-attachment').forEach(button => {
        button.addEventListener('click', function() {
            const listItem = this.closest('li');
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'remove_attachments[]';
            hidden.value = this.nextElementSibling.value;
            document.querySelector('form').appendChild(hidden);
            listItem.remove();
        });
    });
    
    // Generate slug from title
    const titleEl = document.getElementById('title');
    const slugEl = document.getElementById('slug');
    if (titleEl && slugEl) {
      titleEl.addEventListener('blur', function(){
        if (!slugEl.value) {
          const slug = this.value.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/--+/g, '-')
            .replace(/^-+|-+$/g, '');
          slugEl.value = slug;
        }
      });
    }

    // Character counters for SEO fields
    const metaTitle = document.getElementById('meta_title');
    const metaDesc = document.getElementById('meta_description');
    
    if (metaTitle) {
        metaTitle.addEventListener('input', function() {
            const remaining = 60 - this.value.length;
            updateCharCounter(this, remaining);
        });
    }
    
    if (metaDesc) {
        metaDesc.addEventListener('input', function() {
            const remaining = 160 - this.value.length;
            updateCharCounter(this, remaining);
        });
    }
    
    function updateCharCounter(field, remaining) {
        let counter = field.nextElementSibling;
        if (!counter || !counter.classList.contains('char-counter')) {
            counter = document.createElement('small');
            counter.className = 'char-counter text-muted';
            field.parentNode.appendChild(counter);
        }
        counter.textContent = `${remaining} karakter tersisa`;
        counter.className = remaining < 10 ? 'char-counter text-danger' : 'char-counter text-muted';
    }
</script>
@endpush
