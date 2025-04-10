<?php

// public/api/oeuvres.php

define('ROOT', dirname(__DIR__, 2));
header('Content-Type: application/json');

require_once ROOT . '/vendor/autoload.php';
use App\Models\Oeuvre;

session_start();
$method = $_SERVER['REQUEST_METHOD'];

if (!in_array($method, ['POST'])) {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Suppression d'une œuvre
if (!empty($data['action']) && $data['action'] === 'delete') {
    $id_oeuvre = $data['id_oeuvre'] ?? null;

    if (!$id_oeuvre) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de l\'œuvre manquant']);
        exit;
    }

    $oeuvreModel = new Oeuvre();
    $response = $oeuvreModel->deleteById($id_oeuvre);

    if ($response) {
        echo json_encode(['success' => true, 'message' => 'Œuvre supprimée']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la suppression']);
    }

    exit;
}

// Modification d'une œuvre
if (!empty($data['action']) && $data['action'] === 'edit') {
    $id_oeuvre = $data['id_oeuvre'] ?? null;
    $titre = $data['titre'] ?? '';
    $auteur = $data['auteur'] ?? '';
    $annee = $data['annee'] ?? '';
    $media = $data['media'] ?? '';
    $video_url = $data['video_url'] ?? '';
    $analyse = $data['analyse'] ?? '';
    $id_type = $data['id_type'] ?? null;

    if (!$id_oeuvre || !$titre || !$auteur || !$annee || !$media || !$id_type) {
        http_response_code(400);
        echo json_encode(['error' => 'Champs manquants']);
        exit;
    }

    $oeuvreModel = new Oeuvre();
    $response = $oeuvreModel->update($id_oeuvre, $titre, $auteur, $annee, $media, $video_url, $analyse, $id_type);

    if ($response) {
        echo json_encode(['success' => true, 'message' => 'Œuvre modifiée']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la modification']);
    }

    exit;
}
