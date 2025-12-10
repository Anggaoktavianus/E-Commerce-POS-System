<div class="d-flex gap-2">
  <a href="{{ $edit }}" class="btn btn-sm btn-outline-primary" title="Edit">
    <i class="bx bx-edit"></i>
  </a>

  @if(($row->role ?? null) === 'mitra')
    <form action="{{ route('admin.users.toggle_verify', $row->id) }}" method="POST">
      @csrf
      <button type="submit" class="btn btn-sm {{ $row->is_verified ? 'btn-outline-warning' : 'btn-outline-success' }}" 
              title="{{ $row->is_verified ? 'Batalkan Verifikasi' : 'Verifikasi' }}">
        <i class='bx {{ $row->is_verified ? 'bx-user-x' : 'bx-user-check' }}'></i>
      </button>
    </form>
  @endif

  <button type="button" class="btn btn-sm btn-outline-danger delete-user" 
          data-id="{{ $row->id }}" title="Hapus">
    <i class="bx bx-trash"></i>
  </button>
</div>
