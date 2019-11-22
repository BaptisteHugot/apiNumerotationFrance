<?php

/**
* @file data.php
* @brief Fichier qui récupère les données à afficher
*/

/* Code utilisé uniquement pour le débug, à supprimer en production */
error_reporting(E_ALL);
ini_set('display_errors',1);
/* Fin du code utilisé uniquement pour le débug, à supprimer en production */

header('Content-Type: application/json');

include "./../../db.php";

if ($_POST['action'] == "nbRes"){ // Nombre de ressources attribuées par opérateur

	$stmt = $connexion->prepare("SELECT Identite_Operateur AS operateur, COUNT(EZABPQM) AS nbRes FROM CONCATENATION GROUP BY Identite_Operateur ORDER BY COUNT(EZABPQM) DESC"); // Requête SQL à exécuter
	$stmt->execute();

	$result = $stmt->get_result();

	if($result == false){ // On arrête le programme si l'exécution de la requête a rencontré un problème
		mysqli_free_result($result); // On libère la variable utilisée pour récupérer le résultat de la requête SQL
		mysqli_close($connexion); // On ferme la connexion à la base de données
		throw new Exception(mysqli_error($connexion));
	}else if(mysqli_num_rows($result) > 0){ // Si au moins un élément est trouvé
		while($array = mysqli_fetch_assoc($result)){ // On stocke chaque ligne de la base de données dans une ligne d'un tableau PHP
			$data[] = $array;
		}

		echo stripslashes(json_encode($data, JSON_UNESCAPED_UNICODE));

		mysqli_free_result($result);
		mysqli_close($connexion);
	}
	else { // On retourne null si aucun élément n'est trouvé
		$jsonData[] = null;
		echo json_encode($jsonData);
		mysqli_free_result($result); // On libère la variable utilisée pour récupérer le résultat de la requête SQL
		mysqli_close($connexion); // On ferme la connexion à la base de données
	}
}else if($_POST['action'] == "annee"){ // Nombre de ressources attribuées par an

	$stmt = $connexion->prepare("SELECT YEAR(Date_Attribution) AS annee, SUM(Nb_Attributions) AS nbRes FROM CONCATENATION_DATE GROUP BY YEAR(Date_Attribution) ORDER BY YEAR(Date_Attribution)");
	$stmt->execute();

	$result = $stmt->get_result();

	if($result == false){
		mysqli_free_result($result);
		mysqli_close($connexion);
		throw new Exception(mysqli_error($connexion));
	}else if(mysqli_num_rows($result) > 0){
		while($array = mysqli_fetch_assoc($result)){
			$data[] = $array;
		}

		echo stripslashes(json_encode($data, JSON_UNESCAPED_UNICODE));

		mysqli_free_result($result);
		mysqli_close($connexion);
	}
	else {
		$jsonData[] = null;
		echo json_encode($jsonData);
		mysqli_free_result($result);
		mysqli_close($connexion);
	}
}else if($_POST['action'] == "cumulAn"){ // Nombre de ressources attribuées par an (cumulées)

	$stmt = $connexion->prepare("WITH data AS (SELECT LEFT(Date_Attribution_MEF,4) AS date, sum(Nb_Attributions) AS nbAttribution FROM CONCATENATION_DATE GROUP BY LEFT(Date_Attribution_MEF,4)) SELECT date AS annee, nbAttribution, sum(nbAttribution) OVER (ORDER BY date ASC) AS nbRes FROM data");
	$stmt->execute();

	$result = $stmt->get_result();

	if($result == false){
		mysqli_free_result($result);
		mysqli_close($connexion);
		throw new Exception(mysqli_error($connexion));
	}else if(mysqli_num_rows($result) > 0){
		while($array = mysqli_fetch_assoc($result)){
			$data[] = $array;
		}

		echo stripslashes(json_encode($data, JSON_UNESCAPED_UNICODE));

		mysqli_free_result($result);
		mysqli_close($connexion);
	}
	else {
		$jsonData[] = null;
		echo json_encode($jsonData);
		mysqli_free_result($result);
		mysqli_close($connexion);
	}
}else if($_POST['action'] == "mois"){ // Nombre de ressources attribuées par mois

	$stmt = $connexion->prepare("SELECT MONTH(Date_Attribution) AS mois, SUM(Nb_Attributions) AS nbRes FROM CONCATENATION_DATE GROUP BY MONTH(Date_Attribution) ORDER BY MONTH(Date_Attribution) ASC");
	$stmt->execute();

	$result = $stmt->get_result();

	if($result == false){
		mysqli_free_result($result);
		mysqli_close($connexion);
		throw new Exception(mysqli_error($connexion));
	}else if(mysqli_num_rows($result) > 0){
		while($array = mysqli_fetch_assoc($result)){
			$data[] = $array;
		}

		echo stripslashes(json_encode($data, JSON_UNESCAPED_UNICODE));

		mysqli_free_result($result);
		mysqli_close($connexion);
	}
	else {
		$jsonData[] = null;
		echo json_encode($jsonData);
		mysqli_free_result($result);
		mysqli_close($connexion);
	}
}else if($_POST['action'] == "cumulMois"){ // Nombre de ressources attribuées par mois (cumulées)

	$stmt = $connexion->prepare("WITH data AS (SELECT LEFT(Date_Attribution_MEF,6) AS date, SUM(Nb_Attributions) AS nbAttribution FROM CONCATENATION_DATE GROUP BY LEFT(Date_Attribution_MEF,6)) SELECT date AS mois, nbAttribution, sum(nbAttribution) OVER (ORDER BY date ASC) AS nbRes FROM data");
	$stmt->execute();

	$result = $stmt->get_result();

	if($result == false){
		mysqli_free_result($result);
		mysqli_close($connexion);
		throw new Exception(mysqli_error($connexion));
	}else if(mysqli_num_rows($result) > 0){
		while($array = mysqli_fetch_assoc($result)){
			$data[] = $array;
		}

		echo stripslashes(json_encode($data, JSON_UNESCAPED_UNICODE));

		mysqli_free_result($result);
		mysqli_close($connexion);
	}
	else {
		$jsonData[] = null;
		echo json_encode($jsonData);
		mysqli_free_result($result);
		mysqli_close($connexion);
	}
}else if($_POST['action'] == "derniersMois"){ // Nombre de ressources attribuées sur les 12 derniers mois

	$stmt = $connexion->prepare("SELECT LEFT(Date_Attribution_MEF,6) AS mois, SUM(Nb_Attributions) AS nbRes FROM CONCATENATION_DATE WHERE Date_Attribution > DATE_SUB(now(), INTERVAL 13 MONTH) GROUP BY LEFT(Date_Attribution_MEF,6) ORDER BY LEFT(Date_Attribution_MEF,6) ASC LIMIT 12");
	$stmt->execute();

	$result = $stmt->get_result();

	if($result == false){
		mysqli_free_result($result);
		mysqli_close($connexion);
		throw new Exception(mysqli_error($connexion));
	}else if(mysqli_num_rows($result) > 0){
		while($array = mysqli_fetch_assoc($result)){
			$data[] = $array;
		}

		echo stripslashes(json_encode($data, JSON_UNESCAPED_UNICODE));

		mysqli_free_result($result);
		mysqli_close($connexion);
	}
	else {
		$jsonData[] = null;
		echo json_encode($jsonData);
		mysqli_free_result($result);
		mysqli_close($connexion);
	}
}else if($_POST['action'] == "Z"){ // Nombre de ressources attribuées par Z

	$stmt = $connexion->prepare("SELECT right(left(EZABPQM,2),1) AS Z, COUNT(EZABPQM) AS nbRes FROM CONCATENATION WHERE CHAR_LENGTH(Tranche_Debut)=10 GROUP BY right(left(EZABPQM,2),1) ORDER BY right(left(EZABPQM,2),1) ASC");
	$stmt->execute();

	$result = $stmt->get_result();

	if($result == false){
		mysqli_free_result($result);
		mysqli_close($connexion);
		throw new Exception(mysqli_error($connexion));
	}else if(mysqli_num_rows($result) > 0){
		while($array = mysqli_fetch_assoc($result)){
			$data[] = $array;
		}

		echo stripslashes(json_encode($data, JSON_UNESCAPED_UNICODE));

		mysqli_free_result($result);
		mysqli_close($connexion);
	}
	else {
		$jsonData[] = null;
		echo json_encode($jsonData);
		mysqli_free_result($result);
		mysqli_close($connexion);
	}
}else if($_POST['action'] == "ZNE"){ // Nombre de ressources attribuées par ZNE

	$stmt = $connexion->prepare("SELECT SUBSTR(Territoire,5) AS ZNE, COUNT(EZABPQM) AS nbRes FROM CONCATENATION WHERE LEFT(Territoire,3)='ZNE' GROUP BY Territoire ORDER BY COUNT(EZABPQM) DESC");
	$stmt->execute();

	$result = $stmt->get_result();

	if($result == false){
		mysqli_free_result($result);
		mysqli_close($connexion);
		throw new Exception(mysqli_error($connexion));
	}else if(mysqli_num_rows($result) > 0){
		while($array = mysqli_fetch_assoc($result)){
			$data[] = $array;
		}

		echo stripslashes(json_encode($data, JSON_UNESCAPED_UNICODE));

		mysqli_free_result($result);
		mysqli_close($connexion);
	}
	else {
		$jsonData[] = null;
		echo json_encode($jsonData);
		mysqli_free_result($result);
		mysqli_close($connexion);
	}
}
?>
