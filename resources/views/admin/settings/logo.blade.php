@extends('admin.layouts.app')

@section('title', 'Logo Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Logo Management</h4>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Back to Settings
        </a>
    </div>

    <div class="row g-4">
        <!-- Site Logo -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Site Logo</h5>
                    @if($siteLogo->value)
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteLogo('site_logo', {{ $siteLogo->id }})">
                        <i class="bx bx-trash me-1"></i> Delete Logo
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update', $siteLogo->id ?? '') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="key" value="site_logo">
                        
                        <div class="text-center mb-4">
                            @if($siteLogo->value)
                                <img src="{{ asset($siteLogo->value) }}" alt="Site Logo" 
                                     class="img-fluid border rounded p-3" 
                                     style="max-height: 150px; width: {{ $siteLogo->logo_width ?? 200 }}px; height: {{ $siteLogo->logo_height ?? 80 }}px; object-fit: {{ $siteLogo->logo_object_fit ?? 'contain' }};">
                            @else
                                <div class="border rounded p-5 bg-light" style="width: {{ $siteLogo->logo_width ?? 200 }}px; height: {{ $siteLogo->logo_height ?? 80 }}px; display: inline-block;">
                                    <i class="bx bx-image-alt fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">No logo uploaded</p>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload New Logo</label>
                            <input type="file" name="file" class="form-control" accept="image/*">
                            <small class="text-muted">Recommended size: 50x50px. Square format works best.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Logo Width (px)</label>
                            <input type="number" name="logo_width" class="form-control" 
                                   value="{{ old('logo_width', $siteLogo->logo_width ?? 200) }}" 
                                   min="50" max="500" step="10">
                            <small class="text-muted">Width in pixels (50-500px). Default: 200px</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Logo Height (px)</label>
                            <input type="number" name="logo_height" class="form-control" 
                                   value="{{ old('logo_height', $siteLogo->logo_height ?? 80) }}" 
                                   min="30" max="200" step="10">
                            <small class="text-muted">Height in pixels (30-200px). Default: 80px</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Object Fit</label>
                            <select name="logo_object_fit" class="form-control">
                                <option value="contain" {{ ($siteLogo->logo_object_fit ?? 'contain') == 'contain' ? 'selected' : '' }}>Contain (fit within bounds)</option>
                                <option value="cover" {{ ($siteLogo->logo_object_fit ?? 'contain') == 'cover' ? 'selected' : '' }}>Cover (fill bounds)</option>
                                <option value="scale-down" {{ ($siteLogo->logo_object_fit ?? 'contain') == 'scale-down' ? 'selected' : '' }}>Scale Down</option>
                                <option value="none" {{ ($siteLogo->logo_object_fit ?? 'contain') == 'none' ? 'selected' : '' }}>None (original size)</option>
                            </select>
                            <small class="text-muted">How the image should fit within its container.</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-upload me-1"></i> Update Site Logo
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Site Name Logo -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Site Name Logo</h5>
                    @if($nameLogo->value)
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteLogo('site_name_logo', {{ $nameLogo->id }})">
                        <i class="bx bx-trash me-1"></i> Delete Logo
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update', $nameLogo->id ?? '') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="key" value="site_name_logo">
                        
                        <div class="text-center mb-4">
                            @if($nameLogo->value)
                                <img src="{{ asset($nameLogo->value) }}" alt="Site Name Logo" 
                                     class="img-fluid border rounded p-3" 
                                     style="max-height: 150px; object-fit: contain;">
                            @else
                                <div class="border rounded p-5 bg-light">
                                    <i class="bx bx-image-alt fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">No logo uploaded</p>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload New Logo</label>
                            <input type="file" name="file" class="form-control" accept="image/*">
                            <small class="text-muted">Recommended size: 100x50px. Horizontal format works best.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Logo Path (Optional)</label>
                            <input type="text" name="value" class="form-control" 
                                   value="{{ old('value', $nameLogo->value ?? '') }}" 
                                   placeholder="e.g., fruitables/img/logo/name-store-logo.png">
                            <small class="text-muted">Or specify the path to existing image file.</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-upload me-1"></i> Update Name Logo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Preview</h5>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">This is how your logos will appear in the website header:</p>
            
            <div class="border rounded p-3 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset($siteLogo->value ?? 'fruitables/img/logo/logo-samsae.png') }}" 
                         alt="Logo" 
                         style="width:{{ $siteLogo->logo_width ?? 200 }}px;height:{{ $siteLogo->logo_height ?? 80 }}px;object-fit:{{ $siteLogo->logo_object_fit ?? 'contain' }};">
                    @if($nameLogo->value)
                    <img src="{{ asset($nameLogo->value) }}" 
                         alt="Site Name" 
                         style="width:{{ $nameLogo->logo_width ?? 100 }}px;height:{{ $nameLogo->logo_height ?? 50 }}px;object-fit:{{ $nameLogo->logo_object_fit ?? 'contain' }};">
                    @endif
                    <span class="ms-3 text-muted">Your Website Name</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteLogo(logoType, settingId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will remove the " + logoType.replace('_', ' ').toUpperCase() + " from the website!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route('admin.settings.deleteLogo') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    setting_id: settingId
                },
                success: function(response) {
                    Swal.fire(
                        'Deleted!',
                        'Logo has been deleted successfully.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire(
                        'Error!',
                        'Failed to delete logo.',
                        'error'
                    );
                }
            });
        }
    });
}
</script>
@endpush
