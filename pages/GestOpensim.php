<?php 
include 'config/variables.php';

if (session_is_registered("authentification") && $_SESSION['privilege']>=3){ // v&eacute;rification sur la session authentification 
	echo '<HR>';
	$ligne1 = '<B>Configuration des INI d\'Opensim.</B>';
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
	//******************************************************
//******************************************************
//  Affichage page principale
//******************************************************
// *** Test des Fichiers suivants ***
	$filename0a = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."ScreenSend";	
	$filename0b = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."RunOpensim.sh";	
	$filename1 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSim.ini";				
	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSimDefaults.ini";
	$filename3 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."config-include/FlotsamCache.ini";	
	$filename4 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."config-include/GridCommon.ini";
	$filename5 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSim.log";
	$filename6 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSim.32BitLaunch.log";
	$filename7 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."startuplogo.txt";
	$filename8 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."startup_commands.txt";
	$filename9 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."shutdown_commands.txt";
//******************************************************
echo "<u><i><b>Choisir le fichier a modifier</b></i></u><br><br>";
//******************************************************
	if (file_exists($filename0a))
		{echo "Le fichier ScreenSend existe.<br>";$dispo = '<input type="submit" name="affichage" value="ScreenSend">';}
		else {echo "<B>Le fichier ScreenSend n'existe pas.</B><br>";}	
	if (file_exists($filename0b))
		{echo "Le fichier RunOpensim.sh existe.<br>";$dispo = $dispo.'<input type="submit" name="affichage" value="RunOpensim.sh">';}
		else {echo "<B>Le fichier RunOpensim.sh n'existe pas.</B><br>";}
	if (file_exists($filename1))
		{echo "Le fichier OpenSim.ini existe.<br>";$dispo = $dispo.'<input type="submit" name="affichage" value="OpenSim.ini">';}
		else {echo "<B>Le fichier OpenSim.ini n'existe pas.</B><br>";}
	if (file_exists($filename2))
		{echo "Le fichier OpenSimDefaults.ini existe.<br>";$dispo = $dispo.'<input type="submit" name="affichage" value="OpenSimDefaults.ini">';}
		else {echo "<B>Le fichier OpenSimDefaults.ini n'existe pas.</B><br>";}
	if (file_exists($filename3))
		{echo "Le fichier FlotsamCache.ini existe.<br>";$dispo = $dispo.'<input type="submit" name="affichage" value="FlotsamCache.ini">';}
		else {echo "<B>Le fichier FlotsamCache.ini n'existe pas.</B><br>";}
	if (file_exists($filename4))
		{echo "Le fichier GridCommon.ini existe.<br>";$dispo = $dispo.'<input type="submit" name="affichage" value="GridCommon.ini">';}
		else {echo "<B>Le fichier GridCommon.ini n'existe pas.</B><br>";}
	if (file_exists($filename5))
		{echo "Le fichier OpenSim.log existe.<br>";$dispo = $dispo.'<input type="submit" name="affichage" value="OpenSim.log">';}
		else {echo "<B>Le fichier OpenSim.log n'existe pas.</B><br>";}
	if (file_exists($filename6))
		{echo "Le fichier OpenSim.32BitLaunch.log existe.<br>";$dispo = $dispo.'<input type="submit" name="affichage" value="OpenSim.32BitLaunch.log">';}
		else {echo "<B>Le fichier OpenSim.32BitLaunch.log n'existe pas.</B><br>";}
	if (file_exists($filename7))
		{echo "Le fichier startuplogo.txt existe.<br>";$dispo = $dispo.'<input type="submit" name="affichage" value="startuplogo.txt">';}
		else {echo "<B>Le fichier startuplogo.txt n'existe pas.</B><br>";}
	if (file_exists($filename8))
		{echo "Le fichier startup_commands.txt existe.<br>";$dispo = $dispo.'<input type="submit" name="affichage" value="startup_commands.txt">';}
		else {echo "<B>Le fichier startup_commands.txt n'existe pas.</B><br>";}
	if (file_exists($filename9))
		{echo "Le fichier shutdown_commands.txt existe.<br>";$dispo = $dispo.'<input type="submit" name="affichage" value="shutdown_commands.txt">';}
		else {echo "<B>Le fichier shutdown_commands.txt n'existe pas.</B><br>";}		
//******************************************************		
	echo '<hr>Choissir le fichier à modifier.<br> <form method="post" action="">';	
		echo $dispo;		
	echo '</form>';	
//******************************************************
	if ($_POST['affichage'] == "ScreenSend"){$fichier = $filename0a;}
	if ($_POST['affichage'] == "RunOpensim.sh"){$fichier = $filename0b;}
	if ($_POST['affichage'] == "OpenSim.ini"){$fichier = $filename1;}
	if ($_POST['affichage'] == "OpenSimDefaults.ini"){$fichier = $filename2;}
	if ($_POST['affichage'] == "FlotsamCache.ini"){$fichier = $filename3;}
	if ($_POST['affichage'] == "GridCommon.ini"){$fichier = $filename4;}
	if ($_POST['affichage'] == "OpenSim.log"){$fichier = $filename5;}
	if ($_POST['affichage'] == "OpenSim.32BitLaunch.log"){$fichier = $filename6;}
	if ($_POST['affichage'] == "startuplogo.txt"){$fichier = $filename7;}
	if ($_POST['affichage'] == "startup_commands.txt"){$fichier = $filename8;}
	if ($_POST['affichage'] == "shutdown_commands.txt"){$fichier = $filename9;}

// *** Enregistre le fichier***********************************************************
if(isset($_POST['boutton']))	// Enregistre le fichier
{    
	unlink($fichier); // suppression du fichier pour le remplacer par le nouveau avec les nouveau éléments
	$ouverture=fopen("$fichier","a+"); // Création du nouveau fichier et ouverture du fichier
	fwrite($ouverture,"$_POST[modif]"); // ecriture
	fclose($ouverture); // fermeture du fichier
	echo '<h2>Modification effectue</h2>'; // Affichage validation
}
//***********************************************************************************	
if(isset($_POST['affichage']))	// Affiche le fichier
{  	
	echo $ligne2 = '<hr><u>Config OpenSim selectionne:</u><b> '.$_SESSION['opensim_select'].' - '.INI_Conf_Moteur($_SESSION['opensim_select'],"version").'</b> Fichier: ==> ';
	echo '<i><u>'.$_POST['affichage'].'</u></i>';
	
    echo '<form method="post" action="">';
	echo '<input type="HIDDEN" name="affichage" value="'.$_POST['affichage'].'"><br>';
	echo '<input type="submit" name="boutton" value="Enregistrer"><br>';
    echo '<TEXTAREA name="modif" rows="30" COLS="100">';
	echo file_get_contents($fichier); 
    echo '</TEXTAREA><br/>';
	echo'</form>';
}

}else{header('Location: index.php');   }
?>