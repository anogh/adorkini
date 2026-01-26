<?php
/**
 * Checkout Customizations
 * 
 * @package Adorkini
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remove Unnecessary Checkout Fields
 */
function adorkini_customize_checkout_fields( $fields ) {
    // Remove Company Name
    unset( $fields['billing']['billing_company'] );
    unset( $fields['shipping']['shipping_company'] );

    // Optional: Remove Country if local store only (comment out to keep)
    // unset( $fields['billing']['billing_country'] );
    // unset( $fields['shipping']['shipping_country'] );

    // Remove Address 2 if not needed
    // unset( $fields['billing']['billing_address_2'] );

    // Reorder fields if needed
    // $fields['billing']['billing_email']['priority'] = 1;
    
    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'adorkini_customize_checkout_fields' );

/**
 * Make Phone Number Required (Usually defaults to optional in some themes)
 */
function adorkini_require_phone( $fields ) {
    $fields['billing_phone']['required'] = true;
    return $fields;
}
add_filter( 'woocommerce_billing_fields', 'adorkini_require_phone' );
