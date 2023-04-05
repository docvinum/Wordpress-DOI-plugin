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
