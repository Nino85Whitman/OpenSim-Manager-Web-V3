<?php 

//*************************************************************************************
function ExtractValeur($chaine){
	$posEgal = strpos($chaine,';');
	if($posEgal <> 0)
	{
		$longueur = strlen($chaine);
		$ExtractValeur[0] = "Commentaire:";
		$ExtractValeur[1] = substr($chaine, 0,$longueur );
		return $ExtractValeur;
	}
	
	$posEgal = strpos($chaine,'=');
	if($posEgal === false)
	{
		$posEgal2 = strpos($chaine,']');
		$longueur = strlen($chaine);
		$ExtractValeur[0] = "CLES";
		$ExtractValeur[1] = substr($chaine, 1,$longueur - 3);
		return $ExtractValeur;
	}else
	{
		$longueur = strlen($chaine);
		$ExtractValeur[0] = trim(substr($chaine, 0, $posEgal - 1));
		$ExtractValeur[1] = trim(substr($chaine, $posEgal + 1));
		return $ExtractValeur;
	}
	
}			
//*************************************************************************************
function INI_Conf($cles,$valeur){
	require 'config/osmw_conf.php';
	
	// on se connecte à MySQL
		$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
		mysql_select_db($database,$db);
		$sql = "SELECT * FROM conf";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		$data = mysql_fetch_array($req);
		switch ($valeur) {
		default:
			$Version = "N.C";
		case "cheminAppli":
			$Versions=$data['cheminAppli'];
			break;
		case "destinataire":
			$Version=$data['destinataire'];
			break;
		case "Autorized":
			$Version=$data['Autorized'];
			break;
		case "VersionOSMW":
			$Version=$data['VersionOSMW'];
			break;
		case "urlOSMW":
			$Version=$data['urlOSMW'];
			break;
		}
		mysql_close();
	return $Version;
}
//*************************************************************************************
function INI_Conf_Moteur($cles,$valeur){
	require 'config/osmw_conf.php';
	
	// on se connecte à MySQL
		$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
		mysql_select_db($database,$db);
		$sql = "SELECT * FROM moteurs WHERE id_os ='".$cles."'";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		$data = mysql_fetch_array($req);
		switch ($valeur) {
		default:
			$Version = "N.C";
		case "name":
			$Versions=$data['name'];
			break;
		case "version":
			$Version=$data['version'];
			break;
		case "address":
			$Version=$data['address'];
			break;
		case "DB_OS":
			$Version=$data['DB_OS'];
			break;
		case "osAutorise":
			$Version=$data['osAutorise'];
			break;
		}
		mysql_close();
	return $Version;
}
//*************************************************************************************
function NbOpensim(){
	require 'config/osmw_conf.php';
	// on se connecte à MySQL
	$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
	mysql_select_db($database,$db);
	$sql = "SELECT * FROM moteurs";
	
	$nb_journee="SELECT COUNT(DISTINCT id_os) AS compteur FROM moteurs";
	$req=mysql_query($nb_journee); 
	$tab=mysql_fetch_array($req) ;
	$Version = $tab["compteur"];
	return $Version;
}
//*************************************************************************************

?>