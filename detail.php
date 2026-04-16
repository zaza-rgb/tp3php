<?php
require 'liaisonbd.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['idprod'])){
        $id = $_POST['idprod'];
        $stmt = $com->prepare("SELECT * FROM produit WHERE idprod = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $image = htmlspecialchars($row['image']);
            $nomprod = htmlspecialchars($row['nomprod']);
            $price = htmlspecialchars($row['prix']);
            $typeprode = htmlspecialchars($row['typrod']);
        } else {
            echo "Produit introuvable.";
        }
    }
    ?>