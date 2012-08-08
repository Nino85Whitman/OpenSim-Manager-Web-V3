<?php 
include 'variables.php';

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
	
// *** Test des Fichiers ini ***
	$filename1 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSim.ini";				
	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSimDefaults.ini";
	$filename3 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."config-include/FlotsamCache.ini";	
	$filename4 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."config-include/GridCommon.ini";
//	$filename5 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."config-include/StandaloneHypergrid.ini";
//******************************************************

echo "<u><i><b>Choisir le fichier a modifier</b></i></u><br><br>";

//******************************************************
	if (file_exists($filename1))
		{echo "Le fichier OpenSim.ini existe.<br>";echo '<form method="post" action=""><input type="submit" name="affichage" value="OpenSim.ini"><br></form>';}
		else {echo "<B>Le fichier OpenSim.ini n'existe pas.</B><br>";}
	if (file_exists($filename2))
		{echo "Le fichier OpenSimDefaults.ini existe.<br>";echo '<form method="post" action=""><input type="submit" name="affichage" value="OpenSimDefaults.ini"><br></form>';}
		else {echo "<B>Le fichier OpenSimDefaults.ini n'existe pas.</B><br>";}
	if (file_exists($filename3))
		{echo "Le fichier FlotsamCache.ini existe.<br>";echo '<form method="post" action=""><input type="submit" name="affichage" value="FlotsamCache.ini"><br></form>';}
		else {echo "<B>Le fichier FlotsamCache.ini n'existe pas.</B><br>";}
	if (file_exists($filename4))
		{echo "Le fichier GridCommon.ini existe.<br>";echo '<form method="post" action=""><input type="submit" name="affichage" value="GridCommon.ini"><br></form>';}
		else {echo "<B>Le fichier GridCommon.ini n'existe pas.</B><br>";}
/*	if (file_exists($filename5))
		{echo "Le fichier StandaloneHypergrid.ini existe.<br>";echo '<form method="post" action=""><input type="submit" name="affichage" value="StandaloneHypergrid.ini"><br></form>';}
		else {echo "<B>Le fichier StandaloneHypergrid.ini n'existe pas.</B><br>";}
*/
//******************************************************
	if ($_POST['affichage'] == "OpenSim.ini"){$fichier = $filename1;}
	if ($_POST['affichage'] == "OpenSimDefaults.ini"){$fichier = $filename2;}
	if ($_POST['affichage'] == "FlotsamCache.ini"){$fichier = $filename3;}
	if ($_POST['affichage'] == "GridCommon.ini"){$fichier = $filename4;}
//	if ($_POST['affichage'] == "StandaloneHypergrid.ini"){$fichier = $filename5;}
	
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