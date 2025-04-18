// public/js/commentaires.js

// Affiche un message temporaire dans la zone prévue (#message-commentaire)
// Utile pour signaler un succès, une erreur ou une information suite à une action utilisateur (ajout, suppression, etc.)
function afficherMessage(message, type = "info") {
  const msgDiv = document.getElementById("message-commentaire"); // Je récupère la div dédiée à l’affichage des messages
  if (!msgDiv) return; // Si elle n’existe pas dans le DOM, je ne fais rien (évite une erreur JS)

  // Je définis dynamiquement la classe CSS en fonction du type de message
  // Cela permet de colorer différemment les messages selon leur nature (info, success, error)
  msgDiv.className = `message-flash message-${type}`;
  msgDiv.textContent = message;
  msgDiv.style.opacity = "1";       // Message totalement visible
  msgDiv.style.display = "block";   // Je rends la div visible

  // Je nettoie d’éventuels timers précédents (utile si l’utilisateur déclenche plusieurs actions de suite)
  if (msgDiv._timeoutId) clearTimeout(msgDiv._timeoutId);
  if (msgDiv._fadeId) clearTimeout(msgDiv._fadeId);

  // J’attends 4 secondes avant de commencer la disparition du message
  msgDiv._timeoutId = setTimeout(() => {
    msgDiv.style.transition = "opacity 0.5s ease"; // Je définis une transition douce
    msgDiv.style.opacity = "0";                    // Le message commence à disparaître (fondu)

    // Une fois le fondu terminé, je nettoie totalement la zone pour la rendre prête à réutiliser
    msgDiv._fadeId = setTimeout(() => {
      msgDiv.style.display = "none";
      msgDiv.textContent = "";
      msgDiv.className = "";
    }, 500); // Attente égale à la durée du fondu
  }, 4000);
}

// Fonction de sécurité pour empêcher toute injection de code HTML ou JS
// Je transforme les caractères spéciaux en entités HTML (ex : < devient &lt;)
// Cela neutralise toute tentative d’injection XSS dans les noms, contenus, etc.
function escapeHtml(unsafe) {
  return unsafe
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

// Fonction qui ajoute dynamiquement un commentaire dans la page (DOM)
// Elle est utilisée après récupération des commentaires ou après ajout en AJAX
function renderCommentaire(commentaire) {
  // Pour éviter les doublons, je vérifie si un commentaire avec cet ID est déjà affiché
  if (document.querySelector(`.commentaire[data-id="${commentaire.id_commentaire}"]`)) return;

  const div = document.createElement('div');        // Je crée un nouveau bloc pour le commentaire
  div.className = 'commentaire';
  div.dataset.id = commentaire.id_commentaire;      // Je stocke son identifiant dans un attribut data

  // Je précise dans un attribut data si ce commentaire est modifiable/supprimable
  // L’utilisateur connecté peut modifier uniquement les siens, sauf s’il est admin
  div.dataset.supprimable = (userId == commentaire.id_utilisateur || userRole === 'admin') ? "true" : "false";

  // Je construis le contenu HTML sécurisé (auteur, date et contenu échappé)
  div.innerHTML = `
    <p><strong>${escapeHtml(commentaire.auteur)}</strong> — ${commentaire.date_redaction || commentaire.date}</p>
    <p class="contenu-commentaire">${escapeHtml(commentaire.contenu)}</p>
  `;

  // Si le commentaire peut être modifié ou supprimé, j’ajoute les boutons correspondants
  if (div.dataset.supprimable === "true") {
    ajouterBoutonSuppression(div, commentaire.id_commentaire);
    ajouterBoutonModification(div, commentaire.id_commentaire);
  }

  // Enfin, je l’ajoute à la zone visible des commentaires
  document.getElementById('commentaires-liste').appendChild(div);
}

// Fonction qui ajoute un bouton de suppression à un commentaire
// Ce bouton ouvre une modale de confirmation, puis supprime le commentaire via AJAX
function ajouterBoutonSuppression(divCommentaire, id_commentaire) {
  const boutonSupprimer = document.createElement('button');
  boutonSupprimer.textContent = 'Supprimer';
  boutonSupprimer.className = 'btn-supprimer';

  // Je déclenche une modale personnalisée au clic
  boutonSupprimer.addEventListener('click', () => {
    const modal = document.getElementById("commentaire-confirm-modal");                 // Fenêtre modale
    const confirmBtn = document.getElementById("confirm-commentaire-suppression");     // Bouton "Oui, supprimer"
    const cancelBtn = document.getElementById("cancel-commentaire-suppression");       // Bouton "Non, annuler"

    // Si un des éléments de la modale est manquant, j’affiche une erreur
    if (!modal || !confirmBtn || !cancelBtn) {
      console.error("Modale de suppression commentaire introuvable.");
      return;
    }

    modal.style.display = "flex"; // J’affiche la modale

    // Si l’utilisateur clique sur "Confirmer", j’envoie la requête de suppression à l’API
    const onConfirm = () => {
      fetch(`${BASE_URL}/public/api/commentaires.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'delete', id_commentaire })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            divCommentaire.remove(); // Je retire le commentaire du DOM
            afficherMessage("Commentaire supprimé", "success");
          } else {
            afficherMessage(data.error || "Échec de la suppression", "error");
          }
        })
        .catch(() => afficherMessage("Erreur de connexion au serveur", "error"))
        .finally(() => {
          modal.style.display = "none"; // Je ferme la modale dans tous les cas
          confirmBtn.removeEventListener("click", onConfirm); // Nettoyage des événements
          cancelBtn.removeEventListener("click", onCancel);
        });
    };

    // Si l’utilisateur clique sur "Annuler", je ferme la modale sans rien faire
    const onCancel = () => {
      modal.style.display = "none";
      confirmBtn.removeEventListener("click", onConfirm);
      cancelBtn.removeEventListener("click", onCancel);
    };

    // Je connecte les deux actions aux bons boutons
    confirmBtn.addEventListener("click", onConfirm);
    cancelBtn.addEventListener("click", onCancel);
  });

  // J’ajoute le bouton dans le DOM (sous le commentaire)
  divCommentaire.appendChild(boutonSupprimer);
}

// Ajoute un bouton de modification en ligne du commentaire
// Cette fonction permet à l’auteur du commentaire (ou à un admin) de modifier un commentaire directement depuis l’interface
function ajouterBoutonModification(divCommentaire, id_commentaire) {
  const boutonModifier = document.createElement('button');
  boutonModifier.textContent = 'Modifier';
  boutonModifier.className = 'btn-modifier';

  boutonModifier.addEventListener('click', () => {
    // Je vérifie si un champ d’édition est déjà présent pour éviter d’en empiler plusieurs
    if (divCommentaire.querySelector('.edit-area')) return;

    // Je récupère l’ancien texte pour l’afficher dans le champ de modification
    const paragraphe = divCommentaire.querySelector('.contenu-commentaire');
    const ancienTexte = paragraphe.textContent;

    // Je crée un champ textarea pour permettre la modification
    const textarea = document.createElement('textarea');
    textarea.className = 'edit-area';
    textarea.value = ancienTexte;

    // Je crée les boutons d’action : valider ou annuler
    const boutonValider = document.createElement('button');
    boutonValider.textContent = 'Valider';

    const boutonAnnuler = document.createElement('button');
    boutonAnnuler.textContent = 'Annuler';

    // Je remplace le paragraphe par le champ textarea et j’affiche les boutons
    paragraphe.replaceWith(textarea);
    boutonModifier.style.display = 'none'; // Je masque le bouton "Modifier" pendant l’édition
    divCommentaire.appendChild(boutonValider);
    divCommentaire.appendChild(boutonAnnuler);

    // Si l’utilisateur clique sur "Valider", j’envoie la mise à jour en AJAX
    boutonValider.addEventListener('click', () => {
      const nouveauContenu = textarea.value.trim();

      if (!nouveauContenu) {
        afficherMessage("Le commentaire ne peut pas être vide", "error");
        return;
      }

      fetch(`${BASE_URL}/public/api/commentaires.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: 'edit',
          id_commentaire,
          nouveau_contenu: nouveauContenu
        })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            // Si tout s’est bien passé, je remplace le champ textarea par un paragraphe mis à jour
            const nouveauP = document.createElement('p');
            nouveauP.className = 'contenu-commentaire';
            nouveauP.textContent = data.nouveau_contenu;

            textarea.replaceWith(nouveauP);
            boutonValider.remove();
            boutonAnnuler.remove();
            boutonModifier.style.display = '';
            afficherMessage("Commentaire modifié !", "success");
          } else {
            afficherMessage(data.error || "Erreur lors de la modification", "error");
          }
        })
        .catch(() => afficherMessage("Erreur serveur lors de la modification", "error"));
    });

    // Si l’utilisateur annule, je restaure l’affichage initial du commentaire
    boutonAnnuler.addEventListener('click', () => {
      const originalP = document.createElement('p');
      originalP.className = 'contenu-commentaire';
      originalP.textContent = ancienTexte;

      textarea.replaceWith(originalP);
      boutonValider.remove();
      boutonAnnuler.remove();
      boutonModifier.style.display = '';
    });
  });

  divCommentaire.appendChild(boutonModifier);
}

// Quand le DOM est prêt, je charge les commentaires existants et j’active le formulaire d’ajout
document.addEventListener('DOMContentLoaded', () => {
  const idArticleInput = document.querySelector('input[name="id_article"]');
  const zoneCommentaires = document.getElementById('commentaires-liste');
  const form = document.getElementById('form-commentaire');

  // Si l’élément caché contenant l’ID de l’article ou la zone d’affichage des commentaires n’existe pas, j’arrête ici
  if (!idArticleInput || !zoneCommentaires) return;

  const id_article = idArticleInput.value;
  zoneCommentaires.innerHTML = ""; // Je vide la zone avant d’afficher les commentaires (propre)

  // Requête AJAX vers l’API pour récupérer les commentaires liés à l’article
  fetch(`${BASE_URL}/public/api/commentaires.php?id_article=${id_article}`)
    .then(res => res.json())
    .then(data => {
      // Pour chaque commentaire reçu, j’appelle la fonction d’affichage
      data.forEach(renderCommentaire);
    })
    .catch(() => afficherMessage("Erreur lors du chargement des commentaires", "error"));

  // Activation du formulaire d’ajout si présent
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault(); // J’empêche l’envoi classique du formulaire

      const contenu = document.getElementById('contenu').value.trim(); // Je récupère le texte saisi

      if (!contenu) {
        afficherMessage("Le commentaire est vide", "error");
        return;
      }

      // Envoi du nouveau commentaire en AJAX vers l’API
      fetch(`${BASE_URL}/public/api/commentaires.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ contenu, id_article })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            renderCommentaire(data); // Je l’ajoute dynamiquement à la liste
            document.getElementById('contenu').value = ''; // Je vide le champ de saisie
            afficherMessage("Commentaire ajouté !", "success");
          } else {
            afficherMessage(data.error || "Une erreur est survenue", "error");
          }
        })
        .catch(() => afficherMessage("Erreur de connexion au serveur", "error"));
    });
  }
});
