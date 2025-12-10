@extends('admin.layouts.app')

@section('title', $banner ? 'Edit Banner' : 'Create Banner')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $banner ? 'Edit' : 'Create' }} Banner</h4>
    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ $banner ? route('admin.banners.update', $banner->id) : route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($banner)
          @method('PUT')
        @endif

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title ?? '') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Subtitle</label>
            <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $banner->subtitle ?? '') }}">
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-4">
            <label class="form-label">Button Text</label>
            <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $banner->button_text ?? '') }}">
          </div>
          <div class="col-md-8">
            <label class="form-label">Button URL</label>
            <input type="text" name="button_url" class="form-control" value="{{ old('button_url', $banner->button_url ?? '') }}" placeholder="e.g., /page/about or https://example.com">
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-6">
            <label class="form-label">Image</label>
            <input type="file" name="image_path" class="form-control">
            @if(!empty($banner?->image_path))
              <small class="text-muted d-block mt-1">Current: <img src="{{ asset($banner->image_path) }}" alt="" style="height:60px"> </small>
            @endif
          </div>
          <div class="col-md-3">
            <label class="form-label">Position</label>
            <select name="position" class="form-select" id="position_select" required>
              @php $pos = old('position', $banner->position ?? 'home_middle'); @endphp
              <option value="home_top" {{ $pos==='home_top' ? 'selected' : '' }}>Home Top</option>
              <option value="home_middle" {{ $pos==='home_middle' ? 'selected' : '' }}>Home Middle</option>
              <option value="home_bottom" {{ $pos==='home_bottom' ? 'selected' : '' }}>Home Bottom</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $banner->sort_order ?? $nextSortOrder ?? 0) }}" readonly>
            @if(!$banner)
            <small class="text-muted d-block mt-1">Automatically calculated</small>
            @endif
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active"> Active </label>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-1" id="circle_fields_row">
          <div class="col-md-2" id="circle_number_field">
            <label class="form-label">Circle Number</label>
            <input type="text" name="circle_number" class="form-control" value="{{ old('circle_number', $banner->circle_number ?? '') }}" placeholder="e.g., 1">
          </div>
          <div class="col-md-3" id="circle_value_field">
            <label class="form-label">Circle Value</label>
            <input type="text" name="circle_value" class="form-control" value="{{ old('circle_value', $banner->circle_value ?? '') }}" placeholder="e.g., 50">
          </div>
          <div class="col-md-3" id="circle_unit_field">
            <label class="form-label">Circle Unit</label>
            <input type="text" name="circle_unit" class="form-control" value="{{ old('circle_unit', $banner->circle_unit ?? '') }}" placeholder="e.g., kg">
          </div>
        </div>

        <div class="mt-4">
          <button class="btn btn-primary" type="submit">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const positionSelect = document.getElementById('position_select');
    const circleFieldsRow = document.getElementById('circle_fields_row');
    
    function toggleCircleFields() {
        if (positionSelect.value === 'home_top') {
            circleFieldsRow.style.display = 'flex';
        } else {
            circleFieldsRow.style.display = 'none';
        }
    }
    
    positionSelect.addEventListener('change', toggleCircleFields);
    toggleCircleFields(); // Initialize on page load
});
</script>
@endpush
