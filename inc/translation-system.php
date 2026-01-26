<?php
/**
 * Custom Translation System
 * Lightweight replacement for heavy translation plugins.
 * 
 * @package Adorkini
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get current language
 * Defaults to 'en' if not set.
 */
function adorkini_get_current_lang() {
    if ( isset( $_COOKIE['adorkini_lang'] ) && in_array( $_COOKIE['adorkini_lang'], array( 'en', 'bn' ) ) ) {
        return $_COOKIE['adorkini_lang'];
    }
    return 'en';
}

/**
 * Get translation string
 * 
 * @param string $key The key to translate.
 * @return string Translated string or key if not found.
 */
function __t( $key ) {
    static $translations = null;
    $lang = adorkini_get_current_lang();

    // Load translations if not loaded
    if ( $translations === null ) {
        $file = get_template_directory() . '/assets/translations/translations.json';
        if ( file_exists( $file ) ) {
            $content = file_get_contents( $file );
            $translations = json_decode( $content, true );
        } else {
            $translations = array();
        }
    }

    // Return translation
    if ( isset( $translations[$lang][$key] ) ) {
        return $translations[$lang][$key];
    }

    // Fallback to English if BN is missing
    if ( $lang !== 'en' && isset( $translations['en'][$key] ) ) {
        return $translations['en'][$key];
    }

    return $key; // Fallback to key itself
}

/**
 * Handle Language Switch
 * Uses a query param ?lang=bn to set cookie and redirect.
 */
function adorkini_language_switcher_init() {
    if ( isset( $_GET['lang'] ) ) {
        $lang = sanitize_text_field( $_GET['lang'] );
        if ( in_array( $lang, array( 'en', 'bn' ) ) ) {
            setcookie( 'adorkini_lang', $lang, time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
            $_COOKIE['adorkini_lang'] = $lang; // Set for current request
        }
        
        // Remove lang param from URL to prevent loop/clutter
        $redirect = remove_query_arg( 'lang' );
        wp_safe_redirect( $redirect );
        exit;
    }
}
add_action( 'init', 'adorkini_language_switcher_init' );
