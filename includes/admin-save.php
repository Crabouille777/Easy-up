<?php
// Fichier : includes/admin-save.php

if (!defined('ABSPATH')) exit;

add_action('wp_ajax_easyup_create_field', 'easyup_create_field');
add_action('wp_ajax_easyup_save_field', 'easyup_save_field');
add_action('wp_ajax_easyup_delete_field', 'easyup_delete_field');


function easyup_create_field() {
    check_ajax_referer('easyup_nonce');

    $fields = get_option('easyup_fields', []);
    $name  = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $id = sanitize_title($name);

    if (!$name || !$email) {
        wp_send_json_error('Nom et email obligatoires.');
    }

    // Utiliser le nom comme ID (si unique)
    if (isset($fields[$id])) {
        wp_send_json_error('Ce champ existe déjà.');
    }

    $fields[$id] = [
        'id'        => $id,
        'name'      => $name,
        'email'     => $email,
        'active'    => true,
        'max_files' => 1,
        'max_size'  => 2048,
        'accept_pdf'=> 0
    ];

    update_option('easyup_fields', $fields);
    error_log('✅ Enregistrement champ : ' . print_r($fields, true));
    wp_send_json_success(['message' => 'Champ créé', 'id' => $id]);
    
}




function easyup_save_field() {
    check_ajax_referer('easyup_nonce');

    $fields = get_option('easyup_fields', []);
    $id = sanitize_text_field($_POST['id'] ?? '');
    $name = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $max_files = absint($_POST['max_files'] ?? 1);
    $max_size = absint($_POST['max_size'] ?? 2); // en Mo
    $active = isset($_POST['active']) && $_POST['active'] === '1' ? true : false;

    if (!$name || !$email || !$id) {
        wp_send_json_error(['message' => 'Champs requis manquants.']);
    }

    $fields[$id] = [
        'id'        => $id,
        'name'      => $name,
        'email'     => $email,
        'active'    => $active,
        'max_files' => $max_files ?: 1,
        'max_size'  => $max_size ?: 2,
    ];

    update_option('easyup_fields', $fields);
    wp_send_json_success(['message' => 'Champ enregistré']);
    error_log('✅ Enregistrement champ : ' . print_r($fields, true));
}

function easyup_delete_field() {
    check_ajax_referer('easyup_nonce');

    $id = sanitize_text_field($_POST['id'] ?? '');
    $fields = get_option('easyup_fields', []);

    if (isset($fields[$id])) {
        unset($fields[$id]);
        update_option('easyup_fields', $fields);
        wp_send_json_success(['message' => 'Champ supprimé']);
    }

    wp_send_json_error(['message' => 'Champ non trouvé']);
}

