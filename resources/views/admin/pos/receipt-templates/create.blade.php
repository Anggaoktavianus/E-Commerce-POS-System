@extends('admin.layouts.app')

@section('title', 'Buat Receipt Template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card page-header-card mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1">
            <i class="bx bx-receipt me-2 text-primary"></i>Buat Receipt Template
          </h4>
          <p class="text-muted mb-0">Buat template struk baru untuk POS</p>
        </div>
        <div>
          <a href="{{ route('admin.pos.receipt-templates.index', ['outlet_id' => $outletId]) }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
          </a>
        </div>
      </div>
    </div>
  </div>

  <form id="templateForm">
    <div class="row g-4">
      <!-- Template Info -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Template Info</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Outlet <small class="text-muted">(kosongkan untuk global)</small></label>
              <select name="outlet_id" id="outlet_id" class="form-select">
                <option value="">Global Template</option>
                @foreach($outlets as $outlet)
                  <option value="{{ $outlet->id }}" {{ $outletId == $outlet->id ? 'selected' : '' }}>
                    {{ $outlet->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Nama Template <span class="text-danger">*</span></label>
              <input type="text" name="name" id="name" class="form-control" required placeholder="Contoh: Template Default">
            </div>
            <div class="mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_default" id="is_default">
                <label class="form-check-label" for="is_default">Set sebagai default</label>
              </div>
            </div>
            <div class="alert alert-info">
              <small>
                <strong>Template Variables:</strong><br>
                <code>{{ '{{transaction_number}}' }}</code> - No. Transaksi<br>
                <code>{{ '{{date}}' }}</code> - Tanggal<br>
                <code>{{ '{{time}}' }}</code> - Waktu<br>
                <code>{{ '{{outlet_name}}' }}</code> - Nama Outlet<br>
                <code>{{ '{{cashier_name}}' }}</code> - Nama Kasir<br>
                <code>{{ '{{customer_name}}' }}</code> - Nama Customer<br>
                <code>{{ '{{total_amount}}' }}</code> - Total<br>
                <code>{{ '{{items}}' }}</code> - List Items<br>
                <code>{{ '{{payment_method}}' }}</code> - Metode Pembayaran
              </small>
            </div>
          </div>
        </div>
      </div>

      <!-- Template Editor -->
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Template Content <span class="text-danger">*</span></h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <textarea name="template_content" id="template_content" class="form-control" rows="20" required></textarea>
            </div>
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-info" onclick="loadDefaultTemplate()">
                <i class="bx bx-refresh me-1"></i>Load Default Template
              </button>
              <button type="button" class="btn btn-secondary" onclick="previewTemplate()">
                <i class="bx bx-show me-1"></i>Preview
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-4">
      <button type="submit" class="btn btn-primary">
        <i class="bx bx-save me-1"></i>Simpan Template
      </button>
    </div>
  </form>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Template Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <iframe id="previewFrame" style="width: 100%; height: 600px; border: 1px solid #ddd;"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
// Default template content
const defaultTemplate = `<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-width: 80mm;
            margin: 0 auto;
            padding: 10px;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .receipt-body {
            margin: 10px 0;
        }
        .receipt-footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            padding: 3px 0;
        }
        .text-right {
            text-align: right;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <h2 style="margin: 0; font-size: 18px;">TERIMA KASIH</h2>
        <p style="margin: 5px 0;">
            <strong>{{outlet_name}}</strong><br>
            Alamat Outlet<br>
            Telp: 081234567890
        </p>
    </div>

    <div class="receipt-body">
        <table>
            <tr>
                <td>No. Transaksi:</td>
                <td class="text-right"><strong>{{transaction_number}}</strong></td>
            </tr>
            <tr>
                <td>Tanggal:</td>
                <td class="text-right">{{date}} {{time}}</td>
            </tr>
            <tr>
                <td>Kasir:</td>
                <td class="text-right">{{cashier_name}}</td>
            </tr>
        </table>

        <div class="divider"></div>

        {{items}}

        <div class="divider"></div>

        <table>
            <tr>
                <td><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>Rp {{total_amount}}</strong></td>
            </tr>
        </table>

        <div class="divider"></div>
        <table>
            <tr>
                <td>Metode:</td>
                <td class="text-right"><strong>{{payment_method}}</strong></td>
            </tr>
        </table>
    </div>

    <div class="receipt-footer">
        <p style="margin: 5px 0;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
        <p style="margin: 5px 0;">Terima kasih atas kunjungan Anda!</p>
    </div>
</body>
</html>`;

function loadDefaultTemplate() {
  document.getElementById('template_content').value = defaultTemplate;
}

function previewTemplate() {
  const content = document.getElementById('template_content').value;
  if (!content) {
    Swal.fire({
      icon: 'warning',
      title: 'Template Kosong',
      text: 'Silakan isi template content terlebih dahulu'
    });
    return;
  }

  // Replace variables with sample data
  const previewContent = content
    .replace(/\{\{transaction_number\}\}/g, 'POS-SAMPLE-001')
    .replace(/\{\{date\}\}/g, new Date().toLocaleDateString('id-ID'))
    .replace(/\{\{time\}\}/g, new Date().toLocaleTimeString('id-ID'))
    .replace(/\{\{outlet_name\}\}/g, 'Sample Outlet')
    .replace(/\{\{cashier_name\}\}/g, 'Sample Cashier')
    .replace(/\{\{customer_name\}\}/g, 'Sample Customer')
    .replace(/\{\{total_amount\}\}/g, 'Rp 150.000')
    .replace(/\{\{payment_method\}\}/g, 'CASH')
    .replace(/\{\{items\}\}/g, `
      <table>
        <tr>
          <td>Sample Product 1</td>
          <td class="text-right">2 x Rp 50.000</td>
        </tr>
        <tr>
          <td></td>
          <td class="text-right">Rp 100.000</td>
        </tr>
        <tr>
          <td>Sample Product 2</td>
          <td class="text-right">1 x Rp 50.000</td>
        </tr>
        <tr>
          <td></td>
          <td class="text-right">Rp 50.000</td>
        </tr>
      </table>
    `);

  const previewFrame = document.getElementById('previewFrame');
  previewFrame.srcdoc = previewContent;
  
  new bootstrap.Modal(document.getElementById('previewModal')).show();
}

document.getElementById('templateForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const data = {
    outlet_id: formData.get('outlet_id') || null,
    name: formData.get('name'),
    template_content: formData.get('template_content'),
    is_default: formData.has('is_default')
  };

  Swal.fire({
    title: 'Menyimpan...',
    text: 'Mohon tunggu',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  fetch('{{ route("admin.pos.receipt-templates.store") }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Template berhasil dibuat',
        timer: 2000
      }).then(() => {
        window.location.href = '{{ route("admin.pos.receipt-templates.index", ["outlet_id" => $outletId]) }}';
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: data.message || 'Gagal membuat template'
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Terjadi kesalahan saat menyimpan template'
    });
  });
});

// Load default template on page load
document.addEventListener('DOMContentLoaded', function() {
  @if($defaultTemplate)
    document.getElementById('template_content').value = `{!! addslashes($defaultTemplate->template_content) !!}`;
  @else
    loadDefaultTemplate();
  @endif
});
</script>
@endsection
