<?php 
//include 'config/variables.php';


if (isset($_SESSION['authentification']) && $_SESSION['privilege']>=3){ // v&eacute;rification sur la session authentification 
	echo '<HR>';
	$ligne1 = '<B>Configuration des Moteurs Opensim connectes.</B>';
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
	$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
	mysql_select_db($database,$db);
//******************************************************
// CONSTRUCTION de la commande pour ENVOI sur la console via  SSH
//******************************************************
if($_POST['cmd'])
{
		if($_POST['cmd'] == 'Ajouter')
		{
			$compteur = NbOpensim() + 1;
			echo '<FORM METHOD=POST ACTION=""><table width="100%" BORDER=0>';
			echo '<tr><td>Libellé du Moteur</td><td>Description du moteur</td><td>Chemin Physique moteur</td><td>Nom de la BDD Opensim du moteur</td></tr>';
	
			echo '<td><INPUT TYPE = "text" NAME = "NewName" VALUE = "Opensim_'.$compteur.'" '.$btnN3.'"></td>
				<td><INPUT TYPE = "text" NAME = "version" VALUE = "Un petit descriptif" '.$btnN3.'"></td>
				<td><INPUT TYPE = "text" NAME = "address" VALUE = "/home/user/moteur2/" '.$btnN3.' size=50></td>
				<td><INPUT TYPE = "text" NAME = "DB_OS" VALUE = "OpensimDB" '.$btnN3.'></td>
				<tr><td align=center><INPUT TYPE="submit" VALUE = "Enregistrer" NAME="cmd" '.$btnN3.'></td></tr>
			</table><hr></FORM>';
		} 
		if($_POST['cmd'] == 'Enregistrer')
		{	
			$sqlIns = "INSERT INTO `moteurs` (`osAutorise` ,`id_os` ,`name` ,`version` ,`address` ,`DB_OS`)VALUES (NULL , '".$_POST['NewName']."', '".$_POST['NewName']."', '".$_POST['version']."', '".$_POST['address']."', '".$_POST['DB_OS']."')";
			$reqIns = mysql_query($sqlIns) or die('Erreur SQL !<br>'.$sqlIns.'<br>'.mysql_error());
			echo "Moteur Enregistré <br>";
		} 

		if($_POST['cmd'] == 'Supprimer')
		{			
			$sqlIns = "DELETE FROM `moteurs` WHERE `moteurs`.`osAutorise` = ".$_POST['osAutorise'];
			$reqIns = mysql_query($sqlIns) or die('Erreur SQL !<br>'.$sqlIns.'<br>'.mysql_error());
			echo "Moteur Supprimé <br>";
		} 
}
//******************************************************
//  Affichage page principale
//******************************************************
	
	// *** Lecture BDD config  ***
	$sql = 'SELECT * FROM moteurs';
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	echo '<b><u>Nb Moteurs Opensim connectes:</u> '.NbOpensim().'</b><BR><br>';
	echo '<FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="Ajouter" NAME="cmd" '.$btn.'> Permet d\'ajouter une nouvelle region au moteur Opensim.</FORM></center><hr>';
	
		if(NbOpensim() >= 4)
			{$btn = 'disabled';}else{$btn=$btnN3;}
		if(INI_Conf("Parametre_OSMW","Autorized") == '1' )
			{$btn = '';}
			
	echo '<table width="100%" BORDER=0>';
	echo '<tr><td>Libellé du Moteur</td><td>Description du moteur</td><td>Chemin Physique moteur</td><td>Nom de la BDD Opensim du moteur</td></tr>';
	while($data = mysql_fetch_assoc($req))
	{
		echo '<tr><FORM METHOD=POST ACTION=""><INPUT TYPE = "hidden" NAME = "osAutorise" VALUE="'.$data['osAutorise'].'" >
			<tr>
				<td><INPUT TYPE = "text" NAME = "NewName" VALUE = "'.$data['name'].'" '.$btnN3.'></td>
				<td><INPUT TYPE = "text" NAME = "version" VALUE = "'.$data['version'].'" '.$btnN3.'></td>
				<td><INPUT TYPE = "text" NAME = "address" VALUE = "'.$data['address'].'" '.$btnN3.' size=50></td>
				<td><INPUT TYPE = "text" NAME = "DB_OS" VALUE = "'.$data['DB_OS'].'" '.$btnN3.'></td>
			</tr>	
			<tr><td colspan=3 align=center><INPUT TYPE = "submit" VALUE = "Supprimer" NAME="cmd" '.$btnN3.'></td></tr>
			</FORM>			
			</tr>';

	
	}
	echo '</table>';
//******************************************************	

}else{header('Location: index.php');   }

?>