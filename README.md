# apiNumerotationFrance
Cette API permet d'effectuer des recherches sur les ressources en numérotation attribuées par l'Arcep. Les données sont tirées des fichiers MAJNUM, MAJSDT, MAJPORTA et MAJOPE mis à disposition par l'[Arcep](https://www.data.gouv.fr/fr/datasets/ressources-en-numerotation-telephonique/) en open data.

## Utilisation
L'API permet d'effectuer une recherche selon les critères suivants pour les ressources attribuées :
* A un opérateur spécifique, à l'aide du mot-clé OPERATEUR suivi d'un code alphanumérique sur 4 ou 5 caractères
* Pour une tranche de numéro (EZABPQM), à l'aide du mot-clé TRANCHE suivi d'un nombre allant de 2 à 7 caractères
* Selon le fichier d'origine, à l'aide du mot-clé FICHIER suivi d'un texte compris entre MAJNUM, MAJSDT et MAJPORTA
* Avant une date donnée, à l'aide du mot-clé DATEINF suivi d'un nombre sur 8 caractères au format DDMMAAAA
* Après une date donnée, à l'aide du mot-clé DATESUP suivi d'un nombre sur 8 caractères au format DDMMAAAA
* Entre deux dates données, à l'aide des mots-clés DATEENTREINF suivi d'un nombre sur 8 caractères au format DDMMAAAA et DATEENTRESUP suivi d'un nombre sur 8 caractères au format DDMMAAAA
L'API retourne null dans les cas où aucun élément n'est trouvé ou si les données en entrée ne respectent pas le format attendu ou sont incorrectes (par exemple, si une date entrée n'existe pas).

Le projet contient également un fichier permettant d'automatiser le processus de récupération des fichiers et d'inclusion dans une base de données pour maintenir à jour les données de l'API.

## Ecrit avec
* [PHP](https://secure.php.net/) - Le langage de programmation utilisé pour l'API et la page d'exemple
* [HTML](https://www.w3.org/html/) - Le langage de programmation utilisé pour afficher la page Internet
* [CSS](https://www.w3.org/Style/CSS/) - Le langage de programmation utilisé pour gérer les styles de la page Internet

## Bibliothèques utilisées
* [PHPSpreadsheet]() - La bibliothèque utilisée pour convertir les fichiers du format .xls au format .csv

## Versions
[SemVer](http://semver.org/) est utilisé pour la gestion de versions. Pour connaître les versions disponibles, veuillez vous référer aux [étiquettes disponibles dans ce dépôt](https://github.com/BaptisteHugot/apiNumerotationFrance/releases/).

## Auteurs
* **Baptiste Hugot** - *Travail initial* - [BaptisteHugot](https://github.com/BaptisteHugot)

## Licence
Ce projet est disponible sous licence logiciel MIT. Veuillez lire le fichier [LICENSE](LICENSE) pour plus de détails.

## Règles de conduite
Pour connaître l'ensemble des règles de conduite à respecter sur ce dépôt, veuillez lire le fichier [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md).

## Contribution au projet
Si vous souhaitez contribuer au projet, que ce soit en corrigeant des bogues ou en proposant de nouvelles fonctionnalités, veuillez lire le fichier [CONTRIBUTING.md](CONTRIBUTING.md) pour plus de détails.