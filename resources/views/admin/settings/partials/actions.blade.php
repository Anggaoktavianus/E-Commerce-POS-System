<div class="d-flex gap-2">
  <a href="{{ $edit }}" class="btn btn-sm btn-outline-primary" title="Edit Pengaturan">
    <i class="bx bx-edit"></i>
  </a>
  <button class="btn btn-sm btn-outline-info" onclick="copyToClipboard('{{ $key }}', '{{ $value }}')" title="Salin Nilai">
    <i class="bx bx-copy"></i>
  </button>
  <button type="button" class="btn btn-sm btn-outline-danger delete-setting" 
          data-id="{{ str_replace('/edit', '', $edit) }}" title="Hapus Pengaturan">
    <i class="bx bx-trash"></i>
  </button>
</div>

<script>
function copyToClipboard(key, value) {
  const text = `${key}: ${value}`;
  navigator.clipboard.writeText(text).then(function() {
    // Show success notification
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">
          <i class="bx bx-check me-2"></i> Berhasil disalin!
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    `;
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast after hiding
    toast.addEventListener('hidden.bs.toast', () => {
      document.body.removeChild(toast);
    });
  });
}
</script>
