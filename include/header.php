<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion </title>
    <link rel="stylesheet" href="css/header.css">
    
    


</head>
<script>
function toggleMenu() {
    document.querySelector(".list").classList.toggle("active");
}
</script>
<body>
    <nav>
        

         <!-- MENU HAMBURGER -->
    <div class="menu-toggle" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>

        <h1 class="logo">S<span>HOPALL</span></h1>
        
        <ul class="list">
       
       <li ><a href="accueil.php">Accueil</a></li>
        
         <li><a href="inscription.php">Inscription</a></li>
        <li><a href="connectedp.php">Connexion</a></li>
        <li><a href="produit.php">Produits</a></li>
        <li ><a href="commander.php">...</a></li>
        
        <li class="panier">
            <img src="image/panier.jpg" alt="panier" width="100%">
        </li>
    </ul>
    
    </nav>




</body>
</html>