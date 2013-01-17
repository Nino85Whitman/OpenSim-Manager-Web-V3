<?php 
include 'config/variables.php';


if (isset($_SESSION['authentification'])){ // v&eacute;rification sur la session authentification 
	echo '<HR>';
	$ligne1 = '<B>Sauvegarde de son inventaire.</B>';
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
//	echo '<iframe src="files_to_upload/index.php" width="100%" height="600"></iframe>';
//******************************************************
// CONSTRUCTION de la commande pour ENVOI sur la console via  SSH
//******************************************************
	if($_POST['cmd'])
	{
	// *** Affichage mode debug ***
	echo $_POST['cmd'];
	//echo $_POST['name_sim'];	echo '<BR><HR>';
		
		if($_POST['cmd'] == 'Recuperer')
		{ 
			$commande = $cmd_OS_save_iar.$_POST['first']." ".$_POST['last']." / ".$_POST['pass']." ".$_POST['first'].$_POST['last'].".iar";
			echo '<br><center>DEMANDE EFFECTUER ** Veuillez consulter le log ***</center><br>';
		}  
	}	
//	echo $commande;
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

		echo '<center><b><u>Vos identifiants:</u></b></center><br>
		<FORM METHOD=POST ACTION="">
		<table><tr>
		<td>Firstname<br><INPUT TYPE="text" NAME="first"></td>
		<td>Lastname<br><INPUT TYPE="text" NAME="last"></td>
		<td>Password<br><INPUT TYPE="text" NAME="pass"></td>
		<td><br><INPUT TYPE="submit" VALUE="Recuperer" NAME="cmd" '.$btnN3.'></td>
		<td><INPUT TYPE="hidden" VALUE="" NAME="name_sim"></td>
		</tr></table>
		</FORM>';
	
//******************************************************	
}else{header('Location: index.php');   }
?>