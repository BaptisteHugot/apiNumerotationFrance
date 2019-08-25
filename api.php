<?php
/**
 * @file api.php
 * @brief Génère l'API pour rechercher le contenu d'un fichier spécifique, l'attributaire d'une tranche de numéros, d'un numéro complet ou d'un numéro court, les ressources attribuées à un opérateur ou bien les ressources attribuées avant et/ou après une date donnée
 */

/* Code utilisé uniquement pour le débug, à supprimer en production */
error_reporting(E_ALL);
ini_set('display_errors',1);
/* Fin du code utilisé uniquement pour le débug, à supprimer en production */

header("Content-Type:application/json");

/* Recherche de l'attributaire d'une tranche ou d'un début de tranche */
if(isset($_GET['TRANCHE']) && $_GET['TRANCHE']!=""){
	$ezabpqm=$_GET['TRANCHE'];

	$regEx = "#^((3[0|1|2|4|9][0-9]{0,2})|(1[0|6][0-9]{0,2})|(118[0-9]{0,3})|(0[1-9][0-9]{0,5}))$#"; // Expression régulière d'une tranche de numéros

	if(preg_match($regEx,$ezabpqm)){ // Si la tranche entrée correspond à l'expression régulière
		include('db.php'); // On se connecte à la base de données

	$query = "SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep FROM CONCATENATION WHERE EZABPQM LIKE '$ezabpqm%' ORDER BY EZABPQM"; // Requête SQL à exécuter
	$result = mysqli_query($connexion,$query);

	$jsonData = array();
	if($result==false){ // On arrête le programme si l'exécution de la requête a rencontré un problème
		throw new Exception(mysqli_error($connexion));
		mysqli_free_result($result); // On libère la variable utilisée pour récupérer le résultat de la requête SQL
		mysqli_close($connexion); // On ferme la connexion à la base de données
	}else if(mysqli_num_rows($result) > 0){ // Si au moins un élément est trouvé
while($array = mysqli_fetch_assoc($result)){ // On stocke chaque ligne de la base de données dans une ligne d'un tableau PHP
		$jsonData[] = $array;
	}

	echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE)); // On affiche le résultat au format JSON
	mysqli_free_result($result); // On libère la variable utilisée pour récupérer le résultat de la requête SQL
	mysqli_close($connexion); // On ferme la connexion à la base de données
}else { // On retourne null si aucun élément n'est trouvé
	$jsonData[]=null;
	echo json_encode($jsonData);
	}
	}else { // On retourne null si le format entré ne correspond pas à l'expression régulière
		$jsonData[]=null;
	echo json_encode($jsonData);
	}
}

/* Recherche des numéros attribués à un opérateur donné (via son code Arcep) */
if(isset($_GET['OPERATEUR']) && $_GET['OPERATEUR']!=""){
	$operateur=strtoupper($_GET['OPERATEUR']); // On met en majuscule la valeur entrée par l'utilisateur

	$regEx = "#^[A-Za-z0-9]{4,5}$#";

	if(preg_match($regEx,$operateur)){
			include('db.php');

	$query = "SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep FROM CONCATENATION WHERE Code_Operateur='$operateur' ORDER BY EZABPQM";
	$result = mysqli_query($connexion,$query);

	$jsonData = array();
	if($result==false){
		throw new Exception(mysqli_error($connexion));
				mysqli_free_result($result);
		mysqli_close($connexion);
	}else if(mysqli_num_rows($result) > 0){
	while($array = mysqli_fetch_assoc($result)){
		$jsonData[] = $array;
	}
	echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE));
		mysqli_free_result($result);
		mysqli_close($connexion); 
}else {
	$jsonData[]=null;
	echo json_encode($jsonData);
}
}else{
	$jsonData[]=null;
	echo json_encode($jsonData);	
}
}

/* Recherche des attributions après une date donnée */
if(isset($_GET['DATESUP']) && $_GET['DATESUP']!=""){
	$date=$_GET['DATESUP'];

	$regEx = "#^[0-9]{8}$#";

	if(preg_match($regEx,$date) && validateDate($date)){
	$date_mef=substr($date,4,4)*10000+substr($date, 2, 2)*100+substr($date,0,2); // On met en forme la date entrée pour pouvoir requêter plus facilement

	include('db.php');

	$query = "SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep FROM CONCATENATION WHERE CAST(Date_Attribution_MEF AS UNSIGNED)>='$date_mef' ORDER BY Date_Attribution_MEF";
	$result = mysqli_query($connexion,$query);

	$jsonData = array();
	if($result==false){
		throw new Exception(mysqli_error($connexion));
				mysqli_free_result($result);
		mysqli_close($connexion);
	}else if(mysqli_num_rows($result)>0){
	while($array = mysqli_fetch_assoc($result)){
		$jsonData[] = $array;
	}
	echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE));
		mysqli_free_result($result);
		mysqli_close($connexion); 
}else {
	$jsonData[]=null;
	echo json_encode($jsonData);
}
}else{
	$jsonData[]=null;
	echo json_encode($jsonData);
}
}

/* Recherche des attributions avant une date donnée */
if(isset($_GET['DATEINF']) && $_GET['DATEINF']!=""){
	$date=$_GET['DATEINF'];

	$regEx = "#^[0-9]{8}$#";

		if(preg_match($regEx,$date) && validateDate($date)){
	$date_mef=substr($date,4,4)*10000+substr($date, 2, 2)*100+substr($date,0,2);

	include('db.php');

	$query = "SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep FROM CONCATENATION WHERE CAST(Date_Attribution_MEF AS UNSIGNED)<='$date_mef' ORDER BY Date_Attribution_MEF";
	$result = mysqli_query($connexion,$query);

	$jsonData = array();
	if($result==false){
		throw new Exception(mysqli_error($connexion));
				mysqli_free_result($result);
		mysqli_close($connexion);
	}else if(mysqli_num_rows($result)>0){
	while($array = mysqli_fetch_assoc($result)){
		$jsonData[] = $array;
	}
	echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE));
		mysqli_free_result($result);
		mysqli_close($connexion); 
}else {
	$jsonData[]=null;
	echo json_encode($jsonData);
}
}else{
$jsonData[]=null;
	echo json_encode($jsonData);	
}
}

/* Recherche des attributions entre deux dates données */
if(isset($_GET['DATEENTREINF']) && $_GET['DATEENTREINF']!="" &&isset($_GET['DATEENTRESUP']) && $_GET['DATEENTRESUP']!=""){
	$dateInf=$_GET['DATEENTREINF'];
	$dateSup=$_GET['DATEENTRESUP'];

	$regEx = "#^[0-9]{8}$#";

if(preg_match($regEx,$dateInf) && preg_match($regEx,$dateSup) && validateDate($dateInf) && validateDate($dateSup)){
	$date_inf=substr($dateInf,4,4)*10000+substr($dateInf, 2, 2)*100+substr($dateInf,0,2);
	$date_sup=substr($dateSup,4,4)*10000+substr($dateSup, 2, 2)*100+substr($dateSup,0,2);

	include('db.php');

	$query = "SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep FROM CONCATENATION WHERE (CAST(Date_Attribution_MEF AS UNSIGNED)<='$date_sup' AND CAST(Date_Attribution_MEF AS UNSIGNED)>='$date_inf') ORDER BY Date_Attribution_MEF";
	$result = mysqli_query($connexion,$query);

	$jsonData = array();
	if($result==false){
		throw new Exception(mysqli_error($connexion));
				mysqli_free_result($result);
		mysqli_close($connexion);
	}else if(mysqli_num_rows($result)>0){
	while($array = mysqli_fetch_assoc($result)){
		$jsonData[] = $array;
	}
	echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE));
		mysqli_free_result($result);
		mysqli_close($connexion); 
}else {
	$jsonData[]=null;
	echo json_encode($jsonData);
}
}else{
$jsonData[]=null;
	echo json_encode($jsonData);	
}
}

/* Recherche de l'attributaire d'un numéro */
if(isset($_GET['NUMERO']) && $_GET['NUMERO']!=""){
	$numero=$_GET['NUMERO'];

	$regEx = "#^((3[0|1|2|4|9][0-9]{2})|(1[0|6][0-9]{2})|(118[0-9]{3})|(0[1-9][0-9]{8}))$#";

	if(preg_match($regEx,$numero)){
			include('db.php');

	$query = "SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep FROM CONCATENATION WHERE '$numero' BETWEEN CAST(Tranche_Debut AS UNSIGNED) AND CAST(Tranche_Fin AS UNSIGNED) ORDER BY EZABPQM";
	$result = mysqli_query($connexion,$query);

	$jsonData = array();
	if($result==false){
		throw new Exception(mysqli_error($connexion));
				mysqli_free_result($result);
		mysqli_close($connexion);
	}else if(mysqli_num_rows($result) > 0){
while($array = mysqli_fetch_assoc($result)){
		$jsonData[] = $array;
	}

	echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE));
	mysqli_free_result($result);
	mysqli_close($connexion); 
}else {
	$jsonData[]=null;
	echo json_encode($jsonData);
	}
	}else {
		$jsonData[]=null;
	echo json_encode($jsonData);
	}
}

/* Recherche des données d'un fichier */
if(isset($_GET['FICHIER']) && $_GET['FICHIER']!=""){
	$fichier=$_GET['FICHIER'];

	$regEx = "#^(MAJPORTA|MAJNUM|MAJSDT)$#";

	if(preg_match($regEx,$fichier)){
			include('db.php');

	$query = "SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep FROM CONCATENATION WHERE '$fichier'=Fichier_Arcep ORDER BY EZABPQM";
	$result = mysqli_query($connexion,$query);

	$jsonData = array();
	if($result==false){
		throw new Exception(mysqli_error($connexion));
				mysqli_free_result($result);
		mysqli_close($connexion);
	}else if(mysqli_num_rows($result) > 0){
while($array = mysqli_fetch_assoc($result)){
		$jsonData[] = $array;
	}

	echo stripslashes(json_encode($jsonData, JSON_UNESCAPED_UNICODE));
	mysqli_free_result($result);
	mysqli_close($connexion); 
}else {
	$jsonData[]=null;
	echo json_encode($jsonData);
	}
	}else {
		$jsonData[]=null;
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
	$now = (new DateTime('now'))->format($format);
	return $d && ($d->format($format) === $date) && ($d->format($format) <= $now);
}
?>