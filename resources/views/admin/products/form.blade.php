@extends('admin.layouts.app')

@section('title', $product ? 'Edit Product' : 'Create Product')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $product ? 'Edit' : 'Create' }} Product</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ $product ? route('admin.products.update', encode_id($product->id)) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($product)
          @method('PUT')
        @endif

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Store</label>
            @php
              $currentStoreId = app()->has('current_store') ? app('current_store')->id : 1;
              $selectedStoreId = old('store_id', $product->store_id ?? $currentStoreId);
            @endphp
            <select name="store_id" class="form-select" required>
              @foreach(($stores ?? []) as $s)
                <option value="{{ $s->id }}" {{ (int)$selectedStoreId === (int)$s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->code }})</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" class="form-control" value="{{ old('slug', $product->slug ?? '') }}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price ?? '') }}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Compare At Price</label>
            <input type="number" step="0.01" name="compare_at_price" class="form-control" value="{{ old('compare_at_price', $product->compare_at_price ?? '') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">Unit</label>
            <input type="text" name="unit" class="form-control" value="{{ old('unit', $product->unit ?? '') }}" placeholder="kg, pcs, dll">
          </div>
          <div class="col-md-3">
            <label class="form-label">Stock Qty</label>
            <input type="number" name="stock_qty" class="form-control" value="{{ old('stock_qty', $product->stock_qty ?? 0) }}" min="0">
          </div>
          <div class="col-12">
            <label class="form-label">Short Description</label>
            <input type="text" name="short_description" class="form-control" value="{{ old('short_description', $product->short_description ?? '') }}">
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Main Image</label>
            <input type="file" name="main_image" class="form-control" accept="image/*">
            @if(!empty($product?->main_image_path))
              <img src="{{ asset('storage/'.$product->main_image_path) }}" class="img-thumbnail mt-2" style="height:80px">
            @endif
          </div>
          <div class="col-md-6">
            <label class="form-label">Gallery Images (opsional)</label>
            <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
            @if(!empty($product))
              @php
                $gallery = \Illuminate\Support\Facades\DB::table('product_images')
                    ->where('product_id', $product->id)
                    ->orderBy('sort_order')
                    ->get();
              @endphp
              @if($gallery->count() > 1 || ($gallery->count() === 1 && $gallery->first()->image_path !== $product->main_image_path))
                <div class="mt-2 d-flex flex-wrap gap-2" id="gallery-sortable">
                  @foreach($gallery as $img)
                    @continue($img->image_path === $product->main_image_path)
                    <div class="position-relative gallery-item" draggable="true" data-id="{{ $img->id }}">
                      <img src="{{ asset('storage/'.$img->image_path) }}" class="img-thumbnail" style="height:60px">
                      <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 px-1 py-0 js-gallery-delete">&times;</button>
                      <input type="hidden" name="gallery[{{ $img->id }}][sort_order]" value="{{ $img->sort_order }}" class="js-gallery-sort">
                      <input type="hidden" name="gallery[{{ $img->id }}][delete]" value="0" class="js-gallery-delete-input">
                    </div>
                  @endforeach
                </div>
                <small class="text-muted d-block mt-1">Drag & drop untuk mengatur urutan, klik X untuk menghapus.</small>
              @endif
            @endif
          </div>
          <div class="col-md-6">
            <label class="form-label">Categories</label>
            <select name="category_ids[]" class="form-select" multiple>
              @php $selected = $selected ?? []; @endphp
              @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ in_array($c->id, old('category_ids', $selected)) ? 'selected' : '' }}>{{ $c->name }}</option>
              @endforeach
            </select>
            <small class="text-muted">Tahan Ctrl/Cmd untuk memilih lebih dari satu.</small>
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check me-4">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active"> Active </label>
            </div>
            <div class="form-check me-4">
              <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_featured"> Featured </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_bestseller" value="1" id="is_bestseller" {{ old('is_bestseller', $product->is_bestseller ?? false) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_bestseller"> Bestseller </label>
            </div>
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
document.addEventListener('DOMContentLoaded', function(){
  const container = document.getElementById('gallery-sortable');
  if (!container) return;

  let dragSrcEl = null;

  container.querySelectorAll('.gallery-item').forEach(item => {
    item.addEventListener('dragstart', function(e){
      dragSrcEl = this;
      e.dataTransfer.effectAllowed = 'move';
      this.classList.add('opacity-50');
    });
    item.addEventListener('dragend', function(){
      this.classList.remove('opacity-50');
    });
    item.addEventListener('dragover', function(e){
      e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
    });
    item.addEventListener('drop', function(e){
      e.preventDefault();
      if (dragSrcEl && dragSrcEl !== this) {
        if (this.nextSibling === dragSrcEl) {
          this.parentNode.insertBefore(dragSrcEl, this);
        } else {
          this.parentNode.insertBefore(dragSrcEl, this.nextSibling);
        }
        // update sort order hidden inputs
        container.querySelectorAll('.gallery-item').forEach((el, idx) => {
          const sortInput = el.querySelector('.js-gallery-sort');
          if (sortInput) sortInput.value = idx + 1;
        });
      }
    });
  });

  container.addEventListener('click', function(e){
    if (e.target.classList.contains('js-gallery-delete')) {
      const item = e.target.closest('.gallery-item');
      const delInput = item.querySelector('.js-gallery-delete-input');
      if (delInput) {
        delInput.value = 1;
        item.style.opacity = 0.4;
      }
    }
  });
});
</script>
@endpush
