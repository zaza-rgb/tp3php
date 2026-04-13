<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/couvert.css">
</head>
<body>
    <?php
    include("./include/header.php")
    ?>
    <div class="container">
        <form action="">
            <h2>ajouter un produit</h2>
            <input type="text" name="nomp" placeholder="nom du produit" >
            <input type="number" name="prix" placeholder="prix">
            <input type="number" name="qte" placeholder="quantité">

            <textarea placeholder="Description"></textarea>
            <input type="submit" class="bouton">
        </form>
    </div>
</body>
</html>