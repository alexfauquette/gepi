<?php
/**
 * Gestion des absences et remlacements de professeurs
 * 
 * $_POST['activer'] activation/désactivation
 * $_POST['is_posted']
 * 
 *
 * @copyright Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
 * @license GNU/GPL, 
 * @package Carnet_de_notes
 * @subpackage administration
 * @see checkAccess()
 * @see saveSetting()
 * @see suivi_ariane()
 */

/* This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$accessibilite="y";
$titre_page = "Gestion module Engagements";
$niveau_arbo = 1;
$gepiPathJava="./..";

/**
 * Fichiers d'initialisation
 */
require_once("../lib/initialisations.inc.php");
// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_engagements/index_admin.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_engagements/index_admin.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Engagements',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

/******************************************************************
 *    Enregistrement des variables passées en $_POST si besoin
 ******************************************************************/
$msg = '';
$post_reussi=FALSE;

//debug_var();

if((isset($_POST['is_posted']))&&($_POST['is_posted']==1)) {
	check_token();

	if (isset($_POST['activer'])) {
		if (!saveSetting("active_mod_engagements", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
	}
}

//debug_var();

if((isset($_POST['is_posted']))&&($_POST['is_posted']==2)) {
	check_token();

	$msg="";

	$tab_engagements=get_tab_engagements();

	$nom=isset($_POST['nom']) ? $_POST['nom'] : array();
	$description=isset($_POST['description']) ? $_POST['description'] : array();
	$type=isset($_POST['type']) ? $_POST['type'] : array();
	$conseil_de_classe=isset($_POST['conseil_de_classe']) ? $_POST['conseil_de_classe'] : array();
	$ConcerneEleve=isset($_POST['ConcerneEleve']) ? $_POST['ConcerneEleve'] : array();
	$ConcerneResponsable=isset($_POST['ConcerneResponsable']) ? $_POST['ConcerneResponsable'] : array();
	$SaisieScol=isset($_POST['SaisieScol']) ? $_POST['SaisieScol'] : array();
	$SaisieCpe=isset($_POST['SaisieCpe']) ? $_POST['SaisieCpe'] : array();
	$SaisiePP=isset($_POST['SaisiePP']) ? $_POST['SaisiePP'] : array();

	$nb_modif=0;
	for($loop=0;$loop<count($tab_engagements['indice']);$loop++) {

		if($nom[$tab_engagements['indice'][$loop]['id']]!="") {

			$sql="SELECT 1=1 FROM engagements WHERE nom='".$nom[$tab_engagements['indice'][$loop]['id']]."' AND id!='".$tab_engagements['indice'][$loop]['id']."';";
			//echo "$sql<br />";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0) {
				$msg.="Deux engagements ne peuvent pas avoir le même nom : ".$nom[$tab_engagements['indice'][$loop]['id']]."<br />";
			}
			else {
				$ajout_sql="";
				if(($nom[$tab_engagements['indice'][$loop]['id']]!="")&&(stripslashes($nom[$tab_engagements['indice'][$loop]['id']])!=$tab_engagements['indice'][$loop]['nom'])) {
					$ajout_sql.=", nom='".$nom[$tab_engagements['indice'][$loop]['id']]."'";
				}
				if(stripslashes(preg_replace('/(\\\n)+/',"\n", $description[$tab_engagements['indice'][$loop]['id']]))!=$tab_engagements['indice'][$loop]['description']) {
					$ajout_sql.=", description='".$description[$tab_engagements['indice'][$loop]['id']]."'";
				}

				if((isset($conseil_de_classe[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['conseil_de_classe']!='yes')) {
					$ajout_sql.=", conseil_de_classe='yes'";
				}
				elseif((!isset($conseil_de_classe[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['conseil_de_classe']=='yes')) {
					$ajout_sql.=", conseil_de_classe='no'";
				}

				if((isset($type[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['type']!='id_classe')) {
					$ajout_sql.=", type='id_classe'";
				}
				elseif((!isset($type[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['type']=='id_classe')) {
					$ajout_sql.=", type=''";
				}

				if((isset($ConcerneEleve[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['ConcerneEleve']!='yes')) {
					$ajout_sql.=", ConcerneEleve='yes'";
				}
				elseif((!isset($ConcerneEleve[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['ConcerneEleve']=='yes')) {
					$ajout_sql.=", ConcerneEleve='no'";
				}

				if((isset($ConcerneResponsable[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['ConcerneResponsable']!='yes')) {
					$ajout_sql.=", ConcerneResponsable='yes'";
				}
				elseif((!isset($ConcerneResponsable[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['ConcerneResponsable']=='yes')) {
					$ajout_sql.=", ConcerneResponsable='no'";
				}

				if((isset($SaisieScol[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['SaisieScol']!='yes')) {
					$ajout_sql.=", SaisieScol='yes'";
				}
				elseif((!isset($SaisieScol[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['SaisieScol']=='yes')) {
					$ajout_sql.=", SaisieScol='no'";
				}

				if((isset($SaisieCpe[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['SaisieCpe']!='yes')) {
					$ajout_sql.=", SaisieCpe='yes'";
				}
				elseif((!isset($SaisieCpe[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['SaisieCpe']=='yes')) {
					$ajout_sql.=", SaisieCpe='no'";
				}

				if((isset($SaisiePP[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['SaisiePP']!='yes')) {
					$ajout_sql.=", SaisiePP='yes'";
				}
				elseif((!isset($SaisiePP[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['SaisiePP']=='yes')) {
					$ajout_sql.=", SaisiePP='no'";
				}

				if($ajout_sql!="") {
					$sql="UPDATE engagements SET id='".$tab_engagements['indice'][$loop]['id']."'";
					$sql.=$ajout_sql;
					$sql.=" WHERE id='".$tab_engagements['indice'][$loop]['id']."';";
					//echo "$sql<br />";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$update) {
						$msg.="Erreur lors de la modification de l'engagement n°".$tab_engagements['indice'][$loop]['id']."<br />";
					}
					else {
						//echo "Modif sur $sql<br />";
						$nb_modif++;

						// Modifier aussi le id_type et valeur dans engagements_user
						if((isset($type[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['type']!='id_classe')) {

							$sql="SELECT * FROM engagements_user WHERE id_type='' AND id_engagement='".$tab_engagements['indice'][$loop]['id']."';";
							$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_u)>0) {
								$chaine_u='';
								while($lig_u=mysqli_fetch_object($res_u)) {
									if($chaine_u!='') {
										$chaine_u.=', ';
									}
									$chaine_u.=civ_nom_prenom($lig_u->login);
								}

								$sql="DELETE FROM engagements_user WHERE id_type='' AND id_engagement='".$tab_engagements['indice'][$loop]['id']."';";
								$del=mysqli_query($GLOBALS["mysqli"], $sql);
								if(!$del) {
									$msg.="Erreur lors de la suppression des engagements préalablment non liés à une classe pour les utilisateurs qui avaient préalablement l'engagement n°".$tab_engagements['indice'][$loop]['id']."<br />";
								}
								else {
									$msg.="Suppression de l'engagement n°".$tab_engagements['indice'][$loop]['id']." pour ".$chaine_u." qui avaient cet engagement avant qu'il devienne lié à une classe.<br />Il faudra re-déclarer ces personnes en précisant à quelle classe ils sont liés.<br />";
								}
							}
						}
						elseif((!isset($type[$tab_engagements['indice'][$loop]['id']]))&&($tab_engagements['indice'][$loop]['type']=='id_classe')) {
							$sql="UPDATE engagements_user SET id_type='', valeur='0' WHERE id_engagement='".$tab_engagements['indice'][$loop]['id']."';";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$update) {
								$msg.="Erreur lors de la modification du type 'non lié à une classe' pour les utilisateurs qui avaient préalablement l'engagement n°".$tab_engagements['indice'][$loop]['id']."<br />";
							}
						}
					}
				}

				$SaisieLogin=isset($_POST["SaisieLogin_".$tab_engagements['indice'][$loop]['id']]) ? $_POST["SaisieLogin_".$tab_engagements['indice'][$loop]['id']] : array();
				if(isset($tab_engagements['indice'][$loop]['droit_user'])) {
					for($loop_u=0;$loop_u<count($tab_engagements['indice'][$loop]['droit_user']);$loop_u++) {
						if(!in_array($tab_engagements['indice'][$loop]['droit_user'][$loop_u], $SaisieLogin)) {
							$sql="DELETE FROM engagements_droit_saisie WHERE id_engagement='".$tab_engagements['indice'][$loop]['id']."' AND login='".$tab_engagements['indice'][$loop]['droit_user'][$loop_u]."';";
							//echo "$sql<br />";
							$del=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$del) {
								$msg.="Erreur lors de la suppression de ".$tab_engagements['indice'][$loop]['droit_user'][$loop_u]." pour l'engagement n°".$tab_engagements['indice'][$loop]['id']."<br />";
							}
							else {
								//echo "Modif sur $sql<br />";
								$nb_modif++;
							}
						}
					}
				}

				for($loop_u=0;$loop_u<count($SaisieLogin);$loop_u++) {
					if(($SaisieLogin[$loop_u]!="")&&(!in_array($SaisieLogin[$loop_u], $tab_engagements['indice'][$loop]['droit_user']))) {
						$sql="INSERT INTO engagements_droit_saisie SET id_engagement='".$tab_engagements['indice'][$loop]['id']."', login='".$SaisieLogin[$loop_u]."';";
						//echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							$msg.="Erreur lors de l'ajout de ".$tab_engagements['indice'][$loop]['droit_user'][$loop_u]." pour l'engagement n°".$tab_engagements['indice'][$loop]['id']."<br />";
						}
						else {
							//echo "Modif sur $sql<br />";
							$nb_modif++;
						}
					}
				}
			}
		}
	}
	if($nb_modif>0) {
		$msg.=$nb_modif." engagement(s) modifié(s).<br />";
	}

	$suppr=isset($_POST['suppr']) ? $_POST['suppr'] : array();
	for($loop=0;$loop<count($suppr);$loop++) {
		$sql="DELETE FROM engagements_user WHERE id_engagement='".$suppr[$loop]."';";
		//echo "$sql<br />";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$del) {
			$msg.="Erreur lors de la suppression de l'association avec l'engagement n°".$suppr[$loop]."<br />";
		}
		else {
			$sql="DELETE FROM engagements_droit_saisie WHERE id_engagement='".$suppr[$loop]."';";
			//echo "$sql<br />";
			$del=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$del) {
				$msg.="Erreur lors de la suppression de l'association utilisateur avec droit de saisie avec l'engagement n°".$suppr[$loop]."<br />";
			}
			else {
				$sql="DELETE FROM engagements WHERE id='".$suppr[$loop]."';";
				//echo "$sql<br />";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$del) {
					$msg.="Erreur lors de la suppression de l'engagement n°".$suppr[$loop]."<br />";
				}
				else {
					$msg.="Engagement n°".$suppr[$loop]." supprimé.<br />";
				}
			}
		}
	}

	$AjoutEngagementNom=isset($_POST['AjoutEngagementNom']) ? $_POST['AjoutEngagementNom'] : "";
	$AjoutEngagementDescription=isset($_POST['AjoutEngagementDescription']) ? $_POST['AjoutEngagementDescription'] : "";
	$AjoutEngagementType=isset($_POST['AjoutEngagementType']) ? $_POST['AjoutEngagementType'] : "";
	$AjoutEngagementConseilClasse=isset($_POST['AjoutEngagementConseilClasse']) ? $_POST['AjoutEngagementConseilClasse'] : "";
	$AjoutEngagementEle=isset($_POST['AjoutEngagementEle']) ? $_POST['AjoutEngagementEle'] : "";
	$AjoutEngagementResp=isset($_POST['AjoutEngagementResp']) ? $_POST['AjoutEngagementResp'] : "";
	$AjoutEngagementSaisieCpe=isset($_POST['AjoutEngagementSaisieCpe']) ? $_POST['AjoutEngagementSaisieCpe'] : "";
	$AjoutEngagementSaisieScol=isset($_POST['AjoutEngagementSaisieScol']) ? $_POST['AjoutEngagementSaisieScol'] : "";
	$AjoutEngagementSaisiePP=isset($_POST['AjoutEngagementSaisiePP']) ? $_POST['AjoutEngagementSaisiePP'] : "";
	$AjoutEngagementSaisieLogin=isset($_POST['AjoutEngagementSaisieLogin']) ? $_POST['AjoutEngagementSaisieLogin'] : array();
	if($AjoutEngagementNom!="") {
		$sql="SELECT 1=1 FROM engagements WHERE nom='".$AjoutEngagementNom."';";
		//echo "$sql<br />";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)>0) {
			$msg.="Deux engagements ne peuvent pas avoir le même nom : ".$nom[$tab_engagements['indice'][$loop]['id']]."<br />";
		}
		else {
			$sql="INSERT INTO engagements SET nom='".$AjoutEngagementNom."', description='".$AjoutEngagementDescription."'";
			if($AjoutEngagementType=='id_classe') {
				$sql.=", type='id_classe'";
			}
			if($AjoutEngagementConseilClasse=='yes') {
				$sql.=", conseil_de_classe='yes'";
			}
			if($AjoutEngagementEle=='yes') {
				$sql.=", ConcerneEleve='yes'";
			}
			if($AjoutEngagementResp=='yes') {
				$sql.=", ConcerneResponsable='yes'";
			}
			if($AjoutEngagementSaisieScol=='yes') {
				$sql.=", SaisieScol='yes'";
			}
			if($AjoutEngagementSaisieCpe=='yes') {
				$sql.=", SaisieCpe='yes'";
			}
			if($AjoutEngagementSaisiePP=='yes') {
				$sql.=", SaisiePP='yes'";
			}
			$sql.=";";
			//echo "$sql<br />";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$insert) {
				$msg.="Erreur lors de l'ajout d'un engagement.<br />";
			}
			else {
				$msg.="Engagement ajouté.<br />";

				$nb_modif=0;
				$id_engagement=mysqli_insert_id($GLOBALS['mysqli']);
				for($loop=0;$loop<count($AjoutEngagementSaisieLogin);$loop++) {
					if($AjoutEngagementSaisieLogin[$loop]!="") {
						$sql="INSERT INTO engagements_droit_saisie SET id_engagement='".$id_engagement."', login='".$AjoutEngagementSaisieLogin[$loop]."';";
						echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							$msg.="Erreur lors de l'ajout de ".$AjoutEngagementSaisieLogin[$loop]." pour le nouvel engagement (n°".$id_engagement.")<br />";
						}
						else {
							//echo "Modif sur $sql<br />";
							$nb_modif++;
						}
					}
				}
			}
		}
	}

}

if((isset($_POST['is_posted']))&&($_POST['is_posted']==3)) {
	check_token();

	$msg="";

	$tab_engagements=get_tab_engagements();
	$tab_engagements_pages=get_tab_engagements_pages();

	/*
	echo "<div style='float:left;width:600px; margin:1em; background:white;'>
	<pre>";
	print_r($tab_engagements);
	echo "</pre>
	</div>";
	echo "<div style='float:left;width:600px; margin:1em; background:white;'>
	<pre>";
	print_r($tab_engagements_pages);
	echo "</pre>
	</div>";
	echo "<div style='clear:both'></div>";
	debug_var();
	*/

	$visu_profs_class=isset($_POST['visu_profs_class']) ? $_POST['visu_profs_class'] : array();
	$visu_profs_eleve=isset($_POST['visu_profs_eleve']) ? $_POST['visu_profs_eleve'] : array();

	$nb_modif=0;
	for($loop=0;$loop<count($tab_engagements['indice']);$loop++) {
		$current_id_engagement=$tab_engagements['indice'][$loop]['id'];

		if(isset($visu_profs_class[$current_id_engagement])) {
			if(!array_key_exists($current_id_engagement, $tab_engagements_pages['id_type'])) {
				$sql="INSERT INTO engagements_pages SET page='visu_profs_class', id_type='".$current_id_engagement."';";
				//echo "$sql<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'enregistrement pour l'engagement n°$current_id_engagement.<br />";
				}
				else {
					$nb_modif++;
				}
			}
			elseif((!isset($tab_engagements_pages['id_type'][$current_id_engagement]["pages"]))||(!in_array("visu_profs_class", $tab_engagements_pages['id_type'][$current_id_engagement]["pages"]))) {
				$sql="INSERT INTO engagements_pages SET page='visu_profs_class', id_type='".$current_id_engagement."';";
				//echo "$sql<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'enregistrement pour l'engagement n°$current_id_engagement.<br />";
				}
				else {
					$nb_modif++;
				}
			}
		}
		else {
			if((isset($tab_engagements_pages['id_type'][$current_id_engagement]["pages"]))&&(in_array("visu_profs_class", $tab_engagements_pages['id_type'][$current_id_engagement]["pages"]))) {
				$sql="DELETE FROM engagements_pages WHERE page='visu_profs_class' AND id_type='".$current_id_engagement."';";
				//echo "$sql<br />";
				$delete=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$delete) {
					$msg.="Erreur lors de la suppression de l'affichage de l'engagement n°$current_id_engagement sur 'visu_profs_class'.<br />";
				}
				else {
					$nb_modif++;
				}
			}
		}

		if(isset($visu_profs_eleve[$current_id_engagement])) {
			if(!array_key_exists($current_id_engagement, $tab_engagements_pages['id_type'])) {
				$sql="INSERT INTO engagements_pages SET page='visu_profs_eleve', id_type='".$current_id_engagement."';";
				//echo "$sql<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'enregistrement pour l'engagement n°$current_id_engagement.<br />";
				}
				else {
					$nb_modif++;
				}
			}
			elseif((!isset($tab_engagements_pages['id_type'][$current_id_engagement]["pages"]))||(!in_array("visu_profs_eleve", $tab_engagements_pages['id_type'][$current_id_engagement]["pages"]))) {
				$sql="INSERT INTO engagements_pages SET page='visu_profs_eleve', id_type='".$current_id_engagement."';";
				//echo "$sql<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'enregistrement pour l'engagement n°$current_id_engagement.<br />";
				}
				else {
					$nb_modif++;
				}
			}
		}
		else {
			if((isset($tab_engagements_pages['id_type'][$current_id_engagement]["pages"]))&&(in_array("visu_profs_eleve", $tab_engagements_pages['id_type'][$current_id_engagement]["pages"]))) {
				$sql="DELETE FROM engagements_pages WHERE page='visu_profs_eleve' AND id_type='".$current_id_engagement."';";
				//echo "$sql<br />";
				$delete=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$delete) {
					$msg.="Erreur lors de la suppression de l'affichage de l'engagement n°$current_id_engagement sur 'visu_profs_eleve'.<br />";
				}
				else {
					$nb_modif++;
				}
			}
		}
	}

	$msg.=$nb_modif." modification(s) enregistrée(s).<br />";
}

if (isset($_POST['is_posted']) and ($msg=='')){
	$msg = "Les modifications ont été enregistrées !";
	$post_reussi=TRUE;
}

$msg.=menage_engagements_user();

$tab_engagements=get_tab_engagements();

// on demande une validation si on quitte sans enregistrer les changements
$messageEnregistrer="Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?";
/****************************************************************
                     HAUT DE PAGE
****************************************************************/

// ====== Inclusion des balises head et du bandeau =====
/**
 * Entête de la page
 */
include_once("../lib/header_template.inc.php");

/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/

if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la création du fil d'ariane";

/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime="";
$tbs_pmv="";
require_once ("../lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseigné
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var();


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/mod_engagements/index_admin_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
/**
 * Inclusion du gabarit
 */
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($menuAffiche);

?>
