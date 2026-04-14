<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <?php
    include("./include/header.php")
    ?>
    <section class="signup">
    <form method="post" action="traitementInscrip.php">
    <div class="left">
        <h2><span>I</span>nscription</h2>
        <img src="image/monimage.jpg" alt="">
       
    </div>
    <div class="right">
        <div class="formItem">
            <label for="nom">Nom</label>
            <br>
            <input type="text"name="nom" placeholder="Nom" required>

        </div>
         <div class="formItem">
            <label for="prenom">Prénom</label>
            <br>
            <input type="text"name="prenom" placeholder="Prénom" required>

        </div>
         <div class="formItem">
            <label for="email">Email</label>
            <br>
            <input type="email"name="email" placeholder="Email" required>

        </div>
         <div class="formItem">
            <label for="mdp">Password</label>
            <br>
            <input type="text"name="mdp" placeholder="Mot de passe" required>

        </div>
         <div class="formItem">
            <label for="mdpc">Vérification</label>
            <br>
            <input type="text"name="mdpc" placeholder="Comfirmer le mot de passe" required>

        </div>

        
        <input type="submit" class="bouton" value="s'inscrire">

        <h5 class="signorlog">
            Déjà un compte ? <a href="connectedp.php">Se connecter</a>
        </h5>
    </div>
    </form>
    </section>
</body>
</html>