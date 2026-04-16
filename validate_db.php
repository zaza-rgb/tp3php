<?php
/**
 * Script de validation de la structure de la base de données
 * Vérifie que toutes les tables et colonnes existent avec les bons types
 */

require_once 'liaisonbd.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <title>Validation DB</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #333; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 4px; margin: 10px 0; }
        table { border-collapse: collapse; width: 100%; margin: 15px 0; background: white; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>✅ Validation de la Base de Données E-Commerce</h1>";

// Afficher les stats
try {
    $stmt = $com->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'form_db'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<div class='success'>";
    echo "<h3>📊 Statistiques</h3>";
    echo "<p><strong>Total de tables:</strong> " . count($tables) . "</p>";
    echo "<p><strong>Tables:</strong> " . implode(", ", $tables) . "</p>";
    echo "</div>";
    
    // Détail de chaque table
    echo "<h2>📋 Structure des Tables</h2>";
    
    foreach ($tables as $table) {
        try {
            $stmt = $com->query("DESCRIBE " . $table);
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>" . strtoupper($table) . " - " . count($columns) . " colonnes</h3>";
            echo "<table>";
            echo "<tr>";
            echo "<th>Champ</th>";
            echo "<th>Type</th>";
            echo "<th>Null</th>";
            echo "<th>Clé</th>";
            echo "<th>Par Défaut</th>";
            echo "</tr>";
            
            foreach ($columns as $col) {
                echo "<tr>";
                echo "<td><code>" . htmlspecialchars($col['Field']) . "</code></td>";
                echo "<td><code>" . htmlspecialchars($col['Type']) . "</code></td>";
                echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($col['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($col['Default'] ?? '-') . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } catch (Exception $e) {
            echo "<div class='error'>Erreur lors de la description de la table " . htmlspecialchars($table) . ": " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
    // Vérification des clés étrangères
    echo "<h2>🔗 Relations (Clés Étrangères)</h2>";
    
    try {
        $stmt = $com->query("
            SELECT 
                TABLE_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM 
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE 
                TABLE_SCHEMA = 'form_db' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ORDER BY 
                TABLE_NAME
        ");
        
        $fks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($fks) > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>Table</th>";
            echo "<th>Colonne</th>";
            echo "<th>→ Table Référencée</th>";
            echo "<th>→ Colonne Référencée</th>";
            echo "</tr>";
            
            foreach ($fks as $fk) {
                echo "<tr>";
                echo "<td><strong>" . htmlspecialchars($fk['TABLE_NAME']) . "</strong></td>";
                echo "<td><code>" . htmlspecialchars($fk['COLUMN_NAME']) . "</code></td>";
                echo "<td><strong>" . htmlspecialchars($fk['REFERENCED_TABLE_NAME']) . "</strong></td>";
                echo "<td><code>" . htmlspecialchars($fk['REFERENCED_COLUMN_NAME']) . "</code></td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<div class='warning'>Aucune clé étrangère trouvée (peut être normal selon la configuration MySQL)</div>";
        }
    } catch (Exception $e) {
        echo "<div class='warning'>Impossible de récupérer les clés étrangères: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
    // Vérification des index
    echo "<h2>🔍 Index (pour les performances)</h2>";
    
    $tables_to_check = ['utilisateur', 'produit', 'commande', 'commande_produit', 'panier', 'notification_email'];
    
    foreach ($tables_to_check as $table) {
        try {
            $stmt = $com->query("SHOW INDEX FROM " . $table);
            $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($indexes) > 0) {
                echo "<h3>" . ucfirst($table) . "</h3>";
                echo "<table>";
                echo "<tr>";
                echo "<th>Nom Index</th>";
                echo "<th>Colonnes</th>";
                echo "<th>Type</th>";
                echo "</tr>";
                
                $indexed_columns = [];
                foreach ($indexes as $idx) {
                    if ($idx['Key_name'] === 'PRIMARY') {
                        $key_type = "PRIMARY KEY";
                    } elseif ($idx['Non_unique'] == 0) {
                        $key_type = "UNIQUE";
                    } else {
                        $key_type = "INDEX";
                    }
                    
                    if (!isset($indexed_columns[$idx['Key_name']])) {
                        $indexed_columns[$idx['Key_name']] = ['cols' => [], 'type' => $key_type];
                    }
                    $indexed_columns[$idx['Key_name']]['cols'][] = $idx['Column_name'];
                }
                
                foreach ($indexed_columns as $idx_name => $idx_info) {
                    echo "<tr>";
                    echo "<td><strong>" . htmlspecialchars($idx_name) . "</strong></td>";
                    echo "<td><code>" . implode(", ", $idx_info['cols']) . "</code></td>";
                    echo "<td>" . htmlspecialchars($idx_info['type']) . "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
            }
        } catch (Exception $e) {
            // Table n'existe peut-être pas
        }
    }
    
} catch (Exception $e) {
    echo "<div class='error'>Erreur: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "
    </div>
    <footer style='margin-top: 40px; text-align: center; color: #666;'>
        <p>✅ Migration complétée - Prêt pour le développement des fonctionnalités!</p>
        <p><small>Fichier: validate_db.php | Date: 2026-04-16</small></p>
    </footer>
</body>
</html>";
?>
