<?php
// public/api/commentaires.php

// ini_set('display_errors', 1); // =============================== A ENLEVER EN PROD ===============================
// error_reporting(E_ALL);

define('ROOT', dirname(__DIR__, 2));
header('content-type: application/json');

require_once ROOT . '/vendor/autoload.php';
use App\Models\Commentaire;

session_start();
$method = $_SERVER['REQUEST_METHOD'];
$commentaire = new Commentaire();

// 📥 Si c’est une demande AJAX de récupération des commentaires d’un article
if ($method === 'GET' && isset($_GET['id_article'])) {
    $id_article = (int) $_GET['id_article'];
    $commentaires = $commentaire->getByArticle($id_article);

    echo json_encode($commentaires);
    exit;
}

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Utilisateur non connecté']);
    exit;
}

$id_utilisateur = $_SESSION['user']['id'];
$data = json_decode(file_get_contents('php://input'), true);
$commentaire = new Commentaire();

// 🔁 PRIORITÉ : si c’est une demande de suppression, on traite et on sort
if (!empty($data['action']) && $data['action'] === 'delete') {
    $id_commentaire = $data['id_commentaire'] ?? null;

    if (!$id_commentaire) {
        http_response_code(400);
        echo json_encode(['error' => 'ID du commentaire manquant']);
        exit;
    }

    $commentaireCible = $commentaire->getById($id_commentaire);

    if (!$commentaireCible) {
        http_response_code(404);
        echo json_encode(['error' => 'Commentaire introuvable']);
        exit;
    }

    $estAuteur = $commentaireCible['id_utilisateur'] == $id_utilisateur;
    $estAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';

    if (!$estAuteur && !$estAdmin) {
        http_response_code(403);
        echo json_encode(['error' => 'Action non autorisée']);
        exit;
    }

    $success = $commentaire->deleteById($id_commentaire);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Commentaire supprimé']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Échec de la suppression']);
    }

    exit; // ⛔ STOP ici, on ne passe pas à l'ajout
}

// ✏️ Si c’est une demande de modification, on la traite ici
if (!empty($data['action']) && $data['action'] === 'edit') {
    $id_commentaire = $data['id_commentaire'] ?? null;
    $nouveau_contenu = trim($data['nouveau_contenu'] ?? '');

    if (!$id_commentaire || $nouveau_contenu === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Champs manquants ou contenu vide']);
        exit;
    }

    $commentaireCible = $commentaire->getById($id_commentaire);

    if (!$commentaireCible) {
        http_response_code(404);
        echo json_encode(['error' => 'Commentaire introuvable']);
        exit;
    }

    if ($commentaireCible['id_utilisateur'] != $id_utilisateur) {
        http_response_code(403);
        echo json_encode(['error' => 'Modification non autorisée']);
        exit;
    }

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
        echo json_encode(['error' => 'Echec de la modification']);
    }

    exit;
}


// 🟢 Sinon, on continue avec l’AJOUT
$contenu = $data['contenu'] ?? '';
$id_article = $data['id_article'] ?? null;

if (!isset($data['contenu'], $data['id_article']) || trim($contenu) === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Champs manquants ou invalides']);
    exit;
}

$id_commentaire = $commentaire->add($contenu, $id_article, $id_utilisateur);

if ($id_commentaire) {
    echo json_encode([
        'success' => true,
        'id_commentaire' => $id_commentaire,
        'contenu' => $contenu,
        'id_article' => $id_article,
        'auteur' => $_SESSION['user']['nom'],
        'date' => date('Y-m-d H:i:s')
    ]);
} else {
    http_response_code(500);
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
