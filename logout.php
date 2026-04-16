<?php
/**
 * Déconnexion utilisateur
 * Détruit la session et redirige vers l'accueil
 */

require 'liaisonbd.php';
require 'classes/Auth.php';

try {
    $auth = Auth::getInstance($com);
    $result = $auth->logout();
    
    // Rediriger vers l'accueil
    header("Location: accueil.php?logout=1");
    exit();
} catch (Exception $e) {
    die("Erreur lors de la déconnexion: " . htmlspecialchars($e->getMessage()));
}

?>
