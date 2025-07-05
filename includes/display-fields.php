<?php
// Sécurité : empêche l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Affiche les champs enregistrés dans l’interface d’administration du plugin.
 */
function easyup_display_fields_page() {
    // Récupération des champs enregistrés
    $fields = get_option('easyup_fields', []);

    ?>
    <div class="wrap" style="max-width: 800px;">
        <h2>Champs Easy-up enregistrés</h2>
        <?php if (!empty($fields)) : ?>
            <?php foreach ($fields as $field) : ?>
                <div class="easyup-field-block" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
                    <h3><?php echo esc_html($field['name']); ?></h3>

                    <!-- Shortcode affiché dans un champ avec bouton copier -->
                    <p>
                        Shortcode :
                        <input type="text" value='[easyup id="<?php echo esc_attr($field['id']); ?>"]' readonly
                            class="shortcode-input" style="width: 60%;">
                        <button class="button copy-shortcode-btn">Copier</button>
                    </p>

                    <!-- Email associé -->
                    <p>Email associé : <strong><?php echo esc_html($field['email']); ?></strong></p>

                    <!-- Boutons Modifier / Supprimer -->
                    <form method="post" style="margin-top: 10px;">
                        <input type="hidden" name="easyup_field_id" value="<?php echo esc_attr($field['id']); ?>">
                        <input type="submit" name="easyup_delete_field" class="button button-secondary"
                            value="Supprimer le champ">
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>Aucun champ n’a encore été enregistré.</p>
        <?php endif; ?>
    </div>

    <!-- JS pour le bouton "Copier" -->
    <script>
        document.querySelectorAll('.copy-shortcode-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = this.previousElementSibling;
                input.select();
                document.execCommand('copy');
                this.textContent = 'Copié !';
                setTimeout(() => this.textContent = 'Copier', 2000);
            });
        });
    </script>
    <?php
}

