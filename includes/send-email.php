<?php
if (!defined('ABSPATH')) exit;

/**
 * Envoie un email HTML avec fichiers en pièce jointe.
 *
 * @param string $field_id
 * @param array $user_info ['name' => 'Nom complet', 'email' => 'ex@domaine.com']
 * @param array $uploaded_files Liste des chemins de fichiers uploadés
 * @return bool true si l'e-mail a été envoyé, false sinon
 */
function easyup_send_email_with_attachments($field_id, $user_info, $uploaded_files) {
    $fields = get_option('easyup_fields', []);
    if (empty($field_id) || !isset($fields[$field_id])) {
        return false;
    }

    $user_name  = isset($user_info['name']) ? sanitize_text_field($user_info['name']) : 'Utilisateur';
    $user_email = isset($user_info['email']) ? sanitize_email($user_info['email']) : '';

    $field = $fields[$field_id];
    $admin_email = !empty($field['email_to']) ? sanitize_email($field['email_to']) : get_option('admin_email');
    if (!is_email($admin_email)) {
        $admin_email = get_option('admin_email');
    }

    $subject = "Fichiers reçus - EasyUp - formulaire '{$field_id}'";

    // Construction du message HTML
    $message  = "<h2>Fichiers envoyés via EasyUp</h2>";
    $message .= "<p><strong>Formulaire :</strong> " . esc_html($field_id) . "</p>";
    $message .= "<p><strong>Utilisateur :</strong> " . esc_html($user_name);
    if ($user_email) {
        $message .= " (" . esc_html($user_email) . ")";
    }
    $message .= "</p>";
    $message .= "<p><strong>Fichiers :</strong></p><ul>";

    $attachments = [];

    foreach ($uploaded_files as $file_path) {
        if (!file_exists($file_path)) continue;
        $filename = basename($file_path);
        $message .= "<li>" . esc_html($filename) . "</li>";
        $attachments[] = $file_path;
    }

    $message .= "</ul>";

    $headers = [];
    $headers[] = "Content-Type: text/html; charset=UTF-8";

    return wp_mail($admin_email, $subject, $message, $headers, $attachments);
}
