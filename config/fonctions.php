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

// FONCTIONS NON UTILISE
//*************************************************************************************
function Creation_ConfigINI(){
 
$cheminComplet = $_SERVER['SCRIPT_FILENAME'];
$chemin = explode("/", $cheminComplet);

	$fp = fopen ("config.ini.php", "w+");  
	fputs($fp,"[Parametre_OSMW]\r\n");
	fputs($fp,";******** Repertoire ou est installe OpenSim Manager Web ************\r\n");
	fputs($fp,"cheminAppli = /".$chemin[count($chemin)-2]."/\r\n");
	fputs($fp,";******** Destinataire des messages provenant de OSMW ************\r\n");
	fputs($fp,"destinataire = votreEmail@toto.com\r\n");
	fputs($fp,";******** Permet de creer un nb limité de sim 0 = Limité / 1 = NO Limit ************\r\n");
	fputs($fp,"Autorized = 1\r\n");
	fputs($fp,";******** Texte en haut a droite dans les menus de OSMW ************\r\n");
	fputs($fp,"VersionOSMW = Version N.C - ".$_SERVER['SERVER_NAME']."\r\n");
	fputs($fp,";******** Constante du fichier Regions.ini ************\r\n");
	fputs($fp,"CONST_InternalAddress = ".$_SERVER['SERVER_ADDR']."\r\n");
	fputs($fp,"CONST_AllowAlternatePorts = False\r\n");
	fputs($fp,"CONST_ExternalHostName = ".$_SERVER['SERVER_ADDR']."\r\n");
	fputs($fp,";******** Acces à la base de donnee du serveur ************ ; Non Utilisé actuellement\r\n");
	fputs($fp,"hostnameBDD = localhost\r\n");
	fputs($fp,"database =  Opensim\r\n");
	fputs($fp,"userBDD = login\r\n");
	fputs($fp,"passBDD =  password\r\n");
	fputs($fp,";******** Acces SSH du serveur ************\r\n");
	fputs($fp,"hostnameSSH = ".$_SERVER['SERVER_ADDR']."\r\n");
	fputs($fp,"usernameSSH =  login\r\n");
	fputs($fp,"passwordSSH = password\r\n");
	fclose ($fp);	
	
}
//*************************************************************************************
function Creation_MoteursINI(){
	$fp = fopen ("moteurs.ini.php", "w+");  
	fputs($fp,"[1]\r\n");
	fputs($fp,";******** Libellé du moteur doit etre le meme que pour le SCREEN lancé ************\r\n");
	fputs($fp,"name = Opensim_1\r\n");
	fputs($fp,";******** Libellé du moteur dans OSMW ************\r\n");	
	fputs($fp,"version = Version - Votre Region et ou vos sims\r\n");
	fputs($fp,";******** Chemin physique du moteur sur le serveur  ************\r\n");	
	fputs($fp,"address = /home/exemple/Opensim-0.7.1-Sim1/\r\n");
	fputs($fp,";******** Base de donnéé du moteur  ************\r\n");	
	fputs($fp,"DB_OS = Opensim\r\n");
	fclose ($fp);		 
}
//*************************************************************************************
function Creation_UsersINI(){
	$fp = fopen ("users.ini.php", "w+");  
	fputs($fp,"[root root]\r\n");
	fputs($fp,"pass = osmw\r\n");
	fputs($fp,"privilege = 4\r\n");
	fclose ($fp);		 
}
//*************************************************************************************



?>