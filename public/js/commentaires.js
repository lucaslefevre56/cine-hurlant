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

// Je m’assure que le DOM est chargé avant d’agir
document.addEventListener('DOMContentLoaded', () => {
  
    // Je cible le formulaire d’ajout de commentaire
    const form = document.getElementById('form-commentaire');
  
    // Je vérifie que le formulaire existe (au cas où l’utilisateur n’est pas connecté)
    if (!form) return;
  
    // J’ajoute un écouteur d’événement sur l’envoi du formulaire
    form.addEventListener('submit', function (e) {
      e.preventDefault(); // J’empêche le rechargement de la page
  
      // Je récupère les valeurs du formulaire
      const contenu = document.getElementById('contenu').value.trim();
      const id_article = form.querySelector('input[name="id_article"]').value;
  
      // Je vérifie que le contenu n’est pas vide (sécurité côté client en plus du PHP)
      if (!contenu) {
        afficherMessage("Le commentaire est vide", "error");
        return;
      }
  
      // J’envoie les données à l’API interne
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
  .then(response => response.json()) // Je transforme la réponse JSON en objet JS
  .then(data => {
    // Je vérifie que l’ajout a réussi
    if (data.success) {
      afficherMessage("Commentaire ajouté !", "success");
  
     // Je crée dynamiquement un bloc HTML pour le nouveau commentaire
const commentaireHTML = `
  <div class="commentaire">
    <p><strong>${data.auteur}</strong> — ${data.date}</p>
    <p>${escapeHtml(data.contenu)}</p>
  </div>
`;

// Je l’ajoute en bas de la liste des commentaires existants
document.getElementById('commentaires-liste').insertAdjacentHTML('beforeend', commentaireHTML);

  
      // Je vide le champ du commentaire
      document.getElementById('contenu').value = '';
    } else {
      // S’il y a une erreur renvoyée par l’API
      afficherMessage(data.error || "Une erreur est survenue", "error");
    }
  })
  .catch(error => {
    // Si l’appel fetch() échoue (connexion, serveur planté, etc.)
    console.error("Erreur AJAX :", error);
    afficherMessage("Erreur de connexion au serveur", "error");
  });
  
    });
  
  });

  // Petite fonction de sécurité pour éviter l’exécution de code HTML (XSS)
function escapeHtml(unsafe) {
    return unsafe
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
  