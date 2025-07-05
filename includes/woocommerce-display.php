<?php
// === Affichage des fichiers uploadés dans WooCommerce (admin commande) ===

// S'assure que WooCommerce est chargé avant d'ajouter le hook
add_action('plugins_loaded', function () {

    // Vérifie que WooCommerce est actif
    if (!class_exists('WooCommerce')) {
        return;
    }

    /**
     * Affiche les fichiers uploadés dans l'administration WooCommerce
     *
     * @param mixed $order L'objet de commande ou autre
     */
    function easyup_display_uploaded_files($order) {

        // Tente de convertir en objet commande
        if (is_numeric($order)) {
            $order = wc_get_order($order);
        }

        // Vérifie que c’est bien un objet WC_Order
        if (!is_a($order, 'WC_Order')) {
            return; // On quitte proprement si ce n'est pas valide
        }

        // Récupère les fichiers associés
        $uploads = get_post_meta($order->get_id(), '_easyup_uploads', true);

        if (is_array($uploads) && !empty($uploads)) {
            echo '<div class="order_data_column">';
            echo '<h4>Fichiers envoyés :</h4>';
            echo '<ul>';
            foreach ($uploads as $file) {
                if (!empty($file)) {
                    echo '<li>' . esc_html(basename($file)) . '</li>';
                }
            }
            echo '</ul>';
            echo '</div>';
        }
    }

    // Hook WooCommerce : affichage dans la fiche commande
    add_action('woocommerce_admin_order_data_after_order_details', 'easyup_display_uploaded_files');
});

