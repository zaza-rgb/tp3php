<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/panier.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <?php
    include("./include/header.php");
    require 'detail.php';

    session_start();
    $tota=0;
    if (!isset($_POST['vider']) && !isset($_POST['commander'])) {
    $id = $_POST['idprod'];
    $quantite = (int)$_POST['qte'];

    if (isset($_POST['ajouter'])) {
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
            }
        if (!isset($_SESSION['panier'][$id])) {
            $_SESSION['panier'][$id] = $quantite;
        }
            }else if (isset($_POST['modifier'])) { 
                $_SESSION['panier'][$id]= $quantite;
                }else if (isset($_POST['supprimer'])) {
                    unset($_SESSION['panier'][$id]);
                    }
    }else if (isset($_POST['vider'])) {
        unset($_SESSION['panier']);
        }else if (isset($_POST['commander'])) {
            if(isset($_SESSION['ref_uti'])){ 

                $ref_uti = $_SESSION['ref_uti'];
                $total=0;
                $date = date("Y-m-d H:i:s");
                foreach ($_SESSION['panier'] as $idprod => $qte) {

                    $sql = "SELECT prix FROM produit WHERE idprod = ?";
                    $stmt = $com->prepare($sql);
                    $stmt->execute([$idprod]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $total+=$row['prix']*$qte;
                    }
                $sql = "INSERT INTO commande (id_utilisateur, date_commande, total) VALUES (?, ?, ?)";
                $stmt = $com->prepare($sql);
                $stmt->execute([$ref_uti,$date, $total]);
                $lastId = $com->lastInsertId();

                foreach ($_SESSION['panier'] as $idprod => $qte) {

                    $sql = "SELECT prix FROM produit WHERE idprod = ?";
                    $stmt = $com->prepare($sql);
                    $stmt->execute([$idprod]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $price=$row['prix'];
                    $sql = "INSERT INTO commande_produit (id_commande,id_produit, quantite,prix_unitaire) VALUES (?, ?, ?, ?)";
                    $stmt = $com->prepare($sql);
                    $stmt->execute([$lastId, $idprod, $qte, $price]);

                    }
                echo "Achat effectuéé avec succes ,";
                unset($_SESSION['panier']);
                }else{
                    echo "Vous devez vous connecter avant d'effectuer un achat";
                    }}

    if (!empty($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as $idprod => $qte) {
            $sql = "SELECT * FROM produit WHERE idprod = ?";
            $stmt = $com->prepare($sql);
            $stmt->execute([$idprod]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
                    <section class="articles">
                <div class="article">
                    <div class="left">
                        <div class="image"><img src="image/<?php echo htmlspecialchars($row['image']) ?>" alt=""height="100%"></div>

                        <div class="vertical">
                            <h5 class="productname"><?php echo htmlspecialchars($row['nomprod']) ?></h5>
                            <h6 class="prix"><?php echo htmlspecialchars($row['prix']*$qte) ?></h6>
                            <?php $tot=$row['prix']*$qte;
                                  $tota+=$tot;
                            ?>
                        </div>
                    </div>
                    <div class="right">
                        <form method="post" action="panier.php" >
                        <input type="hidden" name="idprod" value="<?php echo $idprod; ?>">
                        <input type="number" name="qte" value="<?php echo $qte; ?>" min="1" class="large">
                        <button type="submit" name="modifier">Mise à jour</button>
                        <button type="submit" name="supprimer">Supprimer</button>
                        </form>
                    </div>
                </div>
                
            </section>
            <?php
        }
            ?>
            <form method="post" action="panier.php">
            <h6 class="prix">TOTAL= <?php echo $tota; ?> fcfa</h6>
            <button type="submit" name="vider">Vider le panier</button>
            <button type="submit" name="commander">commander</button>
            </form>

           <?php
    } else {
        echo "Panier vide.";
    }
    include("./include/footer.php")
    ?>
</body>
</html>