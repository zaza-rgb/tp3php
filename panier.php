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
    include("./include/header.php")
    ?>
    <section class="articles">
        <div class="article">
            <div class="left">
                <div class="image"><img src="image/airpods.jpeg" alt=""height="100%"></div>

                <div class="vertical">
                    <h5 class="productname">Airpods</h5>
                    <h6 class="prix">3500 fcfa</h6>
                </div>
            </div>
            <div class="right">
                <input type="number" class="large">
                <input type="button" value="supprimer">
            </div>
        </div>


        <div class="article">
            <div class="left">
                <div class="image"><img src="image/casque.jpeg" alt="" height="100%"></div>

                <div class="vertical">
                    <h5 class="productname">Casque</h5>
                    <h6 class="prix">4000 fcfa</h6>
                </div>
            </div>
            <div class="right">
                <input type="number">
                <input type="button" value="supprimer">
            </div>
        </div>


        <div class="article">
            <div class="left">
                <div class="image"><img src="image/ordinateur.png" alt="" height="100%"></div>

                <div class="vertical">
                    <h5 class="productname">PC</h5>
                    <h6 class="prix">126.000 fcfa</h6>
                </div>
            </div>
            <div class="right">
                <input type="number">
                <input type="button" value="supprimer">
            </div>
        </div>


        <div class="article">
            <div class="left">
                <div class="image"><img src="image/souris.jpeg" alt="" height="100%"></div>

                <div class="vertical">
                    <h5 class="productname">sourist</h5>
                    <h6 class="prix">4000 fcfa</h6>
                </div>
            </div>
            <div class="right">
                <input type="number">
                <input type="button" value="supprimer">
            </div>
        </div>


        <div class="article">
            <div class="left">
                <div class="image"><img src="image/téléchargement (3).jpeg" alt="" height="100%"></div>

                <div class="vertical">
                    <h5 class="productname">Pc</h5>
                    <h6 class="prix">120.000 fcfa</h6>
                </div>
            </div>
            <div class="right">
                <input type="number">
                <input type="button" value="supprimer">
            </div>
        </div>
    </section>
     <?php
    include("./include/footer.php")
    ?>
</body>
</html>