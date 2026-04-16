<?php
/**
 * Middleware d'authentification
 * À inclure en début de page pour vérifier les accès
 */

require_once 'liaisonbd.php';
require_once 'classes/Auth.php';

// Configuration du middleware
$REQUIRE_LOGIN = false;      // Exiger une connexion
$REQUIRE_ADMIN = false;      // Exiger un accès admin
$ALLOW_GUESTS = true;        // Autoriser les visiteurs non connectés

// Initialiser Auth
$auth = Auth::getInstance($com);

// Vérifier les exigences
if ($REQUIRE_LOGIN && !$auth->isLoggedIn()) {
    header("Location: connectedp.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

if ($REQUIRE_ADMIN && !$auth->isAdmin()) {
    header("Location: accueil.php?error=unauthorized");
    exit();
}

// Récupérer l'utilisateur connecté si disponible
$current_user = $auth->isLoggedIn() ? $auth->getCurrentUser() : null;

?>
