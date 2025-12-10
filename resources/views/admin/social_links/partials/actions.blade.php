<div class="d-flex gap-2">
  <a href="{{ $edit }}" class="btn btn-sm btn-outline-primary"><i class="bx bx-edit"></i></a>
  <form action="{{ $del }}" method="POST" class="js-delete-form">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button>
  </form>
</div>
