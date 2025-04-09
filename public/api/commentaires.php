<?php
// public/api/commentaires.php

define('ROOT', dirname(__DIR__, 2));
header('Content-Type: application/json');

require_once ROOT . '/vendor/autoload.php';
use App\Models\Commentaire;

session_start();
$method = $_SERVER['REQUEST_METHOD'];

// 🛡️ Bloque les requêtes autres que GET et POST
if (!in_array($method, ['GET', 'POST'])) {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// 📥 Récupération des commentaires (accessible sans être connecté)
if ($method === 'GET' && isset($_GET['id_article'])) {
    $id_article = (int) $_GET['id_article'];
    $commentaire = new Commentaire();
    $commentaires = $commentaire->getByArticle($id_article);

    echo json_encode($commentaires);
    exit;
}

// 🔐 Protection : utilisateur non connecté
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Utilisateur non connecté']);
    exit;
}

$id_utilisateur = $_SESSION['user']['id'];

// 🛡️ Protection contre corps JSON vide
if ($method === 'POST' && empty(file_get_contents('php://input'))) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$commentaire = new Commentaire();

// 🔁 Suppression
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

    exit;
}

// ✏️ Modification
if (!empty($data['action']) && $data['action'] === 'edit') {
    $id_commentaire = $data['id_commentaire'] ?? null;
    $nouveau_contenu = trim($data['nouveau_contenu'] ?? '');

    if (!$id_commentaire || $nouveau_contenu === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Champs manquants ou contenu vide']);
        exit;
    }

    if (strlen($nouveau_contenu) > 5000) {
        http_response_code(400);
        echo json_encode(['error' => 'Commentaire trop long']);
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
        echo json_encode(['error' => 'Échec de la modification']);
    }

    exit;
}

// 🟢 Ajout
$contenu = trim($data['contenu'] ?? '');
$id_article = $data['id_article'] ?? null;

if ($contenu === '' || !$id_article) {
    http_response_code(400);
    echo json_encode(['error' => 'Champs manquants ou invalides']);
    exit;
}

if (strlen($contenu) > 5000) {
    http_response_code(400);
    echo json_encode(['error' => 'Commentaire trop long']);
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
    echo json_encode(['error' => "Échec de l'ajout"]);
}
