// public/js/verifierUpload.js

// Script de validation de taille d'image lors de l'ajout ou de la modification d’un article ou d’une œuvre
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#form-ajout-modif');         // Formulaire d’ajout ou de modification
    const fileInput = document.querySelector('input[type="file"]');  // Champ d’upload de fichier
    const divErreur = document.getElementById('erreur-upload');       // Zone d’affichage de l’erreur
  
    // Si l’un des éléments nécessaires est absent, on interrompt le script
    if (!form || !fileInput || !divErreur) return;
  
    let timeoutId = null; // Permet de garder une référence au timer de disparition automatique
  
    form.addEventListener('submit', (e) => {
        const file = fileInput.files[0]; // Je récupère le fichier sélectionné, s’il existe
  
        // Je masque toute erreur précédente
        divErreur.style.display = 'none';
        divErreur.style.opacity = '1';
  
        if (file) {
            const maxSize = 2 * 1024 * 1024; // Taille limite en octets (2 Mo)
  
            // Si le fichier dépasse la taille maximale autorisée
            if (file.size > maxSize) {
                const mo = (file.size / (1024 * 1024)).toFixed(2); // Je calcule la taille en Mo avec 2 décimales
                divErreur.textContent = `L'image fait ${mo} Mo. Taille maximale autorisée : 2 Mo.`;
                divErreur.style.display = 'block';
                divErreur.style.opacity = '1';
                divErreur.style.transition = 'opacity 0.5s ease';
  
                // Si un timer de disparition automatique était en cours, je le supprime pour éviter un bug visuel
                if (timeoutId) clearTimeout(timeoutId);
  
                // Je programme une disparition progressive du message après 5 secondes
                timeoutId = setTimeout(() => {
                    divErreur.style.opacity = '0';
                    setTimeout(() => {
                        divErreur.style.display = 'none';
                    }, 500); // Correspond à la durée du fondu
                }, 5000);
  
                e.preventDefault(); // Je bloque l’envoi du formulaire
            }
        }
    });
  });
  