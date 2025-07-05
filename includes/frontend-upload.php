<?php
if (!defined('ABSPATH')) exit;

// Ce fichier sert uniquement à enregistrer les actions AJAX
// On suppose que upload-handler.php contient la fonction appelée
require_once plugin_dir_path(__FILE__) . 'upload-handler.php';

add_action('wp_ajax_easyup_upload_files', 'easyup_handle_file_upload');
add_action('wp_ajax_nopriv_easyup_upload_files', 'easyup_handle_file_upload');
