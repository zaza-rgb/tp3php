<?php
 require 'liaisonbd.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $mdp   = trim($_POST['mdp']);
    session_start();
    $sql = "SELECT * FROM utilisateur WHERE email = ?";
    $stmt = $com->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

     if ($user && $mdp === $user['password']) {
        echo "Connexion réussie";
        $_SESSION['ref_uti']=$user['ref_uti'];
    } else {
        echo "Email ou mot de passe incorrect";
    }
}
?>