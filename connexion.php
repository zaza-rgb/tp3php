<?php
 require 'liaisonbd.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $mdp   = trim($_POST['mdp']);

    $sql = "SELECT * FROM utilisateur WHERE email = ?";
    $stmt = $com->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

     if ($user && $mdp === $user['password']) {
        echo "Connexion réussie";
    } else {
        echo "Email ou mot de passe incorrect";
    }
}
?>