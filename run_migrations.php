<?php
/**
 * Script d'exécution des migrations SQL
 * À exécuter une fois lors de l'installation initiale
 */

session_start();
require_once 'liaisonbd.php';

try {
    // Lire le fichier SQL
    $sqlFile = file_get_contents(__DIR__ . '/migrations/002_create_missing_tables.sql');
    
    // Diviser les requêtes (séparées par ;)
    $queries = array_filter(
        array_map('trim', explode(';', $sqlFile)),
        function($query) {
            return !empty($query) && strpos($query, '--') !== 0;
        }
    );
    
    $successCount = 0;
    $errors = [];
    
    foreach ($queries as $query) {
        if (trim($query)) {
            try {
                $com->exec($query);
                $successCount++;
                echo "<div style='color:green;'>✓ Requête exécutée avec succès</div>";
            } catch (PDOException $e) {
                // Si table existe déjà, ce n'est pas une erreur
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    $successCount++;
                    echo "<div style='color:orange;'>⚠ Table déjà existante</div>";
                } else {
                    $errors[] = $e->getMessage();
                    echo "<div style='color:red;'>✗ Erreur: " . $e->getMessage() . "</div>";
                }
            }
        }
    }
    
    echo "<hr>";
    echo "<h3>Résumé des migrations:</h3>";
    echo "<p>✓ Requêtes réussies: <strong>" . $successCount . "</strong></p>";
    
    if (count($errors) > 0) {
        echo "<p>✗ Erreurs: <strong>" . count($errors) . "</strong></p>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:green;'><strong>Toutes les migrations ont été appliquées avec succès!</strong></p>";
    }
    
    // Vérifier les tables créées
    echo "<hr>";
    echo "<h3>Tables actuelles dans la base de données:</h3>";
    
    $stmt = $com->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'form_db' ORDER BY TABLE_NAME");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li><strong>" . htmlspecialchars($table) . "</strong></li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    die("<div style='color:red;'><h3>Erreur lors de l'exécution des migrations:</h3>" . htmlspecialchars($e->getMessage()) . "</div>");
}
?>
