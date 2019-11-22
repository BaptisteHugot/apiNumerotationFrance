-- @file db_traitements.sql
-- @brief Effectue les traitements sur la base de donnée qui sera utilisée par l'API grâce aux fichiers disponibles en open data et convertis au format .csv

-- Voir pour créer une variable globale contenant le chemin du dossier

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- AVANT IMPORT

-- On créé les tables si elles n'existent pas déjà
CREATE TABLE IF NOT EXISTS `MAJNUM`(
  `EZABPQM` varchar(10) COLLATE latin1_bin NOT NULL,
  `Tranche_Debut` varchar(14) COLLATE latin1_bin NOT NULL,
  `Tranche_Fin` varchar(14) COLLATE latin1_bin NOT NULL,
  `Mnémo` varchar(6) COLLATE latin1_bin NOT NULL,
  `Territoire` text COLLATE latin1_bin NOT NULL,
  `Date_Attribution` varchar(10) COLLATE latin1_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

CREATE TABLE IF NOT EXISTS `MAJOPE`(
  `CODE_OPERATEUR` varchar(6) COLLATE latin1_bin NOT NULL,
  `IDENTITE_OPERATEUR` text COLLATE latin1_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

CREATE TABLE IF NOT EXISTS `MAJPORTA`(
  `EZABPQM` varchar(10) COLLATE latin1_bin NOT NULL,
  `Mnémo` varchar(6) COLLATE latin1_bin NOT NULL,
  `Date_Attribution` varchar(10) COLLATE latin1_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

CREATE TABLE IF NOT EXISTS `MAJSDT`(
  `EZABPQM` varchar(10) COLLATE latin1_bin NOT NULL,
  `Mnémo` varchar(6) COLLATE latin1_bin NOT NULL,
  `Attributaire` text COLLATE latin1_bin NOT NULL,
  `Date_Attribution` varchar(10) COLLATE latin1_bin NOT NULL,
  `N° décision d'attribution` text COLLATE latin1_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

CREATE TABLE IF NOT EXISTS `CONCATENATION`(
  `EZABPQM` varchar(10) COLLATE latin1_bin NOT NULL,
  `Tranche_Debut` varchar(14) COLLATE latin1_bin NOT NULL,
  `Tranche_Fin` varchar(14) COLLATE latin1_bin NOT NULL,
  `Code_Operateur` varchar(6) COLLATE latin1_bin NOT NULL,
  `Identite_Operateur` text COLLATE latin1_bin NOT NULL,
  `Territoire` text COLLATE latin1_bin NOT NULL,
  `Date_Attribution` varchar(10) COLLATE latin1_bin NOT NULL,
  `Fichier_Arcep` varchar(8) COLLATE latin1_bin NOT NULL,
  `Date_Attribution_MEF` varchar(8) COLLATE latin1_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

CREATE TABLE IF NOT EXISTS `CONCATENATION_DATE`(
  `Nb_Attributions` int COLLATE latin1_bin NOT NULL,
  `Date_Attribution` DATE COLLATE latin1_bin NOT NULL,
  `Date_Attribution_MEF` varchar(8) COLLATE latin1_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

CREATE TABLE IF NOT EXISTS `CALENDAR`( -- Pour gérer le cas des dates où aucun opérateur n'a été déclaré
  `datefield` DATE
);

-- On supprime le contenu des tables
DELETE FROM `MAJOPE` WHERE 1;
DELETE FROM `MAJNUM` WHERE 1;
DELETE FROM `MAJPORTA` WHERE 1;
DELETE FROM `MAJSDT` WHERE 1;
DELETE FROM `CONCATENATION` WHERE 1;
DELETE FROM `CONCATENATION_DATE` WHERE 1;
DELETE FROM `CALENDAR` WHERE 1;

-- On créé la procédure pour remplir la table CALENDAR
DROP PROCEDURE IF EXISTS fill_calendar;

CREATE PROCEDURE fill_calendar(start_date DATE, end_date DATE)
BEGIN
DECLARE crt_date DATE;
SET crt_date=start_date;
WHILE crt_date < end_date DO
INSERT INTO calendar VALUES(crt_date);
SET crt_date = ADDDATE(crt_date, INTERVAL 1 DAY);
END WHILE;
END;

-- IMPORT

-- On importe les fichiers au format csv dans les tables
-- Attention à modifier si nécessaire le fichier de confifguration de mysql (my.ini ou my.cnf) pour ajouter la ligne secure_file_priv="" à la fin en cas d'erreur !
-- Attention : l'insertion de données nécessite un chemin complet et non un chemin relatif, modifier le chemin avant d'exécuter le script !

-- On insère le fichier MAJOPE dans la table MAJOPE
LOAD DATA INFILE "/CHEMIN/ABSOLU/VERS/LE/DOSSIER/temp/MAJOPE.csv"
INTO TABLE MAJOPE
CHARACTER SET UTF8
FIELDS
TERMINATED BY ';'
ENCLOSED BY '"'
LINES
TERMINATED BY '\n'
IGNORE 1 LINES;

-- On insère le fichier MAJNUM dans la table MAJNUM
LOAD DATA INFILE "/CHEMIN/ABSOLU/VERS/LE/DOSSIER/temp/MAJNUM.csv"
INTO TABLE MAJNUM
CHARACTER SET UTF8
FIELDS
TERMINATED BY ';'
ENCLOSED BY '"'
LINES
TERMINATED BY '\n'
IGNORE 1 LINES;

-- On insère le fichier MAJPORTA dans la table MAJPORTA
LOAD DATA INFILE "/CHEMIN/ABSOLU/VERS/LE/DOSSIER/temp/MAJPORTA.csv"
INTO TABLE MAJPORTA
FIELDS
TERMINATED BY ';'
ENCLOSED BY '"'
LINES
TERMINATED BY '\n'
IGNORE 1 LINES;

-- On insère le fichier MAJSDT dans la table MAJSDT
LOAD DATA INFILE "/CHEMIN/ABSOLU/VERS/LE/DOSSIER/temp/MAJSDT.csv"
INTO TABLE MAJSDT
FIELDS
TERMINATED BY ';'
ENCLOSED BY '"'
LINES
TERMINATED BY '\n'
IGNORE 1 LINES;

-- On remplit la table CALENDAR
CALL fill_calendar('1999-01-01', DATE(NOW()));

-- APRES IMPORT

-- On insère la table MAJNUM dans la table CONCATENATION
INSERT INTO CONCATENATION (EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep, Date_Attribution_MEF)
SELECT EZABPQM, Tranche_Debut, Tranche_Fin, Mnémo AS Code_Operateur, MAJOPE.IDENTITE_OPERATEUR AS Identite_Operateur, Territoire, Date_Attribution, "MAJNUM" AS Fichier_Arcep, right(Date_Attribution,4)*10000+left(right(Date_Attribution,7),2)*100+left(Date_Attribution,2) AS Date_Attribution_MEF
FROM MAJNUM
INNER JOIN MAJOPE ON MAJNUM.Mnémo=MAJOPE.CODE_OPERATEUR;

-- On insère la table MAJPORTA dans la table CONCATENATION (en créant les champs Tranche_Debut par ajout de 0000 à EZABPQM et Tranche_Fin par ajout de 9999 à EZABPQM)
INSERT INTO CONCATENATION (EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep, Date_Attribution_MEF)
SELECT EZABPQM, CONCAT(EZABPQM,"0000") AS Tranche_Debut, CONCAT(EZABPQM, "9999") AS Tranche_Fin, Mnémo AS Code_Operateur, MAJOPE.IDENTITE_OPERATEUR AS Identite_Operateur, "" AS Territoire, Date_Attribution, "MAJPORTA" AS Fichier_Arcep, right(Date_Attribution,4)*10000+left(right(Date_Attribution,7),2)*100+left(Date_Attribution,2) AS Date_Attribution_MEF
FROM MAJPORTA
INNER JOIN MAJOPE ON MAJPORTA.Mnémo=MAJOPE.CODE_OPERATEUR;

-- On insère la table MAJSDT dans la table CONCATENATION (les 16XY sont rassemblés en un champ texte de 4 caractères sans espace)
INSERT INTO CONCATENATION (EZABPQM, Tranche_Debut, Tranche_Fin, Code_Operateur, Identite_Operateur, Territoire, Date_Attribution, Fichier_Arcep, Date_Attribution_MEF)
SELECT if(left(EZABPQM,2)="16",CONCAT(left(EZABPQM,2),right(EZABPQM,2)),EZABPQM) AS EZABPQM, if(left(EZABPQM,2)="16",CONCAT(left(EZABPQM,2),right(EZABPQM,2)),EZABPQM) AS Tranche_Debut, if(left(EZABPQM,2)="16",CONCAT(left(EZABPQM,2),right(EZABPQM,2)),EZABPQM) AS Tranche_Fin, Mnémo AS Code_Operateur, MAJOPE.IDENTITE_OPERATEUR AS Identite_Operateur, "" AS Territoire, Date_Attribution, "MAJSDT" AS Fichier_Arcep, right(Date_Attribution,4)*10000+left(right(Date_Attribution,7),2)*100+left(Date_Attribution,2) AS Date_Attribution_MEF
FROM MAJSDT
INNER JOIN MAJOPE ON MAJSDT.Mnémo=MAJOPE.CODE_OPERATEUR;

-- On crée une table spécifique pour récupérer le nombre d'attributions par jour
INSERT INTO CONCATENATION_DATE(Nb_Attributions, Date_Attribution, Date_Attribution_MEF)
SELECT IFNULL(COUNT(EZABPQM),0) AS Nb_Attributions, CALENDAR.datefield AS Date_Attribution, YEAR(CALENDAR.datefield) * 10000 + MONTH(CALENDAR.datefield) * 100 + DAY(CALENDAR.datefield) AS Date_Attribution_MEF
FROM CONCATENATION
RIGHT JOIN CALENDAR ON( CONCATENATION.Date_Attribution_MEF =( YEAR(CALENDAR.datefield) * 10000 + MONTH(CALENDAR.datefield) * 100 + DAY(CALENDAR.datefield) ) )
GROUP BY YEAR(CALENDAR.datefield) * 10000 + MONTH(CALENDAR.datefield) * 100 + DAY(CALENDAR.datefield);
