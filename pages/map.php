<?php 
include 'config/variables.php';
	echo '<HR>';
	$ligne1 = '<B>Map des regions pour le serveur.</B>';
	$ligne2 = '*** <u>Moteur OpenSim selectionne: </u>'.$_SESSION['opensim_select'].' - '.INI_Conf_Moteur($_SESSION['opensim_select'],"version").' ***';
	echo '<div class="block" id="clean-gray"><button><CENTER>'.$ligne1.'<br>'.$ligne2.'</CENTER></button></div>';
	echo '<hr>';

//*******************************************************
 //Initialisation des variables ET du tableau
 //*******************************************************
for($x=-30;$x < 30;$x++) 		// Limite de 50x50
{
	//echo "<hr>X:".$x.'<hr>';
	for($y=-30;$y < 30;$y++) 			// Limite de 50x50
	{	//	echo "Y:".$y.'<br />';
		$Matrice[$x][$y]['name']="";	
		$Matrice[$x][$y]['img']="";
		$Matrice[$x][$y]['ip']="";
		$Matrice[$x][$y]['port']="";	
		$Matrice[$x][$y]['uuid']="";
		$Matrice[$coordX][$coordY]['hypergrid']="";
	} 
} 
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
			

			//*******************************************************
			// Recuperation des valeurs ET enregistrement des valeurs dans le tableau
			//echo $key.$tableauIni[$key]['RegionUUID'].$tableauIni[$key]['Location'].$tableauIni[$key]['InternalPort'].'<br>';
			
			 $ImgMap = "http://".$tableauIni[$keyi]['ExternalHostName'].":".trim($srvOS)."/index.php?method=regionImage".str_replace("-","",$tableauIni[$keyi]['RegionUUID']);
		 
			 $location = explode(",", $tableauIni[$keyi]['Location']);
			 $coordX = $location[0]-7000;
			 $coordY = $location[1]-7000;		
			 $Matrice[$coordX][$coordY]['name']=$keyi;			 
			 $Matrice[$coordX][$coordY]['hypergrid']=$hypergrid;
			 $Matrice[$coordX][$coordY]['img'] = $ImgMap ;
			 $Matrice[$coordX][$coordY]['ip']=$tableauIni[$keyi]['ExternalHostName'];
			 $Matrice[$coordX][$coordY]['port']=$tableauIni[$keyi]['InternalPort'];	
			 $Matrice[$coordX][$coordY]['uuid']=$key.$tableauIni[$keyi]['RegionUUID'];
		}
		//*******************************************************
	}
mysql_close();
	
//*******************************************************	
// Map en construction *******************
//*******************************************************	
//echo $_POST['zooming'];
if (isset($_POST['zooming']))
{ $widthMap = $_POST['zooming']; $heightMap = $_POST['zooming'];}
else{$widthMap = "64";$heightMap= "64";  // Par default si pas zoom
}

echo '<table border=0 align=center WIDTH=100%><tr align=center><td>
<FORM METHOD=POST ACTION="">
	<select name="zooming">
	<option value="4" name="id">Zoom -2</option><option value="8" name="id">Zoom -1</option><option value="16" name="id">Zoom 0</option>
	<option value="32" name="id">Zoom 1</option><option value="64" name="id" selected>Zoom 2</option><option value="128" name="id">Zoom 3</option>
	<option value="256" name="id">Zoom 4</option><option value="512" name="id">Zoom 5</option></select><input type="submit" name="goto" value="Appliquer Zoom">
</form></td></tr></table>
<table border=0 align=center><tr align=center>';

for($y=30;$y > -30;$y--) 		// Limite Y
{
	echo '<tr>';
	for($x=-30;$x < 30;$x++) 			// Limite X
	{
		echo '<td>';
		if(Test_Url($Matrice[$x][$y]['img']) <> '1')
		{
			echo $textemap = $Matrice[$x][$y]['name'];
		}
		else
		{
			$textemap = $Matrice[$x][$y]['name'];
			echo '<img src="'.$Matrice[$x][$y]['img'].'" width="'.$widthMap.'" height="'.$heightMap.'" BORDER="0" alt="'.$textemap.'">';
//			echo '<a href="secondlife://'.$Matrice[$coordX][$coordY]['hypergrid'].':'.$Matrice[$x][$y]['name'].'"><img src="'.$Matrice[$x][$y]['img'].'" width="'.$widthMap.'" height="'.$heightMap.'" BORDER="0" alt="'.$textemap.'"></a>';
		}
		echo "</td>";
	} 
	echo '</tr>';
} 
echo "</table>";
//*******************************************

?>