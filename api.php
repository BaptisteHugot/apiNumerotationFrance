<?php
/**
* @file api.php
* @brief Génère l'API pour rechercher le contenu d'un fichier spécifique, l'attributaire d'une tranche de numéros, d'un numéro complet ou d'un numéro court, les ressources attribuées à un opérateur ou bien les ressources attribuées avant et/ou après une date donnée
*/

/* Code utilisé uniquement pour le débug, à supprimer en production */
//error_reporting(E_ALL);
//ini_set('display_errors',1);
/* Fin du code utilisé uniquement pour le débug, à supprimer en production */

header("Content-Type:application/json");

/* Recherche de l'attributaire d'une tranche ou d'un début de tranche */
if(isset($_GET['TRANCHE']) && $_GET['TRANCHE'] != ""){
	$ezabpqm = $_GET['TRANCHE'];
	$ezabpqm = htmlspecialchars($ezabpqm, ENT_QUOTES, 'UTF-8'); // Pour éviter une injection XSS

	$regEx = "#^((3[0|1|2|4|9][0-9]{0,2})|(1[0|6][0-9]{0,2})|(118[0-9]{0,3})|(0[1-9][0-9]{0,5}))$#"; // Expression régulière d'une tranche de numéros

	if(preg_match($regEx, $ezabpqm)){ // Si la tranche entrée correspond à l'expression régulière
		include('db.php'); // On se connecte à la base de données

		$ezabpqm = $connexion->real_escape_string($ezabpqm); // Pour éviter une injection SQL
		$stmt = $connexion->prepare("SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep FROM CONCATENATION WHERE EZABPQM LIKE CONCAT(?,'%') ORDER BY EZABPQM"); // Requête SQL à exécuter
		$stmt->bind_param("s", $ezabpqm); // On vérifie que le type de variable est correct
		$stmt->execute();

		$result = $stmt->get_result();

		$jsonData = array();
		if($result == false){ // On arrête le programme si l'exécution de la requête a rencontré un problème
			mysqli_free_result($result); // On libère la variable utilisée pour récupérer le résultat de la requête SQL
			mysqli_close($connexion); // On ferme la connexion à la base de données
			throw new Exception(mysqli_error($connexion));
		}else if(mysqli_num_rows($result) > 0){ // Si au moins un élément est trouvé
			while($array = mysqli_fetch_assoc($result)){ // On stocke chaque ligne de la base de données dans une ligne d'un tableau PHP
				$jsonData[] = $array;
			}

			echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE)); // On affiche le résultat au format JSON
			mysqli_free_result($result); // On libère la variable utilisée pour récupérer le résultat de la requête SQL
			mysqli_close($connexion); // On ferme la connexion à la base de données
		}else { // On retourne null si aucun élément n'est trouvé
			$jsonData[] = null;
			echo json_encode($jsonData);
			mysqli_free_result($result); // On libère la variable utilisée pour récupérer le résultat de la requête SQL
			mysqli_close($connexion); // On ferme la connexion à la base de données
		}
	}else { // On retourne null si le format entré ne correspond pas à l'expression régulière
		$jsonData[] = null;
		echo json_encode($jsonData);
	}
}

/* Recherche des numéros attribués à un opérateur donné (via son code Arcep) */
if(isset($_GET['OPERATEUR']) && $_GET['OPERATEUR']!=""){
	$operateur = $_GET['OPERATEUR'];
	$operateur = htmlspecialchars($operateur, ENT_QUOTES, 'UTF-8');
	$operateur = strtoupper($operateur); // On met en majuscule les données entrées

	$regEx = "#^[A-Za-z0-9]{4,5}$#";

	if(preg_match($regEx, $operateur)){
		include('db.php');

		$operateur = $connexion->real_escape_string($operateur);
		$stmt = $connexion->prepare("SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep FROM CONCATENATION WHERE Code_Operateur = ? ORDER BY Date_Attribution_MEF");
		$stmt->bind_param("s", $operateur);
		$stmt->execute();

		$result = $stmt->get_result();

		$jsonData = array();
		if($result == false){
			mysqli_free_result($result);
			mysqli_close($connexion);
			throw new Exception(mysqli_error($connexion));
		}else if(mysqli_num_rows($result) > 0){
			while($array = mysqli_fetch_assoc($result)){
				$jsonData[] = $array;
			}
			echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE));
			mysqli_free_result($result);
			mysqli_close($connexion);
		}else {
			$jsonData[] = null;
			echo json_encode($jsonData);
			mysqli_free_result($result);
			mysqli_close($connexion);
		}
	}else{
		$jsonData[] = null;
		echo json_encode($jsonData);
	}
}

/* Recherche des attributions après une date donnée */
if(isset($_GET['DATESUP']) && $_GET['DATESUP'] != ""){
	$date = $_GET['DATESUP'];
	$date = htmlspecialchars($date, ENT_QUOTES, 'UTF-8');

	$regEx = "#^[0-9]{8}$#";

	if(preg_match($regEx, $date) && validateDate($date)){
		$date_mef = substr($date, 4, 4) * 10000 + substr($date, 2, 2) * 100 + substr($date, 0, 2); // On met en forme la date entrée pour pouvoir requêter plus facilement

		include('db.php');

		$date_mef = $connexion->real_escape_string($date_mef);
		$stmt = $connexion->prepare("SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep FROM CONCATENATION WHERE (CAST(Date_Attribution_MEF AS UNSIGNED) >= ?) ORDER BY Date_Attribution_MEF");
		$stmt->bind_param("s", $date_mef);
		$stmt->execute();

		$result = $stmt->get_result();

		$jsonData = array();
		if($result == false){
			mysqli_free_result($result);
			mysqli_close($connexion);
			throw new Exception(mysqli_error($connexion));
		}else if(mysqli_num_rows($result) > 0){
			while($array = mysqli_fetch_assoc($result)){
				$jsonData[] = $array;
			}
			echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE));
			mysqli_free_result($result);
			mysqli_close($connexion);
		}else {
			$jsonData[] = null;
			echo json_encode($jsonData);
			mysqli_free_result($result);
			mysqli_close($connexion);
		}
	}else{
		$jsonData[] = null;
		echo json_encode($jsonData);
	}
}

/* Recherche des attributions avant une date donnée */
if(isset($_GET['DATEINF']) && $_GET['DATEINF'] != ""){
	$date = $_GET['DATEINF'];
	$date = htmlspecialchars($date, ENT_QUOTES, 'UTF-8');

	$regEx = "#^[0-9]{8}$#";

	if(preg_match($regEx, $date) && validateDate($date)){
		$date_mef = substr($date, 4, 4) * 10000 + substr($date, 2, 2) * 100 + substr($date, 0, 2);

		include('db.php');

		$date_mef = $connexion->real_escape_string($date_mef);
		$stmt = $connexion->prepare("SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep FROM CONCATENATION WHERE (CAST(Date_Attribution_MEF AS UNSIGNED) <= ?) ORDER BY Date_Attribution_MEF");
		$stmt->bind_param("s", $date_mef);
		$stmt->execute();

		$result = $stmt->get_result();

		$jsonData = array();
		if($result == false){
			mysqli_free_result($result);
			mysqli_close($connexion);
			throw new Exception(mysqli_error($connexion));
		}else if(mysqli_num_rows($result) > 0){
			while($array = mysqli_fetch_assoc($result)){
				$jsonData[] = $array;
			}
			echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE));
			mysqli_free_result($result);
			mysqli_close($connexion);
		}else {
			$jsonData[] = null;
			echo json_encode($jsonData);
			mysqli_free_result($result);
			mysqli_close($connexion);
		}
	}else{
		$jsonData[] = null;
		echo json_encode($jsonData);
	}
}

/* Recherche des attributions entre deux dates données */
if(isset($_GET['DATEENTREINF']) && $_GET['DATEENTREINF'] != "" && isset($_GET['DATEENTRESUP']) && $_GET['DATEENTRESUP'] != ""){
	$dateInf = $_GET['DATEENTREINF'];
	$dateSup = $_GET['DATEENTRESUP'];
	$dateInf = htmlspecialchars($dateInf, ENT_QUOTES, 'UTF-8');
	$dateSup = htmlspecialchars($dateSup, ENT_QUOTES, 'UTF-8');

	$regEx = "#^[0-9]{8}$#";

	if(preg_match($regEx, $dateInf) && preg_match($regEx, $dateSup) && validateDate($dateInf) && validateDate($dateSup)){
		$date_inf = substr($dateInf, 4, 4) * 10000 + substr($dateInf, 2, 2) * 100 + substr($dateInf, 0, 2);
		$date_sup = substr($dateSup, 4, 4) * 10000 + substr($dateSup, 2, 2) * 100 + substr($dateSup, 0, 2);

		include('db.php');

		$date_inf = $connexion->real_escape_string($date_inf);
		$date_sup = $connexion->real_escape_string($date_sup);
		$stmt = $connexion->prepare("SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep FROM CONCATENATION WHERE (CAST(Date_Attribution_MEF AS UNSIGNED) >= ?) AND (CAST(Date_Attribution_MEF AS UNSIGNED) <= ?) ORDER BY Date_Attribution_MEF");
		$stmt->bind_param("ss", $date_inf, $date_sup);
		$stmt->execute();

		$result = $stmt->get_result();

		$jsonData = array();
		if($result == false){
			mysqli_free_result($result);
			mysqli_close($connexion);
			throw new Exception(mysqli_error($connexion));
		}else if(mysqli_num_rows($result) > 0){
			while($array = mysqli_fetch_assoc($result)){
				$jsonData[] = $array;
			}
			echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE));
			mysqli_free_result($result);
			mysqli_close($connexion);
		}else {
			$jsonData[] = null;
			echo json_encode($jsonData);
			mysqli_free_result($result);
			mysqli_close($connexion);
		}
	}else{
		$jsonData[] = null;
		echo json_encode($jsonData);
	}
}

/* Recherche de l'attributaire d'un numéro */
if(isset($_GET['NUMERO']) && $_GET['NUMERO'] != ""){
	$numero = $_GET['NUMERO'];
	$numero = htmlspecialchars($numero, ENT_QUOTES, 'UTF-8');

	$regEx = "#^((3[0|1|2|4|9][0-9]{2})|(1[0|6][0-9]{2})|(118[0-9]{3})|(0[1-9][0-9]{8}))$#";

	if(preg_match($regEx, $numero)){
		include('db.php');

		$numero = $connexion->real_escape_string($numero);
		$stmt = $connexion->prepare("SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep FROM CONCATENATION WHERE ? BETWEEN CAST(Tranche_Debut AS UNSIGNED) AND CAST(Tranche_Fin AS UNSIGNED) ORDER BY EZABPQM");
		$stmt->bind_param("s", $numero);
		$stmt->execute();

		$result = $stmt->get_result();

		$jsonData = array();
		if($result == false){
			mysqli_free_result($result);
			mysqli_close($connexion);
			throw new Exception(mysqli_error($connexion));
		}else if(mysqli_num_rows($result) > 0){
			while($array = mysqli_fetch_assoc($result)){
				$jsonData[] = $array;
			}

			echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE));
			mysqli_free_result($result);
			mysqli_close($connexion);
		}else {
			$jsonData[] = null;
			echo json_encode($jsonData);
			mysqli_free_result($result);
			mysqli_close($connexion);
		}
	}else {
		$jsonData[] = null;
		echo json_encode($jsonData);
	}
}

/* Recherche des données d'un fichier */
if(isset($_GET['FICHIER']) && $_GET['FICHIER'] != ""){
	$fichier = $_GET['FICHIER'];
	$fichier = htmlspecialchars($fichier, ENT_QUOTES, 'UTF-8');

	$regEx = "#^MAJ(PORTA|NUM|SDT)$#";

	if(preg_match($regEx, $fichier)){
		include('db.php');

		$fichier = $connexion->real_escape_string($fichier);
		$stmt = $connexion->prepare("SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep FROM CONCATENATION WHERE  ? = Fichier_Arcep ORDER BY EZABPQM");
		$stmt->bind_param("s", $fichier);
		$stmt->execute();

		$result = $stmt->get_result();

		$jsonData = array();
		if($result == false){
			mysqli_free_result($result);
			mysqli_close($connexion);
			throw new Exception(mysqli_error($connexion));
		}else if(mysqli_num_rows($result) > 0){
			while($array = mysqli_fetch_assoc($result)){
				$jsonData[] = $array;
			}

			echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE));
			mysqli_free_result($result);
			mysqli_close($connexion);
		}else {
			$jsonData[] = null;
			echo json_encode($jsonData);
			mysqli_free_result($result);
			mysqli_close($connexion);
		}
	}else {
		$jsonData[] = null;
		echo json_encode($jsonData);
	}
}

/**
* Vérification que la date entrée est correcte (année bissextile comprise) et est inférieure ou égale à la date du jour
* @param $date La date d'entrée à vérifier
* @param $format Le format de la date (JJMMAAAA)
* @return bool Vrai si la date existe et est inférieure ou égale à la date du jour, Faux sinon
*/
function validateDate($date, $format="dmY"){
	$d = DateTime::createFromFormat($format, $date);
	$now = (new DateTime('now'));

	return $d && ($d->format($format) === $date) && ($d->format($format) <= $now);
}
?>
