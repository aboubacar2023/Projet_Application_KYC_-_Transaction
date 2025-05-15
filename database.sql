-- Création de la base de données
CREATE DATABASE IF NOT EXISTS kyc_transactions;
USE kyc_transactions;

-- Table 'clients'
CREATE TABLE clients (
    telephone VARCHAR(20) PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    adresse TEXT NOT NULL,
    date_naissance DATE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL, -- mot de passe crypté
    statut ENUM('en_attente', 'verifie_niv1', 'verifie_total', 'rejeté') DEFAULT 'en_attente',
    solde INT DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table 'utilisateurs' (agents, commerciaux, admin)
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    validation_admin BOOLEAN DEFAULT false ,
    role ENUM('agent1', 'agent2', 'commercial', 'admin') DEFAULT NULL
) ENGINE=InnoDB;

-- Table 'documents'
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    telephone_client VARCHAR(20) NOT NULL,
    type_document VARCHAR(50) NOT NULL,
    chemin_fichier VARCHAR(255) NOT NULL,
    date_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (telephone_client) REFERENCES clients(telephone) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table 'verifications' (historique KYC)
CREATE TABLE verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    telephone_client VARCHAR(20) NOT NULL,
    niveau TINYINT NOT NULL CHECK (niveau IN (1, 2)),
    id_agent INT NOT NULL,
    commentaire TEXT,
    date_verification DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (telephone_client) REFERENCES clients(telephone) ON DELETE CASCADE,
    FOREIGN KEY (id_agent) REFERENCES utilisateurs(id)
) ENGINE=InnoDB;

-- Table 'operations'
CREATE TABLE operations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    telephone_client VARCHAR(20) NOT NULL,
    type_operation ENUM('depot', 'retrait', 'transfert_entrant', 'transfert_sortant') NOT NULL,
    montant INT NOT NULL,
    id_commercial INT, -- NULL pour les transferts
    telephone_destinataire VARCHAR(20), -- seulement pour transferts
    validation_operation BOOLEAN DEFAULT true,
    date_operation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (telephone_client) REFERENCES clients(telephone),
    FOREIGN KEY (id_commercial) REFERENCES utilisateurs(id),
    FOREIGN KEY (telephone_destinataire) REFERENCES clients(telephone)
) ENGINE=InnoDB;
