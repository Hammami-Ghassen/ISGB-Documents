CREATE DATABASE IF NOT EXISTS gestion_documents;
USE gestion_documents;

CREATE TABLE Utilisateur (
    id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    mot_de_passe VARCHAR(255),
    role ENUM('utilisateur', 'administrateur') DEFAULT 'utilisateur'
);




CREATE TABLE Demande (
    id_demande INT PRIMARY KEY AUTO_INCREMENT,
    id_utilisateur INT,
    type_document VARCHAR(100),
    date_soumission DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en attente', 'acceptée', 'rejetée') DEFAULT 'en attente',
    commentaire_admin TEXT,
    id_admin_traitant INT NULL,
    identifiant_suivi VARCHAR(100) UNIQUE,

    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur),
    FOREIGN KEY (id_admin_traitant) REFERENCES Utilisateur(id_utilisateur)
);

CREATE TABLE DemandeDetails (
    id_detail INT PRIMARY KEY AUTO_INCREMENT,
    id_demande INT,
    champ_nom VARCHAR(100),
    champ_valeur TEXT,

    FOREIGN KEY (id_demande) REFERENCES Demande(id_demande) ON DELETE CASCADE
);


CREATE TABLE PieceJointe (
    id_piece INT PRIMARY KEY AUTO_INCREMENT,
    id_demande INT,
    nom_fichier VARCHAR(255),
    chemin_fichier TEXT,
    taille INT,
    type_mime VARCHAR(100),
    FOREIGN KEY (id_demande) REFERENCES Demande(id_demande)
);


CREATE TABLE HistoriqueAction (
    id_action INT PRIMARY KEY AUTO_INCREMENT,
    id_admin INT,
    id_demande INT,
    action ENUM('acceptée', 'rejetée'),
    date_action DATETIME DEFAULT CURRENT_TIMESTAMP,
    commentaire TEXT,
    FOREIGN KEY (id_admin) REFERENCES Utilisateur(id_utilisateur),
    FOREIGN KEY (id_demande) REFERENCES Demande(id_demande)
);
