@extends('admin.layouts.app')

@section('title', $collection ? 'Edit Collection' : 'Create Collection')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $collection ? 'Edit' : 'Create' }} Collection</h4>
    <a href="{{ route('admin.collections.index') }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ $collection ? route('admin.collections.update', $collection->id) : route('admin.collections.store') }}" method="POST">
        @csrf
        @if($collection)
          @method('PUT')
        @endif

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $collection->name ?? '') }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Key</label>
            <input type="text" name="key" class="form-control" value="{{ old('key', $collection->key ?? '') }}" placeholder="bestseller" required {{ $collection ? 'readonly' : '' }}>
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-control">{{ old('description', $collection->description ?? '') }}</textarea>
          </div>
          <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $collection->is_active ?? true) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active"> Active </label>
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
