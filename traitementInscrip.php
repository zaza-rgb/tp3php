<?php
require 'liaisonbd.php';

if ($_POST['mdp'] !== $_POST['mdpc']) {
    die("Les mots de passe ne correspondent pas !");
}else{
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];

    $sql = "INSERT INTO utilisateur (nom, prenom, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $com->prepare($sql);
    $stmt->execute([$nom, $prenom, $email, $mdp]);

    echo "Inscription réussie !";
    }

?>


