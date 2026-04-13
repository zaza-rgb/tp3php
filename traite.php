<?php
/*
if($_SERVER["REQUEST_METHOD"]=='POST'){
    
echo $_POST["nom"]."<br>";
echo $_POST["prenom"]."<br>";
echo $_POST["email"]."<br>";
echo $_POST["mdp"]."<br>";


}
*/

    
   
        
        if(isset($_POST["envoyer"])){
    echo $_POST["nom"]."</br>";
    echo $_POST["email"]."<br>";
    echo $_POST["prenom"]."<br>";
    echo $_POST["mdp"]."<br>";
   
        }
           
        if(isset($_POST["envoyer"])){
    echo $_POST["nom"]."</br>";
    echo $_POST["email"]."<br>";
    echo $_POST["prenom"]."<br>";
    echo $_POST["mdp"]."<br>";
   
/*  <?php
    include("./include/header.php")
    ?>
         <?php
    include("./include/footer.php")
    ?>
*/        }
?>
