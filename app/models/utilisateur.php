<?php
// app/models/Utilisateur.php

// Cette classe gère tout ce qui concerne un utilisateur côté base de données.
// Elle regroupe les fonctions liées à ses infos, son rôle, ses commentaires,
// et ce qu'il peut publier s'il est rédacteur ou admin.

class Utilisateur
{
    private $conn; // Je garde ici la connexion PDO pour exécuter mes requêtes SQL

    // Quand je crée un nouvel objet Utilisateur, je lui donne la connexion PDO
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // ----------------------------------------
    // PARTIE 1 : INFOS UTILISATEUR
    // ----------------------------------------

    // Je récupère un utilisateur en base à partir de son ID
    public function getById($id)
    {
        $sql = "SELECT * FROM Utilisateur WHERE id_utilisateur = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // FETCH_ASSOC, c’est la version propre et logique pour lire les résultats SQL.
    // Je veux des tableaux avec des noms, pas des chiffres incompréhensibles.
    // Et c’est ce que j’utilise dans tout mon site, car je travaille avec des données claires.

    
    // Je récupère un utilisateur en base à partir de son email
    // (pratique pour la connexion ou vérifier si l’email est déjà pris à l’inscription)
    public function getByEmail($email)
    {
        $sql = "SELECT * FROM Utilisateur WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // J’ajoute un utilisateur dans la base
    // Le mot de passe est déjà hashé avant d’arriver ici
    public function add($nom, $email, $hashedPassword)
    {
        $sql = "INSERT INTO Utilisateur (nom, email, password) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nom, $email, $hashedPassword]);
    }

    // ----------------------------------------
    // PARTIE 2 : ROLES ET DROITS
    // ----------------------------------------

    // Je vérifie si l’utilisateur est admin
    // Utile pour autoriser des actions réservées comme supprimer des utilisateurs ou changer des rôles
    public function isAdmin($id)
    {
        $user = $this->getById($id);
        return $user && $user['role'] === 'admin';
    }

    // Je vérifie si l’utilisateur est rédacteur
    // Attention : les admins ont aussi les droits des rédacteurs, donc je les inclus ici
    public function isRedacteur($id)
    {
        $user = $this->getById($id);
        return $user && ($user['role'] === 'redacteur' || $user['role'] === 'admin');
    }

    // Permet à un admin de changer le rôle d’un utilisateur (ex : le promouvoir rédacteur)
    public function updateRole($id, $newRole)
    {
        $sql = "UPDATE Utilisateur SET role = ? WHERE id_utilisateur = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$newRole, $id]);
    }

    // ----------------------------------------
    // PARTIE 3 : COMMENTAIRES
    // ----------------------------------------

    // Je récupère tous les commentaires postés par un utilisateur
    public function getCommentaires($id_utilisateur)
    {
        $sql = "SELECT * FROM Commentaire WHERE id_utilisateur = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Je supprime un commentaire (le contrôleur doit vérifier que l’utilisateur en a le droit)
    public function deleteCommentaire($id_commentaire)
    {
        $sql = "DELETE FROM Commentaire WHERE id_commentaire = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_commentaire]);
    }

    // ----------------------------------------
    // PARTIE 4 : PUBLICATIONS DE L'UTILISATEUR (OEUVRES ET ARTICLES)
    // ----------------------------------------

    // Je récupère toutes les œuvres publiées par un utilisateur
    public function getOeuvres($id_utilisateur)
    {
        $sql = "SELECT * FROM Oeuvre WHERE id_utilisateur = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Je récupère tous les articles publiés par un utilisateur
    public function getArticles($id_utilisateur)
    {
        $sql = "SELECT * FROM Article WHERE id_utilisateur = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


// Cette classe Utilisateur, c’est mon lien entre le code et la base pour tout ce qui touche à un utilisateur.
// Elle regroupe ses infos, ses rôles, ses commentaires, et ce qu’il publie.
// Tout est bien organisé par blocs, et chaque méthode fait une seule chose bien précise, avec la connexion PDO toujours à dispo.
// Je peux maintenant utiliser cette classe dans mes contrôleurs pour récupérer ou modifier des données liées aux utilisateurs, sans jamais avoir à toucher à SQL en dehors de ce fichier.
// Bref, c’est propre, modulaire, prêt à être utilisé partout dans mon site