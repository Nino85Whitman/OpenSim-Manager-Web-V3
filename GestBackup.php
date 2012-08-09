<?php 
include 'variables.php';

if (session_is_registered("authentification") && $_SESSION['privilege']==4){ // v&eacute;rification sur la session authentification 
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

//*****************************************************************
if($_POST['cmd'])
{
	// *** Affichage mode debug ***

	echo $_POST['cmd'].'<br>';

		if($_POST['cmd'] == 'Ajouter')
		{
			
		} 
		if($_POST['cmd'] == 'Enregistrer')
		{	
			
		} 
		if($_POST['cmd'] == 'Modifier')
		{
		
		} 
		if($_POST['cmd'] == 'Supprimer')
		{			

		} 
}
//******************************************************

echo 'Bienvenue, ici tous les parametres de sauvegardes.<br>';

echo '
<li>Definir Profils:</li>
- Moteurs					<br>
- Sims / OAR /IAR ???		<br>	
- Base OS ???				<br>
<li>Definir Archivages:</li>
- Plannification d\'un profil<br>
<li>Definir Media:</li>



';
//***********************************************************************************************	
	echo '<hr>';
//******************************************************		
mysql_close();			
}else{header('Location: index.php');   }
?>