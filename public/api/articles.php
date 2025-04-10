<?php
// public/api/articles.php

define('ROOT', dirname(__DIR__, 2));
header('Content-Type: application/json');

require_once ROOT . '/vendor/autoload.php';
use App\Models\Article;

session_start();

// Vérification de la méthode POST
$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Vérification si l'utilisateur est connecté (par exemple, pour les admin)
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Utilisateur non connecté']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Suppression d'un article
if (!empty($data['action']) && $data['action'] === 'delete') {
    $id_article = $data['id_article'] ?? null;

    if (!$id_article) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de l\'article manquant']);
        exit;
    }

    // Création du modèle d'article
    $articleModel = new Article();
    $response = $articleModel->deleteById($id_article);

    if ($response) {
        echo json_encode(['success' => true, 'message' => 'Article supprimé']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la suppression']);
    }

    exit;
}

// Modification d'un article
if (!empty($data['action']) && $data['action'] === 'edit') {
    $id_article = $data['id_article'] ?? null;
    $titre = $data['titre'] ?? '';
    $contenu = $data['contenu'] ?? '';
    $image = $data['image'] ?? '';
    $video_url = $data['video_url'] ?? '';

    // Vérification de la présence des champs requis
    if (!$id_article || !$titre || !$contenu) {
        http_response_code(400);
        echo json_encode(['error' => 'Champs manquants ou invalides']);
        exit;
    }

    // Création du modèle d'article
    $articleModel = new Article();
    $response = $articleModel->update($id_article, $titre, $contenu, $image, $video_url);

    if ($response) {
        echo json_encode(['success' => true, 'message' => 'Article modifié']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la modification']);
    }

    exit;
}
