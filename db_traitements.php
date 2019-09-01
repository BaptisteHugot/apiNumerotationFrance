<?php
/**
 * @file db_traitements.php
 * @brief Fichier effectuant les traitements sur les fichiers disponibles en open data au format xls pour les inclure dans une table MySQL commune
 */

// On définit l'ensemble des dépendances
require_once('phpoffice_phpspreadsheet/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
include ('db.php');

// On définit l'ensemble des variables
// Les URL de téléchargement des fichiers en open data
$urlArray = array(); // On crée un tableau vide où l'on stocke chaque URL contenant un fichier à télécharger
$urlArray["MAJOPE"] = "https://www.data.gouv.fr/fr/datasets/r/19630568-4b05-4192-a989-9040a4383520";
$urlArray["MAJPORTA"] = "https://www.data.gouv.fr/fr/datasets/r/e22d0c7f-24a9-4dae-81c3-56932889025f";
$urlArray["MAJNUM"] = "https://www.data.gouv.fr/fr/datasets/r/90e8bdd0-0f5c-47ac-bd39-5f46463eb806";
$urlArray["MAJSDT"] = "https://www.data.gouv.fr/fr/datasets/r/e516ccc4-70a8-46b6-b31f-6004e042f196";

$tempSaveFolder = "./temp/"; // Dossier où seront mis les fichiers temporaires
$fileSQL = "./db_traitements.sql"; // Le chemin relatif où se situe le script sql à exécuter

// On crée le dossier /temp/ si celui-ci n'existe pas déjà
if(!file_exists($tempSaveFolder)){
	mkdir($tempSaveFolder, 0777, true);
}

// On télécharge l'ensemble des fichiers au format .xls disponibles en open data
foreach($urlArray as $key=>$value){
	downloadFile($value, $tempSaveFolder . $key . ".xls", $key);
}

// On convertit l'ensemble des fichiers du format .xls au format .csv
$filesXLS = glob($tempSaveFolder . "*.{xls}", GLOB_BRACE);
foreach($filesXLS as $file){
	$basename = basename($file, ".xls");
	$fileCSV = $tempSaveFolder . $basename . ".csv";
	convertXLSToCSV($file, $fileCSV, $basename);
}

// On insère les fichiers au format .csv dans la base de données et on effectue les traitements adéquats
insertionBDD($connexion, $fileSQL);

// On supprime l'ensemble des fichiers au format .xls et au format .csv
$filesCSV = glob($tempSaveFolder . "*.{csv}", GLOB_BRACE);
foreach($filesXLS as $file){
	$basename = basename($file, ".xls");
	deleteFile($file, $basename);
}
foreach($filesCSV as $file){
	$basename = basename($file, ".csv");
	deleteFile($file, $basename);
}

/** 
 * Téléchargement d'un fichier via son URL et enregistrement à un endroit précisé
 * @param $fileUrl L'URL du fichier à télécharger
 * @param $saveTo L'endrot où le fichier sera sauvegardé
 * @param $name Le nom du fichier sauvegardé
 */
function downloadFile($fileUrl, $saveTo, $name){
	$start = microtime(true); // Début du chronomètre

	$fp = fopen($saveTo, 'w+'); // On créé un fichier en écriture

	if($fp == false){ // Si le fichier ne peut pas être ouvert
		throw new Exception("Ne peut pas ouvrir : " . $saveTo . nl2br("\n"));
	}

	$ch = curl_init($fileUrl); // On créé un gestionnaire cURL
	curl_setopt($ch, CURLOPT_FILE, $fp); // On passe le fichier au gestionnaire cURL
	curl_setopt($ch, CURLOPT_TIMEOUT, 20); // On stoppe si le fichier n'est pas téléchargé après 20 secondes
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE); // On force toutes les éventuelles redirections mises en place
	curl_exec($ch); // On exécute la requête

	if(curl_errno($ch)){ // Si un message d'erreur cURL existe
		throw new Exception(curl_error($ch));
	}

	$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // On récupèle le code de statut HTTP
	curl_close($ch); // On ferme le gestionnaire cURL
	fclose($fp); // On ferme le fichier

	$end = microtime(true); // Fin du chronomètre

	if($statusCode == 200){
		echo "Fichier : " . $name . " téléchargé en " . number_format($end-$start,2) . " secondes." . nl2br("\n");
	} else {
		echo "Statut : " . $statusCode . " " . nl2br("\n");
	}
}

/**
 * Conversion d'un fichier au format .xls vers un fichier au format .csv 
 * @param $infile Le fichier d'entrée au format .xls
 * @param $outfile Le fichier de sortie au format .csv
 * @param $name Le nom du fichier (le fichier généré aura le même nom que le fichier d'entrée)
 */
function convertXLSToCSV($infile, $outfile, $name){
$start = microtime(true);

$fileType = IOFactory::identify($infile); // On identifie le type de fichier
$reader = IOFactory::createReader($fileType); // On crée le tampon de lecture
$spreadsheet = $reader->load($infile); // On met le fichier d'entrée dans le tampon

$writer = IOFactory::createWriter($spreadsheet, "Csv"); // On crée le tampon d'écriture
$writer->setSheetIndex(0); // On définit la feuille où écrire
$writer->setDelimiter(";"); // On définit le délimiteur
$writer->save($outfile); // On sauvegarde le fichier

$end = microtime(true);

echo "Conversion du fichier " . $name . " réussie en " . number_format($end-$start,2) . " secondes." . nl2br("\n");
}

/**
 * Insertion d'un fichier .sql dans la base de données
 * @param $connexion La connexion à la base de données
 * @param $myfile Le fichier au format .sql qui doit être inséré
 */
function insertionBDD($connexion, $myfile){
	$start = microtime(true);

	$sqlSource = file_get_contents($myfile);
	mysqli_multi_query($connexion, $sqlSource) or die("Impossible d'exécuter le fichier SQL" . nl2br("\n")); // On exécute le fichier au format .sql
	
	
	// On attend que l'ensemble des requêtes SQL du script se soient exécutées
	while(mysqli_next_result($connexion)){

	}

	if(mysqli_error($connexion)){
		die(mysqli_error($connexion));
	}
	
	$end = microtime(true);

	echo "Insertion des fichiers dans la base de données réussie en " . number_format($end-$start,2) . " secondes." . nl2br("\n");
}

/**
 * Suppression d'un fichier donné
 * @param $myfile Le fichier à supprimer
 * @param $name Le nom du fichier à supprimer
 */
function deleteFile($myfile, $name){
	$start = microtime(true);

unlink($myfile) or die("Impossible de supprimer le fichier " . $myfile . nl2br("\n")); // On supprime le fichier

$end = microtime(true);
echo "Fichier " . $myfile . " supprimé en " . number_format($end-$start,2) . " secondes." . nl2br("\n");	
}

?>