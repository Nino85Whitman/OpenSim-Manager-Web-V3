<?php 
include 'config/variables.php';
	echo '<HR>';
	$ligne1 = '<B>Raccourcis Hypergrid.</B>';
//	$ligne2 = '*** <u>Moteur OpenSim selectionne: </u>'.$_SESSION['opensim_select'].' - '.INI_Conf_Moteur($_SESSION['opensim_select'],"version").' ***';
	echo '<div class="block" id="clean-gray"><button><CENTER>'.$ligne1.'<br>'.$ligne2.'</CENTER></button></div>';
	echo '<hr>';

//*******************************************************	
// Lecture des regions.ini et enregistrement dans Matrice
//*******************************************************
// Parcours des serveur installes

	$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
	mysql_select_db($database,$db);
	$sql = 'SELECT * FROM moteurs';
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	$hypergrid = "";
	echo "<table width=100% border=0 cellspacing=0 cellpadding=0 >";
	while($data = mysql_fetch_assoc($req))
	{
		$hypergrid = $data['hypergrid'];$i=0;
		// Pour chaque serveur
		$tableauIni = parse_ini_file($data['address']."Regions/".$FichierINIRegions, true);
		if($tableauIni == FALSE){echo 'prb lecture ini '.$data['address']."Regions/".$FichierINIRegions.'<br>';}
		while (list($keyi, $vali) = each($tableauIni))
		{
			// **** Recuperation du port http du serveur ******		
			$filename = $data['address'].$FichierINIOpensim;		
			if (!$fp = fopen($filename,"r")){echo "Echec de l'ouverture du fichier ".$filename;}		
				$tabfich=file($filename); 
				for( $i = 1 ; $i < count($tabfich) ; $i++ )
				{
				$porthttp = strstr($tabfich[$i],"http_listener_port");
				if($porthttp){$posEgal = strpos($porthttp,'=');$longueur = strlen($porthttp);$srvOS = substr($porthttp, $posEgal + 1);}
				}
				fclose($fp);
			//****************************************************		
			
			$ImgMap = "http://".$tableauIni[$keyi]['ExternalHostName'].":".trim($srvOS)."/index.php?method=regionImage".str_replace("-","",$tableauIni[$keyi]['RegionUUID']);
			// Affichage 
			$TD_Hypergrid = '<td width=75 align=center><a href="secondlife://'.$hypergrid.':'.$keyi.'">'.$keyi.'</a><br><a href="secondlife://'.$hypergrid.':'.$keyi.'"><img src="'.$ImgMap.'" width=45 height=45 BORDER=1 "></a></td>';
			if ($cpt==3)
				{echo $TD_Hypergrid.'</tr>';
				$cpt=0;}
			else
				{echo $TD_Hypergrid;
				$cpt++;} 			 
		}
		//*******************************************************
		//*******************************************************
	}
	echo '</table>';
mysql_close();

?>