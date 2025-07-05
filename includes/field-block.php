<?php
// Sécurité
if (!defined('ABSPATH')) exit;
?>
<?php
/**
 * Affiche un bloc de champ Easy-up dans l’interface admin
 * Ce fichier est inclus via admin-ui.php pour chaque champ
 */
?>

   <div id="easyup-fields-list">

<!-- Bloc du champ Easy-up -->
<div id="easyup-field-<?php echo esc_attr($id); ?>" class="easyup-field-block" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; background: #f5f5f5; border-radius: 4px;">

                <!-- Ligne principale : Nom du champ + champ texte shortcode + boutons -->
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <strong style="min-width: 120px;"><?php echo esc_html($id); ?></strong>
<span>Shortcode : </span>

                    <input 
                        type="text" 
                        value='[easyup id="<?php echo esc_attr($id); ?>"]' 
                        readonly 
                        style="flex: 1; min-width: 200px;" 
                        id="shortcode-<?php echo esc_attr($id); ?>"
                    >

                    <button type="button" class="button" onclick="copyShortcode('<?php echo esc_attr($id); ?>')">Copier</button>
                    <button type="button" class="button edit-button" id="edit-button-<?php echo esc_attr($id); ?>" onclick="toggleEditForm('<?php echo esc_attr($id); ?>')">Modifier</button>
                    <button type="button" class="button easyup-delete-btn" data-id="<?php echo esc_attr($id); ?>">Supprimer</button>
                    



                </div>

                <!-- Ligne secondaire : détails techniques -->
                <p style="margin-top: 10px; margin-left: 5px;">
                    fichiers max :               <?php echo esc_html($field['max_files']); ?> —
                    taille :                     <?php echo esc_html($field['max_size']); ?> Ko —
                    PDF :                        <?php echo ($field['accept_pdf'] === '1' ? 'oui' : 'non'); ?> —
                    e-mail :                     <?php echo esc_html($field['email_to']); ?>
                </p>
     <!-- Formulaire de modification caché (affiché au clic sur "Modifier") -->
<div class="easyup-edit-form" id="edit-form-<?php echo esc_attr($id); ?>" style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease; margin-top: 10px;">


    <!-- Nom du champ (affiché, non modifiable) -->
<label for="edit-name-<?php echo esc_attr($id); ?>">
    <p><strong>Nom du champ &nbsp;: &nbsp;</strong><style="font-size: 12px; font-style: italic; color: #666; margin-top: 4px;">&nbsp;(non modifiable)</style></p>
</label>
<input 
    type="text" 
    id="edit-name-<?php echo esc_attr($id); ?>" 
    value="<?php echo esc_attr($id); ?>" 
    disabled 
    style="background: #eee; width: 100%; margin-top: 4px;"
>




    <label>Nombre de fichiers max :
        <input type="number" id="edit-max-files-<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($field['max_files']); ?>">
    </label><br><br>

    <label>Taille maximale (Ko) :
        <input type="number" id="edit-max-size-<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($field['max_size']); ?>">
    </label><br><br>

    <label>Autoriser PDF :
        <select id="edit-accept-pdf-<?php echo esc_attr($id); ?>">
            <option value="1" <?php selected($field['accept_pdf'], '1'); ?>>Oui</option>
            <option value="0" <?php selected($field['accept_pdf'], '0'); ?>>Non</option>
        </select>
        <small style="color:#777;font-style:italic;">Permet d'autoriser les fichiers PDF au téléversement</small>

    </label><br><br>

    <label>Email spécifique :
        <input type="email" id="edit-email-<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($field['email_to']); ?>">
    </label><br><br>

    <button type="submit" class="button button-primary easyup-save-btn" data-id="<?php echo esc_attr($id); ?>">Enregistrer</button>
    


</div>
       

</div>