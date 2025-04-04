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
  boutonSupprimer.textContent = '🗑️';
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

// Je m’assure que le DOM est chargé avant d’agir
document.addEventListener('DOMContentLoaded', () => {

  // Ajout du bouton 🗑️ à tous les commentaires existants s’ils sont supprimables
  document.querySelectorAll('.commentaire').forEach(div => {
    const id = div.dataset.id;
    const supprimable = div.dataset.supprimable === "true";
    if (id && supprimable) {
      ajouterBoutonSuppression(div, id);
    }
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
            <p>${escapeHtml(data.contenu)}</p>
          `;

          document.getElementById('commentaires-liste').appendChild(commentaireDiv);
          ajouterBoutonSuppression(commentaireDiv, data.id_commentaire);
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
