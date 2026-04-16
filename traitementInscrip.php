<?php
/**
 * Traitement de l'inscription utilisateur
 * Utilise la classe Auth pour le hashage et la validation
 */

require 'liaisonbd.php';
require 'classes/Auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Récupérer les données
$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$email = $_POST['email'] ?? '';
$mdp = $_POST['mdp'] ?? '';
$mdpc = $_POST['mdpc'] ?? '';

// Vérifier que les mots de passe correspondent
if ($mdp !== $mdpc) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas !']);
    exit();
}

// Initialiser Auth et enregistrer l'utilisateur
try {
    $auth = Auth::getInstance($com);
    $result = $auth->register($email, $nom, $prenom, $mdp);
    
    if ($result['success']) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => $result['message'],
            'redirect' => 'connectedp.php?success=1'
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $result['message']
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}

?>



