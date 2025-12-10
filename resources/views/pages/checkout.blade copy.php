@extends('layouts.app')

@section('title', 'Checkout - ' . config('app.name'))

@section('meta_description', 'Lakukan checkout pembelian Anda di ' . config('app.name') . '. Proses pembayaran aman dan mudah untuk produk berkualitas.')

@section('meta_keywords', 'checkout, pembayaran, belanja, keranjang, ' . config('app.name') . ', toko online, pembelian')

@section('og_image', asset('storage/defaults/og-checkout.jpg'))

@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Checkout</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart') }}">Cart</a></li>
            <li class="breadcrumb-item active text-white">Checkout</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Checkout Page Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <form method="POST" action="{{ route('checkout.process') }}" id="checkoutForm">
                @csrf
                <div class="row g-5">
                    <div class="col-md-12 col-lg-6 col-xl-7">
                        <!-- Billing Information -->
                        <div class="border-start border-3 border-primary ps-4 mb-5">
                            <h4 class="mb-4">Billing Information</h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                                <div class="col-12">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-12">
                                    <label for="phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label">Address *</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="city" class="form-label">City *</label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="postal_code" class="form-label">Postal Code *</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" required>
                                </div>
                                <div class="col-12">
                                    <label for="country" class="form-label">Country *</label>
                                    <input type="text" class="form-control" id="country" name="country" value="Indonesia" required>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Method -->
                        <div class="border-start border-3 border-success ps-4 mb-5">
                            <h4 class="mb-4">Shipping Method</h4>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="shipping_method" id="regular_shipping" value="regular" checked>
                                <label class="form-check-label" for="regular_shipping">
                                    <i class="bx bx-truck me-2"></i>Regular Shipping (2-3 days) - IDR 10.000
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="shipping_method" id="express_shipping" value="express">
                                <label class="form-check-label" for="express_shipping">
                                    <i class="bx bx-time-five me-2"></i>Express Shipping (1-2 days) - IDR 25.000
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="shipping_method" id="pickup" value="pickup">
                                <label class="form-check-label" for="pickup">
                                    <i class="bx bx-store me-2"></i>Pickup from Store (Free)
                                </label>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="border-start border-3 border-warning ps-4">
                            <h4 class="mb-4">Payment Method</h4>
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle"></i>
                                You will be redirected to secure payment page after placing order.
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="https://midtrans.com/assets/images/logo/midtrans-color.png" alt="Midtrans" height="30" class="me-3">
                                        <span class="fw-bold">Secure Payment by Midtrans</span>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="bx bx-check-circle text-success me-1"></i> Credit Card
                                        <i class="bx bx-check-circle text-success me-1"></i> Bank Transfer
                                        <i class="bx bx-check-circle text-success me-1"></i> E-Wallet (GoPay, OVO, Dana)
                                        <i class="bx bx-check-circle text-success me-1"></i> QRIS
                                        <i class="bx bx-check-circle text-success me-1"></i> Over the Counter
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-6 col-xl-5">
                        <!-- Order Summary -->
                        <div class="border-start border-3 border-info ps-4">
                            <h4 class="mb-4">Order Summary</h4>
                            
                            <!-- Cart Items -->
                            <div class="mb-4">
                                <h5>📦 Cart Items</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(($cart ?? []) as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $item['image'] ? asset($item['image']) : asset('fruitables/img/vegetable-item-3.png') }}" 
                                                             class="img-fluid me-2 rounded" style="width: 30px; height: 30px; object-fit: cover;" alt="">
                                                        <div>
                                                            <small class="mb-0">{{ $item['name'] }}</small>
                                                            @if(isset($item['product']) && $item['product']->shelf_life_days <= 7)
                                                                <div><span class="badge bg-warning">🥬 Segar</span></div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $item['qty'] }}</td>
                                                <td class="text-end">IDR {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="text-center">Cart is empty</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Price Summary -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>IDR {{ number_format($totals['subtotal'] ?? 0, 0, ',', '.') }}</span>
                                </div>
                                @if(($totals['discount'] ?? 0) > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Discount:</span>
                                    <span class="text-danger">- IDR {{ number_format($totals['discount'] ?? 0, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span class="text-primary">IDR {{ number_format($totals['shipping'] ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <h5>Total:</h5>
                                    <h5 class="text-success">IDR {{ number_format($totals['total'] ?? 0, 0, ',', '.') }}</h5>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="place-order-btn">
                                    <i class="bx bx-lock-alt me-2"></i>Place Order & Pay
                                </button>
                                <a href="{{ route('cart') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-arrow-back me-2"></i>Back to Cart
                                </a>
                            </div>

                            <!-- Security Note -->
                            <div class="mt-3">
                                <div class="alert alert-light">
                                    <small class="text-muted">
                                        <i class="bx bx-shield-check me-1"></i>
                                        Your payment information is secure and encrypted. 
                                        We never store your credit card details.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Checkout Page End -->

@endsection

@push('scripts')
<!-- Midtrans Snap.js -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
$(document).ready(function() {
    // Update shipping cost based on selection
    $('input[name="shipping_method"]').on('change', function() {
        updateShippingCost();
    });

    function updateShippingCost() {
        const selectedMethod = $('input[name="shipping_method"]:checked').val();
        let shippingCost = 0;

        switch(selectedMethod) {
            case 'regular':
                shippingCost = 10000;
                break;
            case 'express':
                shippingCost = 25000;
                break;
            case 'pickup':
                shippingCost = 0;
                break;
        }

        // Update shipping display
        $('.text-primary:contains("Shipping:")').text('IDR ' + shippingCost.toLocaleString('id-ID'));
        
        // Update total
        const subtotal = {{ $totals['subtotal'] ?? 0 }};
        const discount = {{ $totals['discount'] ?? 0 }};
        const total = subtotal - discount + shippingCost;
        $('.text-success:contains("IDR")').text('IDR ' + total.toLocaleString('id-ID'));
    }

    // Form submission with Midtrans
    $('#checkoutForm').on('submit', function(e) {
        e.preventDefault();
        
        $('#place-order-btn')
            .html('<i class="bx bx-loader-alt bx-spin me-2"></i>Processing Order...')
            .prop('disabled', true);

        // Submit order via AJAX
        $.ajax({
            url: '{{ route("checkout.process") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    // Success - Open Midtrans Snap
                    $('#place-order-btn')
                        .html('<i class="bx bx-check-circle me-2"></i>Opening Payment...')
                        .prop('disabled', true);

                    // Open Midtrans Snap
                    snap.pay(response.snap_token, {
                        onSuccess: function(result) {
                            console.log('Payment success:', result);
                            window.location.href = '/payment/finish?order_id=' + response.order_id;
                        },
                        onPending: function(result) {
                            console.log('Payment pending:', result);
                            window.location.href = '/payment/finish?order_id=' + response.order_id + '&status=pending';
                        },
                        onError: function(result) {
                            console.log('Payment error:', result);
                            window.location.href = '/payment/finish?order_id=' + response.order_id + '&status=error';
                        },
                        onClose: function() {
                            console.log('Customer closed the popup without finishing the payment');
                            $('#place-order-btn')
                                .html('<i class="bx bx-lock-alt me-2"></i>Place Order & Pay')
                                .prop('disabled', false);
                            
                            // Show notification
                            showNotification('Payment cancelled. Please try again.', 'warning');
                        }
                    });
                } else {
                    // Error
                    showNotification(response.error || 'An error occurred while processing your order.', 'danger');
                    $('#place-order-btn')
                        .html('<i class="bx bx-lock-alt me-2"></i>Place Order & Pay')
                        .prop('disabled', false);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while processing your order.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.status === 422) {
                    errorMessage = 'Please fill in all required fields correctly.';
                }
                
                showNotification(errorMessage, 'danger');
                $('#place-order-btn')
                    .html('<i class="bx bx-lock-alt me-2"></i>Place Order & Pay')
                    .prop('disabled', false);
            }
        });
    });

    // Notification function
    function showNotification(message, type) {
        // Remove existing notifications
        $('.notification').remove();
        
        const notificationHtml = `
            <div class="notification alert alert-${type} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="bx ${type === 'success' ? 'bx-check-circle' : type === 'danger' ? 'bx-x-circle' : 'bx-info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(notificationHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.notification').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Form validation
    $('#checkoutForm').on('submit', function() {
        let isValid = true;
        
        // Check required fields
        $(this).find('input[required], textarea[required]').each(function() {
            if (!$(this).val().trim()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            showNotification('Please fill in all required fields.', 'warning');
            return false;
        }
        
        return true;
    });

    // Email validation
    $('#email').on('blur', function() {
        const email = $(this).val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            $(this).addClass('is-invalid');
            showNotification('Please enter a valid email address.', 'warning');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Phone validation
    $('#phone').on('blur', function() {
        const phone = $(this).val();
        const phoneRegex = /^[0-9+\-\s()]+$/;
        
        if (phone && !phoneRegex.test(phone)) {
            $(this).addClass('is-invalid');
            showNotification('Please enter a valid phone number.', 'warning');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});
</script>
@endpush
