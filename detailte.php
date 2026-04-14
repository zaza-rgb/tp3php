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
    include("./include/header.php")
    ?>
    <section class="detail">
        <div class="horizontal">
            <div class="image">
                <img src="image/téléchargement (3).jpeg" alt="" height="100%">

            </div>
                <div class="vertical">
                    <div class="top">
                        <h2 class="productname">
                            pC
                        </h2>
                        <h4 class="prix">
                            120.000fcfa
                        </h4>
                        <p class="description">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Architecto illum esse voluptas accusantium. A explicabo repellendus, velit excepturi quaerat dolores perspiciatis corrupti ullam aliquam quibusdam reiciendis. Architecto cumque totam unde.</p>
                    </div>
                </div>
                <div class="bouton">
                    <a href=""><button class="acheter">acheter </button></a>
                    <a href="panier.php"><button class="ajouter">ajouter au panier</button></a>
                    
                </div>
            
        </div>
    </section>
     <?php
    include("./include/footer.php")
    ?>
</body>
</html>