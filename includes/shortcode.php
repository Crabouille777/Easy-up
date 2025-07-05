<?php
if (!defined('ABSPATH')) exit;

// Shortcode unique : [easyup_form id="..."]
function easyup_render_form_shortcode($atts) {
    $atts = shortcode_atts([
        'id' => ''
    ], $atts, 'easyup');

    $field_id = sanitize_text_field($atts['id']);

    if (empty($field_id)) {
        return '<p><em>Aucun ID fourni dans le shortcode.</em></p>';
    }

    $stored_fields = get_option('easyup_fields', []);

    if (empty($stored_fields) || !isset($stored_fields[$field_id])) {
        return '<p><em>Le champ demandé est introuvable.</em></p>';
    }

    $field = $stored_fields[$field_id];

    ob_start();
    ?>
    <div class="easyup-upload-container" data-id="<?php echo esc_attr($field_id); ?>">
        <form class="easyup-upload-form"
            enctype="multipart/form-data"
            data-field="<?php echo esc_attr($field_id); ?>"
            data-maxsize="<?php echo esc_attr($field['max_size'] * 1024 * 1024); ?>"
            data-email="<?php echo esc_attr($field['email_to']); ?>">

            <input type="file"
                name="easyup_files[]"
                <?php echo ($field['max_files'] != 1) ? 'multiple' : ''; ?>
<?php
$accept_types = [];

if (!empty($field['types']) && is_array($field['types'])) {
    $accept_types = $field['types'];
}

if (isset($field['accept_pdf']) && $field['accept_pdf'] === '1') {
    $accept_types[] = 'application/pdf';
}

$accept_attr = esc_attr(implode(',', $accept_types));
?>
accept="<?php echo $accept_attr; ?>"

                data-max="<?php echo esc_attr($field['max_files']); ?>"
                data-maxsize="<?php echo esc_attr($field['max_size']); ?>"
                class="easyup-file-input">
            <button type="button" class="easyup-send-btn">Envoyer</button>
        </form>

        <div class="easyup-message"></div>
        <div class="easyup-preview"></div>
        
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('easyup', 'easyup_render_form_shortcode');

add_filter('block_parser_classic_preserve_shortcodes', function ($shortcodes) {
    $shortcodes[] = 'easyup_form';
    return $shortcodes;
});
