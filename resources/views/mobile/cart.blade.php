@extends('mobile.layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Keranjang')

@section('content')
@if(isset($cart) && count($cart) > 0)
<!-- Cart Items -->
<div style="background: white; padding: 1rem 0;">
  @foreach($cart as $id => $item)
    @php
      $product = $item['product'] ?? \App\Models\Product::find($id);
      $imagePath = $item['image'] ?? ($product->main_image_path ?? null);
      $qty = $item['qty'] ?? $item['quantity'] ?? 1;
      $price = $item['price'] ?? ($product->price ?? 0);
      $subtotal = $price * $qty;
    @endphp
    
    <div class="cart-item" style="padding: 1rem; border-bottom: 1px solid #f0f0f0; display: flex; gap: 1rem;">
      <div style="position: relative;">
        <img src="{{ $imagePath ? Storage::url($imagePath) : asset('sneat/assets/img/placeholder.png') }}" 
             alt="{{ $item['name'] ?? $product->name ?? 'Product' }}"
             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;"
             onerror="this.src='{{ asset('sneat/assets/img/placeholder.png') }}'">
      </div>
      
      <div style="flex: 1;">
        <h6 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: #333;">
          {{ $item['name'] ?? $product->name ?? 'Product' }}
        </h6>
        <div style="font-size: 1rem; font-weight: 700; color: #147440; margin-bottom: 0.75rem;">
          Rp{{ number_format($price, 0, ',', '.') }}
        </div>
        
        <div style="display: flex; align-items: center; justify-content: space-between;">
          <div style="display: flex; align-items: center; gap: 0.5rem; border: 1px solid #e0e0e0; border-radius: 8px; padding: 0.25rem;">
            <button type="button" 
                    class="btn-qty-decrease" 
                    data-product-id="{{ $id }}"
                    style="border: none; background: none; font-size: 1.125rem; color: #147440; padding: 0 0.5rem; cursor: pointer;">
              <i class="bx bx-minus"></i>
            </button>
            <span class="qty-display" data-product-id="{{ $id }}" style="min-width: 30px; text-align: center; font-weight: 600; font-size: 0.875rem;">{{ $qty }}</span>
            <button type="button" 
                    class="btn-qty-increase" 
                    data-product-id="{{ $id }}"
                    style="border: none; background: none; font-size: 1.125rem; color: #147440; padding: 0 0.5rem; cursor: pointer;">
              <i class="bx bx-plus"></i>
            </button>
          </div>
          
          <button type="button" 
                  class="btn-remove-item" 
                  data-product-id="{{ $id }}"
                  style="background: #dc3545; color: white; border: none; padding: 0.5rem 0.75rem; border-radius: 8px; font-size: 0.875rem; cursor: pointer;">
            <i class="bx bx-trash"></i>
          </button>
        </div>
        
        <div style="margin-top: 0.5rem; font-size: 0.875rem; font-weight: 600; color: #333;">
          Subtotal: <span style="color: #147440;">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>
      </div>
    </div>
  @endforeach
</div>

<!-- Cart Summary -->
<div style="background: white; margin-top: 0.5rem; padding: 1rem; border-top: 2px solid #147440;">
  @if($coupon)
    <div style="background: #e8f5e9; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
      <div>
        <div style="font-size: 0.75rem; color: #666;">Kode Kupon</div>
        <div style="font-weight: 600; color: #147440;">{{ $coupon['code'] }}</div>
      </div>
      <button type="button" 
              class="btn-remove-coupon"
              style="background: #dc3545; color: white; border: none; padding: 0.5rem; border-radius: 6px; font-size: 0.75rem; cursor: pointer;">
        <i class="bx bx-x"></i>
      </button>
    </div>
  @else
    <div style="margin-bottom: 1rem;">
      <form id="couponForm" style="display: flex; gap: 0.5rem;">
        <input type="text" 
               name="code" 
               placeholder="Masukkan kode kupon" 
               style="flex: 1; padding: 0.625rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem;">
        <button type="submit" 
                style="background: #147440; color: white; border: none; padding: 0.625rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
          Terapkan
        </button>
      </form>
    </div>
  @endif
  
  <div style="border-top: 1px solid #e0e0e0; padding-top: 1rem; margin-top: 1rem;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
      <span style="color: #666;">Subtotal</span>
      <span style="font-weight: 600;">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
    </div>
    @if($discount > 0)
      <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
        <span style="color: #666;">Diskon</span>
        <span style="font-weight: 600; color: #28a745;">-Rp{{ number_format($discount, 0, ',', '.') }}</span>
      </div>
    @endif
    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.875rem;">
      <span style="color: #666;">Ongkir</span>
      <span style="font-weight: 600;">Rp{{ number_format($shipping, 0, ',', '.') }}</span>
    </div>
    <div style="border-top: 2px solid #147440; padding-top: 0.75rem; margin-top: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
      <span style="font-size: 1.125rem; font-weight: 700; color: #333;">Total</span>
      <span style="font-size: 1.25rem; font-weight: 700; color: #147440;">Rp{{ number_format($total, 0, ',', '.') }}</span>
    </div>
  </div>
</div>

<!-- Checkout Button -->
<div style="position: fixed; bottom: 70px; left: 0; right: 0; background: white; padding: 1rem; border-top: 1px solid #e0e0e0; z-index: 999; box-shadow: 0 -2px 8px rgba(0,0,0,0.1);">
  @auth
    <a href="{{ route('mobile.checkout') }}" 
       style="display: block; background: #147440; color: white; text-align: center; padding: 1rem; border-radius: 12px; font-weight: 700; font-size: 1rem; text-decoration: none;">
      <i class="bx bx-credit-card"></i> Lanjut ke Checkout
    </a>
  @else
    <a href="{{ route('login') }}?redirect={{ urlencode(route('mobile.cart')) }}" 
       style="display: block; background: #147440; color: white; text-align: center; padding: 1rem; border-radius: 12px; font-weight: 700; font-size: 1rem; text-decoration: none;">
      <i class="bx bx-log-in"></i> Login untuk Checkout
    </a>
  @endauth
</div>

@else
<!-- Empty Cart -->
<div class="empty-state" style="padding: 3rem 1rem; text-align: center;">
  <i class="bx bx-cart" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
  <h5 style="color: #666; margin-bottom: 0.5rem;">Keranjang Belanja Kosong</h5>
  <p style="color: #999; font-size: 0.875rem; margin-bottom: 1.5rem;">Yuk, mulai belanja produk favoritmu!</p>
  <a href="{{ route('mobile.shop') }}" 
     style="display: inline-block; background: #147440; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
    <i class="bx bx-shopping-bag"></i> Mulai Belanja
  </a>
</div>
@endif
@endsection

@push('scripts')
<script>
  // Store product stock info
  const productStocks = {};
  @foreach($cart as $id => $item)
    @php
      $product = $item['product'] ?? \App\Models\Product::find($id);
      $stockQty = $product ? ($product->stock_qty ?? 0) : 0;
    @endphp
    productStocks['{{ $id }}'] = {{ $stockQty }};
  @endforeach
  
  // Quantity controls
  document.querySelectorAll('.btn-qty-increase').forEach(btn => {
    btn.addEventListener('click', function() {
      const productId = this.dataset.productId;
      const qtyDisplay = document.querySelector(`.qty-display[data-product-id="${productId}"]`);
      let currentQty = parseInt(qtyDisplay.textContent) || 1;
      const maxStock = productStocks[productId] || 0;
      
      // Validasi: tidak boleh melebihi stok
      if (maxStock > 0 && currentQty >= maxStock) {
        MobileNotification.warning('Stok tidak mencukupi. Stok tersedia: ' + maxStock);
        return;
      }
      
      updateQuantity(productId, currentQty + 1);
    });
  });
  
  document.querySelectorAll('.btn-qty-decrease').forEach(btn => {
    btn.addEventListener('click', function() {
      const productId = this.dataset.productId;
      const qtyDisplay = document.querySelector(`.qty-display[data-product-id="${productId}"]`);
      let currentQty = parseInt(qtyDisplay.textContent) || 1;
      
      if (currentQty > 1) {
        updateQuantity(productId, currentQty - 1);
      }
    });
  });
  
  // Remove item
  document.querySelectorAll('.btn-remove-item').forEach(btn => {
    btn.addEventListener('click', function() {
      const productId = this.dataset.productId;
      
      Swal.fire({
        title: 'Hapus Produk?',
        text: 'Apakah Anda yakin ingin menghapus produk ini dari keranjang?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        width: '90%',
        customClass: {
          popup: 'mobile-swal-popup'
        }
      }).then((result) => {
        if (result.isConfirmed) {
          removeItem(productId);
        }
      });
    });
  });
  
  // Remove coupon
  const removeCouponBtn = document.querySelector('.btn-remove-coupon');
  if (removeCouponBtn) {
    removeCouponBtn.addEventListener('click', function() {
      removeCoupon();
    });
  }
  
  // Apply coupon
  const couponForm = document.getElementById('couponForm');
  if (couponForm) {
    couponForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const code = this.querySelector('input[name="code"]').value;
      applyCoupon(code);
    });
  }
  
  function updateQuantity(productId, quantity) {
    MobileLoading.show('Mengupdate keranjang...');
    
    fetch('{{ route("cart.update") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        id: productId,
        qty: quantity
      })
    })
    .then(response => {
      if (!response.ok) {
        return response.json().then(data => {
          throw { response: { status: response.status, data: data } };
        });
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        MobileNotification.success('Keranjang berhasil diupdate');
        setTimeout(() => location.reload(), 500);
      } else {
        // Tampilkan popup untuk error stok
        const errorMsg = data.error || 'Gagal mengupdate jumlah produk';
        Swal.fire({
          icon: 'warning',
          title: 'Stok Tidak Mencukupi',
          html: errorMsg.replace(/\n/g, '<br>'),
          confirmButtonColor: '#147440',
          confirmButtonText: 'Mengerti',
          width: '90%',
          customClass: {
            popup: 'mobile-swal-popup'
          }
        });
      }
    })
    .catch(error => {
      let errorMsg = 'Terjadi kesalahan saat mengupdate keranjang';
      
      if (error.response && error.response.data) {
        const errorData = error.response.data;
        errorMsg = errorData.error || errorData.message || errorMsg;
      }
      
      Swal.fire({
        icon: 'error',
        title: 'Error',
        html: errorMsg.replace(/\n/g, '<br>'),
        confirmButtonColor: '#147440',
        confirmButtonText: 'Mengerti',
        width: '90%',
        customClass: {
          popup: 'mobile-swal-popup'
        }
      });
    })
    .finally(() => {
      MobileLoading.hide();
    });
  }
  
  function removeItem(productId) {
    MobileLoading.show('Menghapus produk...');
    
    fetch('{{ route("cart.remove") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        id: productId
      })
    })
    .then(response => {
      if (!response.ok) {
        return response.json().then(data => {
          throw { response: { status: response.status, data: data } };
        });
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        MobileNotification.success('Produk berhasil dihapus dari keranjang');
        setTimeout(() => location.reload(), 500);
      } else {
        MobileNotification.error(data.error || 'Gagal menghapus produk');
      }
    })
    .catch(error => {
      MobileErrorHandler.handle(error, 'Remove from Cart');
    })
    .finally(() => {
      MobileLoading.hide();
    });
  }
  
  function applyCoupon(code) {
    MobileLoading.show('Menerapkan kupon...');
    
    fetch('{{ route("cart.coupon") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        code: code
      })
    })
    .then(response => {
      if (!response.ok) {
        return response.json().then(data => {
          throw { response: { status: response.status, data: data } };
        });
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        MobileNotification.success('Kupon berhasil diterapkan');
        setTimeout(() => location.reload(), 500);
      } else {
        MobileNotification.error(data.error || 'Kode kupon tidak valid');
      }
    })
    .catch(error => {
      MobileErrorHandler.handle(error, 'Apply Coupon');
    })
    .finally(() => {
      MobileLoading.hide();
    });
  }
  
  function removeCoupon() {
    MobileLoading.show('Menghapus kupon...');
    
    fetch('{{ route("cart.coupon") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        code: ''
      })
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Gagal menghapus kupon');
      }
      return response.json();
    })
    .then(data => {
      MobileNotification.success('Kupon berhasil dihapus');
      setTimeout(() => location.reload(), 500);
    })
    .catch(error => {
      MobileErrorHandler.handle(error, 'Remove Coupon');
      setTimeout(() => location.reload(), 1000);
    })
    .finally(() => {
      MobileLoading.hide();
    });
  }
</script>
@endpush
