<?php
/**
 * Fichier de mise à jour de la version 1.7.3 à la version 1.7.4 par défaut
 *
 *
 * Le code PHP présent ici est exécuté tel quel.
 * Pensez à conserver le code parfaitement compatible pour une application
 * multiple des mises à jour. Toute modification ne doit être réalisée qu'après
 * un test pour s'assurer qu'elle est nécessaire.
 *
 * Le résultat de la mise à jour est du html préformaté. Il doit être concaténé
 * dans la variable $result, qui est déjà initialisé.
 *
 * Exemple : $result .= msj_ok("Champ XXX ajouté avec succès");
 *
 * @copyright Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @see msj_ok()
 * @see msj_erreur()
 * @see msj_present()
 */

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.7.4 :</h3>";

/*
// Section d'exemple

// Attention : on peut effectuer des mysqli_query() pour des tests en SELECT,
//             mais toujours utiliser traite_requete() pour les CREATE, ALTER, INSERT, UPDATE
//             pour que le message indiquant qu'il s'est produit une erreur soit affiché en haut de la page (l'admin ne lit pas toute la page;)

$result .= "&nbsp;-> Ajout d'un champ 'tel_pers' à la table 'eleves'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM eleves LIKE 'tel_pers';"));
if ($test_champ==0) {
	$sql="ALTER TABLE eleves ADD tel_pers varchar(255) NOT NULL default '';";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'droits_acces_fichiers' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'droits_acces_fichiers'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS droits_acces_fichiers (
	id INT(11) unsigned NOT NULL auto_increment,
	fichier VARCHAR( 255 ) NOT NULL ,
	identite VARCHAR( 255 ) NOT NULL ,
	type VARCHAR( 255 ) NOT NULL,
	PRIMARY KEY ( id )
	) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

// Merci de ne pas enlever le témoin ci-dessous de "fin de section exemple"
// Fin SECTION EXEMPLE
*/

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'abs_bull_delais' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'abs_bull_delais'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE abs_bull_delais (periode int(11) NOT NULL default '0', id_classe int(11) NOT NULL default '0', totaux CHAR(1) NOT NULL default 'n', appreciation CHAR(1) NOT NULL default 'n',date_limite TIMESTAMP NOT NULL, mode VARCHAR(100) NOT NULL DEFAULT '', PRIMARY KEY  (periode, id_classe), INDEX id_classe (id_classe)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$acces_moy_ele_resp=getSettingValue('acces_moy_ele_resp');
$result .= "&nbsp;-> Initialisation de 'acces_moy_ele_resp' pour l'accès élève/responsable aux moyennes des bulletins&nbsp;: ";
if ($acces_moy_ele_resp=='') {
	$result_inter=saveSetting('acces_moy_ele_resp', 'immediat');
	if ($result_inter) {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Valeur déjà renseignée.");
}

$acces_moy_ele_resp_cn=getSettingValue('acces_moy_ele_resp_cn');
$result .= "&nbsp;-> Initialisation de 'acces_moy_ele_resp_cn' pour l'accès élève/responsable aux moyennes des carnets de notes&nbsp;: ";
if ($acces_moy_ele_resp_cn=='') {
	$result_inter=saveSetting('acces_moy_ele_resp_cn', 'immediat');
	if ($result_inter) {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Valeur déjà renseignée.");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'b_droits_divers' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'b_droits_divers'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS b_droits_divers (login varchar(50) NOT NULL default '', nom_droit varchar(50) NOT NULL default '', valeur_droit varchar(50) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "<br />";
$result .= "<strong>Ajout d'une table 'commentaires_types_d_apres_moy' :</strong><br />";
$test = sql_query1("SHOW TABLES LIKE 'commentaires_types_d_apres_moy'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS commentaires_types_d_apres_moy (
						id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						login VARCHAR( 255 ) NOT NULL ,
						note_min float(10,2) NOT NULL DEFAULT '0.0' ,
						note_max float(10,2) NOT NULL DEFAULT '20.1' ,
						app TEXT NOT NULL
						) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("La table existe déjà");
}

$result .= "&nbsp;-> Ajout d'un champ 'visibilite_eleve' à la table 'aid'<br />";
$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM aid LIKE 'visibilite_eleve';"));
if ($test_champ==0) {
	$sql="ALTER TABLE aid ADD visibilite_eleve ENUM( 'y', 'n' ) NOT NULL DEFAULT 'y';";
	$result_inter = traite_requete($sql);
	if ($result_inter == '') {
		$result .= msj_ok("SUCCES !");
	}
	else {
		$result .= msj_erreur("ECHEC !");
	}
} else {
	$result .= msj_present("Le champ existe déjà");
}

?>