-- Migrations SQL pour le projet E-Commerce
-- Date : 2026-04-16
-- Description : Création des tables manquantes et ajout des champs nécessaires

-- 1. Modifier la table utilisateur (ajouter user_type, email_verified)
ALTER TABLE utilisateur 
ADD COLUMN IF NOT EXISTS idutilisateur INT PRIMARY KEY AUTO_INCREMENT FIRST,
ADD COLUMN IF NOT EXISTS user_type ENUM('admin', 'client') DEFAULT 'client' AFTER is_active,
ADD COLUMN IF NOT EXISTS email_verified BOOLEAN DEFAULT FALSE AFTER user_type,
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER email_verified;

-- 2. Modifier la table produit (ajouter stock, is_archived, etc.)
ALTER TABLE produit 
ADD COLUMN IF NOT EXISTS stock INT DEFAULT 0 AFTER image,
ADD COLUMN IF NOT EXISTS is_archived BOOLEAN DEFAULT FALSE AFTER stock,
ADD COLUMN IF NOT EXISTS couleur VARCHAR(100) DEFAULT 'Défaut' AFTER is_archived,
ADD COLUMN IF NOT EXISTS categorie VARCHAR(100) AFTER couleur,
ADD COLUMN IF NOT EXISTS date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER categorie;

-- 3. Créer la table commande
CREATE TABLE IF NOT EXISTS commande (
    idcommande INT PRIMARY KEY AUTO_INCREMENT,
    idutilisateur INT NOT NULL,
    montant_total DECIMAL(10, 2) NOT NULL,
    statut ENUM('pending', 'paid', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    adresse_livraison VARCHAR(255),
    telephone VARCHAR(20),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (idutilisateur) REFERENCES utilisateur(idutilisateur) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Créer la table commande_produit (articles de la commande)
CREATE TABLE IF NOT EXISTS commande_produit (
    idcommande_produit INT PRIMARY KEY AUTO_INCREMENT,
    idcommande INT NOT NULL,
    idprod INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    couleur VARCHAR(100),
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idcommande) REFERENCES commande(idcommande) ON DELETE CASCADE,
    FOREIGN KEY (idprod) REFERENCES produit(idprod) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Créer la table panier (panier d'achat)
CREATE TABLE IF NOT EXISTS panier (
    idpanier INT PRIMARY KEY AUTO_INCREMENT,
    idutilisateur INT NOT NULL,
    idprod INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    couleur VARCHAR(100),
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_product (idutilisateur, idprod, couleur),
    FOREIGN KEY (idutilisateur) REFERENCES utilisateur(idutilisateur) ON DELETE CASCADE,
    FOREIGN KEY (idprod) REFERENCES produit(idprod) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Créer la table couleur_produit (variantes de couleurs)
CREATE TABLE IF NOT EXISTS couleur_produit (
    idcouleur INT PRIMARY KEY AUTO_INCREMENT,
    idprod INT NOT NULL,
    couleur VARCHAR(50) NOT NULL,
    stock INT DEFAULT 0,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idprod) REFERENCES produit(idprod) ON DELETE CASCADE,
    UNIQUE KEY unique_product_color (idprod, couleur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Créer la table notification_email (logs des emails)
CREATE TABLE IF NOT EXISTS notification_email (
    idnotification INT PRIMARY KEY AUTO_INCREMENT,
    idutilisateur INT,
    email_destinataire VARCHAR(255) NOT NULL,
    type ENUM('registration', 'order_confirmation', 'shipping', 'password_reset', 'admin_notification') DEFAULT 'order_confirmation',
    sujet VARCHAR(255) NOT NULL,
    contenu LONGTEXT,
    statut ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
    date_envoi TIMESTAMP,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idutilisateur) REFERENCES utilisateur(idutilisateur) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Créer la table modification_log (suivi des modifications)
CREATE TABLE IF NOT EXISTS modification_log (
    idlog INT PRIMARY KEY AUTO_INCREMENT,
    idutilisateur INT,
    type ENUM('produit', 'commande', 'utilisateur') NOT NULL,
    action ENUM('create', 'update', 'delete', 'archive') NOT NULL,
    id_cible INT,
    ancien_contenu TEXT,
    nouveau_contenu TEXT,
    description VARCHAR(255),
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idutilisateur) REFERENCES utilisateur(idutilisateur) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Créer des index pour améliorer les performances
CREATE INDEX idx_commande_utilisateur ON commande(idutilisateur);
CREATE INDEX idx_commande_produit_commande ON commande_produit(idcommande);
CREATE INDEX idx_commande_produit_produit ON commande_produit(idprod);
CREATE INDEX idx_panier_utilisateur ON panier(idutilisateur);
CREATE INDEX idx_panier_produit ON panier(idprod);
CREATE INDEX idx_notification_utilisateur ON notification_email(idutilisateur);
CREATE INDEX idx_notification_statut ON notification_email(statut);
CREATE INDEX idx_couleur_produit ON couleur_produit(idprod);

-- Vérification
-- SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'form_db';
