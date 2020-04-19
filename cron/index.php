<?php

/**
* @file index.php
* @brief Fonctions utilisées pour mettre à jour périodiquement les données des tables à travers l'utilisation d'un cron
*/

declare(strict_types = 1); // On définit le mode strict

/* Code utilisé uniquement pour le débug, à supprimer en production */
error_reporting(E_ALL);
ini_set('display_errors','1');
/* Fin du code utilisé uniquement pour le débug, à supprimer en production */

/*
* Permet au cronjob d'exécuter la fonction "majTables" si l'utilisateur appelle l'élément "majTables" en argument de la ligne de commande
*/
if(!empty($argv[1])){
	switch($argv[1]){
		case "majTables":
		$start = microtime(true);
		include("./../db_traitements.php");
		$end = microtime(true);
		echo "Script exécuté en : " .$end-$start. " secondes.";
		break;
	}
}
?>