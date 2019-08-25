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
$fileUrl_MAJOPE = "https://www.data.gouv.fr/fr/datasets/r/19630568-4b05-4192-a989-9040a4383520";
$fileUrl_MAJPORTA = "https://www.data.gouv.fr/fr/datasets/r/e22d0c7f-24a9-4dae-81c3-56932889025f";
$fileUrl_MAJNUM = "https://www.data.gouv.fr/fr/datasets/r/90e8bdd0-0f5c-47ac-bd39-5f46463eb806";
$fileUrl_MAJSDT = "https://www.data.gouv.fr/fr/datasets/r/e516ccc4-70a8-46b6-b31f-6004e042f196";

// Les chemins relatifs vers lesquels seront stockés les fichiers au format.xls
$saveTo_XLS_MAJOPE = "./temp/MAJOPE.xls";
$saveTo_XLS_MAJPORTA = "./temp/MAJPORTA.xls";
$saveTo_XLS_MAJNUM = "./temp/MAJNUM.xls";
$saveTo_XLS_MAJSDT = "./temp/MAJSDT.xls";

// Les chemins relatifs vers lesquels seront stockés les fichiers au format .csv
$saveTo_CSV_MAJOPE = "./temp/MAJOPE.csv";
$saveTo_CSV_MAJPORTA = "./temp/MAJPORTA.csv";
$saveTo_CSV_MAJNUM = "./temp/MAJNUM.csv";
$saveTo_CSV_MAJSDT = "./temp/MAJSDT.csv";

// Les noms des fichiers
$name_MAJPORTA = "MAJPORTA";
$name_MAJOPE = "MAJOPE";
$name_MAJNUM = "MAJNUM";
$name_MAJSDT = "MAJSDT";

// Le chemin relatif où se situe 
$fileSQL = "./db_traitements.sql";

// On télécharge l'ensemble des fichiers au format .xls disponibles en open data
downloadFile($fileUrl_MAJOPE, $saveTo_XLS_MAJOPE, $name_MAJOPE);
downloadFile($fileUrl_MAJPORTA, $saveTo_XLS_MAJPORTA, $name_MAJPORTA);
downloadFile($fileUrl_MAJNUM, $saveTo_XLS_MAJNUM, $name_MAJNUM);
downloadFile($fileUrl_MAJSDT, $saveTo_XLS_MAJSDT, $name_MAJSDT);

// On convertit l'ensemble des fichiers du format .xls au format .csv
convertXLSToCSV($saveTo_XLS_MAJOPE, $saveTo_CSV_MAJOPE, $name_MAJOPE);
convertXLSToCSV($saveTo_XLS_MAJPORTA, $saveTo_CSV_MAJPORTA, $name_MAJPORTA);
convertXLSToCSV($saveTo_XLS_MAJNUM, $saveTo_CSV_MAJNUM, $name_MAJNUM);
convertXLSToCSV($saveTo_XLS_MAJSDT, $saveTo_CSV_MAJSDT, $name_MAJSDT);

// On insère les fichiers au format .csv dans la base de données et on effectue les traitements adéquats
insertionBDD($connexion, $fileSQL);

// On retarde l'exécution de la suite du script pour s'assurer que les fichiers au format .csv ne seront pas supprimés avant d'avoir été insérés dans les tables correspondantes
sleep(60);

// On supprime l'ensemble des fichiers au format .xls et au format .csv
deleteFile($saveTo_XLS_MAJOPE, $name_MAJOPE);
deleteFile($saveTo_XLS_MAJNUM, $name_MAJNUM);
deleteFile($saveTo_XLS_MAJPORTA, $name_MAJPORTA);
deleteFile($saveTo_XLS_MAJSDT, $name_MAJSDT);
deleteFile($saveTo_CSV_MAJOPE, $name_MAJOPE);
deleteFile($saveTo_CSV_MAJNUM, $name_MAJNUM);
deleteFile($saveTo_CSV_MAJPORTA, $name_MAJPORTA);
deleteFile($saveTo_CSV_MAJSDT, $name_MAJSDT);

/** 
 * Téléchargement d'un fichier via son URL et enregistrement à un endroit précisé
 * @param $fileUrl L'URL du fichier à télécharger
 * @param $saveTo L'endrot où le fichier sera sauvegardé
 * @param $name Le nom du fichier sauvegardé
 */
function downloadFile($fileUrl, $saveTo, $name){
	$start = microtime(true); // Début du chronomètre

	$fp = fopen($saveTo, 'w+'); // On créé un fichier en écriture

	if($fp==false){ // Si le fichier ne peut pas être ouvert
		throw new Exception("Ne peut pas ouvrir : " .$saveTo);
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

	if($statusCode==200){
		echo "Fichier : " .$name . " téléchargé en ".number_format($end-$start,2)." secondes.".nl2br("\n");
	} else {
		echo "Statut : " . $statusCode. " ".nl2br("\n");
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

echo "Conversion du fichier " .$name. " réussie en ".number_format($end-$start,2)." secondes.".nl2br("\n");
}

/**
 * Insertion d'un fichier .sql dans la base de données
 * @param $connexion La connexion à la base de données
 * @param $myfile Le fichier au format .sql qui doit être inséré
 */
function insertionBDD($connexion, $myfile){
	$debut = microtime(true);

	$sqlSource = file_get_contents($myfile);
	mysqli_multi_query($connexion, $sqlSource) or die("Impossible d'exécuter le fichier SQL\n"); // On exécute le fichier au format .sql
	
	$fin = microtime(true);

	echo "Insertion des fichiers dans la base de données réussie en ".number_format($end-$start,2)." secondes.".nl2br("\n");
}

/**
 * Suppression d'un fichier donné
 * @param $myfile Le fichier à supprimer
 * @param $name Le nom du fichier à supprimer
 */
function deleteFile($myfile, $name){
	$debut = microtime(true);

unlink($myfile) or die("Impossible de supprimer le fichier " .$myfile."\n"); // On supprime le fichier

$fin = microtime(true);
echo "Fichier " .$myfile. " supprimé en ".number_format($end-$start,2)." secondes.".nl2br("\n");	
}

?>