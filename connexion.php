<?php
$host = 'localhost';
$user = 'root';
$database = 'form_db';
$password = '';


try{
$com= new PDO(
    "mysql:host=$host; dbname=$database",$user,$password
);
}catch(PDOException $a){
    die("erreur de connexion".$a->getMessage());
}


try{
$com= new PDO(
    "mysql:host=$host; dbname=$database",$user,$password
);
}catch(PDOException $a){
    die("erreur de connexion".$a->getMessage());
}
  /*<nav class="navbar">
    <div class="logo"> <h2>Gestion Commandes </h2> </div>

    <ul class="menu">
            <div class="la">
               <li> <a href="accueil.php"> Accueil</a></li>
            </div>


        
            <div class="la">
                <li class="dropdown">
                 <a href=""><i>Commandes</i></a> 
                    <ul class="submenu">
                        <li>Nouvelle</li>
                        <li>Liste des commandes</li>
                    </ul>
                </li>
            </div>


           
            <div class="la">
                <li><a href="parametres.html">  Se connecter</a></li>
            </div>


           
                <li class="logout"><a href="logout.html">Paramètres</i></a></li> 
           

    </ul>
   
    </nav>
    /*

?>
?>
