<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/detail.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
     <?php
    include("./include/header.php");
    require 'detail.php';

    ?>
    <section class="detail">
    <form method="post" action="panier.php">
        <input type="hidden" name="idprod" value="<?php echo $id; ?>">
        <input type="hidden" name="qte" value="1" min="1">
        <div class="horizontal">
            <div class="image">
                <img src="image/<?php echo $image ?>" alt="" height="100%">

            </div>
                <div class="vertical">
                    <div class="top">
                        <h2 class="productname">
                            <?php echo $nomprod ?>
                        </h2>
                        <h4 class="prix">
                            <?php echo $price ?>
                        </h4>
                        <p class="description"><?php echo $typeprode ?></p>
                    </div>
                </div>
                <div class="bouton">
                    <a href=""><button class="acheter">acheter </button></a>
                    <a href=""><button name="ajouter" class="ajouter" formaction="panier.php">ajouter au panier</button></a>
                    
                </div>
            
        </div>
    </form>
    </section>
     <?php
    include("./include/footer.php");
    ?>
</body>
</html>