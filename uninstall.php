<?php
// Sécurité : empêche l'accès direct
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Supprime les options stockées en base par Easy-up
delete_option('easyup_fields');
delete_option('easyup_other_setting_if_any'); // Ajoute d'autres options à supprimer ici si besoin

// Supprime éventuellement les fichiers uploadés par Easy-up (optionnel)
$upload_dir = WP_CONTENT_DIR . '/uploads/easyup';
if (is_dir($upload_dir)) {
    // Fonction récursive pour supprimer les fichiers
    function easyup_rrmdir($dir) {
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            is_dir($path) ? easyup_rrmdir($path) : unlink($path);
        }
        rmdir($dir);
    }
    easyup_rrmdir($upload_dir);
}

