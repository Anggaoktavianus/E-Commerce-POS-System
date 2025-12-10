@extends('admin.layouts.app')

@section('title', ($link ? 'Edit' : 'Create') . ' Link')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $link ? 'Edit' : 'Create' }} Link — {{ $parent->name }}</h4>
    <a href="{{ route('admin.links.index', $parent->id) }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form action="{{ $link ? route('admin.links.update', $link->id) : route('admin.links.store', $parent->id) }}" method="POST">
        @csrf
        @if($link)
          @method('PUT')
        @endif

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Label</label>
            <input type="text" name="label" class="form-control" value="{{ old('label', $link->label ?? '') }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Target</label>
            @php $tgt = old('target', $link->target ?? '_self'); @endphp
            <select name="target" class="form-select">
              <option value="_self" {{ $tgt==='_self' ? 'selected' : '' }}>Same Tab</option>
              <option value="_blank" {{ $tgt==='_blank' ? 'selected' : '' }}>New Tab</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">URL</label>
            <input type="text" name="url" class="form-control" value="{{ old('url', $link->url ?? '') }}" placeholder="https://...">
          </div>
          <div class="col-md-6">
            <label class="form-label">Route Name</label>
            <input type="text" name="route_name" class="form-control" value="{{ old('route_name', $link->route_name ?? '') }}" placeholder="e.g. home">
          </div>
          <div class="col-md-6">
            <label class="form-label">Page (optional)</label>
            <select name="page_id" class="form-select">
              <option value="">— None —</option>
              @foreach($pages as $page)
                <option value="{{ $page->id }}" {{ (string)old('page_id', $link->page_id ?? '') === (string)$page->id ? 'selected' : '' }}>
                  {{ $page->title }}
                </option>
              @endforeach
            </select>
            <div class="form-text">Jika diisi, link akan mengarah ke halaman ini. URL/Route manual bisa dikosongkan.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Parent</label>
            <select name="parent_id" class="form-select">
              <option value="">— None —</option>
              @foreach($parents as $p)
                <option value="{{ $p->id }}" {{ (string)old('parent_id', $link->parent_id ?? '') === (string)$p->id ? 'selected' : '' }}>{{ $p->label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $link->sort_order ?? ($nextSortOrder ?? 0)) }}">
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-4 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $link->is_active ?? true) ? 'checked' : '' }}>
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
