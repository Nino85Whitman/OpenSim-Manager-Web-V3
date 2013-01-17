<?php 
include 'config/variables.php';

if (isset($_SESSION['authentification']) && $_SESSION['privilege']>=3){ // v&eacute;rification sur la session authentification 
	echo '<HR>';
	$ligne1 = '<B>Gestion de la configuration de OpenSim Manager Web.</B>';
	$ligne2 = '*** <u>Moteur OpenSim selectionne: </u>'.$_SESSION['opensim_select'].' - '.INI_Conf_Moteur($_SESSION['opensim_select'],"version").' ***';
	echo '<div class="block" id="clean-gray"><button><CENTER>'.$ligne1.'<br>'.$ligne2.'</CENTER></button></div>';
	echo '<hr>';
	//******************************************************
	$btnN1 = "disabled"; $btnN2 = "disabled"; $btnN3 = "disabled";
	if( $_SESSION['privilege']==4){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 4	
	if( $_SESSION['privilege']==3){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 3
	if( $_SESSION['privilege']==2){$btnN1="";$btnN2="";}				//	Niv 2
	if( $_SESSION['privilege']==1){$btnN1="";}							//	Niv 1
	//******************************************************	
//*******************************************************************	
	$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
	mysql_select_db($database,$db);
//*****************************************************************
if($_POST['cmd'])
{
	// *******************************************************************
	// *************** ACTION BOUTON *************************************
	// *******************************************************************

	if($_POST['cmd'] == 'Enregistrer')
	{	
			$sqlIns = "UPDATE `conf` SET `cheminAppli` = '".$_POST['cheminAppli']."',`destinataire` = '".$_POST['destinataire']."',`Autorized` = '".$_POST['Autorized']."',`NbAutorized` = '".$_POST['NbAutorized']."',`VersionOSMW` = '".$_POST['VersionOSMW']."',`urlOSMW` = '".$_POST['urlOSMW']."' WHERE `conf`.`id` =1";
			$reqIns = mysql_query($sqlIns) or die('Erreur SQL !<br>'.$sqlIns.'<br>'.mysql_error());
			echo "Configuration Enregistré";
	}
}
//******************************************************

	// *** Lecture BDD config  ***
	$sql = 'SELECT * FROM conf';
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	
	while($data = mysql_fetch_assoc($req))
	{

		echo '<center><table><FORM METHOD=POST ACTION=""><INPUT TYPE = "hidden" NAME = "NewName" VALUE = "'.$key.'" '.$btnN3.'>
		<tr><td>Chemin OSMW (ex: /OSMW/): </td><td><INPUT TYPE = "text" VALUE = "'.$data['cheminAppli'].'" NAME="cheminAppli" '.$btnN3.' style="width:300px; height:25px;"></tr>
		<tr><td>Destinataire Mail Contact: </td><td><INPUT TYPE = "text" VALUE = "'.$data['destinataire'].'" NAME="destinataire" '.$btnN3.' style="width:300px; height:25px;"></tr>
		<tr><td>Autorisation NO LIMIT région: </td><td><INPUT TYPE = "text" VALUE = "'.$data['Autorized'].'" NAME="Autorized" '.$btnN3.' style="width:300px; height:25px;"></tr>
		<tr><td>Autorisation Nb LIMIT région: </td><td><INPUT TYPE = "text" VALUE = "'.$data['NbAutorized'].'" NAME="NbAutorized" '.$btnN3.' style="width:300px; height:25px;"></tr>
		<tr><td>Version OSMW (bas de page): </td><td><INPUT TYPE = "text" VALUE = "'.$data['VersionOSMW'].'" NAME="VersionOSMW" '.$btnN3.' style="width:300px; height:25px;"></tr>

		<tr><td><td><INPUT TYPE = "submit" VALUE = "Enregistrer" NAME="cmd" '.$btnN3.'></td></tr>	
		</FORM></table></center>';
	}
	echo '</table><hr>';

//******************************************************				
}else{header('Location: index.php');   }

?>