<?php
if (!defined('ABSPATH')) {
    exit;
}

// Interface d’administration complète
function easyup_render_admin_interface() {
    $fields = get_option('easyup_fields', []);
// Affiche la page d'administration


    // Si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['easyup_new_field'])) {
    // On récupère tous les champs enregistrés pour ne pas les écraser
    $fields = get_option('easyup_fields', []);

    // Sanitize et récupération des valeurs du formulaire
    $field_id = sanitize_text_field($_POST['label']);
    $max_files = intval($_POST['max_files']);
    $max_size = intval($_POST['max_size']);
    $accept_pdf = isset($_POST['accept_pdf']) ? '1' : '0';
    $email_to = sanitize_email($_POST['email_to']);

    // Vérifie que le champ est non vide
    if (!empty($field_id)) {
        // Si le champ existe déjà, message d'erreur
        if (isset($fields[$field_id])) {
            echo '<div class="error"><p>Ce champ existe déjà. Veuillez choisir un autre identifiant.</p></div>';
        } else {
            // Ajout du nouveau champ
            $fields[$field_id] = [
                'max_files'  => $max_files,
                'max_size'   => $max_size,
                'accept_pdf' => $accept_pdf,
                'email_to'   => $email_to,
            ];

            // Mise à jour en base
            update_option('easyup_fields', $fields);
            echo '<div class="updated"><p>Champ enregistré.</p></div>';
        }
    } else {
        echo '<div class="error"><p>Veuillez saisir un identifiant de champ.</p></div>';
    }
}


    // Formulaire de création d'un shortcode
    ?>
    <div class="wrap">
        <h1>Paramètres de Easy-up</h1>

        <h2>Créer un nouveau champ</h2>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="field_id">Identifiant du champ (unique)</label></th>

                    <!-- Ce champ devient $field['label'] dans la base -->
                    <td><input type="text" name="label" id="label" required></td>

                </tr>
                <tr>
                    <th scope="row"><label for="max_files">Nombre maximum de fichiers</label></th>
                    <td><input type="number" name="max_files" id="max_files" min="1" value="1"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="max_size">Taille maximale par fichier (en Ko)</label></th>
                    <td><input type="number" name="max_size" id="max_size" min="1" value="2048"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="accept_pdf">Accepter les fichiers PDF ?</label></th>
                    <td><input type="checkbox" name="accept_pdf" id="accept_pdf"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="email_to">Adresse e-mail de réception</label></th>
                    <td><input type="email" name="email_to" id="email_to" required></td>
                </tr>
            </table>
            <p><input type="submit" name="easyup_new_field" class="button button-primary" value="Enregistrer le champ"></p>
        </form>

       
   <h2>Champs enregistrés</h2>
<?php if (!empty($fields)): ?>
<div style="max-width: 800px;"> <!-- Bloc parent à largeur fixée -->
   
   <?php
$fields = get_option('easyup_fields', []);

if (!empty($fields)) {
    foreach ($fields as $id => $field) {
        // Affiche chaque bloc HTML complet ici
        // 👇 soit avec HTML directement (comme tu faisais),
        // 👇 soit en incluant une version propre de field-block.php :
        include plugin_dir_path(__FILE__) . 'field-block.php';
    }
}
?>
   



<?php else: ?>
    <p>Aucun champ enregistré pour le moment.</p>
<?php endif; ?>

<!-- Script JS : copie du shortcode -->
<script>
function copyShortcode(id) {
    const input = document.getElementById('shortcode-' + id);
    input.select();
    input.setSelectionRange(0, 99999); // Pour mobile
    document.execCommand("copy");
    alert("Shortcode copié : " + input.value);
}
</script>

<script>
    // Fonction de suppression d'un champ via AJAX
    function deleteField(fieldId) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce champ ?')) return;

        const data = new FormData();
        data.append('action', 'easyup_delete_field');
        data.append('field_id', fieldId);

        fetch(ajaxurl, {
            method: 'POST',
            credentials: 'same-origin',
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Supprime visuellement le bloc du champ supprimé
                const bloc = document.getElementById('easyup-field-' + fieldId);
                if (bloc) bloc.remove();
            } else {
                alert('Erreur : ' + result.data);
            }
        });
    }
// Affiche ou masque le formulaire de modification
function toggleEditForm(fieldId) {
    const form = document.getElementById('edit-form-' + fieldId);
    const editButton = document.querySelector('#easyup-field-' + fieldId + ' .edit-button');

    if (!form || !editButton) return;

    const isOpen = form.classList.contains('open');

    // Ferme tous les autres formulaires ouverts
    document.querySelectorAll('.easyup-edit-form').forEach(otherForm => {
        otherForm.classList.remove('open');
        otherForm.style.maxHeight = null;
    });

    // Réactive tous les boutons Modifier
    document.querySelectorAll('.edit-button').forEach(btn => {
        btn.disabled = false;
    });

    if (!isOpen) {
        // Ouvre ce formulaire
        form.classList.add('open');
        form.style.maxHeight = form.scrollHeight + 'px';
        editButton.disabled = true;
    }
}




// Enregistre les modifications via AJAX
function saveFieldEdit(fieldId) {
    const maxFiles = document.getElementById('edit-max-files-' + fieldId).value;
    const maxSize = document.getElementById('edit-max-size-' + fieldId).value;
    const acceptPdf = document.getElementById('edit-accept-pdf-' + fieldId).value;
    const email = document.getElementById('edit-email-' + fieldId).value;

    const data = new FormData();
    data.append('action', 'easyup_update_field');
    data.append('field_id', fieldId);
    data.append('max_files', maxFiles);
    data.append('max_size', maxSize);
    data.append('accept_pdf', acceptPdf);
    data.append('email_to', email);

    fetch(ajaxurl, {
        method: 'POST',
        credentials: 'same-origin',
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // 🔄 Met à jour dynamiquement les infos dans le bloc
            const bloc = document.getElementById('easyup-field-' + fieldId);
            if (bloc) {
                const infos = bloc.querySelector('p');
                infos.innerHTML =
                    'fichiers max : ' + maxFiles +
                    ' — taille : ' + maxSize + ' Ko' +
                    ' — PDF : ' + (acceptPdf === '1' ? 'oui' : 'non') +
                    ' — e-mail : ' + email;

                // ⛔ Replie le formulaire avec transition
                const form = document.getElementById('edit-form-' + fieldId);
                if (form) {
                    form.style.maxHeight = null;
                    form.classList.remove('open');
                }

                // ✅ Réactive le bouton "Modifier"
                const editButton = bloc.querySelector('.button.edit-button');
                if (editButton) {
                    editButton.disabled = false;
                }
            }
        } else {
            alert('Erreur : ' + result.data);
        }
    })
    .catch(error => {
        console.error('Erreur AJAX :', error);
        alert("Une erreur s'est produite lors de l'enregistrement.");
    });
}


</script>



    </div>
    <?php
}// Traitement AJAX pour supprimer un champ
add_action('wp_ajax_easyup_delete_field', function () {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Accès refusé');
    }

    $field_id = sanitize_text_field($_POST['field_id'] ?? '');
    if (!$field_id) {
        wp_send_json_error('Champ non spécifié');
    }

    $fields = get_option('easyup_fields', []);

    

    
    // Vérifie si le champ existe dans les données enregistrées
if (!array_key_exists($field_id, $fields)) {
    wp_send_json_error('Champ introuvable');
}


    unset($fields[$field_id]);
    update_option('easyup_fields', $fields);

    wp_send_json_success('Champ supprimé');
});
// Traitement AJAX pour modifier un champ
add_action('wp_ajax_easyup_update_field', function () {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Accès refusé');
    }

    $field_id = sanitize_text_field($_POST['field_id'] ?? '');
    $max_files = intval($_POST['max_files'] ?? 1);
    $max_size = intval($_POST['max_size'] ?? 2048);
    $accept_pdf = sanitize_text_field($_POST['accept_pdf'] ?? '0');
    $email_to = sanitize_email($_POST['email_to'] ?? '');

    $fields = get_option('easyup_fields', []);
    if (!isset($fields[$field_id])) {
        wp_send_json_error('Champ introuvable');
    }

    $fields[$field_id]['max_files'] = $max_files;
    $fields[$field_id]['max_size'] = $max_size;
    $fields[$field_id]['accept_pdf'] = $accept_pdf;
    $fields[$field_id]['email_to'] = $email_to;

    update_option('easyup_fields', $fields);
    wp_send_json_success('Champ mis à jour');
    
});
