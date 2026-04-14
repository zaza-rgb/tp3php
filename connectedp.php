
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="css/footer.css">
    
</head>
<body>
    <?php
    include("./include/header.php")
    ?>
    <section class="signup">
    <form method="post" action="connexion.php">
    <div class="left">
        
        <h2><span>C</span>onnexion</h2>
        <img src="image/monimage.jpg" alt="">
       
    </div>
    <div class="right">
        
            <div class="formItem">
                <label for="email">Email</label>
                <br>
                <input type="email"name="email" placeholder="Email">

            </div>
            <div class="formItem">
                <label for="mdp">Password</label>
                <br>
                <input type="text"name="mdp" placeholder="Mot de passe">

            </div>
        
            <input type="submit" class="bouton" value="Se connecter">

            <h5 class="signorlog">
            Pas encore de compte ? <a href="inscription.php">S'inscrire</a>
            </h5>
    </div>
    </form>
</section>
 <?php
    include("./include/footer.php")
    ?>
   
</body>
</html>