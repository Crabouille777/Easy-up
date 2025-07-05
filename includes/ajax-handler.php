<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_easyup_add_field', function () {
    $fields = get_option('easyup_fields', []);
    $key = uniqid('easyup_');
    $fields[$key] = [
        'label' => sanitize_text_field($_POST['label']),
        'admin_email' => sanitize_email($_POST['admin_email']),
        'max_files' => intval($_POST['max_files']),
        'max_size' => floatval($_POST['max_size']),
        'accept_pdf' => isset($_POST['accept_pdf']) ? sanitize_text_field($_POST['accept_pdf']) : 'off',
        'email_to' => sanitize_email($_POST['admin_email']),
    ];
    update_option('easyup_fields', $fields);
    wp_send_json_success();
    return;
});

add_action('wp_ajax_easyup_delete_field', function () {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Accès refusé');
        return;
    }

    $field_id = sanitize_text_field($_POST['field_id'] ?? '');
    if (!$field_id) {
        wp_send_json_error('Champ non spécifié');
        return;
    }

    $fields = get_option('easyup_fields', []);
    if (!isset($fields[$field_id])) {
        wp_send_json_error('Champ introuvable');
        return;
    }

    unset($fields[$field_id]);
    update_option('easyup_fields', $fields);
    wp_send_json_success();
    return;
});

function easyup_update_field_callback() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Accès refusé');
        return;
    }

    $field_id = sanitize_text_field($_POST['field_id'] ?? '');
    if (!$field_id) {
        wp_send_json_error('ID manquant');
        return;
    }

    $fields = get_option('easyup_fields', []);
    if (!isset($fields[$field_id])) {
        wp_send_json_error('Champ introuvable');
        return;
    }

    $fields[$field_id]['max_files'] = intval($_POST['max_files']);
    $fields[$field_id]['max_size'] = floatval($_POST['max_size']);
    $fields[$field_id]['accept_pdf'] = isset($_POST['accept_pdf']) ? sanitize_text_field($_POST['accept_pdf']) : 'off';
    $fields[$field_id]['admin_email'] = sanitize_email($_POST['email_to']);
    $fields[$field_id]['email_to'] = sanitize_email($_POST['email_to']);

    update_option('easyup_fields', $fields);
    wp_send_json_success();
    return;
}
add_action('wp_ajax_easyup_update_field', 'easyup_update_field_callback');
