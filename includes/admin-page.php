<?php
// Sécurité
if (!defined('ABSPATH')) exit;

// Fonction d'affichage de la page admin 
function easyup_admin_page_content() {
    require_once EASYUP_PLUGIN_PATH . 'includes/admin-ui.php';
    easyup_render_admin_interface();
}

// Enregistrement du menu admin
function easyup_register_admin_menu() {
    add_menu_page(
        'Easy-up',
        'Easy-up',
        'manage_options',
        'easyup',
        'easyup_admin_page_content',
        'dashicons-upload',
        80
    );
}
add_action('admin_menu', 'easyup_register_admin_menu');

add_action('wp_ajax_easyup_delete_field', 'easyup_delete_field_callback');

function easyup_delete_field_callback() {
    check_ajax_referer('easyup_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission refusée');
    }

    $field_id = sanitize_text_field($_POST['field_id']);
    if (!$field_id) {
        wp_send_json_error('Champ invalide');
    }

    $fields = get_option('easyup_fields', []);
    if (!isset($fields[$field_id])) {
        wp_send_json_error('Champ introuvable');
    }

    unset($fields[$field_id]);
    update_option('easyup_fields', $fields);

    wp_send_json_success();
}
