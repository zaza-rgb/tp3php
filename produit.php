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
    <form method="post" action="traitementProd.php" enctype="multipart/form-data">
    <div class="container">
            <h2>ajouter un produit</h2>
            <input type="text" name="nomp" placeholder="nom du produit" required>
            <input type="number" name="prix" placeholder="prix" required>
            <input type="file" name="image" required>

            <textarea type="text" name="descrip" placeholder="Description"></textarea>
            <input type="submit" class="bouton">
    
    </div>
    </form>
</body>
</html>