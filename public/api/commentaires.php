<?php

// ini_set('display_errors', 1); ===============================A ENLEVER EN PROD !!!!! ===============================
// error_reporting(E_ALL);

// Je définis manuellement la constante ROOT pour cette API
define('ROOT', dirname(__DIR__, 2));

// J’indique que toutes les réponses de ce fichier seront envoyées au format JSON
// → C’est essentiel pour que le JavaScript qui appelle ce fichier sache comment lire la réponse
header('content-type: application/json');

// Je charge l’autoloader généré par Composer, qui me permet d’utiliser mes classes avec les namespaces
// → ROOT est une constante définie dans le projet qui pointe vers la racine
require_once ROOT . '/vendor/autoload.php';

// J’importe la classe Commentaire de mon dossier Models
// → Cela me permettra d’utiliser new Commentaire() plus bas
use App\Models\Commentaire;

// Je démarre la session PHP pour accéder aux informations de l’utilisateur connecté
// → Sans ça, $_SESSION['utilisateur'] sera vide
session_start();

// Je récupère la méthode HTTP utilisée pour appeler ce fichier (POST, GET, DELETE, etc.)
// → Ici, on attend un POST (ajout de commentaire)
$method = $_SERVER['REQUEST_METHOD'];

// Je vérifie que l’utilisateur est connecté
// → Si la clé 'utilisateur' n’est pas présente dans $_SESSION, je bloque l’accès
if (!isset($_SESSION['user'])) {
    // Je renvoie un code HTTP 401 (non autorisé)
    http_response_code(401);
    // Je renvoie un message JSON compréhensible par le frontend
    echo json_encode(['error' => 'Utilisateur non connecté']);
    exit; // Je stoppe l’exécution du script
}

// Je récupère l’id de l’utilisateur connecté depuis la session
$id_utilisateur = $_SESSION['user']['id'];

// Je récupère les données envoyées par le frontend via JavaScript (au format JSON)
// → 'php://input' lit le corps brut de la requête HTTP
// → json_decode(..., true) transforme ce JSON en tableau associatif PHP
$data = json_decode(file_get_contents('php://input'), true);

// J’instancie mon modèle Commentaire, pour pouvoir utiliser ses méthodes (comme add)
$commentaire = new Commentaire();

// Je récupère les champs envoyés : le contenu du commentaire, et l’id de l’article concerné
// → Si une des deux clés n’existe pas, je mets une valeur par défaut (chaîne vide ou null)
$contenu = $data['contenu'] ?? '';
$id_article = $data['id_article'] ?? null;

// Je vérifie que les deux champs sont bien remplis
// → Je ne veux pas autoriser l’ajout de commentaires vides ou sans article associé
if (empty($contenu) || empty($id_article)) {
    // Je retourne un code 400 = requête mal formée
    http_response_code(400);
    // Et un message d’erreur clair pour le frontend
    echo json_encode(['error' => 'Champs manquants']);
    exit; // Je stoppe le script, car on ne va pas plus loin si les champs sont invalides
}

// J’appelle la méthode add() de mon modèle Commentaire pour insérer les données en BDD
// → Si l’insertion réussit, je récupère l’ID du nouveau commentaire
// → Sinon, $id_commentaire vaudra false
$id_commentaire = $commentaire->add($contenu, $id_article, $id_utilisateur);

// Si l’ajout a fonctionné (la BDD a bien enregistré le commentaire)
if ($id_commentaire) {
    // Je renvoie une réponse JSON contenant :
    // - un indicateur de succès
    // - les infos du commentaire (id, contenu, article, auteur, date)
    // → Ces infos seront utiles pour que le JavaScript puisse directement l’afficher
    echo json_encode([
        'success' => true,
        'id_commentaire' => $id_commentaire,
        'contenu' => $contenu,
        'id_article' => $id_article,
        'auteur' => $_SESSION['user']['nom'], // À adapter selon ce que tu stockes côté utilisateur
        'date' => date('Y-m-d H:i:s') // Génère la date actuelle au moment de l’ajout
    ]);
} else {
    // Si l’ajout a échoué, je renvoie un code 500 = erreur serveur
    http_response_code(500);
    // Et un message clair pour le JS
    echo json_encode(['error' => "Echec de l'ajout"]);
}

/**
 * ----------------------------------------------------------------------------
 * Ce fichier est mon API interne pour gérer les commentaires côté JavaScript.
 * ----------------------------------------------------------------------------
 *
 *  Il est appelé directement par du fetch() en JS (pas via le routeur MVC).
 *  Il ne renvoie pas de page HTML, mais uniquement du JSON.
 *  Il est placé dans /public/api car c’est une passerelle AJAX entre le front et la base.
 *
 *  Que fait ce fichier exactement ?
 * 
 * 1. Il vérifie que l’utilisateur est connecté via la session.
 *    → Sinon, il bloque avec une erreur 401 (non autorisé).
 *
 * 2. Il récupère les données JSON envoyées par le JS :
 *    - le contenu du commentaire
 *    - l’identifiant de l’article concerné
 *
 * 3. Il s’assure que ces données sont bien présentes.
 *    → Si l’une d’elles est vide, il bloque avec une erreur 400 (mauvaise requête).
 *
 * 4. Il appelle la méthode add() du modèle Commentaire
 *    → pour insérer les données dans la base (comme un vrai contrôleur).
 *
 * 5. Il renvoie une réponse JSON bien structurée au JS :
 *    - Si tout va bien → succès + données du commentaire (contenu, auteur, date, etc.)
 *    - Si ça échoue → erreur 500 (bug côté serveur)
 *
 *  Pourquoi ce fichier est important ?
 * 
 * → Il me permet d’ajouter un commentaire sans recharger la page.
 * → Il suit la logique d’une vraie API REST : codes HTTP, JSON, sécurité, séparation du HTML.
 * → Il rend mon site plus fluide, plus moderne, et bien organisé (frontend ↔ backend).
 *
 *  C’est ce fichier que le JavaScript va appeler chaque fois que l’utilisateur envoie un commentaire.
 * Ensuite, c’est JS qui s’occupera de l’afficher dans la page.
 */
