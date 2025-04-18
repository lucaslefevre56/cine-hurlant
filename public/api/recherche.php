<?php
// public/api/recherche.php

// Je définis le chemin racine du projet pour charger les dépendances correctement
define('ROOT', dirname(__DIR__, 2));

// Je précise que la réponse de cette API sera envoyée au format JSON
header('Content-Type: application/json');

// Je charge automatiquement les classes du projet via Composer
require_once ROOT . '/vendor/autoload.php';

use App\Models\Article;
use App\Models\Oeuvre;

// Je bloque toute méthode autre que GET pour éviter les usages détournés
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Méthode non autorisée
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Je récupère les paramètres de recherche depuis l’URL
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$type  = isset($_GET['type']) ? trim($_GET['type']) : '';

// Si aucun mot-clé n’est fourni, je renvoie une erreur
if ($query === '') {
    http_response_code(400); // Requête invalide
    echo json_encode(['error' => 'Paramètre de recherche manquant']);
    exit;
}

// Je prépare mes deux tableaux de résultats
$oeuvres  = [];
$articles = [];

// Je filtre la recherche selon le type demandé : article, œuvre ou les deux
if ($type === 'oeuvre') {
    $oeuvres = Oeuvre::searchByTitleOrAuthor($query);
} elseif ($type === 'article') {
    $articles = Article::searchByTitleOrAuthor($query);
} else {
    $oeuvres = Oeuvre::searchByTitleOrAuthor($query);
    $articles = Article::searchByTitleOrAuthor($query);
}

// Je renvoie les résultats sous forme d’un objet JSON
echo json_encode([
    'query' => $query,        // Le mot-clé recherché
    'oeuvres' => $oeuvres,    // Les œuvres trouvées (si applicable)
    'articles' => $articles   // Les articles trouvés (si applicable)
]);
