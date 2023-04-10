<?php
/**
 * Plugin Name: WP CrossRef DOI
 * Plugin URI: https://example.com/wp-crossref-doi
 * Description: Un plugin WordPress pour générer des DOI pour les articles en utilisant l'API CrossRef.
 * Version: 1.0.0
 * Author: Alexandre Bastard AKA DocVinum
 * Author URI: https://etoh.digital
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
// Empêcher l'accès direct au fichier
defined('ABSPATH') or die('Accès direct interdit.');

// Ajoutez le champ DOI dans le menu Quick Edit
function wp_crossref_doi_quick_edit_doi_field($column_name, $post_type) {
    if ($column_name === 'doi' && $post_type === 'post') {
        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <label>
                    <span class="title"><?php _e('DOI', 'wp-crossref-doi'); ?></span>
                    <span class="input-text-wrap">
                        <input type="text" name="wp_crossref_doi" class="ptitle" value="">
                    </span>
                </label>
            </div>
        </fieldset>
        <?php
    }
}
add_action('quick_edit_custom_box', 'wp_crossref_doi_quick_edit_doi_field', 10, 2);

// Enregistrez le DOI lors de la sauvegarde à partir du menu Quick Edit
function wp_crossref_doi_save_quick_edit_doi($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['wp_crossref_doi'])) {
        update_post_meta($post_id, '_wp_crossref_doi', sanitize_text_field($_POST['wp_crossref_doi']));
    }
}
add_action('save_post', 'wp_crossref_doi_save_quick_edit_doi');

// Générez le DOI en utilisant un appel AJAX
function wp_crossref_doi_generate_ajax() {
    check_ajax_referer('wp_crossref_doi_ajax_nonce', 'nonce');

    $post_id = intval($_POST['post_id']);

    if (!current_user_can('edit_post', $post_id)) {
        wp_die(__('Vous n\'avez pas les permissions nécessaires pour effectuer cette action.', 'wp-crossref-doi'));
    }

    // Générez le DOI en suivant les étapes décrites précédemment

    wp_die();
}
add_action('wp_ajax_wp_crossref_doi_generate', 'wp_crossref_doi_generate_ajax');

// Ajoutez un champ personnalisé pour le DOI dans la boîte des méta-données sur la page d'édition des articles
function wp_crossref_doi_add_doi_meta_box() {
    add_meta_box(
        'wp_crossref_doi_meta_box',
        __('DOI', 'wp-crossref-doi'),
        'wp_crossref_doi_doi_meta_box_html',
        'post',
        'side'
    );
}
add_action('add_meta_boxes', 'wp_crossref_doi_add_doi_meta_box');

// Affichez le champ personnalisé du DOI dans la boîte des méta-données



function wp_crossref_doi_doi_meta_box_html($post) {
    $doi = get_post_meta($post->ID, '_wp_crossref_doi', true);

    ?>
    <label for="wp_crossref_doi"><?php _e('DOI:', 'wp-crossref-doi'); ?></label>
    <input type="text" name="wp_crossref_doi" id="wp_crossref_doi" value="<?php echo esc_attr($doi); ?>">
    <?php
}

// Enregistrez le DOI lors de la sauvegarde à partir de la boîte des méta-données
function wp_crossref_doi_save_doi_meta_box($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['wp_crossref_doi'])) {
        update_post_meta($post_id, '_wp_crossref_doi', sanitize_text_field($_POST['wp_crossref_doi']));
    }
}
add_action('save_post', 'wp_crossref_doi_save_doi_meta_box');

// Ajoutez les scripts et les styles pour l'administration
function wp_crossref_doi_enqueue_admin_scripts($hook) {
    global $post;

    if ($hook == 'post-new.php' || $hook == 'post.php') {
        wp_enqueue_script('wp_crossref_doi_admin', plugins_url('js/admin-script.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'));
        wp_enqueue_style('wp_crossref_doi_jquery_ui', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

        wp_localize_script('wp_crossref_doi_admin', 'wp_crossref_doi_vars', array(
            'wp_crossref_doi_nonce' => wp_create_nonce('wp_crossref_doi_ajax_nonce'),
            'wp_crossref_doi_ajaxurl' => admin_url('admin-ajax.php')
        ));
    }
}
add_action('admin_enqueue_scripts', 'wp_crossref_doi_enqueue_admin_scripts');





/**
 * Activation du plugin
 */
function wp_crossref_doi_activation() {
    // Code à exécuter lors de l'activation du plugin
}
register_activation_hook(__FILE__, 'wp_crossref_doi_activation');

/**
 * Désactivation du plugin
 */
function wp_crossref_doi_deactivation() {
    // Code à exécuter lors de la désactivation du plugin
}
register_deactivation_hook(__FILE__, 'wp_crossref_doi_deactivation');

/**
 * Démarrage du plugin
 */
function wp_crossref_doi_init() {
    // Code à exécuter lors du démarrage du plugin (initialisation, chargement des dépendances, etc.)
    load_plugin_textdomain('wp-crossref-doi', false, basename(dirname(__FILE__)) . '/languages');
}
add_action('init', 'wp_crossref_doi_init');