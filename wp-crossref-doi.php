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

// Inclure toutes les fonctions ci-dessous

// wp_crossref_doi_create_settings_page(): Crée la page de paramètres dans l'administration de WordPress.
function wp_crossref_doi_create_settings_page() {
    // Ajouter une page de menu dans l'administration de WordPress
    add_options_page(
        __('Paramètres de DOI CrossRef', 'wp-crossref-doi'),
        __('DOI CrossRef', 'wp-crossref-doi'),
        'manage_options',
        'wp-crossref-doi',
        'wp_crossref_doi_render_settings'
    );
}
add_action('admin_menu', 'wp_crossref_doi_create_settings_page');

// wp_crossref_doi_settings_init(): Initialise les champs et sections de paramètres.
function wp_crossref_doi_settings_init() {
    // Enregistrer les paramètres
    register_setting('wp_crossref_doi', 'wp_crossref_doi_settings');

    // Ajouter une section pour les paramètres de connexion
    add_settings_section(
        'wp_crossref_doi_login_section',
        __('Paramètres de connexion', 'wp-crossref-doi'),
        '',
        'wp_crossref_doi'
    );

    // Ajouter les champs pour les identifiants de connexion
    add_settings_field(
        'login_id_prod',
        __('Identifiant de connexion (Production)', 'wp-crossref-doi'),
        'wp_crossref_doi_login_id_prod_render',
        'wp_crossref_doi',
        'wp_crossref_doi_login_section'
    );

    add_settings_field(
        'login_passwd_prod',
        __('Mot de passe (Production)', 'wp-crossref-doi'),
        'wp_crossref_doi_login_passwd_prod_render',
        'wp_crossref_doi',
        'wp_crossref_doi_login_section'
    );

    add_settings_field(
        'login_id_test',
        __('Identifiant de connexion (Test)', 'wp-crossref-doi'),
        'wp_crossref_doi_login_id_test_render',
        'wp_crossref_doi',
        'wp_crossref_doi_login_section'
    );

    add_settings_field(
        'login_passwd_test',
        __('Mot de passe (Test)', 'wp-crossref-doi'),
        'wp_crossref_doi_login_passwd_test_render',
        'wp_crossref_doi',
        'wp_crossref_doi_login_section'
    );

    // Ajouter un champ pour sélectionner l'environnement (test ou production)
    add_settings_field(
        'environment',
        __('Environnement', 'wp-crossref-doi'),
        'wp_crossref_doi_environment_render',
        'wp_crossref_doi',
        'wp_crossref_doi_login_section'
    );
}
add_action('admin_init', 'wp_crossref_doi_settings_init');

// wp_crossref_doi_render_settings(): Rendu des champs et sections de paramètres.
function wp_crossref_doi_render_settings() {
    // Récupérer les options enregistrées
    $options = get_option('wp_crossref_doi_options');

    // Vérifier si chaque option est définie, sinon utiliser une valeur par défaut
    $prod_login_id = isset($options['prod_login_id']) ? $options['prod_login_id'] : '';
    $prod_login_passwd = isset($options['prod_login_passwd']) ? $options['prod_login_passwd'] : '';
    $test_login_id = isset($options['test_login_id']) ? $options['test_login_id'] : '';
    $test_login_passwd = isset($options['test_login_passwd']) ? $options['test_login_passwd'] : '';
    $environment = isset($options['environment']) ? $options['environment'] : 'test';

    // Afficher les champs de saisie
    ?>
    Identifiant de connexion (Production):<br>
    <input type="text" name="wp_crossref_doi_options[prod_login_id]" value="<?php echo esc_attr($prod_login_id); ?>"><br>
    
    Mot de passe (Production):<br>
    <input type="password" name="wp_crossref_doi_options[prod_login_passwd]" value="<?php echo esc_attr($prod_login_passwd); ?>"><br>
    
    Identifiant de connexion (Test):<br>
    <input type="text" name="wp_crossref_doi_options[test_login_id]" value="<?php echo esc_attr($test_login_id); ?>"><br>
    
    Mot de passe (Test):<br>
    <input type="password" name="wp_crossref_doi_options[test_login_passwd]" value="<?php echo esc_attr($test_login_passwd); ?>"><br>
    
    Environnement:<br>
    <select name="wp_crossref_doi_options[environment]">
        <option value="test" <?php selected($environment, 'test'); ?>>Test</option>
        <option value="production" <?php selected($environment, 'production'); ?>>Production</option>
    </select>
    
    <?php
    // Ajouter un bouton "Enregistrer"
    submit_button();
}


function wp_crossref_doi_login_id_prod_render() {
    $options = get_option('wp_crossref_doi_settings');
    ?>
    <input type='text' name='wp_crossref_doi_settings[login_id_prod]' value='<?php echo $options['login_id_prod']; ?>'>
    <?php
}

function wp_crossref_doi_login_passwd_prod_render() {
    $options = get_option('wp_crossref_doi_settings');
    ?>
    <input type='password' name='wp_crossref_doi_settings[login_passwd_prod]' value='<?php echo $options['login_passwd_prod']; ?>'>
    <?php
}

function wp_crossref_doi_login_id_test_render() {
    $options = get_option('wp_crossref_doi_settings');
    ?>
    <input type='text' name='wp_crossref_doi_settings[login_id_test]' value='<?php echo $options['login_id_test']; ?>'>
    <?php
}

function wp_crossref_doi_login_passwd_test_render() {
    $options = get_option('wp_crossref_doi_settings');
    ?>
    <input type='password' name='wp_crossref_doi_settings[login_passwd_test]' value='<?php echo $options['login_passwd_test']; ?>'>
    <?php
}

function wp_crossref_doi_environment_render() {
    $options = get_option('wp_crossref_doi_settings');
    ?>
    <select name='wp_crossref_doi_settings[environment]'>
        <option value='production' <?php selected($options['environment'], 'production'); ?>><?php _e('Production', 'wp-crossref-doi'); ?></option>
        <option value='test' <?php selected($options['environment'], 'test'); ?>><?php _e('Test', 'wp-crossref-doi'); ?></option>
    </select>
    <?php
}

// wp_crossref_doi_add_button(): Ajoute le bouton 'DOI' dans l'écran 'Posts'.
function wp_crossref_doi_add_button($actions, $post) {
    if ('publish' == $post->post_status && current_user_can('edit_post', $post->ID)) {
        $actions['crossref_doi'] = sprintf(
            '<a href="%s" aria-label="%s">%s</a>',
            admin_url('admin.php?page=crossref-doi-metadata&amp;post_id=' . $post->ID),
            esc_attr__('Générer DOI', 'wp-crossref-doi'),
            esc_html__('DOI', 'wp-crossref-doi')
        );
    }

    return $actions;
}
add_filter('post_row_actions', 'wp_crossref_doi_add_button', 10, 2);

// wp_crossref_doi_generate(): Génère un DOI unique et l'enregistre dans les "Custom Fields" de l'article.
function wp_crossref_doi_generate($post_id) {
    // Vérifier si un DOI existe déjà
    $existing_doi = get_post_meta($post_id, 'DOI', true);

    if (empty($existing_doi)) {
        // Générer un DOI unique
        $doi_prefix = '10.1234'; // Remplacez par votre préfixe DOI fourni par CrossRef
        $doi_suffix = uniqid();
        $doi = $doi_prefix . '/' . $doi_suffix;

        // Enregistrer le DOI dans les Custom Fields de l'article
        update_post_meta($post_id, 'DOI', $doi);
    }
}

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
function wp_crossref_doi_create_xml($post_id, $metadata) {
    // Récupérer les informations de l'article
    $post = get_post($post_id);
    $doi = get_post_meta($post_id, 'DOI', true);

    // Créer le fichier XML
    $xml = new SimpleXMLElement('<root/>');

    // Ajouter les namespaces
    $xml->addAttribute('xmlns', 'http://www.crossref.org/schema/5.3.0');
    $xml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $xml->addAttribute('xsi:schemaLocation', 'http://www.crossref.org/schema/5.3.0 http://www.crossref.org/schema/deposit/crossref5.3.0.xsd');

    // Ajouter les informations du déposant
    $depositor = $xml->addChild('depositor');
    $depositor->addChild('depositor_name', $metadata['depositor_name']);
    $depositor->addChild('email_address', $metadata['email_address']);

    // Ajouter les informations sur la publication
    $publication = $xml->addChild('publication');
    $publication->addChild('doi_batch_id', $doi);

    // Ajouter les informations sur les actes de conférence
    $proceedings = $publication->addChild('proceedings');
    $proceedings->addChild('doi', $doi);
    $proceedings->addChild('resource', get_permalink($post_id));

    // Ajouter les informations sur le document
    $paper = $proceedings->addChild('conference_paper');
    $paper->addChild('doi', $doi);
    $paper->addChild('resource', get_permalink($post_id));
    $paper->addChild('title', $post->post_title);

    // Ajouter les auteurs
    $authors = $paper->addChild('contributors');
    $author_list = explode(',', $metadata['authors']);
    $author_sequence = 1;
    foreach ($author_list as $author_name) {
        $author_name = trim($author_name);
        $name_parts = explode(' ', $author_name);
        $given_name = array_shift($name_parts);
        $surname = implode(' ', $name_parts);
        
        $author_node = $authors->addChild('person_name');
        $author_node->addAttribute('sequence', 'first');
        $author_node->addAttribute('contributor_role', 'author');
        $author_node->addChild('given_name', $given_name);
        $author_node->addChild('surname', $surname);
        
        $author_sequence++;
    }

    // Enregistrer le fichier XML
    $xml_filename = 'crossref_' . $doi . '.xml';
    $xml->asXML($xml_filename);

    return $xml_filename;
}

// wp_crossref_doi_validate_xml(): Valide le fichier XML en utilisant les outils de test de CrossRef.
function wp_crossref_doi_validate_xml($xml) {
    $url = 'https://api.crossref.org/tools/xmlvalidate';

    // Initialiser cURL
    $ch = curl_init($url);

    // Configurer cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));

    // Exécuter cURL et récupérer la réponse
    $response = curl_exec($ch);

    // Fermer cURL
    curl_close($ch);

    // Traiter la réponse
    $xml_response = simplexml_load_string($response);
    $validation_status = $xml_response->status;

    if ($validation_status == 'valid') {
        return true;
    } else {
        return false;
    }
}

// wp_crossref_doi_submit_xml(): Soumet le fichier XML validé à l'API CrossRef en utilisant HTTPS POST.
function wp_crossref_doi_submit_xml($xml, $mode = 'test') {
    // Choisir l'URL appropriée en fonction du mode
    if ($mode == 'production') {
        $url = 'https://doi.crossref.org/servlet/deposit';
    } else {
        $url = 'https://test.crossref.org/servlet/deposit';
    }

    // Récupérer les paramètres de l'utilisateur
    $options = get_option('wp_crossref_doi_options');
    $login_id = $options[$mode . '_login_id'];
    $login_passwd = $options[$mode . '_login_passwd'];

    // Initialiser cURL
    $ch = curl_init($url);

    // Configurer cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'operation' => 'doMDUpload',
        'login_id' => $login_id,
        'login_passwd' => $login_passwd,
        'fname' => new CURLFile($xml, 'application/xml', 'filename.xml')
    ));

    // Exécuter cURL et récupérer la réponse
    $response = curl_exec($ch);

    // Fermer cURL
    curl_close($ch);

    // Retourner la réponse
    return $response;
}

// wp_crossref_doi_handle_errors(): Gère les erreurs potentielles lors de l'interaction avec l'API CrossRef et informe l'utilisateur.
function wp_crossref_doi_handle_errors($response) {
    if (empty($response)) {
        return array(
            'error' => true,
            'message' => __("Erreur : aucune réponse de l'API CrossRef.", 'wp-crossref-doi')
        );
    }

    $xml_response = simplexml_load_string($response);
    $status = $xml_response->status;

    if ($status == 'error') {
        $error_message = __("Erreur lors de l'interaction avec l'API CrossRef :", 'wp-crossref-doi');

        foreach ($xml_response->messages->message as $message) {
            $error_message .= "\n" . (string)$message;
        }

        return array(
            'error' => true,
            'message' => $error_message
        );
    } else {
        return array(
            'error' => false,
            'message' => __("Aucune erreur détectée.", 'wp-crossref-doi')
        );
    }
}

// wp_crossref_doi_display_response(): Traite et affiche les réponses de l'API CrossRef à l'utilisateur.
function wp_crossref_doi_display_response($response) {
    $xml_response = simplexml_load_string($response);
    $status = $xml_response->status;

    if ($status == 'success') {
        $message = __("Soumission réussie. L'API CrossRef a retourné le message suivant : ", 'wp-crossref-doi');

        foreach ($xml_response->messages->message as $msg) {
            $message .= "\n" . (string)$msg;
        }

        return array(
            'success' => true,
            'message' => $message
        );
    } else {
        $error_message = __("Erreur lors de l'interaction avec l'API CrossRef :", 'wp-crossref-doi');

        foreach ($xml_response->messages->message as $msg) {
            $error_message .= "\n" . (string)$msg;
        }

        return array(
            'success' => false,
            'message' => $error_message
        );
    }
}

/**
 * Enqueue admin scripts for the plugin.
 *
 * This function enqueues the necessary JavaScript file(s) for the plugin's
 * admin functionality. It only enqueues the script(s) on the post editing
 * and post creation pages in the WordPress admin area.
 *
 * @param string $hook The current admin page hook.
 */
function wp_crossref_doi_enqueue_admin_scripts($hook) {
    if ('post.php' !== $hook && 'post-new.php' !== $hook) {
        return;
    }

    wp_register_script('wp-crossref-doi-admin-script', plugins_url('js/admin-script.js', __FILE__), array('jquery'), '1.0.0', true);
    wp_enqueue_script('wp-crossref-doi-admin-script');
}


add_action('admin_enqueue_scripts', 'wp_crossref_doi_enqueue_admin_scripts');


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



