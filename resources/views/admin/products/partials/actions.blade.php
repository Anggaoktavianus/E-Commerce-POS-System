<div class="d-flex gap-2">
  <a href="{{ $edit }}" class="btn btn-sm btn-outline-primary" title="Edit">
    <i class="bx bx-edit"></i>
  </a>
  <button type="button" class="btn btn-sm btn-outline-success adjust-stock-btn" 
          data-id="{{ encode_id($row->id) }}" 
          data-name="{{ $row->name }}"
          data-current-stock="{{ $row->stock_qty ?? 0 }}"
          title="Sesuaikan Stok">
    <i class="bx bx-adjust"></i>
  </button>
  <button type="button" class="btn btn-sm btn-outline-warning transfer-product-btn" 
          data-id="{{ encode_id($row->id) }}" 
          data-name="{{ $row->name }}"
          data-store-id="{{ $row->store_id }}"
          title="Pindah/Salin ke Toko Lain">
    <i class="bx bx-transfer"></i>
  </button>
  <a href="{{ route('admin.products.stock_history', encode_id($row->id)) }}" class="btn btn-sm btn-outline-info" title="Riwayat Stok">
    <i class="bx bx-history"></i>
  </a>
  <button type="button" class="btn btn-sm btn-outline-danger delete-product" 
          data-id="{{ encode_id($row->id) }}" title="Hapus">
    <i class="bx bx-trash"></i>
  </button>
</div>
