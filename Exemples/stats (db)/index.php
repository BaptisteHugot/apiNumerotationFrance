<?php
/**
* @file index.php
* @brief Exemple d'utilisation possible des données de l'API
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
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-zoom/0.6.6/chartjs-plugin-zoom.min.js"></script>
	<script type="text/javascript" src="./graph.js"></script>
</head>

<body>
	<!-- Le formulaire qui sera utilisé -->
	<form name="form" method="post" action="index.php" id="form">
		<input type="radio" id="nbRessourcesOperateur" name="choix" value="nbRessourcesOperateur" class="radioSelect" required>Nombre de ressources par opérateur
		<input type="radio" id="nbRessourcesAn" name="choix" value="nbRessourcesAn" class="radioSelect" required>Nombre de ressources annuelles
		<input type="radio" id="nbRessourcesCumuleesAn" name="choix" value="nbRessourcesCumuleesAn" class="radioSelect" required>Nombre de ressources annuelles cumulées
		<input type="radio" id="nbRessourcesMois" name="choix" value="nbRessourcesMois" class="radioSelect" required>Nombre de ressources mensuelles
		<input type="radio" id="nbRessourcesCumuleesMois" name="choix" value="nbRessourcesCumuleesMois" class="radioSelect" required> Nombre de ressources mensuelles cumulées
		<input type="radio" id="nbRessources12Mois" name="choix" value="nbRessources12Mois" class="radioSelect" required>Nombre de ressources des 12 derniers mois
		<input type="radio" id="nbRessourcesZ" name="choix" value="nbRessourcesZ" class="radioSelect" required>Nombre de ressources par Z
		<input type="radio" id="nbRessourcesZNE" name="choix" value="nbRessourcesZNE" class="radioSelect" required>Nombre de ressources par ZNE
		<br />
		<input type="submit" name="submit"></input>
	</form>

	<?php
	if(isset($_POST["choix"]) && $_POST["choix"] != ""){
		$radioValue = $_POST["choix"]; // On récupère la valeur du bouton radio
		if($radioValue == "nbRessourcesOperateur"){
			echo "
			<script>
			showGraphRes();
			</script>
			<div id='chart-container'>
			<canvas id='graphCanvasRes'></canvas>
			</div>";
		}else if($radioValue == "nbRessourcesAn"){
			echo "
			<script>
			showGraphAnnee();
			</script>
			<div id='chart-container'>
			<canvas id='graphCanvasAnnee'></canvas>
			</div>";
		}else if($radioValue == "nbRessourcesCumuleesAn"){
			echo "
			<script>
			showGraphCumulAn();
			</script>
			<div id='chart-container'>
			<canvas id='graphCanvasCumulAn'></canvas>
			</div>";
		}else if($radioValue == "nbRessourcesMois"){
			echo "
			<script>
			showGraphMois();
			</script>
			<div id='chart-container'>
			<canvas id='graphCanvasMois'></canvas>
			</div>";
		}else if($radioValue == "nbRessourcesCumuleesMois"){
			echo "
			<script>
			showGraphCumulMois();
			</script>
			<div id='chart-container'>
			<canvas id='graphCanvasCumulMois'></canvas>
			</div>";
		}
		else if($radioValue == "nbRessources12Mois"){
			echo "
			<script>
			showGraphDerniersMois();
			</script>
			<div id='chart-container'>
			<canvas id='graphCanvasDerniersMois'></canvas>
			</div>";
		}
		else if($radioValue == "nbRessourcesZ"){
			echo "
			<script>
			showGraphZ();
			</script>
			<div id='chart-container'>
			<canvas id='graphCanvasZ'></canvas>
			</div>";

		}
		else if($radioValue == "nbRessourcesZNE"){
			echo "
			<script>
			showGraphZNE();
			</script>
			<div id='chart-container'>
			<canvas id='graphCanvasZNE'></canvas>
			</div>";
		}
	}
	?>

	<div id= "tableau"></div>

</body>
</html>
