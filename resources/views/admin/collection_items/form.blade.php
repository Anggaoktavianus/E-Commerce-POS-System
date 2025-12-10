@extends('admin.layouts.app')

@section('title', ($item ? 'Edit' : 'Create') . ' Collection Item')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $item ? 'Edit' : 'Create' }} Item — {{ $parent->name }} ({{ $parent->key }})</h4>
    <a href="{{ route('admin.collection_items.index', $parent->id) }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ $item ? route('admin.collection_items.update', $item->id) : route('admin.collection_items.store', $parent->id) }}" method="POST">
        @csrf
        @if($item)
          @method('PUT')
        @endif

        <div class="row g-3">
          <div class="col-md-8">
            <label class="form-label">Product</label>
            <select name="product_id" class="form-select" required>
              @foreach($products as $p)
                <option value="{{ $p->id }}" {{ (string)old('product_id', $item->product_id ?? '') === (string)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $item->sort_order ?? 0) }}">
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
