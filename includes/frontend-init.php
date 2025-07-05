<?php
if (!defined('ABSPATH')) exit;

// Enregistrement des scripts frontend et injection des données utilisateur + nonce
add_action('wp_enqueue_scripts', 'easyup_enqueue_frontend_assets');

function easyup_enqueue_frontend_assets() {
    if (!is_user_logged_in()) {
        return;
    }

    wp_enqueue_style(
        'easyup-frontend-style',
        plugin_dir_url(__DIR__) . 'assets/css/frontend.css'
    );

    wp_enqueue_script(
        'easyup-frontend',
        plugin_dir_url(__DIR__) . 'assets/js/frontend.js',
        ['jquery'],
        '1.0',
        true
    );

    $current_user = wp_get_current_user();
    $user_data = [
        'email'      => $current_user->user_email,
        'first_name' => $current_user->first_name,
        'last_name'  => $current_user->last_name,
    ];

    wp_localize_script('easyup-frontend', 'easyup_frontend', [
        'ajax_url'  => admin_url('admin-ajax.php'),
        'nonce'     => wp_create_nonce('easyup_upload_nonce'),
        'user_data' => $user_data,
    ]);
}
