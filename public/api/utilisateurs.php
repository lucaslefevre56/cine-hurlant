<?php

// public/api/utilisateurs.php

define('ROOT', dirname(__DIR__, 2));
header('Content-Type: application/json');

require_once ROOT . '/vendor/autoload.php';
use App\Models\Utilisateur;

session_start();
$method = $_SERVER['REQUEST_METHOD'];

if (!in_array($method, ['POST'])) {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Changer le rôle d'un utilisateur
if (!empty($data['action']) && $data['action'] === 'changer_role') {
    $id_utilisateur = $data['id_utilisateur'] ?? null;
    $nouveau_role = $data['role'] ?? null;

    if (!$id_utilisateur || !$nouveau_role) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de l\'utilisateur ou rôle manquant']);
        exit;
    }

    $utilisateurModel = new Utilisateur();
    $response = $utilisateurModel->updateRole($id_utilisateur, $nouveau_role);

    if ($response) {
        echo json_encode(['success' => true, 'message' => 'Rôle mis à jour']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la mise à jour du rôle']);
    }

    exit;
}

// Suppression d'un utilisateur
if (!empty($data['action']) && $data['action'] === 'delete') {
    $id_utilisateur = $data['id_utilisateur'] ?? null;

    if (!$id_utilisateur) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de l\'utilisateur manquant']);
        exit;
    }

    $utilisateurModel = new Utilisateur();
    $response = $utilisateurModel->deleteById($id_utilisateur);

    if ($response) {
        echo json_encode(['success' => true, 'message' => 'Utilisateur supprimé']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la suppression']);
    }

    exit;
}
