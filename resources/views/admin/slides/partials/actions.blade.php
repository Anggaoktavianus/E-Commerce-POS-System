<div class="d-flex gap-2">
  <a href="{{ $edit }}" class="btn btn-sm btn-outline-primary" title="Edit">
    <i class="bx bx-edit"></i>
  </a>
  <button type="button" class="btn btn-sm btn-outline-danger delete-slide" 
          data-id="{{ $row->id }}" title="Hapus">
    <i class="bx bx-trash"></i>
  </button>
</div>
