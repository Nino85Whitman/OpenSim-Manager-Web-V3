<meta http-equiv="refresh" content="5; url="#" />
<?php 
include 'variables.php';

if (session_is_registered("authentification")){ // v&eacute;rification sur la session authentification 
	echo '<HR>';
	$ligne1 = '<B>Gestion du Fichier Log.</B>';
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
	echo '#   '.$_POST['cmd'].'   #<br>';
	if($_POST['cmd'] == 'Effacer Fichier Log')
	{ 
		if($_POST['versionLog'] == "32"){$commande = $cmd_SYS_Delete_log32;}
		if($_POST['versionLog'] == "64"){$commande = $cmd_SYS_Delete_log64;}
	}  
	if($_POST['cmd'] == 'Refresh'){$commande = $cmd_SYS_etat_OS2;}  
	if($_POST['cmd'] == 'Effacer Fichier XLog'){$commande = $cmd_SYS_Delete_Xlog;}  
	
//**************************************************************************
// Envoi de la commande par ssh  *******************************************
//**************************************************************************
if($commande <> ''){
	if (!function_exists("ssh2_connect")) die(" function ssh2_connect doesn't exist");
	// log in at server1.example.com on port 22
	if(!($con = ssh2_connect($hostnameSSH, 22))){
		echo " fail: unable to establish connection\n";
	} else 
		{// try to authenticate with username root, password secretpassword
			if(!ssh2_auth_password($con,$usernameSSH,$passwordSSH)) {
				echo "fail: unable to authenticate\n";
			} else {
				// allright, we're in!
	//echo " ok: logged in...\n";
				// execute a command
				if (!($stream = ssh2_exec($con, $commande ))) {
					echo " fail: unable to execute command\n";
				} else {
					// collect returning data from command
					stream_set_blocking($stream, true);
					$data = "";
					while ($buf = fread($stream,4096)) {
						echo $data .= $buf."<br>";
					}
					fclose($stream);
				}
			}
		}
	}
	}
	
	//******************************************************
	//********** Test du fichier log 32bit / 64bit
	$fichierXLog = INI_Conf_Moteur($_SESSION['opensim_select'],"address").'XEngine.log';
	if(file_exists($fichierXLog)) { $versionlog = "xlog";
	echo "Fichier existant ";$fichierXLog = INI_Conf_Moteur($_SESSION['opensim_select'],"address").'XEngine.log'; echo 'XEngine.log<br>';}	

	$fichierLog32 = INI_Conf_Moteur($_SESSION['opensim_select'],"address").'OpenSim.log';
	if(file_exists($fichierLog32)) { $versionlog = "32";
	echo "Fichier existant ";$fichierLog = INI_Conf_Moteur($_SESSION['opensim_select'],"address").'OpenSim.log'; echo 'OpenSim.log<br>';}	

	$fichierLog64 = INI_Conf_Moteur($_SESSION['opensim_select'],"address").'OpenSim.32BitLaunch.log';
	if(file_exists($fichierLog64)) { $versionlog = "64";
	echo "Fichier existant ";$fichierLog = INI_Conf_Moteur($_SESSION['opensim_select'],"address").'OpenSim.32BitLaunch.log';echo 'OpenSim.32BitLaunch.log<br>';}

	$taille_fichier = filesize($fichierLog);
	if ($taille_fichier >= 1073741824) {$taille_fichier = round($taille_fichier / 1073741824 * 100) / 100 . " Go";	}
	elseif ($taille_fichier >= 1048576) {$taille_fichier = round($taille_fichier / 1048576 * 100) / 100 . " Mo";	}
	elseif ($taille_fichier >= 1024) {$taille_fichier = round($taille_fichier / 1024 * 100) / 100 . " Ko";	}
	else {$taille_fichier = $taille_fichier . " o";	} 
	//echo ' Taille du Fichier Log: '. $taille_fichier.' <BR><hr>';

	$taille_fichierX = filesize($fichierXLog);
	if ($taille_fichierX >= 1073741824) {$taille_fichierX = round($taille_fichierX / 1073741824 * 100) / 100 . " Go";	}
	elseif ($taille_fichierX >= 1048576) {$taille_fichierX = round($taille_fichierX / 1048576 * 100) / 100 . " Mo";	}
	elseif ($taille_fichierX >= 1024) {$taille_fichierX = round($taille_fichierX / 1024 * 100) / 100 . " Ko";	}
	else {$taille_fichierX = $taille_fichierX . " o";	} 
	//echo ' Taille du Fichier Log: '. $taille_fichierX.' <BR><hr>';
	
	echo '<table width="100%" BORDER=0><tr>';
	echo '<td><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="Refresh" NAME="cmd" '.$btnN1.'></FORM></td>';
	echo '<td><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="Effacer Fichier Log" NAME="cmd" '.$btnN3.'><INPUT TYPE="hidden" VALUE="'.$versionlog.'" NAME="versionLog"></FORM></td>';
	echo '<td><FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="Effacer Fichier XLog" NAME="cmd" '.$btnN3.'><INPUT TYPE="hidden" VALUE="'.$versionlog.'" NAME="versionXLog"></FORM></td>';
	echo '</tr></table>';
	
	echo '<table border=1><tr><td><b> Opensim Log # Taille du Fichier Log: '. $taille_fichier.'</b></td><td><b> XEngine Log # Taille du Fichier Log: '.$taille_fichierX.'</b></td></tr><tr><td>';
	$fcontents = file($fichierLog);
	$i = sizeof($fcontents)-25;
	while ($fcontents[$i]!="")
		{$aff .= $fcontents[$i].'<br>';$i++;}
	echo '<font t size="1">'.$aff.'</font><hr>';
	echo '</td>';
	
	echo '<td>';
	$fcontentsX = file($fichierXLog);
	$i = sizeof($fcontentsX)-25;
	while ($fcontentsX[$i]!="")
		{$aff1 .= $fcontentsX[$i].'<br>';$i++;}
	echo '<font t size="1">'.$aff1.'</font>';
	echo '</td></tr></table>';
	
}else{header('Location: index.php');   }
?>