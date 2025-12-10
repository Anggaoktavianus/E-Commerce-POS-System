// Shipping JavaScript for checkout integration
class ShippingManager {
    constructor() {
        this.selectedMethod = null;
        this.cartItems = [];
        this.originCity = 'Jakarta';
        this.destinationCity = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadCartFromSession();
    }

    bindEvents() {
        // City change events
        $('#destination_city').on('change', () => this.onCityChange());
        $('#origin_city').on('change', () => this.onCityChange());
        
        // Shipping method selection
        $(document).on('click', '.shipping-method-card', (e) => this.selectShippingMethod(e));
        
        // Cart update events
        $(document).on('cart-updated', () => this.onCartUpdate());
    }

    async onCityChange() {
        this.destinationCity = $('#destination_city').val();
        this.originCity = $('#origin_city').val();
        
        if (this.destinationCity) {
            await this.loadShippingMethods();
        }
    }

    async onCartUpdate() {
        this.loadCartFromSession();
        if (this.destinationCity) {
            await this.loadShippingMethods();
        }
    }

    loadCartFromSession() {
        // Load cart from session or global variable
        if (typeof cartData !== 'undefined') {
            this.cartItems = cartData;
        } else {
            // Fallback to session-based cart
            fetch('/cart/data')
                .then(response => response.json())
                .then(data => {
                    this.cartItems = data.items || [];
                })
                .catch(error => console.error('Error loading cart:', error));
        }
    }

    async loadShippingMethods() {
        try {
            this.showLoading();
            
            const response = await fetch('/api/shipping/methods', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    destination_city: this.destinationCity,
                    origin_city: this.originCity
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.displayShippingMethods(data.data);
                this.displayWarnings(data.data.warnings);
                this.updateCartSummary(data.data.cart_summary);
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            console.error('Error loading shipping methods:', error);
            this.showError('Failed to load shipping methods');
        } finally {
            this.hideLoading();
        }
    }

    displayShippingMethods(data) {
        const container = $('#shipping-methods');
        container.empty();

        if (data.methods.length === 0) {
            container.html(`
                <div class="alert alert-warning">
                    <i class="bx bx-info-circle"></i>
                    No shipping methods available for this route.
                </div>
            `);
            return;
        }

        data.methods.forEach(method => {
            const methodCard = this.createShippingMethodCard(method);
            container.append(methodCard);
        });
    }

    createShippingMethodCard(method) {
        const badge = method.badge ? 
            `<span class="badge bg-${method.badge_type} me-2">${method.badge}</span>` : '';
        const warning = method.warning ? 
            `<span class="badge bg-${method.warning_type} me-2">${method.warning}</span>` : '';
        const recommended = method.recommended ? 
            '<span class="badge bg-primary me-2">RECOMMENDED</span>' : '';
        
        const urgencyBadge = method.urgency_text ? 
            `<span class="badge bg-light text-dark me-2">${method.urgency_text}</span>` : '';
        
        const costBadge = method.cost_text ? 
            `<span class="badge bg-info text-white me-2">${method.cost_text}</span>` : '';

        return `
            <div class="shipping-method-card card mb-3 ${method.recommended ? 'border-success' : ''} ${method.warning ? 'border-warning' : ''}" 
                 data-method-id="${method.id}" data-cost="${method.cost}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-2">
                                ${recommended}${badge}${warning}
                                <h6 class="mb-0 me-2">${method.name}</h6>
                            </div>
                            <div class="mb-2">
                                ${urgencyBadge}${costBadge}
                                <small class="text-muted">
                                    <i class="bx bx-time-five"></i> ${method.estimated_text}
                                    ${method.fresh_product_score ? `• Score: ${method.fresh_product_score}/100` : ''}
                                </small>
                            </div>
                            ${method.logo ? `<img src="${method.logo}" alt="${method.name}" class="me-2" style="height: 20px;">` : ''}
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="fw-bold text-primary h5">${method.formatted_cost}</div>
                            <button class="btn btn-${method.recommended ? 'success' : 'outline-primary'} btn-sm select-shipping">
                                Pilih
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    displayWarnings(warnings) {
        const container = $('#shipping-warnings');
        container.empty();

        if (warnings && warnings.length > 0) {
            const warningsHtml = warnings.map(warning => `
                <div class="alert alert-${warning.type} alert-dismissible fade show" role="alert">
                    <i class="bx ${warning.icon}"></i>
                    <strong>${warning.title}</strong><br>
                    ${warning.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `).join('');
            
            container.html(warningsHtml);
            container.show();
        } else {
            container.hide();
        }
    }

    updateCartSummary(summary) {
        $('#cart-summary').html(`
            <div class="row text-center">
                <div class="col-4">
                    <div class="border rounded p-2">
                        <i class="bx bx-package text-primary"></i>
                        <div class="small">${summary.total_items} Items</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border rounded p-2">
                        <i class="bx bx-leaf text-success"></i>
                        <div class="small">${summary.has_fresh_products ? 'Fresh' : 'Regular'}</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border rounded p-2">
                        <i class="bx bx-weight text-info"></i>
                        <div class="small">${summary.total_weight} kg</div>
                    </div>
                </div>
            </div>
        `);
    }

    selectShippingMethod(e) {
        const card = $(e.currentTarget);
        const methodId = card.data('method-id');
        const cost = card.data('cost');

        // Remove previous selection
        $('.shipping-method-card').removeClass('border-success selected');
        
        // Add selection to current
        card.addClass('border-success selected');
        
        // Update hidden input
        $('#shipping_method_id').val(methodId);
        $('#shipping_cost').val(cost);
        
        // Update total
        this.updateOrderTotal(cost);
        
        // Show confirmation
        this.showSuccess('Shipping method selected successfully!');
    }

    updateOrderTotal(shippingCost) {
        const subtotal = parseFloat($('#subtotal').val() || 0);
        const total = subtotal + shippingCost;
        
        $('#shipping_cost_display').text('Rp ' + shippingCost.toLocaleString('id-ID'));
        $('#total_amount').text('Rp ' + total.toLocaleString('id-ID'));
        $('#total').val(total);
    }

    showLoading() {
        $('#shipping-loading').show();
        $('#shipping-methods').hide();
    }

    hideLoading() {
        $('#shipping-loading').hide();
        $('#shipping-methods').show();
    }

    showError(message) {
        $('#shipping-error').html(`
            <div class="alert alert-danger">
                <i class="bx bx-error-circle"></i>
                ${message}
            </div>
        `).show();
    }

    showSuccess(message) {
        $('#shipping-success').html(`
            <div class="alert alert-success">
                <i class="bx bx-check-circle"></i>
                ${message}
            </div>
        `).show().delay(3000).fadeOut();
    }

    // Validate shipping selection for fresh products
    validateFreshProductShipping() {
        if (!this.selectedMethod) {
            this.showError('Please select a shipping method');
            return false;
        }

        // Check if fresh products and method is suitable
        const hasFreshProducts = this.cartItems.some(item => 
            item.product && item.product.shelf_life_days <= 7
        );

        if (hasFreshProducts && this.selectedMethod.type === 'regular') {
            this.showError('Regular shipping not recommended for fresh products. Please choose instant or same-day delivery.');
            return false;
        }

        return true;
    }
}

// Initialize shipping manager
$(document).ready(function() {
    window.shippingManager = new ShippingManager();
});

// Global function for external calls
function validateShippingForCheckout() {
    return window.shippingManager.validateFreshProductShipping();
}
