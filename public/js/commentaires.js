// Fonction utilitaire pour afficher un message dans la zone prévue
function afficherMessage(message, type = "info") {
  const msgDiv = document.getElementById("message-commentaire");
  if (!msgDiv) return;

  msgDiv.textContent = message;
  msgDiv.className = `message-flash ${type}`;

  setTimeout(() => {
    msgDiv.textContent = "";
    msgDiv.className = "message-flash";
  }, 5000);
}

// Sécurité contre le XSS
function escapeHtml(unsafe) {
  return unsafe
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

// Ajoute un commentaire dans le DOM
function renderCommentaire(commentaire) {
  // Vérifie s'il existe déjà
  if (document.querySelector(`.commentaire[data-id="${commentaire.id_commentaire}"]`)) return;

  const div = document.createElement('div');
  div.className = 'commentaire';
  div.dataset.id = commentaire.id_commentaire;
  div.dataset.supprimable = (userId == commentaire.id_utilisateur || userRole === 'admin') ? "true" : "false";

  div.innerHTML = `
    <p><strong>${escapeHtml(commentaire.auteur)}</strong> — ${commentaire.date_redaction || commentaire.date}</p>
    <p class="contenu-commentaire">${escapeHtml(commentaire.contenu)}</p>
  `;

  if (div.dataset.supprimable === "true") {
    ajouterBoutonSuppression(div, commentaire.id_commentaire);
    ajouterBoutonModification(div, commentaire.id_commentaire);
  }

  document.getElementById('commentaires-liste').appendChild(div);
}

// Supprimer un commentaire
function ajouterBoutonSuppression(divCommentaire, id_commentaire) {
  const boutonSupprimer = document.createElement('button');
  boutonSupprimer.textContent = 'Supprimer';
  boutonSupprimer.className = 'btn-supprimer';

  // ⚠️ Modal custom
  boutonSupprimer.addEventListener('click', () => {
    const modal = document.getElementById("commentaire-confirm-modal");
    const confirmBtn = document.getElementById("confirm-commentaire-suppression");
    const cancelBtn = document.getElementById("cancel-commentaire-suppression");

    if (!modal || !confirmBtn || !cancelBtn) {
      console.error("Modale de suppression commentaire introuvable.");
      return;
    }

    modal.style.display = "flex";

    const onConfirm = () => {
      fetch(`${BASE_URL}/public/api/commentaires.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'delete', id_commentaire })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            divCommentaire.remove();
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

    const onCancel = () => {
      modal.style.display = "none";
      confirmBtn.removeEventListener("click", onConfirm);
      cancelBtn.removeEventListener("click", onCancel);
    };

    confirmBtn.addEventListener("click", onConfirm);
    cancelBtn.addEventListener("click", onCancel);
  });

  divCommentaire.appendChild(boutonSupprimer);
}

// Modifier un commentaire
function ajouterBoutonModification(divCommentaire, id_commentaire) {
  const boutonModifier = document.createElement('button');
  boutonModifier.textContent = 'Modifier';
  boutonModifier.className = 'btn-modifier';

  boutonModifier.addEventListener('click', () => {
    if (divCommentaire.querySelector('.edit-area')) return;

    const paragraphe = divCommentaire.querySelector('.contenu-commentaire');
    const ancienTexte = paragraphe.textContent;

    const textarea = document.createElement('textarea');
    textarea.className = 'edit-area';
    textarea.value = ancienTexte;

    const boutonValider = document.createElement('button');
    boutonValider.textContent = 'Valider';
    const boutonAnnuler = document.createElement('button');
    boutonAnnuler.textContent = 'Annuler';

    paragraphe.replaceWith(textarea);
    boutonModifier.style.display = 'none';
    divCommentaire.appendChild(boutonValider);
    divCommentaire.appendChild(boutonAnnuler);

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

// DOM Ready
document.addEventListener('DOMContentLoaded', () => {
  const idArticleInput = document.querySelector('input[name="id_article"]');
  const zoneCommentaires = document.getElementById('commentaires-liste');
  const form = document.getElementById('form-commentaire');

  if (!idArticleInput || !zoneCommentaires) return;

  const id_article = idArticleInput.value;

  // Nettoie les anciens commentaires pour éviter les doublons
  zoneCommentaires.innerHTML = "";

  // Chargement initial
  fetch(`${BASE_URL}/public/api/commentaires.php?id_article=${id_article}`)
    .then(res => res.json())
    .then(data => {
      data.forEach(renderCommentaire);
    })
    .catch(() => afficherMessage("Erreur lors du chargement des commentaires", "error"));

  // Envoi d’un nouveau commentaire
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      const contenu = document.getElementById('contenu').value.trim();
      if (!contenu) {
        afficherMessage("Le commentaire est vide", "error");
        return;
      }

      fetch(`${BASE_URL}/public/api/commentaires.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ contenu, id_article })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            renderCommentaire(data);
            document.getElementById('contenu').value = '';
            afficherMessage("Commentaire ajouté !", "success");
          } else {
            afficherMessage(data.error || "Une erreur est survenue", "error");
          }
        })
        .catch(() => afficherMessage("Erreur de connexion au serveur", "error"));
    });
  }
});
