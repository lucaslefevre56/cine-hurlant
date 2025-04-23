<?php
// public/api/commentaires.php

// Je définis la racine du projet pour pouvoir charger les fichiers correctement
define('ROOT', dirname(__DIR__, 2));

// Je précise que les réponses seront au format JSON (important pour AJAX côté JS)
header('Content-Type: application/json');

// Je charge les classes automatiquement via Composer
require_once ROOT . '/vendor/autoload.php';
use App\Models\Commentaire;

session_name("cine-hurlant");
// Je démarre la session pour accéder à l'utilisateur connecté
session_start();

// Je récupère la méthode HTTP utilisée (GET, POST, etc.)
$method = $_SERVER['REQUEST_METHOD'];

// Je bloque les requêtes autres que GET et POST
if (!in_array($method, ['GET', 'POST'])) {
    http_response_code(405); // Code 405 = Méthode non autorisée
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// ----- GET → Récupération des commentaires pour un article ----- //
// Cette partie est publique (même les visiteurs peuvent lire les commentaires)
if ($method === 'GET' && isset($_GET['id_article'])) {
    $id_article = (int) $_GET['id_article'];
    $commentaire = new Commentaire();
    $commentaires = $commentaire->getByArticle($id_article);

    echo json_encode($commentaires);
    exit;
}

// ----- À partir d’ici, il faut être connecté pour continuer ----- //
if (!isset($_SESSION['user'])) {
    http_response_code(401); // Non autorisé
    echo json_encode(['error' => 'Utilisateur non connecté']);
    exit;
}

// Je récupère l’ID de l’utilisateur connecté
$id_utilisateur = $_SESSION['user']['id'];

// Je vérifie que le corps JSON n’est pas vide avant d’essayer de le décoder
if ($method === 'POST' && empty(file_get_contents('php://input'))) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

// Je décode les données JSON envoyées en POST
$data = json_decode(file_get_contents('php://input'), true);

// J’instancie mon modèle
$commentaire = new Commentaire();

// ----- Suppression d’un commentaire ----- //
if (!empty($data['action']) && $data['action'] === 'delete') {
    $id_commentaire = $data['id_commentaire'] ?? null;

    // Je vérifie que j’ai bien reçu un ID
    if (!$id_commentaire) {
        http_response_code(400);
        echo json_encode(['error' => 'ID du commentaire manquant']);
        exit;
    }

    // Je vais chercher le commentaire ciblé
    $commentaireCible = $commentaire->getById($id_commentaire);

    // Si le commentaire n'existe pas, je bloque
    if (!$commentaireCible) {
        http_response_code(404);
        echo json_encode(['error' => 'Commentaire introuvable']);
        exit;
    }

    // Je vérifie si l’utilisateur est soit l’auteur, soit un admin
    $estAuteur = $commentaireCible['id_utilisateur'] == $id_utilisateur;
    $estAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';

    if (!$estAuteur && !$estAdmin) {
        http_response_code(403);
        echo json_encode(['error' => 'Action non autorisée']);
        exit;
    }

    // Si tout est ok, je tente la suppression
    $success = $commentaire->deleteById($id_commentaire);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Commentaire supprimé']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Échec de la suppression']);
    }

    exit;
}

// ----- Modification d’un commentaire ----- //
if (!empty($data['action']) && $data['action'] === 'edit') {
    $id_commentaire = $data['id_commentaire'] ?? null;
    $nouveau_contenu = trim($data['nouveau_contenu'] ?? '');

    // Je vérifie que les champs sont bien présents et non vides
    if (!$id_commentaire || $nouveau_contenu === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Champs manquants ou contenu vide']);
        exit;
    }

    // Je limite la taille pour éviter les abus
    if (strlen($nouveau_contenu) > 5000) {
        http_response_code(400);
        echo json_encode(['error' => 'Commentaire trop long']);
        exit;
    }

    // Je récupère le commentaire concerné
    $commentaireCible = $commentaire->getById($id_commentaire);

    if (!$commentaireCible) {
        http_response_code(404);
        echo json_encode(['error' => 'Commentaire introuvable']);
        exit;
    }

    // Seul l’auteur peut modifier son commentaire
    if ($commentaireCible['id_utilisateur'] != $id_utilisateur) {
        http_response_code(403);
        echo json_encode(['error' => 'Modification non autorisée']);
        exit;
    }

    // Je tente la mise à jour en base
    $ok = $commentaire->updateContenu($id_commentaire, $nouveau_contenu);

    if ($ok) {
        echo json_encode([
            'success' => true,
            'message' => 'Commentaire modifié',
            'nouveau_contenu' => $nouveau_contenu,
            'date' => date('Y-m-d H:i:s')
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Échec de la modification']);
    }

    exit;
}

// ----- Ajout d’un commentaire ----- //
// Si on arrive ici, c’est une requête POST sans action, donc un ajout

// Je récupère et nettoie le contenu
$contenu = trim($data['contenu'] ?? '');
$id_article = $data['id_article'] ?? null;

// Je vérifie que les deux champs sont bien présents
if ($contenu === '' || !$id_article) {
    http_response_code(400);
    echo json_encode(['error' => 'Champs manquants ou invalides']);
    exit;
}

// Limite de sécurité pour éviter les floods ou spams
if (strlen($contenu) > 5000) {
    http_response_code(400);
    echo json_encode(['error' => 'Commentaire trop long']);
    exit;
}

// Je tente d’ajouter le commentaire en base
$id_commentaire = $commentaire->add($contenu, $id_article, $id_utilisateur);

if ($id_commentaire) {
    echo json_encode([
        'success' => true,
        'id_commentaire' => $id_commentaire,
        'contenu' => $contenu,
        'id_article' => $id_article,
        'auteur' => $_SESSION['user']['nom'], // Je renvoie le nom de l’auteur
        'id_utilisateur' => $_SESSION['user']['id'],
        'date' => date('Y-m-d H:i:s')         // Je renvoie la date du commentaire
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => "Échec de l'ajout"]);
}
