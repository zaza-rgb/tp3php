<?php
/**
 * Script d'exécution des migrations via PDO
 * Crée les tables manquantes et ajoute les champs nécessaires
 */

require_once 'liaisonbd.php';

// Liste des requêtes SQL à exécuter
$queries = array(
    // 1. Ajouter des champs à la table utilisateur
    "ALTER TABLE utilisateur ADD COLUMN IF NOT EXISTS email_verified BOOLEAN DEFAULT FALSE",
    "ALTER TABLE utilisateur ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    
    // 2. Ajouter des champs à la table produit
    "ALTER TABLE produit ADD COLUMN IF NOT EXISTS couleur VARCHAR(100) DEFAULT 'Défaut'",
    "ALTER TABLE produit ADD COLUMN IF NOT EXISTS categorie VARCHAR(100)",
    "ALTER TABLE produit ADD COLUMN IF NOT EXISTS is_archived BOOLEAN DEFAULT FALSE",
    "ALTER TABLE produit ADD COLUMN IF NOT EXISTS date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    
    // 3. Créer la table commande
    "CREATE TABLE IF NOT EXISTS commande (
        idcommande INT PRIMARY KEY AUTO_INCREMENT,
        ref_uti INT NOT NULL,
        montant_total DECIMAL(10, 2) NOT NULL,
        statut ENUM('pending', 'paid', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        adresse_livraison VARCHAR(255),
        telephone VARCHAR(20),
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        notes TEXT,
        FOREIGN KEY (ref_uti) REFERENCES utilisateur(ref_uti) ON DELETE CASCADE,
        KEY idx_utilisateur (ref_uti)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    // 4. Créer la table commande_produit
    "CREATE TABLE IF NOT EXISTS commande_produit (
        idcommande_produit INT PRIMARY KEY AUTO_INCREMENT,
        idcommande INT NOT NULL,
        idprod INT NOT NULL,
        quantite INT NOT NULL DEFAULT 1,
        prix_unitaire DECIMAL(10, 2) NOT NULL,
        couleur VARCHAR(100),
        date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (idcommande) REFERENCES commande(idcommande) ON DELETE CASCADE,
        FOREIGN KEY (idprod) REFERENCES produit(idprod) ON DELETE CASCADE,
        KEY idx_commande (idcommande),
        KEY idx_produit (idprod)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    // 5. Créer la table panier
    "CREATE TABLE IF NOT EXISTS panier (
        idpanier INT PRIMARY KEY AUTO_INCREMENT,
        ref_uti INT NOT NULL,
        idprod INT NOT NULL,
        quantite INT NOT NULL DEFAULT 1,
        couleur VARCHAR(100),
        date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_product (ref_uti, idprod, couleur),
        FOREIGN KEY (ref_uti) REFERENCES utilisateur(ref_uti) ON DELETE CASCADE,
        FOREIGN KEY (idprod) REFERENCES produit(idprod) ON DELETE CASCADE,
        KEY idx_utilisateur (ref_uti),
        KEY idx_produit (idprod)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    // 6. Créer la table couleur_produit
    "CREATE TABLE IF NOT EXISTS couleur_produit (
        idcouleur INT PRIMARY KEY AUTO_INCREMENT,
        idprod INT NOT NULL,
        couleur VARCHAR(50) NOT NULL,
        stock INT DEFAULT 0,
        date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (idprod) REFERENCES produit(idprod) ON DELETE CASCADE,
        UNIQUE KEY unique_product_color (idprod, couleur),
        KEY idx_produit (idprod)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    // 7. Créer la table notification_email
    "CREATE TABLE IF NOT EXISTS notification_email (
        idnotification INT PRIMARY KEY AUTO_INCREMENT,
        ref_uti INT,
        email_destinataire VARCHAR(255) NOT NULL,
        type ENUM('registration', 'order_confirmation', 'shipping', 'password_reset', 'admin_notification') DEFAULT 'order_confirmation',
        sujet VARCHAR(255) NOT NULL,
        contenu LONGTEXT,
        statut ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
        date_envoi TIMESTAMP NULL,
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ref_uti) REFERENCES utilisateur(ref_uti) ON DELETE SET NULL,
        KEY idx_utilisateur (ref_uti),
        KEY idx_statut (statut)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    // 8. Créer la table modification_log
    "CREATE TABLE IF NOT EXISTS modification_log (
        idlog INT PRIMARY KEY AUTO_INCREMENT,
        ref_uti INT,
        type ENUM('produit', 'commande', 'utilisateur') NOT NULL,
        action ENUM('create', 'update', 'delete', 'archive') NOT NULL,
        id_cible INT,
        ancien_contenu TEXT,
        nouveau_contenu TEXT,
        description VARCHAR(255),
        date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ref_uti) REFERENCES utilisateur(ref_uti) ON DELETE SET NULL,
        KEY idx_utilisateur (ref_uti),
        KEY idx_date (date_modification)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);

// Affichage HTML
echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <title>Migrations SQL</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .table-list { margin-top: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
    </style>
</head>
<body>
    <h1>🔧 Exécution des Migrations SQL</h1>";

$successCount = 0;
$errorCount = 0;
$errors = [];

foreach ($queries as $index => $query) {
    if (trim($query)) {
        try {
            $com->exec($query);
            $successCount++;
            echo "<p class='success'>✓ Requête " . ($index + 1) . " exécutée avec succès</p>";
        } catch (PDOException $e) {
            // Si table existe déjà, ce n'est pas une erreur
            if (strpos($e->getMessage(), 'already exists') !== false || 
                strpos($e->getMessage(), 'Duplicate column name') !== false) {
                $successCount++;
                echo "<p class='warning'>⚠ Requête " . ($index + 1) . " : " . substr($e->getMessage(), 0, 80) . "...</p>";
            } else {
                $errorCount++;
                $errors[] = $e->getMessage();
                echo "<p class='error'>✗ Requête " . ($index + 1) . " : " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    }
}

echo "<hr>";
echo "<h3>📊 Résumé des migrations:</h3>";
echo "<p class='success'>✓ Requêtes réussies: <strong>" . $successCount . "</strong></p>";
echo "<p class='error'>✗ Erreurs: <strong>" . $errorCount . "</strong></p>";

// Vérifier les tables créées
echo "<h3>📋 Tables actuelles dans la base de données:</h3>";

try {
    $stmt = $com->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'form_db' ORDER BY TABLE_NAME");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<div class='table-list'>";
    echo "<table>";
    echo "<tr><th>Numéro</th><th>Nom de la Table</th></tr>";
    
    foreach ($tables as $index => $table) {
        echo "<tr>";
        echo "<td>" . ($index + 1) . "</td>";
        echo "<td><strong>" . htmlspecialchars($table) . "</strong></td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "</div>";
    
    echo "<p><strong>Total: " . count($tables) . " tables</strong></p>";
} catch (Exception $e) {
    echo "<p class='error'>Erreur lors de la lecture des tables: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Afficher les détails des principales tables
echo "<h3>📐 Structure des Tables Principales:</h3>";

$tables_to_check = ['utilisateur', 'produit', 'commande', 'panier', 'notification_email'];

foreach ($tables_to_check as $table_name) {
    try {
        $stmt = $com->query("DESCRIBE " . $table_name);
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($columns) > 0) {
            echo "<h4>" . ucfirst($table_name) . ":</h4>";
            echo "<table>";
            echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Par Défaut</th></tr>";
            
            foreach ($columns as $col) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($col['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($col['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($col['Default'] ?? '-') . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        }
    } catch (PDOException $e) {
        echo "<p class='warning'>Table '" . htmlspecialchars($table_name) . "' n'existe pas encore.</p>";
    }
}

echo "
</body>
</html>";
?>
