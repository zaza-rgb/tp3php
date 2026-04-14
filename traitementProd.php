<?php
require 'liaisonbd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomprod = $_POST['nomp'];
    $prix = $_POST['prix'];
    $typrod = $_POST['descrip'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "image/";
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO produit (nomprod, prix, typrod, image)
                    VALUES ('$nomprod', '$prix', '$typrod', '$image')";
            if ($com->exec($sql)) {
                echo "Produit ajouté avec succès !";
            } else {
                echo "Erreur lors de l'insertion.";
            }
        } else {
            echo "Erreur lors de l'upload de l'image.";
        }
    } else {
        echo "Vous devez obligatoirement choisir une image pour ce produit.";
    }
}
?>
