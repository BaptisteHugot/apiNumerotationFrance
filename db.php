<?php
/**
 * Fichier de configuration de la base de données
 */

$host = "";
$username = "";
$password = "";
$database = "";

$connexion = mysqli_connect($host,$username,$password,$database);

if(mysqli_connect_errno()){
	echo "Impossible de se connecter à MySQL : " . mysqli_connect_error();
	die();
} else mysqli_set_charset($connexion, "utf8"); // On force le typage des données en UTF-8 pour le résultat JSON futur

?>