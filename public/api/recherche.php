<?php
// public/api/recherche.php

define('ROOT', dirname(__DIR__, 2));
header('Content-Type: application/json');

require_once ROOT . '/vendor/autoload.php';

use App\Models\Article;
use App\Models\Oeuvre;

// 🛡️ Autoriser uniquement les requêtes GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// 📥 Lecture des paramètres
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$type  = isset($_GET['type']) ? trim($_GET['type']) : '';

if ($query === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètre de recherche manquant']);
    exit;
}

$oeuvres  = [];
$articles = [];

// 🔍 Filtrage des résultats selon le type
if ($type === 'oeuvre') {
    $oeuvres = Oeuvre::searchByTitleOrAuthor($query);
} elseif ($type === 'article') {
    $articles = Article::searchByTitleOrAuthor($query);
} else {
    $oeuvres = Oeuvre::searchByTitleOrAuthor($query);
    $articles = Article::searchByTitleOrAuthor($query);
}

// ✅ Envoi du résultat JSON
echo json_encode([
    'query' => $query,
    'oeuvres' => $oeuvres,
    'articles' => $articles
]);
