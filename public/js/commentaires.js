// public/js/commentaires.js

// Fonction utilitaire pour afficher un message temporaire dans l’interface
function afficherMessage(message, type = "info") {
  const msgDiv = document.getElementById("message-commentaire"); // Je cible le conteneur du message
  if (!msgDiv) return;

  // Je définis une classe dynamique selon le type de message (success, error, etc.)
  msgDiv.className = `message-flash message-${type}`;
  msgDiv.textContent = message;
  msgDiv.style.opacity = "1";
  msgDiv.style.display = "block";

  // Je nettoie les anciens timers si le message précédent n'était pas encore effacé
  if (msgDiv._timeoutId) clearTimeout(msgDiv._timeoutId);
  if (msgDiv._fadeId) clearTimeout(msgDiv._fadeId);

  // Je fais disparaître progressivement le message après 4 secondes
  msgDiv._timeoutId = setTimeout(() => {
    msgDiv.style.transition = "opacity 0.5s ease";
    msgDiv.style.opacity = "0";

    // Après l’animation, je cache complètement le bloc
    msgDiv._fadeId = setTimeout(() => {
      msgDiv.style.display = "none";
      msgDiv.textContent = "";
      msgDiv.className = "";
    }, 500);
  }, 4000);
}

// Fonction de sécurité pour échapper les caractères HTML dans les contenus utilisateurs
function escapeHtml(unsafe) {
  return unsafe
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

// Fonction d’injection d’un commentaire dans le DOM
function renderCommentaire(commentaire) {
  // Je vérifie que ce commentaire n’est pas déjà affiché (évite les doublons)
  if (document.querySelector(`.commentaire[data-id="${commentaire.id_commentaire}"]`)) return;

  const div = document.createElement("div");
  div.className = "commentaire";
  div.dataset.id = commentaire.id_commentaire;

  // Je précise si le commentaire peut être modifié ou supprimé (selon l’auteur ou rôle admin)
  div.dataset.supprimable =
    userId == commentaire.id_utilisateur || userRole === "admin" ? "true" : "false";

  // Je construis le contenu HTML du commentaire en sécurisant les données
  div.innerHTML = `
    <p><strong>${escapeHtml(commentaire.auteur)}</strong> — ${commentaire.date_redaction || commentaire.date}</p>
    <p class="contenu-commentaire">${escapeHtml(commentaire.contenu)}</p>
  `;

  // Si l’utilisateur est autorisé, j’ajoute les boutons de modification et suppression
  if (div.dataset.supprimable === "true") {
    const actionsDiv = document.createElement("div");
    actionsDiv.className = "actions-commentaire";

    actionsDiv.appendChild(creerBoutonModification(div, commentaire.id_commentaire));
    actionsDiv.appendChild(creerBoutonSuppression(div, commentaire.id_commentaire));

    div.appendChild(actionsDiv);
  }

  // Je l’ajoute enfin dans la liste des commentaires
  document.getElementById("commentaires-liste").appendChild(div);
}

// Création d’un bouton de suppression avec modale de confirmation
function creerBoutonSuppression(divCommentaire, id_commentaire) {
  const boutonSupprimer = document.createElement("button");
  boutonSupprimer.textContent = "Supprimer";
  boutonSupprimer.className = "btn-supprimer";

  boutonSupprimer.addEventListener("click", () => {
    const modal = document.getElementById("commentaire-confirm-modal");
    const confirmBtn = document.getElementById("confirm-commentaire-suppression");
    const cancelBtn = document.getElementById("cancel-commentaire-suppression");

    if (!modal || !confirmBtn || !cancelBtn) {
      console.error("Modale de suppression commentaire introuvable.");
      return;
    }

    modal.style.display = "flex";

    // Fonction de suppression réelle si l’utilisateur confirme
    const onConfirm = () => {
      fetch(`${BASE_URL}/public/api/commentaires.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "delete", id_commentaire }),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            divCommentaire.remove(); // Je retire le commentaire du DOM
            afficherMessage("Commentaire supprimé", "success");
          } else {
            afficherMessage(data.error || "Échec de la suppression", "error");
          }
        })
        .catch(() => afficherMessage("Erreur de connexion au serveur", "error"))
        .finally(() => {
          modal.style.display = "none";
          confirmBtn.removeEventListener("click", onConfirm);
          cancelBtn.removeEventListener("click", onCancel);
        });
    };

    // Fonction d’annulation de la suppression
    const onCancel = () => {
      modal.style.display = "none";
      confirmBtn.removeEventListener("click", onConfirm);
      cancelBtn.removeEventListener("click", onCancel);
    };

    confirmBtn.addEventListener("click", onConfirm);
    cancelBtn.addEventListener("click", onCancel);
  });

  return boutonSupprimer;
}

// Création d’un bouton de modification avec textarea et mise à jour AJAX
function creerBoutonModification(divCommentaire, id_commentaire) {
  const boutonModifier = document.createElement("button");
  boutonModifier.textContent = "Modifier";
  boutonModifier.className = "btn-modifier";

  boutonModifier.addEventListener("click", () => {
    // J’empêche plusieurs zones de modification ouvertes en même temps
    if (divCommentaire.querySelector(".edit-area")) return;

    const paragraphe = divCommentaire.querySelector(".contenu-commentaire");
    const ancienTexte = paragraphe.textContent;

    const textarea = document.createElement("textarea");
    textarea.className = "edit-area";
    textarea.value = ancienTexte;

    const boutonValider = document.createElement("button");
    boutonValider.textContent = "Valider";
    boutonValider.className = "btn-valider";

    const boutonAnnuler = document.createElement("button");
    boutonAnnuler.textContent = "Annuler";
    boutonAnnuler.className = "btn-annuler";

    // Je remplace le paragraphe par un textarea
    paragraphe.replaceWith(textarea);
    boutonModifier.style.display = "none";

    const actionsDiv = divCommentaire.querySelector(".actions-commentaire");
    actionsDiv.appendChild(boutonValider);
    actionsDiv.appendChild(boutonAnnuler);

    // Si on valide la modification
    boutonValider.addEventListener("click", () => {
      const nouveauContenu = textarea.value.trim();

      if (!nouveauContenu) {
        afficherMessage("Le commentaire ne peut pas être vide", "error");
        return;
      }

      fetch(`${BASE_URL}/public/api/commentaires.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "edit",
          id_commentaire,
          nouveau_contenu: nouveauContenu,
        }),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            const nouveauP = document.createElement("p");
            nouveauP.className = "contenu-commentaire";
            nouveauP.textContent = data.nouveau_contenu;

            textarea.replaceWith(nouveauP);
            boutonValider.remove();
            boutonAnnuler.remove();
            boutonModifier.style.display = "";
            afficherMessage("Commentaire modifié !", "success");
          } else {
            afficherMessage(data.error || "Erreur lors de la modification", "error");
          }
        })
        .catch(() => afficherMessage("Erreur serveur lors de la modification", "error"));
    });

    // Si on annule la modification
    boutonAnnuler.addEventListener("click", () => {
      const originalP = document.createElement("p");
      originalP.className = "contenu-commentaire";
      originalP.textContent = ancienTexte;

      textarea.replaceWith(originalP);
      boutonValider.remove();
      boutonAnnuler.remove();
      boutonModifier.style.display = "";
    });
  });

  return boutonModifier;
}

// Code principal exécuté après le chargement complet du DOM
document.addEventListener("DOMContentLoaded", () => {
  const idArticleInput = document.querySelector('input[name="id_article"]');
  const zoneCommentaires = document.getElementById("commentaires-liste");
  const form = document.getElementById("form-commentaire");

  if (!idArticleInput || !zoneCommentaires) return;

  const id_article = idArticleInput.value;
  zoneCommentaires.innerHTML = ""; // Je nettoie la zone avant d'afficher

  // Je récupère les commentaires existants via mon API interne
  fetch(`${BASE_URL}/public/api/commentaires.php?id_article=${id_article}`)
    .then((res) => res.json())
    .then((data) => {
      data.forEach(renderCommentaire); // Je les injecte un par un
    })
    .catch(() => afficherMessage("Erreur lors du chargement des commentaires", "error"));

  // Je gère l’envoi du formulaire d’ajout de commentaire
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const contenu = document.getElementById("contenu").value.trim();

      if (!contenu) {
        afficherMessage("Le commentaire est vide", "error");
        return;
      }

      fetch(`${BASE_URL}/public/api/commentaires.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ contenu, id_article }),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            renderCommentaire(data); // J’ajoute le nouveau commentaire
            document.getElementById("contenu").value = "";
            afficherMessage("Commentaire ajouté !", "success");
          } else {
            afficherMessage(data.error || "Une erreur est survenue", "error");
          }
        })
        .catch(() => afficherMessage("Erreur de connexion au serveur", "error"));
    });
  }
});
