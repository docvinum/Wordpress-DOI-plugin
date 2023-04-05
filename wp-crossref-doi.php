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
// ...

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
