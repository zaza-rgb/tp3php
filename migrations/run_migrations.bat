@echo off
REM Script de migration SQL pour le projet E-Commerce
REM Ce script exécute tous les ALTER TABLE et CREATE TABLE nécessaires

setlocal enabledelayedexpansion

cd /d C:\xampp\mysql\bin

echo Execution des migrations...
echo.

REM Modifier la table utilisateur
echo Modification table utilisateur...
mysql -u root form_db -e "ALTER TABLE utilisateur ADD COLUMN IF NOT EXISTS user_type ENUM('admin', 'client') DEFAULT 'client' AFTER is_active;"
mysql -u root form_db -e "ALTER TABLE utilisateur ADD COLUMN IF NOT EXISTS email_verified BOOLEAN DEFAULT FALSE AFTER user_type;"
mysql -u root form_db -e "ALTER TABLE utilisateur ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER email_verified;"

REM Modifier la table produit
echo Modification table produit...
mysql -u root form_db -e "ALTER TABLE produit ADD COLUMN IF NOT EXISTS stock INT DEFAULT 0 AFTER image;"
mysql -u root form_db -e "ALTER TABLE produit ADD COLUMN IF NOT EXISTS is_archived BOOLEAN DEFAULT FALSE AFTER stock;"
mysql -u root form_db -e "ALTER TABLE produit ADD COLUMN IF NOT EXISTS couleur VARCHAR(100) DEFAULT 'Defaut' AFTER is_archived;"
mysql -u root form_db -e "ALTER TABLE produit ADD COLUMN IF NOT EXISTS categorie VARCHAR(100) AFTER couleur;"
mysql -u root form_db -e "ALTER TABLE produit ADD COLUMN IF NOT EXISTS date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER categorie;"

REM Créer la table commande
echo Creation table commande...
mysql -u root form_db -e "CREATE TABLE IF NOT EXISTS commande (idcommande INT PRIMARY KEY AUTO_INCREMENT, idutilisateur INT NOT NULL, montant_total DECIMAL(10, 2) NOT NULL, statut ENUM('pending', 'paid', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending', adresse_livraison VARCHAR(255), telephone VARCHAR(20), date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP, date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, notes TEXT, FOREIGN KEY (idutilisateur) REFERENCES utilisateur(idutilisateur) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

REM Créer la table commande_produit
echo Creation table commande_produit...
mysql -u root form_db -e "CREATE TABLE IF NOT EXISTS commande_produit (idcommande_produit INT PRIMARY KEY AUTO_INCREMENT, idcommande INT NOT NULL, idprod INT NOT NULL, quantite INT NOT NULL DEFAULT 1, prix_unitaire DECIMAL(10, 2) NOT NULL, couleur VARCHAR(100), date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (idcommande) REFERENCES commande(idcommande) ON DELETE CASCADE, FOREIGN KEY (idprod) REFERENCES produit(idprod) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

REM Créer la table panier
echo Creation table panier...
mysql -u root form_db -e "CREATE TABLE IF NOT EXISTS panier (idpanier INT PRIMARY KEY AUTO_INCREMENT, idutilisateur INT NOT NULL, idprod INT NOT NULL, quantite INT NOT NULL DEFAULT 1, couleur VARCHAR(100), date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY unique_user_product (idutilisateur, idprod, couleur), FOREIGN KEY (idutilisateur) REFERENCES utilisateur(idutilisateur) ON DELETE CASCADE, FOREIGN KEY (idprod) REFERENCES produit(idprod) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

REM Créer la table couleur_produit
echo Creation table couleur_produit...
mysql -u root form_db -e "CREATE TABLE IF NOT EXISTS couleur_produit (idcouleur INT PRIMARY KEY AUTO_INCREMENT, idprod INT NOT NULL, couleur VARCHAR(50) NOT NULL, stock INT DEFAULT 0, date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (idprod) REFERENCES produit(idprod) ON DELETE CASCADE, UNIQUE KEY unique_product_color (idprod, couleur)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

REM Créer la table notification_email
echo Creation table notification_email...
mysql -u root form_db -e "CREATE TABLE IF NOT EXISTS notification_email (idnotification INT PRIMARY KEY AUTO_INCREMENT, idutilisateur INT, email_destinataire VARCHAR(255) NOT NULL, type ENUM('registration', 'order_confirmation', 'shipping', 'password_reset', 'admin_notification') DEFAULT 'order_confirmation', sujet VARCHAR(255) NOT NULL, contenu LONGTEXT, statut ENUM('sent', 'failed', 'pending') DEFAULT 'pending', date_envoi TIMESTAMP, date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (idutilisateur) REFERENCES utilisateur(idutilisateur) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

REM Créer la table modification_log
echo Creation table modification_log...
mysql -u root form_db -e "CREATE TABLE IF NOT EXISTS modification_log (idlog INT PRIMARY KEY AUTO_INCREMENT, idutilisateur INT, type ENUM('produit', 'commande', 'utilisateur') NOT NULL, action ENUM('create', 'update', 'delete', 'archive') NOT NULL, id_cible INT, ancien_contenu TEXT, nouveau_contenu TEXT, description VARCHAR(255), date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (idutilisateur) REFERENCES utilisateur(idutilisateur) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"

REM Vérification
echo.
echo Verification des tables creees...
mysql -u root form_db -e "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'form_db' ORDER BY TABLE_NAME;"

echo.
echo Migrations terminees!
pause
