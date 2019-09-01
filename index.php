<?php

/**
 * @file index.php
 * @brief Exemple d'utilisation possible de l'API avec un fichier mis en forme
 */

?>

<!-- Le formulaire qui sera utilisé -->
<form name="form" method="post" action="index.php" id="form">
	<input type="radio" id="tranche" name="choix" value="tranche" required>Tranche
	<input type="radio" id="operateur" name="choix" value="operateur" required>Opérateur
	<input type="radio" id="numero" name="choix" value="numero" required>Numéro
	<input type="radio" id="dateInf" name="choix" value="dateInf" required>Avant le :
	<input type="radio" id="dateSup" name="choix" value="dateSup" required>Après le :
	<input type="radio" id="fichier" name="choix" value="fichier" required>Fichier :
	<br />
	<input type="text" id="data" name="data" placeholder="Entrez la donnée : " required />
	<input type="submit" name="submit"></input>
</form>

<!-- Le style de la page qui sera utilisé -->
<style text="text/css">

html{
	font-family: sans-serif;
}

table{
	border-collapse: collapse;
	border: 2px solid rgb(200,200,200);
	letter-spacing: 1px;
	font-size: 0.8rem;
}

td, th{
	border: 1px solid rgb(190,190,190);
	padding: 10px 20px;
}

th {
	background-color: rgb(235,235,235);
}

td{
	text-align: center;
}

tr:nth-child(even) td{
	background-color: rgb(250,250,250);
}

tr:nth-child(odd) td{
	backgroun-color: rgb(245,245,245);
}

caption{
	padding: 10px;
}

</style>

<?php
if(isset($_POST["choix"]) && $_POST["choix"] != ""){
$radioValue = $_POST["choix"]; // On récupère la valeur du bouton radio
$url = "http://localhost/apiNumerotation/api.php"; // On stocke l'url de l'API

if (isset($_POST['data']) && $_POST['data'] != ""){
$data = $_POST["data"];
$data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

if($radioValue == "tranche"){
	$url = $url . "?TRANCHE=" . $data;

	$client = curl_init($url); // On crée un gestionnaire cURL
	curl_setopt($client, CURLOPT_RETURNTRANSFER, true); // On définit la transmission cURL
	$response = curl_exec($client); // On exécute la requête
	
	if(curl_errno($client)){ // Si un message d'erreur cURL existe
		throw new Exception(curl_error($ch));
	}

	$result = json_decode($response); // On décode la réponse au format JSON reçue

	$i = 0;
	// On affiche un tableau avec l'ensemble des éléments correspondants à la requête demandée
		echo "<table>";
		echo "<tr><th>EZABPQM</th><th>Tranche_Debut</th><th>Tranche_Fin</th><th>Code_Operateur</th><th>Identite_Operateur</th><th>Territoire</th><th>Date_Attribution</th><th>Fichier_Arcep</th></tr>";
		foreach($result as $item){ // Pour chaque élément, on ajoute une nouvelle ligne au tableau
			echo "<tr>";
			echo "<td>" . $result[$i]->EZABPQM . "</td>";
			echo "<td>" . $result[$i]->Tranche_Debut . "</td>";
			echo "<td>" . $result[$i]->Tranche_Fin . "</td>";
			echo "<td>" . $result[$i]->Code_Operateur . "</td>";
			echo "<td>" . $result[$i]->Identite_Operateur . "</td>";
			echo "<td>" . $result[$i]->Territoire . "</td>";
			echo "<td>" . $result[$i]->Date_Attribution . "</td>";
			echo "<td>" . $result[$i]->Fichier_Arcep . "</td>";
			echo "</tr>";
			$i++;
		}
		echo "</table>";
		curl_close($client); // On ferme le gestionnaire cURL
}else if($radioValue == "operateur"){
$url = $url . "?OPERATEUR=" . $data;
	
	$client = curl_init($url);
	curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($client);

		if(curl_errno($client)){
		throw new Exception(curl_error($ch));
	}

	$result = json_decode($response);

	$i = 0;
		echo "<table>";
		echo "<tr><td>EZABPQM</td><td>Tranche_Debut</td><td>Tranche_Fin</td><td>Code_Operateur</td><td>Identite_Operateur</td><td>Territoire</td><td>Date_Attribution</td><td>Fichier_Arcep</td></tr>";
		foreach($result as $item){
			echo "<tr>";
			echo "<td>" . $result[$i]->EZABPQM . "</td>";
			echo "<td>" . $result[$i]->Tranche_Debut . "</td>";
			echo "<td>" . $result[$i]->Tranche_Fin . "</td>";
			echo "<td>" . $result[$i]->Code_Operateur . "</td>";
			echo "<td>" . $result[$i]->Identite_Operateur . "</td>";
			echo "<td>" . $result[$i]->Territoire . "</td>";
			echo "<td>" . $result[$i]->Date_Attribution . "</td>";
			echo "<td>" . $result[$i]->Fichier_Arcep . "</td>";
			echo "</tr>";
			$i++;
		}
		echo "</table>";
				curl_close($client);
}else if($radioValue == "numero"){
$url = $url . "?NUMERO=" . $data;
	
	$client = curl_init($url);
	curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($client);

			if(curl_errno($client)){
		throw new Exception(curl_error($ch));
	}

	$result = json_decode($response);

	$i = 0;
		echo "<table>";
		echo "<tr><td>EZABPQM</td><td>Tranche_Debut</td><td>Tranche_Fin</td><td>Code_Operateur</td><td>Identite_Operateur</td><td>Territoire</td><td>Date_Attribution</td><td>Fichier_Arcep</td></tr>";
		foreach($result as $item){
			echo "<tr>";
			echo "<td>" . $result[$i]->EZABPQM . "</td>";
			echo "<td>" . $result[$i]->Tranche_Debut . "</td>";
			echo "<td>" . $result[$i]->Tranche_Fin . "</td>";
			echo "<td>" . $result[$i]->Code_Operateur . "</td>";
			echo "<td>" . $result[$i]->Identite_Operateur . "</td>";
			echo "<td>" . $result[$i]->Territoire . "</td>";
			echo "<td>" . $result[$i]->Date_Attribution . "</td>";
			echo "<td>" . $result[$i]->Fichier_Arcep . "</td>";
			echo "</tr>";
			$i++;
		}
		echo "</table>";
				curl_close($client);
}else if($radioValue == "dateInf"){
$url = $url . "?DATEINF=" . $data;
	
	$client = curl_init($url);
	curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($client);

			if(curl_errno($client)){
		throw new Exception(curl_error($ch));
	}

	$result = json_decode($response);

	$i = 0;
		echo "<table>";
		echo "<tr><td>EZABPQM</td><td>Tranche_Debut</td><td>Tranche_Fin</td><td>Code_Operateur</td><td>Identite_Operateur</td><td>Territoire</td><td>Date_Attribution</td><td>Fichier_Arcep</td></tr>";
		foreach($result as $item){
			echo "<tr>";
			echo "<td>" . $result[$i]->EZABPQM . "</td>";
			echo "<td>" . $result[$i]->Tranche_Debut . "</td>";
			echo "<td>" . $result[$i]->Tranche_Fin . "</td>";
			echo "<td>" . $result[$i]->Code_Operateur . "</td>";
			echo "<td>" . $result[$i]->Identite_Operateur . "</td>";
			echo "<td>" . $result[$i]->Territoire . "</td>";
			echo "<td>" . $result[$i]->Date_Attribution . "</td>";
			echo "<td>" . $result[$i]->Fichier_Arcep . "</td>";
			echo "</tr>";
			$i++;
		}
		echo "</table>";
				curl_close($client);
}else if($radioValue == "dateSup"){
$url = $url . "?DATESUP=" . $data;
	
	$client = curl_init($url);
	curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($client);

			if(curl_errno($client)){
		throw new Exception(curl_error($ch));
	}

	$result = json_decode($response);

	$i = 0;
		echo "<table>";
		echo "<tr><td>EZABPQM</td><td>Tranche_Debut</td><td>Tranche_Fin</td><td>Code_Operateur</td><td>Identite_Operateur</td><td>Territoire</td><td>Date_Attribution</td><td>Fichier_Arcep</td></tr>";
		foreach($result as $item){
			echo "<tr>";
			echo "<td>" . $result[$i]->EZABPQM . "</td>";
			echo "<td>" . $result[$i]->Tranche_Debut . "</td>";
			echo "<td>" . $result[$i]->Tranche_Fin . "</td>";
			echo "<td>" . $result[$i]->Code_Operateur . "</td>";
			echo "<td>" . $result[$i]->Identite_Operateur . "</td>";
			echo "<td>" . $result[$i]->Territoire . "</td>";
			echo "<td>" . $result[$i]->Date_Attribution . "</td>";
			echo "<td>" . $result[$i]->Fichier_Arcep . "</td>";
			echo "</tr>";
			$i++;
		}
		echo "</table>";
				curl_close($client);
}else if($radioValue == "fichier"){
$url = $url . "?FICHIER=" . $data;
	
	$client = curl_init($url);
	curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($client);

			if(curl_errno($client)){
		throw new Exception(curl_error($ch));
	}

	$result = json_decode($response);

	$i = 0;
		echo "<table>";
		echo "<tr><td>EZABPQM</td><td>Tranche_Debut</td><td>Tranche_Fin</td><td>Code_Operateur</td><td>Identite_Operateur</td><td>Territoire</td><td>Date_Attribution</td><td>Fichier_Arcep</td></tr>";
		foreach($result as $item){
			echo "<tr>";
			echo "<td>" . $result[$i]->EZABPQM . "</td>";
			echo "<td>" . $result[$i]->Tranche_Debut . "</td>";
			echo "<td>" . $result[$i]->Tranche_Fin . "</td>";
			echo "<td>" . $result[$i]->Code_Operateur . "</td>";
			echo "<td>" . $result[$i]->Identite_Operateur . "</td>";
			echo "<td>" . $result[$i]->Territoire . "</td>";
			echo "<td>" . $result[$i]->Date_Attribution . "</td>";
			echo "<td>" . $result[$i]->Fichier_Arcep . "</td>";
			echo "</tr>";
			$i++;
		}
		echo "</table>";
				curl_close($client);
}
}
}
?>