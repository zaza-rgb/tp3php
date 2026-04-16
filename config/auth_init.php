<?php
/**
 * Fichier d'initialisation de l'authentification
 * À inclure en début de chaque page qui nécessite l'auth
 */

require_once 'liaisonbd.php';
require_once 'classes/Auth.php';

// Initialiser Auth
$auth = Auth::getInstance($com);

?>
