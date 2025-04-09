-- Création de la table Utilisateur
CREATE TABLE Utilisateur(
   id_utilisateur INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   email VARCHAR(100) NOT NULL,
   password VARCHAR(255) NOT NULL,
   date_enregistrement DATETIME DEFAULT CURRENT_TIMESTAMP,
   role ENUM('utilisateur', 'redacteur', 'admin') DEFAULT 'utilisateur',
   PRIMARY KEY(id_utilisateur),
   UNIQUE(email)
);

-- Création de la table article
CREATE TABLE article (
   id_article INT AUTO_INCREMENT,
   titre VARCHAR(255) NOT NULL,
   contenu TEXT NOT NULL,
   image VARCHAR(255),
   video_url VARCHAR(255), -- Nouvelle colonne pour l'URL de la vidéo
   date_redaction DATETIME DEFAULT CURRENT_TIMESTAMP,
   id_utilisateur INT NOT NULL,
   PRIMARY KEY(id_article),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

-- Création de la table commentaire
CREATE TABLE commentaire(
   id_commentaire INT AUTO_INCREMENT,
   contenu TEXT,
   date_redaction DATETIME DEFAULT CURRENT_TIMESTAMP,
   valider BOOLEAN NOT NULL DEFAULT 1, -- Les commentaires sont validés par défaut (affichage direct)
   id_article INT NOT NULL,
   id_utilisateur INT NOT NULL,
   PRIMARY KEY(id_commentaire),
   FOREIGN KEY(id_article) REFERENCES article(id_article),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

-- Création de la table forum
CREATE TABLE forum(
   id_sujet INT AUTO_INCREMENT,
   titre VARCHAR(50) NOT NULL,
   contenu TEXT NOT NULL,
   date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
   id_parent INT DEFAULT NULL,
   id_utilisateur INT NOT NULL,
   PRIMARY KEY(id_sujet),
   FOREIGN KEY(id_parent) REFERENCES forum(id_sujet),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

-- Création de la table type
CREATE TABLE type(
   id_type INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_type),
   UNIQUE(nom)
);

-- Création de la table genre
CREATE TABLE genre(
   id_genre INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_genre),
   UNIQUE(nom)
);

-- Création de la table oeuvre
CREATE TABLE oeuvre(
   id_oeuvre INT AUTO_INCREMENT,
   titre VARCHAR(50) NOT NULL,
   auteur VARCHAR(50) NOT NULL,
   annee YEAR,
   media VARCHAR(255),
   video_url VARCHAR(255), -- Nouvelle colonne pour l'URL de la vidéo
   analyse TEXT,
   date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
   id_type INT NOT NULL,
   id_utilisateur INT NOT NULL,
   PRIMARY KEY(id_oeuvre),
   FOREIGN KEY(id_type) REFERENCES type(id_type),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

-- Création de la table ajouter_favoris
CREATE TABLE ajouter_favoris(
   id_utilisateur INT,
   id_oeuvre INT,
   date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY(id_utilisateur, id_oeuvre),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur),
   FOREIGN KEY(id_oeuvre) REFERENCES oeuvre(id_oeuvre)
);

-- Création de la table noter
CREATE TABLE noter(
   id_utilisateur INT,
   id_oeuvre INT,
   note INT CHECK(note BETWEEN 1 AND 5),
   PRIMARY KEY(id_utilisateur, id_oeuvre),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur),
   FOREIGN KEY(id_oeuvre) REFERENCES oeuvre(id_oeuvre)
);

-- Création de la table participer
CREATE TABLE participer(
   id_utilisateur INT,
   id_sujet INT,
   PRIMARY KEY(id_utilisateur, id_sujet),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur),
   FOREIGN KEY(id_sujet) REFERENCES forum(id_sujet)
);

-- Création de la table appartenir
CREATE TABLE appartenir(
   id_oeuvre INT,
   id_genre INT,
   PRIMARY KEY(id_oeuvre, id_genre),
   FOREIGN KEY(id_oeuvre) REFERENCES oeuvre(id_oeuvre),
   FOREIGN KEY(id_genre) REFERENCES genre(id_genre)
);

-- Création de la table analyser
CREATE TABLE analyser (
    id_article INT NOT NULL,
    id_oeuvre INT NOT NULL,
    PRIMARY KEY (id_article, id_oeuvre),
    FOREIGN KEY (id_article) REFERENCES article(id_article) ON DELETE CASCADE,
    FOREIGN KEY (id_oeuvre) REFERENCES oeuvre(id_oeuvre) ON DELETE CASCADE
);

-- Initialisation des données dans la table 'type'
INSERT INTO type (id_type, nom) 
SELECT 1, 'film'
WHERE NOT EXISTS (SELECT 1 FROM type WHERE nom = 'film');

INSERT INTO type (id_type, nom) 
SELECT 2, 'bd'
WHERE NOT EXISTS (SELECT 1 FROM type WHERE nom = 'bd');

-- Initialisation des données dans la table 'genre'
INSERT INTO genre (id_genre, nom) 
SELECT 1, 'Science-fiction'
WHERE NOT EXISTS (SELECT 1 FROM genre WHERE nom = 'Science-fiction');

INSERT INTO genre (id_genre, nom) 
SELECT 2, 'Fantastique'
WHERE NOT EXISTS (SELECT 1 FROM genre WHERE nom = 'Fantastique');

INSERT INTO genre (id_genre, nom) 
SELECT 3, 'Western'
WHERE NOT EXISTS (SELECT 1 FROM genre WHERE nom = 'Western');

INSERT INTO genre (id_genre, nom) 
SELECT 4, 'Cyberpunk'
WHERE NOT EXISTS (SELECT 1 FROM genre WHERE nom = 'Cyberpunk');