// Fonction utilitaire pour afficher un message dans la zone prévue
function afficherMessage(message, type = "info") {
  const msgDiv = document.getElementById("message-commentaire");
  if (!msgDiv) return;

  msgDiv.textContent = message;
  msgDiv.className = `message-flash ${type}`;

  // Le message disparaît automatiquement après 5 secondes
  setTimeout(() => {
    msgDiv.textContent = "";
    msgDiv.className = "message-flash";
  }, 5000);
}

// Petite fonction de sécurité pour éviter l’exécution de code HTML (XSS)
function escapeHtml(unsafe) {
  return unsafe
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

// Fonction pour ajouter un bouton de suppression à un commentaire
function ajouterBoutonSuppression(divCommentaire, id_commentaire) {
  const boutonSupprimer = document.createElement('button');
  boutonSupprimer.textContent = 'Supprimer';
  boutonSupprimer.className = 'btn-supprimer';
  boutonSupprimer.title = 'Supprimer ce commentaire';

  boutonSupprimer.addEventListener('click', () => {
    if (!confirm("Supprimer ce commentaire ?")) return;

    fetch('/cine-hurlant/public/api/commentaires.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        action: 'delete',
        id_commentaire: id_commentaire
      })
    })
      .then(response => response.json())
      .then(data => {
        console.log("Réponse API (suppression) :", data); // 📌 LOG suppression
        if (data.success) {
          divCommentaire.remove();
          afficherMessage("Commentaire supprimé", "success");
        } else {
          afficherMessage(data.error || "Échec de la suppression", "error");
        }
      })
      .catch(error => {
        console.error("Erreur AJAX suppression :", error);
        afficherMessage("Erreur de connexion au serveur", "error");
      });
  });

  divCommentaire.appendChild(boutonSupprimer);
}

// Fonction pour ajouter un bouton de modification à un commentaire
function ajouterBoutonModification(divCommentaire, id_commentaire) {
  const boutonModifier = document.createElement('button');
  boutonModifier.textContent = 'Modifier';
  boutonModifier.className = 'btn-modifier';
  boutonModifier.title = 'Modifier ce commentaire';

  boutonModifier.addEventListener('click', () => {
    // Empêcher d’avoir plusieurs zones d’édition
    if (divCommentaire.querySelector('.edit-area')) return;

    // Récupérer le texte actuel
    const paragraphe = divCommentaire.querySelector('.contenu-commentaire');
    const ancienTexte = paragraphe.textContent;

    // Remplacer par un textarea + boutons
    const textarea = document.createElement('textarea');
    textarea.className = 'edit-area';
    textarea.value = ancienTexte;

    const boutonValider = document.createElement('button');
    boutonValider.textContent = 'Valider';
    boutonValider.className = 'btn-valider';

    const boutonAnnuler = document.createElement('button');
    boutonAnnuler.textContent = 'Annuler';
    boutonAnnuler.className = 'btn-annuler';

    // Remplacement dans le DOM
    paragraphe.replaceWith(textarea);
    boutonModifier.style.display = 'none'; // cacher pendant édition
    divCommentaire.appendChild(boutonValider);
    divCommentaire.appendChild(boutonAnnuler);

    boutonValider.addEventListener('click', () => {
      const nouveauContenu = textarea.value.trim();
      if (!nouveauContenu) {
        afficherMessage("Le commentaire ne peut pas être vide", "error");
        return;
      }

      fetch('/cine-hurlant/public/api/commentaires.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          action: 'edit',
          id_commentaire: id_commentaire,
          nouveau_contenu: nouveauContenu
        })
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            afficherMessage("Commentaire modifié !", "success");

            // Mettre à jour le DOM
            const nouveauParagraphe = document.createElement('p');
            nouveauParagraphe.className = 'contenu-commentaire';
            nouveauParagraphe.textContent = data.nouveau_contenu;

            textarea.replaceWith(nouveauParagraphe);
            boutonValider.remove();
            boutonAnnuler.remove();
            boutonModifier.style.display = ''; // on réaffiche
          } else {
            afficherMessage(data.error || "Erreur lors de la modification", "error");
          }
        })
        .catch(error => {
          console.error("Erreur AJAX édition :", error);
          afficherMessage("Erreur serveur lors de la modification", "error");
        });
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

// Je m’assure que le DOM est chargé avant d’agir
document.addEventListener('DOMContentLoaded', () => {

  // Ajout du bouton supprimer et modifier à tous les commentaires existants s’ils sont supprimables
  document.querySelectorAll('.commentaire').forEach(div => {
    const id = div.dataset.id;
    const supprimable = div.dataset.supprimable === "true";
    if (id && supprimable) {
      ajouterBoutonSuppression(div, id);
      ajouterBoutonModification(div, id);
    }
  });

  // Chargement AJAX des commentaires existants
const id_article = document.querySelector('input[name="id_article"]').value;

// ⚠️ userId et userRole doivent être fournis via PHP dans un script en bas de la page
fetch(`/cine-hurlant/public/api/commentaires.php?id_article=${id_article}`)
  .then(response => response.json())
  .then(data => {
    const liste = document.getElementById('commentaires-liste');
    data.forEach(com => {
      const div = document.createElement('div');
      div.className = 'commentaire';
      div.dataset.id = com.id_commentaire;
      div.dataset.supprimable = (userId == com.id_utilisateur || userRole === 'admin') ? "true" : "false";

      div.innerHTML = `
        <p><strong>${escapeHtml(com.auteur)}</strong> — ${com.date_redaction}</p>
        <p class="contenu-commentaire">${escapeHtml(com.contenu)}</p>
      `;

      liste.appendChild(div);

      if (div.dataset.supprimable === "true") {
        ajouterBoutonSuppression(div, com.id_commentaire);
        ajouterBoutonModification(div, com.id_commentaire);
      }
    });
  })
  .catch(error => {
    console.error("Erreur chargement des commentaires :", error);
    afficherMessage("Erreur lors du chargement des commentaires", "error");
  });

  
  // Je cible le formulaire d’ajout de commentaire
  const form = document.getElementById('form-commentaire');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault(); // J’empêche le rechargement de la page

    const contenu = document.getElementById('contenu').value.trim();
    const id_article = form.querySelector('input[name="id_article"]').value;

    // 🔍 LOG des données envoyées
    console.log("Envoi des données :", {
      contenu: contenu,
      id_article: id_article
    });

    if (!contenu) {
      afficherMessage("Le commentaire est vide", "error");
      return;
    }

    fetch('/cine-hurlant/public/api/commentaires.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        contenu: contenu,
        id_article: id_article
      })
    })
      .then(response => response.json())
      .then(data => {
        // 🔍 LOG de la réponse API
        console.log("Réponse API (ajout) :", data);

        if (data.success) {
          afficherMessage("Commentaire ajouté !", "success");

          const commentaireDiv = document.createElement('div');
          commentaireDiv.className = 'commentaire';
          commentaireDiv.dataset.id = data.id_commentaire;
          commentaireDiv.dataset.supprimable = "true"; // Le créateur peut toujours supprimer

          commentaireDiv.innerHTML = `
            <p><strong>${data.auteur}</strong> — ${data.date}</p>
            <p class="contenu-commentaire">${escapeHtml(data.contenu)}</p>
          `;

          document.getElementById('commentaires-liste').appendChild(commentaireDiv);
          ajouterBoutonSuppression(commentaireDiv, data.id_commentaire);
          ajouterBoutonModification(commentaireDiv, data.id_commentaire);
          document.getElementById('contenu').value = '';
        } else {
          afficherMessage(data.error || "Une erreur est survenue", "error");
        }
      })
      .catch(error => {
        console.error("Erreur AJAX :", error);
        afficherMessage("Erreur de connexion au serveur", "error");
      });
  });

});
