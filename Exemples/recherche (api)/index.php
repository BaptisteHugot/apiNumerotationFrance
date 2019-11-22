<?php

/**
* @file index.php
* @brief Exemple d'utilisation possible de l'API avec un fichier mis en forme
*/

/* Code utilisé uniquement pour le débug, à supprimer en production */
error_reporting(E_ALL);
ini_set('display_errors',1);
/* Fin du code utilisé uniquement pour le débug, à supprimer en production */

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Exemple d'utilisation de l'API numérotation</title>
	<link rel="StyleSheet" type="text/css" href="style.css">
	<script
	src="https://code.jquery.com/jquery-3.4.1.min.js"
	integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
	crossorigin="anonymous"></script>
	<script src="./style.js"></script>
</head>

<body>
	<!-- Le formulaire qui sera utilisé -->
	<form name="form" method="post" action="index.php" id="form">
		<input type="radio" id="tranche" name="choix" value="tranche" class="radioSelect" required>Tranche
		<input type="radio" id="operateur" name="choix" value="operateur" class="radioSelect" required>Opérateur
		<input type="radio" id="numero" name="choix" value="numero" class="radioSelect" required>Numéro
		<input type="radio" id="dateInf" name="choix" value="dateInf" class="radioSelect" required>Avant le :
		<input type="radio" id="dateSup" name="choix" value="dateSup" class="radioSelect" required>Après le :
		<input type="radio" id="fichier" name="choix" value="fichier" class="radioSelect" required>Fichier :
		<input type="radio" id="dateEntre" name="choix" value="dateEntre" class="radioSelect" required>Entre :
		<br />

		<!-- On modifie le champ d'entrée de texte selon le bouton radio choisi -->
		<input type="text" class="specificField" id="tranche" name="tranche" placeholder="Tranche : " minlength="2" maxlength="7" />
		<input type="text" class="specificField" id="operateur" name="operateur" placeholder="Opérateur : " minlength="3" maxlength="5" />
		<input type="text" class="specificField" id="numero" name="numero" placeholder="Numéro : " minlength="4" maxlength="14" />
		<input type="text" class="specificField" id="dateInf" name="dateInf" placeholder="Avant le : " minlength="8" maxlength="8" />
		<input type="text" class="specificField" id="dateSup" name="dateSup" placeholder="Après le : " minlength="8" maxlength="8" />
		<input type="text" class="specificField" id="fichier" name="fichier" placeholder="Fichier : " minlength="6" maxlength="8" />
		<input type="text" class="specificField" id="dateEntre" name="dateEntreInf" placeholder="Après le : " minlength="8" maxlength="8" />
		<input type="text" class="specificField" id="dateEntre" name="dateEntreSup" placeholder="Et avant le : " minlength="8" maxlength="8" />
		<br />

		<input type="submit" name="submit"></input>
	</form>

</body>
</html>

<?php
if(isset($_POST["choix"]) && $_POST["choix"] != ""){
	$radioValue = $_POST["choix"]; // On récupère la valeur du bouton radio
	$url = "/CHEMIN/VERS/APPLICATION/api.php"; // On stocke l'url de l'API

	if($radioValue == "tranche"){
		if(isset($_POST["tranche"]) && $_POST["tranche"] != ""){
			$data = $_POST["tranche"];
			$data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

			$url = $url . "?TRANCHE=" . $data;

			$client = curl_init($url); // On crée un gestionnaire cURL
			curl_setopt($client, CURLOPT_RETURNTRANSFER, true); // On définit la transmission cURL
			$response = curl_exec($client); // On exécute la requête

			if(curl_errno($client)){ // Si un message d'erreur cURL existe
				throw new Exception(curl_error($client));
			}

			$result = json_decode($response); // On décode la réponse au format JSON reçue

			if($result[0] == null){ // Dans le cas où l'API retourne null, afin d'éviter d'afficher un tableau vide
				echo "Votre recherche n'a retourné aucune donnée";
			}else{
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
			}
			curl_close($client); // On ferme le gestionnaire cURL
		}
	}else if($radioValue == "operateur"){
		if(isset($_POST["operateur"]) && $_POST["operateur"] != ""){
			$data = $_POST["operateur"];

			$url = $url . "?OPERATEUR=" . $data;

			$client = curl_init($url);
			curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($client);

			if(curl_errno($client)){
				throw new Exception(curl_error($client));
			}

			$result = json_decode($response);

			if($result[0] == null){
				echo "Votre recherche n'a retourné aucune donnée";
			}else{
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
			}
			curl_close($client);
		}
	}else if($radioValue == "numero"){
		if(isset($_POST["numero"]) && $_POST["numero"] != ""){
			$data = $_POST["numero"];

			$url = $url . "?NUMERO=" . $data;

			$client = curl_init($url);
			curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($client);

			if(curl_errno($client)){
				throw new Exception(curl_error($client));
			}

			$result = json_decode($response);
			if($result[0] == null){
				echo "Votre recherche n'a retourné aucune donnée";
			}else{
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
			}
			curl_close($client);
		}
	}else if($radioValue == "dateInf"){
		if(isset($_POST["dateInf"]) && $_POST["dateInf"] != ""){
			$data = $_POST["dateInf"];
			$url = $url . "?DATEINF=" . $data;

			$client = curl_init($url);
			curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($client);

			if(curl_errno($client)){
				throw new Exception(curl_error($client));
			}

			$result = json_decode($response);

			if($result[0] == null){
				echo "Votre recherche n'a retourné aucune donnée";
			}else{
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
			}
			curl_close($client);
		}
	}else if($radioValue == "dateSup"){
		if(isset($_POST["dateSup"]) && $_POST["dateSup"] != ""){
			$data = $_POST["dateSup"];

			$url = $url . "?DATESUP=" . $data;

			$client = curl_init($url);
			curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($client);

			if(curl_errno($client)){
				throw new Exception(curl_error($client));
			}

			$result = json_decode($response);

			if($result[0] == null){
				echo "Votre recherche n'a retourné aucune donnée";
			}else{
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
			}
			curl_close($client);
		}
	}else if($radioValue == "fichier"){
		if(isset($_POST["fichier"]) && $_POST["fichier"] != ""){
			$data = $_POST["fichier"];

			$url = $url . "?FICHIER=" . $data;

			$client = curl_init($url);
			curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($client);

			if(curl_errno($client)){
				throw new Exception(curl_error($client));
			}

			$result = json_decode($response);

			if($result[0] == null){
				echo "Votre recherche n'a retourné aucune donnée";
			}else{
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
			}
			curl_close($client);
		}
	}else if($radioValue == "dateEntre"){
		if(isset($_POST["dateEntreInf"]) && $_POST["dateEntreInf"] != "" && isset($_POST["dateEntreSup"]) && $_POST["dateEntreSup"] != ""){
			$dateEntreInf = $_POST["dateEntreInf"];
			$dateEntreSup = $_POST["dateEntreSup"];

			$url = $url . "?DATEENTREINF=" . $dateEntreInf . "&DATEENTRESUP=" . $dateEntreSup;

			$client = curl_init($url);
			curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($client);

			if(curl_errno($client)){
				throw new Exception(curl_error($client));
			}

			$result = json_decode($response);

			if($result[0] == null){
				echo "Votre recherche n'a retourné aucune donnée";
			}else{
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
			}
			curl_close($client);
		}
	}
}
?>
