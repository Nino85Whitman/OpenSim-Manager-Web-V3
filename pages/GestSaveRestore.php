<?php 
include 'config/variables.php';

if (session_is_registered("authentification")){ // v&eacute;rification sur la session authentification 
if($_POST['OSSelect']){$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}
	echo '<HR>';
	$ligne1 = '<B>Gestion des Sauvegardes.</B>';
	$ligne2 = '*** <u>Moteur OpenSim selectionne: </u>'.$_SESSION['opensim_select'].' - '.INI_Conf_Moteur($_SESSION['opensim_select'],"version").' ***';
	echo '<div class="block" id="clean-gray"><button><CENTER>'.$ligne1.'<br>'.$ligne2.'</CENTER></button></div>';
	echo '<hr>';
	//*****************************************************
	// Si NIV 1 - Verification Moteur Autorisé ************
	if($_SESSION['osAutorise'] != '')
	{
	$osAutorise = explode(";", $_SESSION['osAutorise']);
	//echo count($osAutorise);
	//echo $_SESSION['osAutorise'];
		for($i=0;$i < count($osAutorise);$i++)
		{	if(INI_Conf_Moteur($_SESSION['opensim_select'],"osAutorise") == $osAutorise[$i]){$moteursOK="OK";}    } 
	}
	//*****************************************************
	//******************************************************
	$btnN1 = "disabled"; $btnN2 = "disabled"; $btnN3 = "disabled";
	if( $_SESSION['privilege']==4){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 4	
	if( $_SESSION['privilege']==3){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 3
	if( $_SESSION['privilege']==2){$btnN1="";$btnN2="";}				//	Niv 2
	if($moteursOK == "OK"){if( $_SESSION['privilege']==1){$btnN1="";$btnN2="";$btnN3="";}}		//	Niv 1 + SECURITE MOTEUR
	//******************************************************
//******************************************************
// CONSTRUCTION de la commande pour ENVOI sur la console via  SSH
//******************************************************
if($_POST['cmd'])
{
	// *** Affichage mode debug ***
	//echo $_POST['cmd'];
	/*		echo $_POST['name_sim'];	echo '<BR><HR>';
	*/	
		if($_POST['format_backup'] == 'OAR'){$format_backup = "oar"; $format_backup_cmd = "oar";}
		if($_POST['format_backup'] == 'XML2'){$format_backup = "xml2"; $format_backup_cmd = "xml2";}
		//*********************************
		// === Commande BACKUP ===
		//*********************************
		if($_POST['cmd'] == 'Backup Sim')
		{
			$today = mktime(0, 0, 0, date("m"), date("d"), date("y"));$commande = $pre_cmd.'change region '.$_POST['name_sim'].';'.$pre_cmd.'save '.$format_backup.' Backup_'.$_POST['name_sim'].'_'.date("dmY", $today) .'.'.$format_backup;
			echo '<center><b> ==>> Fichier en cours de creation  ou cree  !!  Consultez le log !! <<== </b></center><BR>';
		}

//**************************************************************************
// Envoi de la commande par ssh  *******************************************
//**************************************************************************
	if($commande <> '')
	{
		if (!function_exists("ssh2_connect")) die(" function ssh2_connect doesn't exist");
		// log in at server1.example.com on port 22
		if(!($con = ssh2_connect($hostnameSSH, 22))){
			echo " fail: unable to establish connection\n";
		} else 
		{// try to authenticate with username root, password secretpassword
			if(!ssh2_auth_password($con,$usernameSSH,$passwordSSH)) {
				echo "fail: unable to authenticate\n";
			} else {
			//echo " ok: logged in...\n";
				if (!($stream = ssh2_exec($con, $commande ))) {
					echo " fail: unable to execute command\n";
				} else {
					// collect returning data from command
					stream_set_blocking($stream, true);	$data = "";
					while ($buf = fread($stream,4096)) 
					{
					$data .= $buf."\n";}
					//echo $data;					
					fclose($stream);
				}
			}
		}	

	}
//******************************************************
//  Actions en RETOUR de la console pour la commande SSH 
//******************************************************
	// *** Affichage mode debug ***
	//	echo $data;
	//	echo '<br>Commande soumise.';
	// *** Affichage mode debug ***
	

//**************************************************************************
//**************************************************************************
}
//******************************************************
//  Affichage page principale
//******************************************************
	//*************** Formulaire de choix du moteur a selectionné *****************
		// on se connecte à MySQL
	$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
	mysql_select_db($database,$db);
	$sql = 'SELECT * FROM moteurs';
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	echo '<CENTER><FORM METHOD=POST ACTION="">
		<select name="OSSelect">';
	while($data = mysql_fetch_assoc($req))
		{$sel="";
		 if($data['id_os'] == $_SESSION['opensim_select']){$sel="selected";}
			echo '<option value="'.$data['id_os'].'" '.$sel.'>'.$data['name'].' - '.$data['version'].'</option>';
		}
	mysql_close();	
	echo'</select><INPUT TYPE="submit" VALUE="Choisir" ></FORM></CENTER><hr>';
	//**************************************************************************
	
	//$tableauIni = parse_ini_file(INI_Conf_Moteur($_SESSION['opensim_select'],"address")."Regions/Region.ini", true);
	// *** Lecture Fichier Region.ini ***
	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."Regions/Regions.ini";	// *** V 0.7.1 ***
	if (file_exists($filename2)) 
		{//echo "Le fichier $filename2 existe.<br>";
		$filename = $filename2 ;
		}else {//echo "Le fichier $filename2 n'existe pas.<br>";
		}
	$tableauIni = parse_ini_file($filename, true);
	if($tableauIni == FALSE){echo 'prb lecture ini $filename<br>';}
	
	// *** Lecture Fichier OpenSimDefaults ***
	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSimDefaults.ini";		//*** V 0.7.1
	if (file_exists($filename2)) 
		{//echo "Le fichier $filename2 existe.<br>";
		$filename = $filename2 ;
		}else {//echo "Le fichier $filename2 n'existe pas.<br>";
		}

// **** Recuperation du port http du serveur ******		
	if (!$fp = fopen($filename,"r")) 
	{echo "Echec de l'ouverture du fichier $filename";}		
	$tabfich=file($filename); 
	for( $i = 1 ; $i < count($tabfich) ; $i++ )
	{
	//echo $tabfich[$i]."</br>";
	$porthttp = strstr($tabfich[$i],"http_listener_port");
		if($porthttp)
		{
			$posEgal = strpos($porthttp,'=');
			$longueur = strlen($porthttp);
			$srvOS = substr($porthttp, $posEgal + 1);
		}
	}
	fclose($fp);
	
	echo 'Nb regions connectes: '.count($tableauIni).'<HR>';
	echo '<center><table width="50%" BORDER=1>';
	while (list($key, $val) = each($tableauIni))
	{//echo '<tr><td>'.$key.'</td><td>'.$tableauIni[$key]['RegionUUID'].'</td><td>'.$tableauIni[$key]['InternalAddress'].'</td><td>'.$tableauIni[$key]['InternalPort'].'</td><td><img src="'.$ImgMap.'" width=30 height=30></td></tr>';
		//****************** Lien vers la map ******************
		$ImgMap = "http://".$hostnameSSH.":".trim($srvOS)."/index.php?method=regionImage".str_replace("-","",$tableauIni[$key]['RegionUUID']);
		//******************************************************
		echo '<tr"><td align=center><center><b><u>'.$key.'</u></b></center><br><img src="'.$ImgMap.'" width=90 height=90 BORDER=1></td>';
		echo '<td align=center>
		<FORM METHOD=POST ACTION=""><u>Sauvegarde au format OAR.</u><br><INPUT TYPE="submit" VALUE="Backup Sim" NAME="cmd" '.$btnN2.'><INPUT TYPE="hidden" NAME="format_backup" VALUE="OAR" ><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM>
		<FORM METHOD=POST ACTION=""><u>Sauvegarde au format XML2.</u><br><INPUT TYPE="submit" VALUE="Backup Sim" NAME="cmd" '.$btnN2.'><INPUT TYPE="hidden" NAME="format_backup" VALUE="XML2" ><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM>
		</td></tr>';
	}
	echo '</table><center><hr>';
//******************************************************				
}else{header('Location: index.php');   }
?>