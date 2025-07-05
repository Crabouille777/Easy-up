<?php
if (!defined('ABSPATH')) {
    exit;
}

// Enregistrer un nouveau champ
add_action('wp_ajax_easyup_add_field', function () {
    $fields = get_option('easyup_fields', []);
    $id = time(); // identifiant unique basé sur le timestamp
    $fields[$id] = [
        'name' => 'Nouveau champ',
        'admin_email' => get_option('admin_email'),
        'max_files' => 1,
        'max_size' => 2,
        'types'       => [],
    ];
    update_option('easyup_fields', $fields);
    wp_send_json_success(['id' => $id]);
});

// Sauvegarder les modifications
add_action('wp_ajax_easyup_update_field', function () {
    if (!isset($_POST['id'])) {
        wp_send_json_error();
    }

    $id = sanitize_text_field($_POST['id']);
    $fields = get_option('easyup_fields', []);

    if (!isset($fields[$id])) {
        wp_send_json_error();
    }

    $fields[$id]['name']        = sanitize_text_field($_POST['name'] ?? '');
    $fields[$id]['admin_email'] = sanitize_email($_POST['email'] ?? '');
    $fields[$id]['max_files']   = max(0, intval($_POST['max'] ?? 1));
    $fields[$id]['max_size']    = max(1, intval($_POST['max_size'] ?? 2));
    $fields[$id]['email_to']    = sanitize_email($_POST['email'] ?? ''); // ✅ ajouté pour cohérence
    $fields[$id]['accept_pdf'] = ($_POST['accept_pdf'] ?? '0') === '1' ? '1' : '0';

    $fields[$id]['types'] = isset($_POST['types']) && is_array($_POST['types'])
    ? array_map('sanitize_text_field', $_POST['types'])
    : [];

    update_option('easyup_fields', $fields);
    wp_send_json_success();
});

// Supprimer un champ
add_action('wp_ajax_easyup_delete_field', function () {
    if (!isset($_POST['id'])) {
        wp_send_json_error();
    }

    $id = sanitize_text_field($_POST['id']);
    $fields = get_option('easyup_fields', []);
    if (isset($fields[$id])) {
        unset($fields[$id]);
        update_option('easyup_fields', $fields);
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
});
