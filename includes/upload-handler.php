<?php
if (!defined('ABSPATH')) exit;

require_once dirname(__FILE__) . '/send-email.php';

if (!function_exists('easyup_handle_file_upload')) {
    function easyup_handle_file_upload() {
        // Vérifier le nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'easyup_upload_nonce')) {
            wp_send_json_error(['message' => 'Nonce invalide.']);
            exit;
        }

        // Récupérer le field_id (identifiant formulaire/shortcode)
        $field_id = sanitize_text_field($_POST['field_id'] ?? '');
        if (empty($field_id)) {
            wp_send_json_error(['message' => 'Identifiant de formulaire manquant.']);
            exit;
        }

        // Récupérer la liste des formulaires enregistrés
        $fields = get_option('easyup_fields', []);
        $field = $fields[$field_id] ?? null;

        if (!$field) {
            wp_send_json_error(['message' => 'Formulaire non trouvé ou shortcode manquant.']);
            exit;
        }

        // Récupérer le nom du dossier à utiliser pour cet identifiant
        // Exemple : dans $field on peut avoir une clé 'shortcode' ou 'slug'
        // Si non existant, on utilise $field_id brut
        $shortcode_name = $field['shortcode'] ?? $field['slug'] ?? $field_id;

        // Préparer le dossier d'upload : wp-content/uploads/easyup/nom-shortcode/
        $upload_dir = wp_upload_dir();
        $base_dir = trailingslashit($upload_dir['basedir']) . 'easyup/' . sanitize_file_name($shortcode_name) . '/';

        if (!file_exists($base_dir)) {
            wp_mkdir_p($base_dir); // crée récursivement le dossier si absent
        }

        if (empty($_FILES['easyup_files'])) {
            wp_send_json_error(['message' => 'Aucun fichier uploadé détecté.']);
            exit;
        }

        $saved_files = [];

        // Parcourir les fichiers uploadés
        foreach ($_FILES['easyup_files']['name'] as $index => $filename) {
            $tmp_name = $_FILES['easyup_files']['tmp_name'][$index];
            $error = $_FILES['easyup_files']['error'][$index];

            if ($error !== UPLOAD_ERR_OK) {
                continue; // ignorer ce fichier en cas d'erreur
            }

            // Nettoyer le nom de fichier
            $clean_name = sanitize_file_name($filename);

            // Chemin complet de destination
            $dest_path = $base_dir . $clean_name;

            // Éviter d’écraser un fichier existant en renommant si besoin
            $dest_path = wp_unique_filename($base_dir, $clean_name);
            $dest_path = $base_dir . $dest_path;

            if (move_uploaded_file($tmp_name, $dest_path)) {
                // Enregistrer chemin relatif pour réponse ou mail
                $saved_files[] = $dest_path;
            }
        }

        if (empty($saved_files)) {
            wp_send_json_error(['message' => 'Aucun fichier valide n’a été enregistré.']);
            exit;
        }

        // Préparer infos utilisateur pour mail
        $user = wp_get_current_user();
        $user_email = $user->user_email;
        $user_name = trim($user->first_name . ' ' . $user->last_name);
        if (empty($user_name)) {
            $user_name = $user->display_name ?: 'Utilisateur connecté';
        }

        // Envoyer le mail avec les fichiers joints
        $mail_sent = easyup_send_email_with_attachments($field_id, [
            'name' => $user_name,
            'email' => $user_email,
        ], $saved_files);

        if (!$mail_sent) {
            wp_send_json_error(['message' => 'Erreur lors de l’envoi du mail.']);
            exit;
        }

        wp_send_json_success([
            'message' => 'Fichiers envoyés et mail transmis avec succès.',
            'files' => $saved_files,
        ]);
        exit;
    }
}
