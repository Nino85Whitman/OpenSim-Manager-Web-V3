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

function DownloadFile($filename) {

echo $filename ;

// required for IE, otherwise Content-disposition is ignored
if(ini_get('zlib.output_compression'))
  ini_set('zlib.output_compression', 'Off');

// addition by Jorg Weske
$file_extension = strtolower(substr(strrchr($filename,"."),1));

if( $filename == "" ) 
{
  echo "<html><title> Download Script</title><body>ERROR: download file NOT SPECIFIED. USE force-download.php?file=filepath</body></html>";
  exit;
} elseif ( ! file_exists( $filename ) ) 
{
  echo "<html><title> Download Script</title><body>ERROR: File not found. USE force-download.php?file=filepath</body></html>";
  exit;
};
switch( $file_extension )
{
  case "pdf": $ctype="application/pdf"; break;
  case "exe": $ctype="application/octet-stream"; break;
  case "zip": $ctype="application/zip"; break;
  case "doc": $ctype="application/msword"; break;
  case "xls": $ctype="application/vnd.ms-excel"; break;
  case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
  case "gif": $ctype="image/gif"; break;
  case "png": $ctype="image/png"; break;
  case "jpeg":
  case "jpg": $ctype="image/jpg"; break;
  default: $ctype="application/force-download";
}
header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers 
header("Content-Type: $ctype");
// change, added quotes to allow spaces in filenames, by Rajkumar Singh
header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($filename));
readfile("$filename");
return true;

}

?>