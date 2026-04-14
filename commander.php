<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits</title>
    <link rel="stylesheet" href="css/commander.css">
</head>
<body>
    <?php include("./include/header.php"); ?>

    <section class="sgrille">
        <div class="grille">
            <?php
            // Liaison à la base
            require 'liaisonbd.php';

            try {
                $stmt = $com->query("SELECT * FROM produit");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $image=htmlspecialchars($row['image']);
                    $nomprod=htmlspecialchars($row['nomprod']);
                    $typeprode=htmlspecialchars($row['typrod']);
                    $prix=htmlspecialchars($row['prix']);
                    ?>
                    <form method="post" action="detailpc.php">
                        <div class="product">
                            <div class="image">
                            <a href="detailpc.php"> <img src="image/<?php echo htmlspecialchars($row['image']); ?>" alt="" width="100%"></a>
                            </div>
                            <div class="textes">
                                <h4 class="productname"><?php echo htmlspecialchars($row['nomprod']); ?></h4>
                                <p class="description"><?php echo htmlspecialchars($row['typrod']); ?></p>
                                <h5 class="prix"><?php echo htmlspecialchars($row['prix']); ?> fcfa</h5>
                            </div>
                            <div class="bouton">
                                <button class="detail">details</button>
                            <button class="ajouter">ajouter au panier</button>
                            </div>
                        </div>
                    </form>
                    <?php
                }
            } catch (PDOException $e) {
                echo "Erreur : " . $e->getMessage();
            }
            ?>
        </div>
    </section>
</body>
</html>
