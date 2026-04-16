<?php
/**
 * Traitement de la connexion utilisateur
 * Utilise la classe Auth pour la vérification et les sessions
 */

require 'liaisonbd.php';
require 'classes/Auth.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

try {
    $email = trim($_POST['email'] ?? '');
    $mdp   = trim($_POST['mdp'] ?? '');
    
    if (empty($email) || empty($mdp)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis']);
        exit();
    }
    
    // Initialiser Auth et se connecter
    $auth = Auth::getInstance($com);
    $result = $auth->login($email, $mdp);
    
    if ($result['success']) {
        http_response_code(200);
        
        // Déterminer la redirection selon le type d'utilisateur
        $redirect = 'accueil.php';
        if ($result['user_type'] === 'admin') {
            $redirect = 'admin/dashboard.php';
        }
        
        // Redirection dans le URL de retour si spécifié
        $redirect_param = $_GET['redirect'] ?? '';
        if (!empty($redirect_param) && strpos($redirect_param, 'http') === false) {
            $redirect = $redirect_param;
        }
        
        echo json_encode([
            'success' => true,
            'message' => $result['message'],
            'user_type' => $result['user_type'],
            'redirect' => $redirect
        ]);
    } else {
        http_response_code(401);
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
