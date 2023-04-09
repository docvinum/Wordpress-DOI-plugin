// admin-script.js

// Les fonctionnalités JavaScript spécifiques à l'administration du plugin seront ajoutées ici.

document.addEventListener('DOMContentLoaded', function () {
    // Sélectionner tous les champs de texte et de mot de passe, ainsi que le bouton Enregistrer.
    const inputs = document.querySelectorAll('input[type="text"], input[type="password"], select');
    const saveButton = document.querySelector('input[type="submit"]');

    // Ajouter un écouteur d'événement pour chaque champ d'entrée.
    inputs.forEach(function (input) {
        input.addEventListener('input', function () {
            // Si un champ d'entrée est modifié, activer le bouton Enregistrer et le mettre en bleu.
            saveButton.disabled = false;
            saveButton.style.backgroundColor = '#007cba';
        });
    });

    // Désactiver et rendre vert le bouton Enregistrer si les modifications sont enregistrées avec succès.
    if (typeof window.history.replaceState === 'function') {
        window.history.replaceState({}, '', '?settings-updated=true');
        saveButton.disabled = true;
        saveButton.style.backgroundColor = '#34a853';
    }
});


jQuery(document).ready(function($) {
    $(document).on('click', '.wp-crossref-doi-button', function(e) {
        e.preventDefault();

        var postId = $(this).data('post-id');

        var $dialog = $('<div></div>').html('<iframe style="border: 0;" src="about:blank" width="100%" height="100%"></iframe>').dialog({
            autoOpen: false,
            modal: true,
            height: 625,
            width: 500,
            title: 'Crossref DOI',
            close: function() {
                $dialog.dialog('destroy').remove();
            }
        });

        var targetUrl = wp_crossref_doi_vars.wp_crossref_doi_ajaxurl + '?action=wp_crossref_doi_edit_page&post_id=' + postId + '&_wpnonce=' + wp_crossref_doi_vars.wp_crossref_doi_nonce;

        $dialog.find('iframe').attr('src', targetUrl);
        $dialog.dialog('open');
    });
});
