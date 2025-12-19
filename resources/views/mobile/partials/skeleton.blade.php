{{-- Skeleton Loader Components --}}

{{-- Product Grid Skeleton --}}
@if(!isset($type) || $type === 'product-grid')
<div class="product-grid">
  @for($i = 0; $i < ($count ?? 6); $i++)
    <div class="skeleton-card">
      <div class="skeleton skeleton-image"></div>
      <div class="skeleton skeleton-text"></div>
      <div class="skeleton skeleton-text" style="width: 60%;"></div>
      <div class="skeleton skeleton-text" style="width: 40%; margin-top: 0.5rem;"></div>
    </div>
  @endfor
</div>
@endif

{{-- List Item Skeleton --}}
@if(isset($type) && $type === 'list')
@for($i = 0; $i < ($count ?? 5); $i++)
  <div class="skeleton-card">
    <div style="display: flex; gap: 1rem; align-items: start;">
      <div class="skeleton" style="width: 80px; height: 80px; border-radius: 8px; flex-shrink: 0;"></div>
      <div style="flex: 1;">
        <div class="skeleton skeleton-title"></div>
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-text" style="width: 70%; margin-top: 0.5rem;"></div>
      </div>
    </div>
  </div>
@endfor
@endif

{{-- Card Skeleton --}}
@if(isset($type) && $type === 'card')
@for($i = 0; $i < ($count ?? 3); $i++)
  <div class="skeleton-card">
    <div class="skeleton skeleton-title"></div>
    <div class="skeleton skeleton-text"></div>
    <div class="skeleton skeleton-text"></div>
    <div class="skeleton skeleton-text" style="width: 80%;"></div>
  </div>
@endfor
@endif
