<?php
/**
 * Plugin Name: WP CrossRef DOI
 * Plugin URI: https://example.com/wp-crossref-doi
 * Description: Un plugin WordPress pour générer des DOI pour les articles en utilisant l'API CrossRef.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Vérifier si l'accès direct est protégé
defined('ABSPATH') or die('No script kiddies please!');

// Inclure toutes les fonctions ici
// wp_crossref_doi_render_metadata_form(): Crée et affiche le formulaire de métadonnées pour l'utilisateur.
function wp_crossref_doi_render_metadata_form($post) {
    $post_id = $post->ID;
    $metadata = get_post_meta($post_id, 'wp_crossref_doi_metadata', true);
    
    // Titre
    echo '<p><label for="wp_crossref_doi_metadata_title">' . __('Titre', 'wp-crossref-doi') . '</label>';
    echo '<input type="text" id="wp_crossref_doi_metadata_title" name="wp_crossref_doi_metadata[title]" value="' . esc_attr($metadata['title']) . '"></p>';
    
    // Auteurs
    echo '<p><label for="wp_crossref_doi_metadata_authors">' . __('Auteurs (séparés par des virgules)', 'wp-crossref-doi') . '</label>';
    echo '<input type="text" id="wp_crossref_doi_metadata_authors" name="wp_crossref_doi_metadata[authors]" value="' . esc_attr($metadata['authors']) . '"></p>';
    
    // Date de publication
    echo '<p><label for="wp_crossref_doi_metadata_publication_date">' . __('Date de publication', 'wp-crossref-doi') . '</label>';
    echo '<input type="date" id="wp_crossref_doi_metadata_publication_date" name="wp_crossref_doi_metadata[publication_date]" value="' . esc_attr($metadata['publication_date']) . '"></p>';
    
    // Nom de la conférence
    echo '<p><label for="wp_crossref_doi_metadata_conference_name">' . __('Nom de la conférence', 'wp-crossref-doi') . '</label>';
    echo '<input type="text" id="wp_crossref_doi_metadata_conference_name" name="wp_crossref_doi_metadata[conference_name]" value="' . esc_attr($metadata['conference_name']) . '"></p>';
    
    // Lieu de la conférence
    echo '<p><label for="wp_crossref_doi_metadata_conference_location">' . __('Lieu de la conférence', 'wp-crossref-doi') . '</label>';
    echo '<input type="text" id="wp_crossref_doi_metadata_conference_location" name="wp_crossref_doi_metadata[conference_location]" value="' . esc_attr($metadata['conference_location']) . '"></p>';
    
    // Date de début de la conférence
    echo '<p><label for="wp_crossref_doi_metadata_conference_start_date">' . __('Date de début de la conférence', 'wp-crossref-doi') . '</label>';
    echo '<input type="date" id="wp_crossref_doi_metadata_conference_start_date" name="wp_crossref_doi_metadata[conference_start_date]" value="' . esc_attr($metadata['conference_start_date']) . '"></p>';
    
    // Date de fin de la conférence
    echo '<p><label for="wp_crossref_doi_metadata_conference_end_date">' . __('Date de fin de la conférence', 'wp-crossref-doi') . '</label>';
    echo '<input type="date" id="wp_crossref_doi_metadata_conference_end_date" name="wp_crossref_doi_metadata[conference_end_date]" value="' . esc_attr($metadata['conference_end_date']) . '"></p>';
}

// wp_crossref_doi_save_metadata(): Sauvegarde les métadonnées saisies par l'utilisateur.
function wp_crossref_doi_save_metadata($post_id) {
    // Vérifier si notre champ de nonce est défini.
    if (!isset($_POST['wp_crossref_doi_metadata_nonce'])) {
        return;
    }

    // Vérifier que le nonce est valide.
    if (!wp_verify_nonce($_POST['wp_crossref_doi_metadata_nonce'], 'wp_crossref_doi_save_metadata')) {
        return;
    }

    // Si c'est une sauvegarde automatique, ne faites rien.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Vérifiez les autorisations de l'utilisateur.
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Enregistrez les métadonnées.
    if (isset($_POST['wp_crossref_doi_metadata'])) {
        $metadata = array_map('sanitize_text_field', $_POST['wp_crossref_doi_metadata']);
        update_post_meta($post_id, 'wp_crossref_doi_metadata', $metadata);
    }
}

// wp_crossref_doi_create_xml(): Génère le fichier XML en utilisant les métadonnées de l'article et le modèle fourni par CrossRef.





// Initialiser les paramètres du plugin
add_action('admin_menu', 'wp_crossref_doi_create_settings_page');
add_action('admin_init', 'wp_crossref_doi_settings_init');

// Ajouter le bouton DOI aux actions des articles
add_filter('post_row_actions', 'wp_crossref_doi_add_button', 10, 2);

// Gérer les requêtes AJAX pour générer le DOI et afficher le formulaire de métadonnées
add_action('wp_ajax_wp_crossref_doi_generate', 'wp_crossref_doi_generate_ajax_handler');
add_action('wp_ajax_wp_crossref_doi_render_metadata_form', 'wp_crossref_doi_render_metadata_form_ajax_handler');
add_action('wp_ajax_wp_crossref_doi_save_metadata', 'wp_crossref_doi_save_metadata_ajax_handler');

// Inclure des fichiers JavaScript et CSS pour l'administration de WordPress
add_action('admin_enqueue_scripts', 'wp_crossref_doi_enqueue_admin_scripts');

// Ajouter le filtre pour insérer le DOI dans le contenu de l'article
add_filter('the_content', 'wp_crossref_doi_add_to_content');

// Fonction pour ajouter le DOI au contenu de l'article
function wp_crossref_doi_add_to_content($content) {
    // Vérifier si nous sommes dans la boucle principale et si c'est un article
    if (!is_singular('post') || !in_the_loop()) {
        return $content;
    }

    // Récupérer le DOI à partir des Custom Fields
    $doi = get_post_meta(get_the_ID(), 'DOI', true);

    // Si un DOI existe, l'ajouter au contenu de l'article
    if (!empty($doi)) {
        $doi_html = '<div class="et_pb_text_inner"><p>DOI: ' . esc_html($doi) . '</p></div>';
        $content = $doi_html . $content;
    }

    return $content;
}
