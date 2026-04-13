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