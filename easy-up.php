<?php
/*
 * Plugin Name: Easy-up
 * Description: Permet aux utilisateurs d'envoyer un ou plusieurs fichiers par mail via un champ personnalisé avec shortcode.
 * Version: 1.0
 * Author: Crabouille777
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: easy-up

 * Note de l'auteur :
 * Ce plugin est distribué gratuitement dans un esprit de partage.
 * Merci de ne pas le vendre ou monétiser sous une forme quelconque.
*/

if (!defined('ABSPATH')) exit;

// Définition des chemins
define('EASYUP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('EASYUP_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Chargement des assets frontend (JS Upload + ajaxurl)
 */


/**
 * Chargement des fichiers principaux du plugin
 */
function easyup_load_plugin_files() {

    require_once EASYUP_PLUGIN_PATH . 'includes/admin-ui.php';
    require_once EASYUP_PLUGIN_PATH . 'includes/admin-save.php';
    require_once EASYUP_PLUGIN_PATH . 'includes/ajax-handler.php';
    require_once EASYUP_PLUGIN_PATH . 'includes/save-handler.php';
    require_once EASYUP_PLUGIN_PATH . 'includes/display-fields.php';
    require_once EASYUP_PLUGIN_PATH . 'includes/woocommerce-display.php';
    require_once EASYUP_PLUGIN_PATH . 'includes/functions.php';
    require_once EASYUP_PLUGIN_PATH . 'includes/admin-page.php';
    require_once EASYUP_PLUGIN_PATH . 'includes/shortcode.php';
    require_once EASYUP_PLUGIN_PATH . 'includes/frontend-upload.php';
    require_once EASYUP_PLUGIN_PATH . 'includes/frontend-init.php';
    require_once plugin_dir_path(__FILE__) . 'includes/frontend-upload.php';


    // Enregistrement des hooks de scripts
    add_action('admin_enqueue_scripts', 'easyup_enqueue_admin_assets');
    add_action('wp_enqueue_scripts', 'easyup_enqueue_frontend_assets');
}
add_action('plugins_loaded', 'easyup_load_plugin_files');

/**
 * Ajoute un lien "Paramètres" dans la liste des extensions
 */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'easyup_add_settings_link');
function easyup_add_settings_link($links) {
    $url = admin_url('admin.php?page=easyup');
    $settings_link = '<a href="' . esc_url($url) . '">' . __('Paramètres', 'easyup') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}


/**
 * Fonction d'activation : crée le dossier uploads avec permission 755
 */
function easyup_activate_plugin() {
    $upload_dir = EASYUP_PLUGIN_PATH . 'uploads';

    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true); // création récursive avec permission 755
    } else {
        chmod($upload_dir, 0755);
    }
}
register_activation_hook(__FILE__, 'easyup_activate_plugin');
