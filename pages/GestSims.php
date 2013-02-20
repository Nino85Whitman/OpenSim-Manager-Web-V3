<?php 
include 'config/variables.php';


if (isset($_SESSION['authentification'])){ // v&eacute;rification sur la session authentification 
if($_POST['OSSelect']){$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}
	echo '<HR>';
	$ligne1 = '<B>Gestion des Sims connectes.</B>';
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
// Selon ACTION bouton => CONSTRUCTION de la commande pour ENVOI sur la console via  SSH
//******************************************************
	if($_POST['cmd'])
	{
	// *** Affichage mode debug ***
	echo '#   '.$_POST['cmd'].'   #<br>';
	//echo $_POST['name_sim'];	echo '<BR><HR>';
		 
		if($_POST['cmd'] == 'Refresh'){ $commande = $cmd_SYS_etat_OS;}  
		if($_POST['cmd'] == 'Region Root'){ $commande = $cmd_OS_region_root;}  
		if($_POST['cmd'] == 'Update Client'){ $commande = $cmd_OS_force_update;} 
		if($_POST['cmd'] == 'Start'){ $commande = $cmd_SYS_start;}
		if($_POST['cmd'] == 'Stop'){ $commande = $cmd_OS_stop;}
		if($_POST['cmd'] == 'Restart'){ $commande = $cmd_OS_restart;}
		if($_POST['cmd'] == 'Alerte'){ $commande=$pre_cmd.'change region '.$_POST['name_sim'].';'.$pre_cmd.' alert general '.$_POST['msg_alert'];}
		if($_POST['cmd'] == 'Alerte General'){ $commande=$pre_cmd.$cmd_OS_region_root.';'.$pre_cmd.' alert general '.$_POST['msg_alert'];}		
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
	//	echo $data.'<br>';
	//	echo '<br>Commande soumise.';
	// *** Affichage mode debug ***

	// Test le retour de la console 
	if($_POST['cmd'] == 'Refresh')
	{
		$tableau = explode("mono", $data);
		while (list($key, $val) = each($tableau))
		{echo $val.'<br>';}
		//echo 'N° Instance PID du serveur: ',$PID_Opensim = substr($data,0,$pos);
	}
//**************************************************************************
//**************************************************************************

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
	$hypergrid = "";
	while($data = mysql_fetch_assoc($req))
		{$sel="";
		 if($data['id_os'] == $_SESSION['opensim_select']){$sel="selected";$hypergrid = $data['hypergrid'];}
			echo '<option value="'.$data['id_os'].'" '.$sel.'>'.$data['name'].' - '.$data['version'].'</option>';
		}
	mysql_close();	
	echo'</select><INPUT TYPE="submit" VALUE="Choisir" ></FORM></CENTER><hr>';
	//**************************************************************************
// *** Lecture Fichier Regions.ini ***
	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."Regions/".$FichierINIRegions;	 
	if (file_exists($filename2)) 
		{//echo "Le fichier $filename2 existe.<br>";
		$filename = $filename2 ;
		}else {//echo "Le fichier $filename2 n'existe pas.<br>";
		}
	$tableauIni = parse_ini_file($filename, true);
	if($tableauIni == FALSE){echo 'prb lecture ini '.$filename.'<br>';}
	
// *** Lecture Fichier OpenSimDefaults ***
		$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address").$FichierINIOpensim;		 
	if (file_exists($filename2)) 
		{//echo "Le fichier $filename2 existe.<br>";
		$filename = $filename2 ;
		}else {//echo "Le fichier $filename2 n'existe pas.<br>";
		}

	// **** Recuperation du port http du serveur ******		
		if (!$fp = fopen($filename,"r")) 
		{echo "Echec de l'ouverture du fichier ".$filename;}		
		$tabfich=file($filename); 
		for( $i = 1 ; $i < count($tabfich) ; $i++ )
		{
		$porthttp = strstr($tabfich[$i],"http_listener_port");
			if($porthttp)
			{
				$posEgal = strpos($porthttp,'=');
				$longueur = strlen($porthttp);
				$srvOS = substr($porthttp, $posEgal + 1);
			}
		}
		fclose($fp);
	//**********************************************************
	echo '<br>Nb regions connectes: '.count($tableauIni).'<HR>';
	echo '<table width="100%" BORDER=1><TR>';
		echo '<TD><br><center><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="Region Root" NAME="cmd" '.$btnN1.'><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM></center></TD>';
		echo '<TD><br><center><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="Refresh" NAME="cmd" '.$btnN1.'><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM></center></TD>';
		echo '<TD><br><center><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="Update Client" NAME="cmd" '.$btnN1.'><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM></center></TD>';
		echo '<TD><br><center><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="Restart" NAME="cmd" '.$btnN2.'><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM></center></TD>';
		echo '<TD><br><center><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="Start" NAME="cmd" '.$btnN3.'><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM></center></TD>';
		echo '<TD><br><center><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="Stop" NAME="cmd" '.$btnN3.'><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM></center></TD>';
	echo '</TR>';
	echo '<tr><td colspan=6 align=center><FORM METHOD=POST ACTION="">Message pour TOUTES les Sims<br><INPUT TYPE="text" NAME="msg_alert"  style="width:300px; height:25px;"><INPUT TYPE="submit" VALUE="Alerte General" NAME="cmd" '.$btnN2.'><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"></FORM></td></tr>';
	echo '</TABLE>';
	echo '<HR>';	
	echo '<center><table width="60%" BORDER=1>';
	while (list($key, $val) = each($tableauIni))
	{
		//****************** Lien vers la map ***************************
			$ImgMap = "http://".$hostnameSSH.":".trim($srvOS)."/index.php?method=regionImage".str_replace("-","",$tableauIni[$key]['RegionUUID']);
		//******************************************************
		if(Test_Url($ImgMap) <> '1'){$Couleur_Feux = $Couleur_Feux_R;}else{$Couleur_Feux = $Couleur_Feux_V;}
		
		echo '<tr>
		<td align=center><img src="'.$ImgMap.'" width=90 height=90 BORDER=1></td>
		<td align=center><FORM METHOD=POST ACTION=""><center><b><u>'.$key.'</u></b>  - <a href="secondlife://'.$hypergrid.":".$key.'">Se teleporter</a> -<br>Message pour la Sim.<br>
		<INPUT TYPE="text" NAME="msg_alert" style="width:300px; height:25px;"><INPUT TYPE="submit" VALUE="Alerte" NAME="cmd" '.$btnN2.'><INPUT TYPE="hidden" VALUE="'.$key.'" NAME="name_sim"> </center></FORM></td>
		<td align=center><img src="'.$Couleur_Feux.'"BORDER=1></td>
		</tr>';
	}
	echo '</table></center><hr>';

//******************************************************				
}else{header('Location: index.php');   }


?>