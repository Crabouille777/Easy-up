<?php
// Sécurité
if (!defined('ABSPATH')) exit;

/**
 * Récupère les champs enregistrés.
 */
function get_option_easy_up_fields() {
    return get_option('easyup_fields', []);
}

// Charge les assets (CSS + JS) spécifiques à l’administration
function easyup_enqueue_admin_assets($hook) {
    if (strpos($hook, 'easyup') === false) return;

    wp_enqueue_style(
        'easyup-admin-style',
        plugin_dir_url(__FILE__) . '../assets/css/admin-style.css'
    );

    wp_enqueue_script(
        'easyup-admin-script',
        plugin_dir_url(__FILE__) . '../assets/js/admin.js',
        ['jquery'],
        null,
        true
    );

    error_log('✅ Script admin.js chargé');

    wp_localize_script('easyup-admin-script', 'easyup_ajax_obj', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('easyup_nonce')
    ]);
}
add_action('admin_enqueue_scripts', 'easyup_enqueue_admin_assets');
