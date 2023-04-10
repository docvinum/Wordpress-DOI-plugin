jQuery(document).ready(function ($) {
    var doiDialog = $('<div id="wp_crossref_doi_dialog"></div>').appendTo('body').dialog({
        autoOpen: false,
        modal: true,
        title: 'Générer un DOI',
        buttons: {
            "Générer DOI": function () {
                generateDOI();
            },
            "Annuler": function () {
                $(this).dialog("close");
            }
        }
    });

    function generateDOI() {
        var data = {
            action: 'wp_crossref_doi_generate_ajax',
            post_id: $('#post_ID').val(),
            nonce: wp_crossref_doi_vars.wp_crossref_doi_nonce
        };

        $.post(wp_crossref_doi_vars.wp_crossref_doi_ajaxurl, data, function (response) {
            if (response.success) {
                $('#wp_crossref_doi').val(response.data.doi);
                doiDialog.dialog('close');
            } else {
                alert(response.data.message);
            }
        });
    }

    $('.wp_crossref_doi_generate').on('click', function (e) {
        e.preventDefault();
        doiDialog.dialog('open');
    });
});
