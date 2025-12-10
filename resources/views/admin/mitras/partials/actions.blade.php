<div class="d-flex gap-2">
  <form action="{{ $verify }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-sm {{ $row->is_verified ? 'btn-outline-warning' : 'btn-outline-success' }}" 
            title="{{ $row->is_verified ? 'Batalkan Verifikasi' : 'Verifikasi' }}">
      <i class='bx {{ $row->is_verified ? 'bx-user-x' : 'bx-user-check' }}'></i>
    </button>
  </form>
</div>
