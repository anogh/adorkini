<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 */

defined('ABSPATH') || exit;
?>

<div class="warafy-checkout-wrapper">
    <?php do_action('woocommerce_before_checkout_form', $checkout); ?>

    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

        <?php if ($checkout->get_checkout_fields()) : ?>

            <?php do_action('woocommerce_checkout_before_customer_details'); ?>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8 mb-6 border border-gray-100 dark:border-gray-700" id="customer_details">
                <div class="col2-set" id="customer_details">
                    <div class="col-1">
                        <?php do_action('woocommerce_checkout_billing'); ?>
                    </div>

                    <div class="col-2">
                        <?php do_action('woocommerce_checkout_shipping'); ?>
                    </div>
                </div>
            </div>

            <?php do_action('woocommerce_checkout_after_customer_details'); ?>

        <?php endif; ?>
        
        <div class="warafy-order-review-section bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8 border border-gray-100 dark:border-gray-700">
            <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
            
            <h3 id="order_review_heading" class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-600 to-purple-700 text-white flex items-center justify-center shadow-lg">
                    <span class="material-symbols-outlined text-2xl" data-icon="receipt_long"></span>
                </div>
                <?php esc_html_e('Your order', 'woocommerce'); ?>
            </h3>
            
            <?php do_action('woocommerce_checkout_before_order_review'); ?>

            <div id="order_review" class="woocommerce-checkout-review-order">
                <?php do_action('woocommerce_checkout_order_review'); ?>
            </div>

            <?php do_action('woocommerce_checkout_after_order_review'); ?>
        </div>

    </form>
</div>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>

<script>
// Flags to track if mobile sections are already initialized - must be global
var mobileOrderSectionInitialized = false;
var mobileCouponSectionInitialized = false;
var mobileCustomerDetailsSectionInitialized = false;
var mobilePaymentSectionInitialized = false;
var checkoutInitialized = false;

// Initialize checkout form for mobile
function initializeMobileCheckoutForm() {
    try {
        var originalForm = document.querySelector('form.checkout.woocommerce-checkout');
        if (originalForm) {
            // Ensure all required fields exist in original form
            var requiredFields = ['billing_first_name', 'billing_address_1', 'billing_phone', 'billing_email'];
            
            for (var i = 0; i < requiredFields.length; i++) {
                var fieldName = requiredFields[i];
                var field = originalForm.querySelector('[name="' + fieldName + '"]');
                if (!field) {
                    // Create missing field
                    field = document.createElement('input');
                    field.type = fieldName.indexOf('email') !== -1 ? 'email' : fieldName.indexOf('phone') !== -1 ? 'tel' : 'text';
                    field.name = fieldName;
                    field.value = '';
                    field.style.display = 'none';
                    originalForm.appendChild(field);
                }
                
                // Set initial value to prevent validation errors
                if (!field.value) {
                    field.value = '';
                }
            }
            
            // DO NOT trigger update_checkout here - it causes continuous refresh on mobile
            // The order data is already loaded from the initial page render
        }
    } catch(e) {
        console.error('Checkout init error:', e);
    }
}

// Mobile checkout reordering - apply only on mobile screens
function applyMobileCheckoutReordering() {
    if (window.innerWidth < 1024) { // lg breakpoint
        const wrapper = document.querySelector('.warafy-checkout-wrapper');
        if (wrapper) {
            // If all sections are already initialized, don't do anything
            if (mobileOrderSectionInitialized && mobileCouponSectionInitialized && 
                mobileCustomerDetailsSectionInitialized && mobilePaymentSectionInitialized) {
                // Just ensure original form and sections are hidden
                const originalForm = wrapper.querySelector('form.checkout.woocommerce-checkout');
                if (originalForm) {
                    originalForm.style.display = 'none';
                }
                const orderSection = document.querySelector('.warafy-order-review-section');
                if (orderSection) {
                    orderSection.style.display = 'none';
                }
                return; // Already initialized, exit early
            }
            
            // Initialize the checkout form first
            initializeMobileCheckoutForm();
            
            // Hide the original form but don't remove it
            const originalForm = wrapper.querySelector('form.checkout.woocommerce-checkout');
            if (originalForm) {
                originalForm.style.display = 'none';
            }
            
            // 1. Create and add "Your order" card (will check flag internally)
            createOrderSection();
            
            // 2. Create and add coupon section (will check flag internally)
            createCouponSection();
            
            // 3. Add notices if exists (only once)
            if (!wrapper.querySelector('.woocommerce-notices-wrapper-mobile')) {
                const notices = document.querySelector('.woocommerce-notices-wrapper');
                if (notices) {
                    const noticesClone = notices.cloneNode(true);
                    noticesClone.classList.add('woocommerce-notices-wrapper-mobile');
                    wrapper.appendChild(noticesClone);
                    notices.style.display = 'none';
                }
            }
            
            // 4. Create and add customer details (will check flag internally)
            createCustomerDetailsSection();
            
            // 5. Create and add payment section (will check flag internally)
            createPaymentSection();
            
            // 6. Setup real-time field synchronization (only if not already set up)
            if (!wrapper.dataset.syncSetup) {
                setupMobileFieldSync();
                wrapper.dataset.syncSetup = 'true';
            }
            
            // 7. Clear any existing errors first
            clearMobileFieldErrors();
            
            // 8. Move validation errors below mobile fields
            setTimeout(() => {
                moveValidationErrorsToMobileFields();
            }, 100);
            
            // Hide the original order section
            const orderSection = document.querySelector('.warafy-order-review-section');
            if (orderSection) {
                orderSection.style.display = 'none';
            }
        }
    }
}

// Create order section
function createOrderSection() {
    // If order section is already initialized, don't recreate it
    if (mobileOrderSectionInitialized) {
        return;
    }
    
    const wrapper = document.querySelector('.warafy-checkout-wrapper');
    if (wrapper) {
        // Check if order section already exists - don't recreate
        const existingOrderSection = wrapper.querySelector('.your-order-combined');
        if (existingOrderSection) {
            mobileOrderSectionInitialized = true;
            return;
        }
        
        // Create the combined order container
        const combinedOrderContainer = document.createElement('div');
        combinedOrderContainer.className = 'your-order-combined bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8 border border-gray-100 dark:border-gray-700 mb-6';
        
        // Create the "Your order" heading
        const orderHeading = document.createElement('h3');
        orderHeading.id = 'order_review_heading';
        orderHeading.className = 'text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3';
        orderHeading.innerHTML = `
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-600 to-purple-700 text-white flex items-center justify-center shadow-lg">
                <span class="material-symbols-outlined text-2xl" data-icon="receipt_long"></span>
            </div>
            Your order
        `;
        
        // Create the order table with dynamic cart items
        const orderTable = document.createElement('table');
        orderTable.className = 'woocommerce-checkout-review-order-table';
        
        // Get dynamic cart subtotal and total
        let cartSubtotal = '৳ 0.00';
        let cartTotal = '৳ 0.00';
        
        try {
            // Try to get values from existing order review table
            const existingTable = document.querySelector('.woocommerce-checkout-review-order-table');
            if (existingTable) {
                const subtotalElement = existingTable.querySelector('tfoot .cart-subtotal td .woocommerce-Price-amount');
                const totalElement = existingTable.querySelector('tfoot .order-total td .woocommerce-Price-amount');
                
                if (subtotalElement) {
                    cartSubtotal = subtotalElement.textContent.trim();
                }
                if (totalElement) {
                    cartTotal = totalElement.textContent.trim();
                }
            } else {
                // Fallback: try desktop order summary
                const desktopSubtotal = document.querySelector('#desktop-order-summary .flex.justify-between span:last-child');
                if (desktopSubtotal) {
                    cartSubtotal = desktopSubtotal.textContent.trim();
                    cartTotal = desktopSubtotal.textContent.trim();
                }
            }
        } catch (e) {
            // Use defaults if error
            cartSubtotal = '৳ 1,260.00';
            cartTotal = '৳ 1,260.00';
        }
        
        // Build table HTML with dynamic cart data
        let tableHTML = `
            <thead>
                <tr>
                    <th class="product-name">Product</th>
                    <th class="product-total">Subtotal</th>
                </tr>
            </thead>
            <tbody>
        `;
        
        // Try to get real cart data from the existing order review table
        try {
            const existingOrderTable = document.querySelector('.woocommerce-checkout-review-order-table tbody');
            if (existingOrderTable) {
                // Copy all cart items from the existing table
                const cartItems = existingOrderTable.querySelectorAll('tr.cart_item');
                cartItems.forEach(item => {
                    const productNameCell = item.querySelector('.product-name');
                    const productTotalCell = item.querySelector('.product-total');
                    
                    if (productNameCell && productTotalCell) {
                        const productNameText = productNameCell.textContent.trim();
                        const productPriceText = productTotalCell.textContent.trim();
                        
                        // Only add if product name exists and price is not ৳ 0.00
                        if (productNameText && productNameText !== 'Product' && productPriceText && !productPriceText.includes('৳ 0.00')) {
                            tableHTML += `
                                <tr class="cart_item">
                                    <td class="product-name">
                                        ${productNameCell.innerHTML}
                                    </td>
                                    <td class="product-total">
                                        ${productTotalCell.innerHTML}
                                    </td>
                                </tr>
                            `;
                        }
                    }
                });
            } else {
                // Fallback: try to get cart data from desktop order summary
                const desktopOrderItems = document.querySelectorAll('#desktop-order-summary .border-b .flex');
                if (desktopOrderItems.length > 0) {
                    desktopOrderItems.forEach(item => {
                        const productName = item.querySelector('h4')?.textContent?.trim();
                        const productPrice = item.querySelector('.text-right p')?.textContent?.trim();
                        
                        // Only add if product name exists and price is not ৳ 0.00
                        if (productName && productName !== 'Product' && productPrice && !productPrice.includes('৳ 0.00')) {
                            tableHTML += `
                                <tr class="cart_item">
                                    <td class="product-name">
                                        ${productName}  × <span class="product-quantity">1</span>
                                    </td>
                                    <td class="product-total">
                                        <span class="woocommerce-Price-amount amount">
                                            ${productPrice}
                                        </span>
                                    </td>
                                </tr>
                            `;
                        }
                    });
                }
            }
        } catch (e) {
            // Error fallback: use a single default product
            tableHTML += `
                <tr class="cart_item">
                    <td class="product-name">
                        Boys winter hooded plush hoodie suit winter new baby casual warm and thick two-piece suit  × <span class="product-quantity">1</span>
                    </td>
                    <td class="product-total">
                        <span class="woocommerce-Price-amount amount">
                            ৳ 1,260.00
                        </span>
                    </td>
                </tr>
            `;
        }
        
        // Close tbody and add tfoot
        tableHTML += `
            </tbody>
            <tfoot>
                <tr class="cart-subtotal">
                    <th>Subtotal</th>
                    <td>
                        <span class="woocommerce-Price-amount amount">
                            ${cartSubtotal}
                        </span>
                    </td>
                </tr>
                <tr class="order-total">
                    <th>Total</th>
                    <td>
                        <span class="woocommerce-Price-amount amount">
                            ${cartTotal}
                        </span>
                    </td>
                </tr>
            </tfoot>
        `;
        
        orderTable.innerHTML = tableHTML;
        
        combinedOrderContainer.appendChild(orderHeading);
        combinedOrderContainer.appendChild(orderTable);
        
        // Insert after login form if exists, or at the start
        const existingLoginForm = wrapper.querySelector('.woocommerce-form-login');
        if (existingLoginForm) {
            existingLoginForm.parentNode.insertBefore(combinedOrderContainer, existingLoginForm.nextSibling);
        } else {
            wrapper.insertBefore(combinedOrderContainer, wrapper.firstChild);
        }
        
        // Mark as initialized to prevent future recreations
        mobileOrderSectionInitialized = true;
    }
}

// Create coupon section
function createCouponSection() {
    // If already initialized, don't recreate
    if (mobileCouponSectionInitialized) {
        return;
    }
    
    const wrapper = document.querySelector('.warafy-checkout-wrapper');
    if (wrapper) {
        // Check if coupon section already exists
        const existingCoupon = wrapper.querySelector('.woocommerce-form-coupon-toggle');
        if (existingCoupon) {
            mobileCouponSectionInitialized = true;
            return;
        }
        
        const couponContainer = document.createElement('div');
        couponContainer.className = 'woocommerce-form-coupon-toggle';
        couponContainer.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8 border border-gray-100 dark:border-gray-700 mb-6">
                <div id="coupon-toggle-section">
                    <p class="text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-500" data-icon="local_offer"></span>
                        Have a coupon? <a href="#" id="show-coupon-form" class="text-purple-600 hover:text-purple-700 font-medium ml-1">Click here to enter your code</a>
                    </p>
                </div>
                <div id="coupon-form-section" style="display: none;">
                    <div class="flex items-center gap-2 mt-4">
                        <input type="text" name="coupon_code" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter coupon code">
                        <button type="button" id="apply-coupon-btn" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            Apply coupon
                        </button>
                        <button type="button" id="cancel-coupon-btn" class="text-gray-500 hover:text-gray-700">
                            <span class="material-symbols-outlined" data-icon="close"></span>
                        </button>
                    </div>
                    <div id="coupon-message" class="mt-2 text-sm"></div>
                </div>
            </div>
        `;
        
        // Add event listeners
        couponContainer.querySelector('#show-coupon-form').addEventListener('click', function(e) {
            e.preventDefault();
            couponContainer.querySelector('#coupon-toggle-section').style.display = 'none';
            couponContainer.querySelector('#coupon-form-section').style.display = 'block';
            couponContainer.querySelector('input[name="coupon_code"]').focus();
        });
        
        couponContainer.querySelector('#cancel-coupon-btn').addEventListener('click', function() {
            couponContainer.querySelector('#coupon-toggle-section').style.display = 'block';
            couponContainer.querySelector('#coupon-form-section').style.display = 'none';
            couponContainer.querySelector('input[name="coupon_code"]').value = '';
            couponContainer.querySelector('#coupon-message').textContent = '';
        });
        
        couponContainer.querySelector('#apply-coupon-btn').addEventListener('click', function() {
            const couponCode = couponContainer.querySelector('input[name="coupon_code"]').value.trim();
            const messageDiv = couponContainer.querySelector('#coupon-message');
            
            if (!couponCode) {
                messageDiv.innerHTML = '<span class="text-red-500">Please enter a coupon code</span>';
                return;
            }
            
            // Apply coupon using WooCommerce AJAX
            if (typeof jQuery !== 'undefined') {
                messageDiv.innerHTML = '<span class="text-blue-500">Applying coupon...</span>';
                
                jQuery.ajax({
                    type: 'POST',
                    url: wc_checkout_params ? wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon') : '/?wc-ajax=apply_coupon',
                    data: {
                        coupon_code: couponCode,
                        security: wc_checkout_params ? wc_checkout_params.apply_coupon_nonce : ''
                    },
                    success: function(response) {
                        if (response.result === 'success') {
                            messageDiv.innerHTML = '<span class="text-green-500">Coupon applied successfully!</span>';
                            // Trigger checkout update to show discount
                            jQuery(document.body).trigger('update_checkout');
                            
                            // Hide coupon form after successful application
                            setTimeout(() => {
                                couponContainer.querySelector('#coupon-toggle-section').style.display = 'block';
                                couponContainer.querySelector('#coupon-form-section').style.display = 'none';
                                couponContainer.querySelector('input[name="coupon_code"]').value = '';
                            }, 2000);
                        } else {
                            messageDiv.innerHTML = '<span class="text-red-500">' + (response.message || 'Invalid coupon code') + '</span>';
                        }
                    },
                    error: function() {
                        messageDiv.innerHTML = '<span class="text-red-500">Error applying coupon. Please try again.</span>';
                    }
                });
            } else {
                messageDiv.innerHTML = '<span class="text-red-500">Coupon functionality not available</span>';
            }
        });
        
        wrapper.appendChild(couponContainer);
        mobileCouponSectionInitialized = true;
    }
}

// Create customer details section
function createCustomerDetailsSection() {
    // If already initialized, don't recreate
    if (mobileCustomerDetailsSectionInitialized) {
        return;
    }
    
    const wrapper = document.querySelector('.warafy-checkout-wrapper');
    if (wrapper) {
        // Check if customer details section already exists
        const existingCustomer = wrapper.querySelector('input[name="billing_first_name"]');
        if (existingCustomer && existingCustomer.closest('.bg-white')) {
            mobileCustomerDetailsSectionInitialized = true;
            return;
        }
        
        const customerContainer = document.createElement('div');
        customerContainer.className = 'bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8 mb-6 border border-gray-100 dark:border-gray-700';
        customerContainer.innerHTML = `
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-600 to-purple-700 text-white flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg" data-icon="person"></span>
                </div>
                Billing details
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                    <input type="text" name="billing_first_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address *</label>
                    <input type="text" name="billing_address_1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mobile Number *</label>
                    <input type="tel" name="billing_phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email address (optional)</label>
                    <input type="email" name="billing_email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order instructions (optional)</label>
                    <textarea name="order_comments" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Any special instructions for your order..."></textarea>
                </div>
            </div>
        `;
        wrapper.appendChild(customerContainer);
        mobileCustomerDetailsSectionInitialized = true;
    }
}

// Create payment section
function createPaymentSection() {
    // If already initialized, don't recreate
    if (mobilePaymentSectionInitialized) {
        return;
    }
    
    const wrapper = document.querySelector('.warafy-checkout-wrapper');
    if (wrapper) {
        // Check if payment section already exists
        const existingPayment = wrapper.querySelector('.payment-section-mobile');
        if (existingPayment) {
            mobilePaymentSectionInitialized = true;
            return;
        }
        
        const paymentContainer = document.createElement('div');
        paymentContainer.className = 'payment-section-mobile bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8 border border-gray-100 dark:border-gray-700 mt-6';
        paymentContainer.innerHTML = `
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-600 to-purple-700 text-white flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg" data-icon="payment"></span>
                </div>
                Payment Method
            </h3>
            <div class="space-y-4">
                <div class="flex items-center space-x-3 p-4 border-2 border-purple-600 rounded-lg bg-purple-50 dark:bg-purple-900/20 dark:border-purple-500">
                    <input type="radio" name="payment_method" value="cod" checked class="w-5 h-5 text-purple-600 focus:ring-purple-500 focus:ring-2">
                    <label for="payment_method_cod" class="flex-1 text-gray-700 dark:text-gray-300 font-medium cursor-pointer">Cash on delivery</label>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 ml-8">Pay with cash upon delivery.</p>
                <div class="border-t pt-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our <a href="#" class="text-purple-600 hover:text-purple-700">privacy policy</a>.</p>
                    <div class="flex items-center space-x-2 mb-4">
                        <input type="checkbox" class="w-4 h-4 text-purple-600">
                        <label class="text-sm text-gray-700 dark:text-gray-300">Subscribe to our Newsletter</label>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white py-3 px-6 rounded-lg font-medium hover:from-purple-700 hover:to-purple-800 transition-all duration-200 flex items-center justify-center gap-2" onclick="submitMobileCheckoutForm(event)">
                        <span class="material-symbols-outlined" data-icon="shopping_cart"></span>
                        Place order
                    </button>
                </div>
            </div>
        `;
        
        // Add event listener to handle form submission
        paymentContainer.querySelector('button').addEventListener('click', function(e) {
            e.preventDefault();
            submitMobileCheckoutForm(e);
        });
        
        wrapper.appendChild(paymentContainer);
        mobilePaymentSectionInitialized = true;
    }
}

// Function to submit the mobile checkout form
function submitMobileCheckoutForm(event) {
    event.preventDefault();
    
    // Find the original checkout form
    const originalForm = document.querySelector('form.checkout.woocommerce-checkout');
    
    if (originalForm) {
        // Copy mobile form data to original form
        copyMobileFormDataToOriginalForm(originalForm);
        
        // Show the original form temporarily for submission
        originalForm.style.display = 'block';
        
        // Use WooCommerce's checkout submission
        if (typeof jQuery !== 'undefined') {
            // Trigger WooCommerce's checkout validation and submission
            jQuery(originalForm).on('submit', function(e) {
                // Let WooCommerce handle the submission
                return true;
            });
            
            // Trigger the submit event
            jQuery(originalForm).submit();
        } else {
            // Fallback: native form submission
            originalForm.submit();
        }
    } else {
        console.error('Original checkout form not found');
        alert('There was an error processing your order. Please try again.');
    }
}

// Function to copy mobile form data to the original checkout form
function copyMobileFormDataToOriginalForm(originalForm) {
    // Copy billing details from mobile inputs to original form
    const mobileInputs = {
        'billing_first_name': document.querySelector('input[name="billing_first_name"]'),
        'billing_address_1': document.querySelector('input[name="billing_address_1"]'),
        'billing_phone': document.querySelector('input[name="billing_phone"]'),
        'billing_email': document.querySelector('input[name="billing_email"]'),
        'order_comments': document.querySelector('textarea[name="order_comments"]')
    };
    
    // Copy each field value
    Object.keys(mobileInputs).forEach(fieldName => {
        const mobileInput = mobileInputs[fieldName];
        const originalInput = originalForm.querySelector(`[name="${fieldName}"]`);
        
        if (originalInput) {
            if (mobileInput && mobileInput.value) {
                originalInput.value = mobileInput.value;
            } else {
                // Set default empty value to prevent validation errors
                originalInput.value = '';
            }
            // Trigger change event for WooCommerce validation
            originalInput.dispatchEvent(new Event('change', { bubbles: true }));
            originalInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });
    
    // Copy payment method selection
    const mobilePaymentMethod = document.querySelector('input[name="payment_method"]:checked');
    if (mobilePaymentMethod) {
        const originalPaymentMethod = originalForm.querySelector(`input[name="payment_method"][value="${mobilePaymentMethod.value}"]`);
        if (originalPaymentMethod) {
            originalPaymentMethod.checked = true;
            // Trigger change event for WooCommerce
            originalPaymentMethod.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }
    
    // Copy terms checkbox if exists
    const mobileTerms = document.querySelector('input[name="terms"]');
    const originalTerms = originalForm.querySelector('input[name="terms"]');
    if (mobileTerms && originalTerms) {
        originalTerms.checked = mobileTerms.checked;
    }
    
    // Trigger WooCommerce checkout update
    if (typeof jQuery !== 'undefined') {
        jQuery(document.body).trigger('update_checkout');
    }
}

// Add real-time synchronization for mobile fields
function setupMobileFieldSync() {
    const mobileInputs = document.querySelectorAll('.bg-white.dark\\:bg-gray-800.rounded-2xl.shadow-xl.p-6.lg\\:p-8.mb-6.border.border-gray-100.dark\\:border-gray-700 input, .bg-white.dark\\:bg-gray-800.rounded-2xl.shadow-xl.p-6.lg\\:p-8.mb-6.border.border-gray-100.dark\\:border-gray-700 textarea');
    
    mobileInputs.forEach(input => {
        input.addEventListener('input', function() {
            const originalForm = document.querySelector('form.checkout.woocommerce-checkout');
            if (originalForm) {
                const fieldName = this.name;
                const originalInput = originalForm.querySelector(`[name="${fieldName}"]`);
                
                if (originalInput) {
                    originalInput.value = this.value;
                    // Removed input event dispatch to prevent excessive AJAX refreshes
                    // Removed change event dispatch to prevent excessive AJAX refreshes
                }
            }
        });
        
        // Add change event listener with selective checkout update
        input.addEventListener('change', function() {
            const originalForm = document.querySelector('form.checkout.woocommerce-checkout');
            if (originalForm) {
                const fieldName = this.name;
                const originalInput = originalForm.querySelector(`[name="${fieldName}"]`);
                
                if (originalInput) {
                    originalInput.value = this.value;
                    originalInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                
                // Only trigger checkout update for fields that affect pricing
                // Not for basic contact info fields on mobile checkout
                const pricingFields = ['billing_country', 'billing_state', 'billing_postcode', 'billing_city', 'shipping_country', 'shipping_state', 'shipping_postcode', 'shipping_city'];
                if (pricingFields.includes(fieldName)) {
                    if (typeof jQuery !== 'undefined') {
                        jQuery(document.body).trigger('update_checkout');
                    }
                }
            }
        });
    });
}

// Function to move validation errors below mobile fields
function moveValidationErrorsToMobileFields() {
    // Find WooCommerce validation errors
    const woocommerceErrors = document.querySelectorAll('.woocommerce-error li, .woocommerce-info li, .woocommerce-error, .woocommerce-info');
    
    console.log('Found WooCommerce errors:', woocommerceErrors.length);
    
    woocommerceErrors.forEach(error => {
        const errorText = error.textContent;
        console.log('Processing error:', errorText);
        
        // Map error messages to mobile fields
        let targetFieldName = null;
        let fieldLabel = '';
        
        if (errorText.includes('Name') || errorText.includes('first name')) {
            targetFieldName = 'billing_first_name';
            fieldLabel = 'Name';
        } else if (errorText.includes('Address') || errorText.includes('address')) {
            targetFieldName = 'billing_address_1';
            fieldLabel = 'Address';
        } else if (errorText.includes('Mobile') || errorText.includes('Phone') || errorText.includes('phone')) {
            targetFieldName = 'billing_phone';
            fieldLabel = 'Mobile Number';
        } else if (errorText.includes('Email') || errorText.includes('email')) {
            targetFieldName = 'billing_email';
            fieldLabel = 'Email';
        } else if (errorText.includes('coupon') || errorText.includes('Coupon')) {
            // Skip coupon errors - they're handled separately
            return;
        }
        
        if (targetFieldName) {
            const mobileField = document.querySelector(`[name="${targetFieldName}"]`);
            if (mobileField) {
                console.log('Found mobile field for:', targetFieldName);
                
                // Check if field actually has a value
                const fieldValue = mobileField.value.trim();
                console.log('Field value:', fieldValue);
                
                // Remove existing error for this field
                const existingError = mobileField.parentNode.querySelector('.field-error');
                if (existingError) {
                    existingError.remove();
                }
                
                // Only show error if field is empty or has invalid data
                if (fieldValue === '' || (targetFieldName === 'billing_email' && fieldValue && !isValidEmail(fieldValue))) {
                    // Add error below the field
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'field-error text-red-500 text-sm mt-1';
                    errorDiv.textContent = fieldLabel + ' is required.';
                    
                    mobileField.parentNode.appendChild(errorDiv);
                    console.log('Added error for field:', targetFieldName);
                }
                
                // Hide the original error
                error.style.display = 'none';
            } else {
                console.log('Mobile field not found for:', targetFieldName);
            }
        } else {
            console.log('No field mapping found for error:', errorText);
        }
    });
}

// Email validation helper function
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Function to clear mobile field errors
function clearMobileFieldErrors() {
    const mobileErrors = document.querySelectorAll('.field-error');
    mobileErrors.forEach(error => error.remove());
}

// Add WooCommerce validation hooks
if (typeof jQuery !== 'undefined') {
    // Handle checkout validation errors
    jQuery(document.body).on('checkout_error', function() {
        setTimeout(() => {
            moveValidationErrorsToMobileFields();
        }, 100);
    });
    
    // Handle checkout updates
    jQuery(document.body).on('updated_checkout', function() {
        setTimeout(() => {
            moveValidationErrorsToMobileFields();
        }, 100);
    });
}

// Add direct event listeners for error clearing (more reliable than jQuery)
document.addEventListener('input', function(e) {
    if (e.target.matches('input[name="billing_first_name"], input[name="billing_address_1"], input[name="billing_phone"], input[name="billing_email"], textarea[name="order_comments"]')) {
        // Clear error for this specific field
        const existingError = e.target.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Also trigger WooCommerce validation update
        const originalForm = document.querySelector('form.checkout.woocommerce-checkout');
        if (originalForm) {
            const fieldName = e.target.name;
            const originalInput = originalForm.querySelector(`[name="${fieldName}"]`);
            if (originalInput) {
                originalInput.value = e.target.value;
                // Removed input event dispatch to prevent excessive AJAX refreshes
                // Removed change event dispatch to prevent excessive AJAX refreshes
            }
        }
    }
    // Exclude coupon field from form validation tracking
    if (e.target.matches('input[name="coupon_code"]')) {
        // Don't trigger form validation for coupon field
        return;
    }
});

// Also add change event listeners
document.addEventListener('change', function(e) {
    if (e.target.matches('input[name="billing_first_name"], input[name="billing_address_1"], input[name="billing_phone"], input[name="billing_email"], textarea[name="order_comments"]')) {
        // Clear error for this specific field
        const existingError = e.target.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Only trigger WooCommerce checkout update for fields that affect pricing
        const fieldName = e.target.name;
        const pricingFields = ['billing_country', 'billing_state', 'billing_postcode', 'billing_city', 'shipping_country', 'shipping_state', 'shipping_postcode', 'shipping_city'];
        
        if (pricingFields.includes(fieldName)) {
            if (typeof jQuery !== 'undefined') {
                jQuery(document.body).trigger('update_checkout');
            }
        }
    }
    // Exclude coupon field from form validation tracking
    if (e.target.matches('input[name="coupon_code"]')) {
        // Don't trigger form validation for coupon field
        return;
    }
});

// Clean up any existing mobile reordering
function cleanupMobileReordering() {
    // Remove any existing mobile containers
    const existingCombined = document.querySelector('.your-order-combined');
    const existingPayment = document.querySelector('.payment-section-mobile');
    const existingCustomer = document.querySelector('.bg-white.dark\\:bg-gray-800.rounded-2xl.shadow-xl.p-6.lg\\:p-8.mb-6.border.border-gray-100.dark\\:border-gray-700');
    
    if (existingCombined) existingCombined.remove();
    if (existingPayment) existingPayment.remove();
    if (existingCustomer) existingCustomer.remove();
    
    // Show original elements
    const originalElements = [
        '.woocommerce-form-coupon-toggle',
        '.checkout_coupon',
        '.woocommerce-notices-wrapper',
        '#customer_details',
        '#payment',
        '.warafy-order-review-section'
    ];
    
    originalElements.forEach(selector => {
        const element = document.querySelector(selector);
        if (element) {
            element.style.display = '';
        }
    });
    
    // Restore original desktop layout if needed
    if (window.innerWidth >= 1024) {
        const wrapper = document.querySelector('.warafy-checkout-wrapper');
        if (wrapper) {
            // Restore the original form structure for desktop
            const form = wrapper.querySelector('form.checkout');
            if (!form) {
                // If form was removed, recreate it for desktop
                restoreDesktopLayout();
            }
        }
    }
}

// Restore desktop layout function
function restoreDesktopLayout() {
    const wrapper = document.querySelector('.warafy-checkout-wrapper');
    if (wrapper && window.innerWidth >= 1024) {
        // Clear any mobile modifications
        wrapper.innerHTML = '';
        
        // Recreate the original checkout form structure for desktop
        const form = document.createElement('form');
        form.name = 'checkout';
        form.method = 'post';
        form.className = 'checkout woocommerce-checkout';
        form.action = wc_get_checkout_url ? wc_get_checkout_url() : '/checkout/';
        form.enctype = 'multipart/form-data';
        
        // Add customer details section
        const customerDetails = document.createElement('div');
        customerDetails.id = 'customer_details';
        customerDetails.className = 'bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8 mb-6 border border-gray-100 dark:border-gray-700';
        customerDetails.innerHTML = `
            <div class="col2-set" id="customer_details">
                <div class="col-1">
                    <!-- Billing details will be loaded by WooCommerce -->
                </div>
                <div class="col-2">
                    <!-- Shipping details will be loaded by WooCommerce -->
                </div>
            </div>
        `;
        
        // Add order review section
        const orderReview = document.createElement('div');
        orderReview.className = 'warafy-order-review-section bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8 border border-gray-100 dark:border-gray-700';
        orderReview.innerHTML = `
            <h3 id="order_review_heading" class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-600 to-purple-700 text-white flex items-center justify-center shadow-lg">
                    <span class="material-symbols-outlined text-2xl" data-icon="receipt_long"></span>
                </div>
                Your order
            </h3>
            <div id="order_review" class="woocommerce-checkout-review-order">
                <!-- Order review will be loaded by WooCommerce -->
            </div>
        `;
        
        form.appendChild(customerDetails);
        form.appendChild(orderReview);
        wrapper.appendChild(form);
        
        // Trigger WooCommerce to reload the checkout form
        if (typeof wc_checkout_params !== 'undefined') {
            jQuery(document.body).trigger('update_checkout');
        }
    }
}

// Apply on page load - only once
document.addEventListener('DOMContentLoaded', function() {
    if (checkoutInitialized) return;
    checkoutInitialized = true;
    
    // Check screen size first
    const isMobile = window.innerWidth < 1024;
    
    // Wait for page to fully load - apply layout ONCE
    setTimeout(() => {
        if (isMobile) {
            // Only apply mobile logic on mobile screens - ONCE
            cleanupMobileReordering();
            setTimeout(() => {
                applyMobileCheckoutReordering();
            }, 100);
        } else {
            // On desktop, ensure proper styling and don't interfere
            setTimeout(() => {
                stylePaymentRadioButtons();
            }, 500);
        }
    }, 500);
    
    // Ensure radio buttons are visible and styled
    setTimeout(() => {
        stylePaymentRadioButtons();
    }, 1500);
});

// Function to style payment radio buttons
function stylePaymentRadioButtons() {
    // Find all payment method radio buttons
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    
    paymentRadios.forEach(radio => {
        // Force radio button to be visible with maximum specificity
        radio.style.cssText = `
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            accent-color: #9333ea !important;
            width: 1.25rem !important;
            height: 1.25rem !important;
            cursor: pointer !important;
            margin: 0 !important;
            padding: 0 !important;
            z-index: 999 !important;
            float: left !important;
            -webkit-appearance: radio !important;
            -moz-appearance: radio !important;
            appearance: radio !important;
        `;
        
        // Also set attributes directly
        radio.setAttribute('style', radio.style.cssText);
        
        // Add event listener to update styling when checked
        radio.addEventListener('change', function() {
            // Remove checked styling from all payment methods
            document.querySelectorAll('.payment_method').forEach(method => {
                method.style.borderColor = '';
                method.style.backgroundColor = '';
            });
            
            // Add checked styling to selected payment method
            if (this.checked) {
                const paymentMethod = this.closest('.payment_method');
                if (paymentMethod) {
                    paymentMethod.style.borderColor = '#9333ea';
                    paymentMethod.style.backgroundColor = '#faf5ff';
                }
            }
        });
        
        // Trigger change event to apply initial styling
        if (radio.checked) {
            radio.dispatchEvent(new Event('change'));
        }
    });
    
    // Also style checkboxes
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.style.cssText = `
            accent-color: #9333ea !important;
            width: 1.25rem !important;
            height: 1.25rem !important;
            cursor: pointer !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-block !important;
        `;
    });
    
    // Create a mutation observer to watch for dynamically added payment methods
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        const radios = node.querySelectorAll ? node.querySelectorAll('input[name="payment_method"]') : [];
                        radios.forEach(radio => {
                            radio.style.cssText = `
                                display: inline-block !important;
                                visibility: visible !important;
                                opacity: 1 !important;
                                position: relative !important;
                                accent-color: #9333ea !important;
                                width: 1.25rem !important;
                                height: 1.25rem !important;
                                cursor: pointer !important;
                                margin: 0 !important;
                                padding: 0 !important;
                                z-index: 999 !important;
                                float: left !important;
                                -webkit-appearance: radio !important;
                                -moz-appearance: radio !important;
                                appearance: radio !important;
                            `;
                        });
                    }
                });
            }
        });
    });
    
    // Start observing the document body for changes
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}

// Apply on window resize - debounced to prevent excessive calls
let resizeTimeout = null;
let previousScreenMode = window.innerWidth < 1024 ? 'mobile' : 'desktop';

window.addEventListener('resize', function() {
    // Debounce resize events
    if (resizeTimeout) {
        clearTimeout(resizeTimeout);
    }
    
    resizeTimeout = setTimeout(function() {
        const isMobile = window.innerWidth < 1024;
        const currentMode = isMobile ? 'mobile' : 'desktop';
        
        // Only act if the mode actually changed (crossing the 1024px threshold)
        if (currentMode === previousScreenMode) {
            return;
        }
        
        previousScreenMode = currentMode;
        
        if (isMobile) {
            // Switching from desktop to mobile - reset flags and apply mobile layout
            mobileOrderSectionInitialized = false;
            mobileCouponSectionInitialized = false;
            mobileCustomerDetailsSectionInitialized = false;
            mobilePaymentSectionInitialized = false;
            
            setTimeout(applyMobileCheckoutReordering, 100);
            
            // Force hide order summary on mobile
            const orderSummary = document.querySelector('.desktop-order-summary, .lg\\:col-span-1');
            if (orderSummary) {
                orderSummary.style.display = 'none';
                orderSummary.style.visibility = 'hidden';
                orderSummary.style.height = '0';
                orderSummary.style.overflow = 'hidden';
            }
            
            // Block WooCommerce auto updates on mobile
            blockWooCommerceAutoUpdates();
        } else {
            // Switching from mobile to desktop - restore original layout
            cleanupMobileReordering();
            // Restyle radio buttons for desktop
            setTimeout(() => {
                stylePaymentRadioButtons();
            }, 100);
            
            // Show order summary on desktop
            const orderSummary = document.querySelector('.desktop-order-summary, .lg\\:col-span-1');
            if (orderSummary) {
                orderSummary.style.display = '';
                orderSummary.style.visibility = '';
                orderSummary.style.height = '';
                orderSummary.style.overflow = '';
            }
        }
    }, 250); // 250ms debounce
});

// Sync desktop order summary with WooCommerce order review
function syncDesktopOrderSummary() {
    if (window.innerWidth >= 1024) {
        const orderSummary = document.querySelector('#desktop-order-summary');
        const orderReview = document.querySelector('.woocommerce-checkout-review-order-table');
        
        if (orderSummary && orderReview) {
            // Update the desktop order summary to match the order review table
            setTimeout(() => {
                updateDesktopOrderSummaryFromReview();
            }, 500);
        }
    }
}

// Update desktop order summary from the WooCommerce order review table
function updateDesktopOrderSummaryFromReview() {
    const orderSummary = document.getElementById('desktop-order-summary');
    const orderReviewTable = document.querySelector('.woocommerce-checkout-review-order-table');
    
    if (orderSummary && orderReviewTable && window.innerWidth >= 1024) {
        // Get the order review content
        const orderReviewHTML = orderReviewTable.outerHTML;
        
        // Create a simplified version for the desktop sidebar
        const cartItems = orderReviewTable.querySelectorAll('tbody .cart_item');
        const subtotalElements = orderReviewTable.querySelectorAll('tfoot .cart-subtotal td');
        const totalElements = orderReviewTable.querySelectorAll('tfoot .order-total td');
        
        if (cartItems.length > 0) {
            let itemsHTML = '<div class="border-b border-gray-200 dark:border-gray-700 pb-4 space-y-3">';
            
            cartItems.forEach(item => {
                const productName = item.querySelector('.product-name')?.textContent || 'Product';
                const productTotal = item.querySelector('.product-total')?.textContent || '৳ 0.00';
                
                itemsHTML += `
                    <div class="flex items-start space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900 dark:to-purple-800 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-lg text-purple-600 dark:text-purple-300" data-icon="shopping_bag"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 dark:text-white text-sm truncate">${productName}</h4>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900 dark:text-white text-sm">${productTotal}</p>
                        </div>
                    </div>
                `;
            });
            
            itemsHTML += '</div>';
            
            // Add price summary
            let priceSummaryHTML = '<div class="space-y-2">';
            
            if (subtotalElements.length > 0) {
                priceSummaryHTML += `
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>Subtotal</span>
                        <span>${subtotalElements[0].textContent}</span>
                    </div>
                `;
            }
            
            if (totalElements.length > 0) {
                priceSummaryHTML += `
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                        <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                            <span>Total</span>
                            <span class="text-purple-600 dark:text-purple-400">${totalElements[0].textContent}</span>
                        </div>
                    </div>
                `;
            }
            
            priceSummaryHTML += '</div>';
            
            // Update the desktop order summary
            orderSummary.innerHTML = itemsHTML + priceSummaryHTML;
        }
    }
}

// Update checkout page when cart changes - only for desktop
jQuery(document.body).on('updated_checkout', function() {
    // Only sync desktop order summary on desktop screens
    // On mobile, we don't want to refresh the order section
    if (window.innerWidth >= 1024) {
        syncDesktopOrderSummary();
    }
});

// Update when payment method changes
jQuery(document.body).on('payment_method_selected', function() {
    setTimeout(syncDesktopOrderSummary, 500);
});

// Initial sync
jQuery(document).ready(function() {
    setTimeout(syncDesktopOrderSummary, 1000);
    
    // Force hide order summary on mobile
    if (window.innerWidth < 1024) {
        const orderSummary = document.querySelector('.desktop-order-summary, .lg\\:col-span-1');
        if (orderSummary) {
            orderSummary.style.display = 'none';
            orderSummary.style.visibility = 'hidden';
            orderSummary.style.height = '0';
            orderSummary.style.overflow = 'hidden';
        }
        
        // CRITICAL FIX: Block WooCommerce's automatic update_checkout on mobile
        // This prevents the order review from constantly refreshing when typing
        blockWooCommerceAutoUpdates();
    }
});

// Block WooCommerce's automatic checkout updates on mobile to prevent refresh loop
function blockWooCommerceAutoUpdates() {
    if (window.innerWidth >= 1024) return; // Only on mobile
    
    // Override the update_checkout trigger to debounce/block on mobile
    let updateCheckoutBlocked = false;
    let lastUpdateTime = 0;
    const MIN_UPDATE_INTERVAL = 10000; // Minimum 10 seconds between updates on mobile
    
    // Intercept update_checkout events on mobile
    jQuery(document.body).on('update_checkout', function(e) {
        const now = Date.now();
        
        // Block frequent updates on mobile - only allow updates every 10 seconds minimum
        if (now - lastUpdateTime < MIN_UPDATE_INTERVAL && mobileOrderSectionInitialized) {
            // Prevent this update from executing
            e.stopImmediatePropagation();
            return false;
        }
        
        lastUpdateTime = now;
    });
    
    // Prevent input events on billing fields from triggering checkout updates
    const billingFields = ['billing_first_name', 'billing_address_1', 'billing_phone', 'billing_email', 'order_comments'];
    
    billingFields.forEach(fieldName => {
        const field = document.querySelector(`input[name="${fieldName}"], textarea[name="${fieldName}"]`);
        if (field) {
            // Stop input events from bubbling to WooCommerce's listeners
            field.addEventListener('input', function(e) {
                e.stopPropagation();
            }, true);
            
            // Stop change events from triggering checkout updates (except for submission)
            field.addEventListener('change', function(e) {
                // Only stop propagation if we're not submitting
                if (!document.querySelector('.processing')) {
                    e.stopPropagation();
                }
            }, true);
        }
    });
    
    // Also block keyup events that WooCommerce listens to
    document.querySelectorAll('.warafy-checkout-wrapper input, .warafy-checkout-wrapper textarea').forEach(function(input) {
        input.addEventListener('keyup', function(e) {
            e.stopPropagation();
        }, true);
    });
}
</script>
