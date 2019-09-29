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

if ($_POST['action'] == "nbRes"){

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
}else { // On retourne null si aucun élément n'est trouvé
	$jsonData[] = null;
	echo json_encode($jsonData);
	mysqli_free_result($result); // On libère la variable utilisée pour récupérer le résultat de la requête SQL
	mysqli_close($connexion); // On ferme la connexion à la base de données
	}
}else if($_POST['action'] == "annee"){

	$stmt = $connexion->prepare("SELECT RIGHT(Date_Attribution,4) AS annee, COUNT(EZABPQM) AS nbRes FROM CONCATENATION GROUP BY RIGHT(Date_Attribution,4) ORDER BY RIGHT(Date_Attribution,4)");
	$stmt->execute();

	$result = $stmt->get_result();

	$data = array();
	foreach ($result as $row) {
		$data[] = $row;
	}

	mysqli_free_result($result);
	mysqli_close($connexion);

	echo json_encode($data);
}else if($_POST['action'] == "mois"){

	$stmt = $connexion->prepare("SELECT LEFT(RIGHT(Date_Attribution,7),2) AS mois, COUNT(EZABPQM) AS nbRes FROM CONCATENATION GROUP BY LEFT(RIGHT(Date_Attribution,7),2) ORDER BY LEFT(RIGHT(Date_Attribution,7),2) ASC");
	$stmt->execute();

	$result = $stmt->get_result();

	$data = array();
	foreach ($result as $row) {
		$data[] = $row;
	}

	mysqli_free_result($result);
	mysqli_close($connexion);

	echo json_encode($data);
}else if($_POST['action'] == "derniersMois"){

	$stmt = $connexion->prepare("SELECT RIGHT(Date_Attribution,4)*100+LEFT(RIGHT(Date_Attribution,7),2) AS mois, COUNT(EZABPQM) AS nbRes FROM CONCATENATION WHERE str_to_date(Date_Attribution, '%d/%m/%Y') > DATE_SUB(now(), INTERVAL 13 MONTH) GROUP BY RIGHT(Date_Attribution,4)*100+LEFT(RIGHT(Date_Attribution,7),2) ORDER BY RIGHT(Date_Attribution,4)*100+LEFT(RIGHT(Date_Attribution,7),2) ASC LIMIT 12");
	$stmt->execute();

	$result = $stmt->get_result();

	$data = array();
	foreach ($result as $row) {
		$data[] = $row;
	}

	mysqli_free_result($result);
	mysqli_close($connexion);

	echo json_encode($data);
}else if($_POST['action'] == "Z"){

	$stmt = $connexion->prepare("SELECT right(left(EZABPQM,2),1) AS Z, COUNT(EZABPQM) AS nbRes FROM CONCATENATION WHERE CHAR_LENGTH(Tranche_Debut)=10 GROUP BY right(left(EZABPQM,2),1) ORDER BY right(left(EZABPQM,2),1) ASC");
	$stmt->execute();

	$result = $stmt->get_result();

	$data = array();
	foreach ($result as $row) {
		$data[] = $row;
	}

	mysqli_free_result($result);
	mysqli_close($connexion);

	echo json_encode($data);
}else if($_POST['action'] == "ZNE"){

	$stmt = $connexion->prepare("SELECT SUBSTR(Territoire,5) AS ZNE, COUNT(EZABPQM) AS nbRes FROM CONCATENATION WHERE LEFT(Territoire,3)='ZNE' GROUP BY Territoire ORDER BY COUNT(EZABPQM) DESC");
	$stmt->execute();

	$result = $stmt->get_result();

	$data = array();
	foreach ($result as $row) {
		$data[] = $row;
	}

	mysqli_free_result($result);
	mysqli_close($connexion);

	echo json_encode($data);
}
?>