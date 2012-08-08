<?php 
include 'variables.php';


if (session_is_registered("authentification") && $_SESSION['privilege']>=3){ // v&eacute;rification sur la session authentification 
	echo '<HR>';
	$ligne1 = '<B>Gestion des Utilisateurs de OpenSim Manager Web.</B>';
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
	
//*******************************************************************	
//*****************************************************************
if($_POST['cmd'])
{
	// *******************************************************************
	// *************** ACTION BOUTON *************************************
	// *******************************************************************
	
	//*****************************************************************
		if($_POST['cmd'] == 'reset password')
		{
			echo '<FORM METHOD=POST ACTION=""><table width="100%" BORDER=0>
			<INPUT TYPE = "hidden" NAME = "oldFirstName" VALUE="'.$_POST['NewFirstName'].'" >
			<INPUT TYPE = "hidden" NAME = "oldLastName" VALUE="'.$_POST['NewLastName'].'" >
			<TR>
				<td>Nouveau Mot de Passe: <br><INPUT TYPE = "password" NAME = "NewPass1" VALUE = "Nom" '.$btnN3.'></td>
				<td>Confirmer nouveau Mot de Passe: <br><INPUT TYPE = "password" NAME = "NewPass2" VALUE = "Nom" '.$btnN3.'></td>
			</tr>
			<tr><td align=center><INPUT TYPE="submit" VALUE = "Modifier Mot de Passe" NAME="cmd" '.$btnN3.'></td></tr>
			</table><hr></FORM>';
		} 
	//*****************************************************************
		if($_POST['cmd'] == 'Ajouter')
		{
			echo '<FORM METHOD=POST ACTION=""><table width="100%" BORDER=0>
			<TR>
				<td>Privilége:<br><select name="username_priv"><option value="1">Invité - Privé</option><option value="2">Gestionnaire</option><option value="3" >Administrateur</option></select></td>
				<td>Nom de l\'utilisateur:<br> <INPUT TYPE = "text" NAME = "NewFirstName" VALUE = "Nom" '.$btnN3.'></td>
				<td>Prénom de l\'utilisateur:<br> <INPUT TYPE = "text" NAME = "NewLastName" VALUE = "Prénom" '.$btnN3.'></td>
				<td>Mot de passe:<br> <INPUT TYPE = "password" NAME = "username_pass" VALUE = "" '.$btnN3.'></td>
			</tr><tr>';
				$sql = 'SELECT * FROM moteurs';
				$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
				while($data = mysql_fetch_assoc($req))
				{	echo $data['id_os'].'<input type="checkbox" name="'.$data['id_os'].'" value="'.$data['osAutorise'].'">';		}
				echo '<br>*** MODE Invité => Laisser vide pour mode démo (Ne pas cocher de moteur) / Pour un usage mode Privé, cocher le(s) moteur(s) autorisé(s)<br><br>
				</tr>
			<tr><td align=center><INPUT TYPE="submit" VALUE = "Enregistrer" NAME="cmd" '.$btnN3.'></td></tr>
			</table><hr></FORM>';
		} 
	//*****************************************************************	
		if($_POST['cmd'] == 'Modifier Mot de Passe')
		{	
		
		if ($_POST['NewPass1'] == $_POST['NewPass2'])
		{	
		$encryptedPassword = sha1($_POST['NewPass1']);
			$sqlUp = "UPDATE users SET `pass` = '".$encryptedPassword."' WHERE `firstname` = '".$_POST['oldFirstName']."' AND `lastname` = '".$_POST['oldLastName']."'";	
			$reqUp = mysql_query($sqlUp) or die('Erreur SQL !<br>'.$sqlUp.'<br>'.mysql_error());
			echo "Mot de passe Modifié";
		}else
		{echo "Mot de passe <b>NON</b> Modifié. Veuillez recommencer !";}	
		} 		
	//*****************************************************************	
		if($_POST['cmd'] == 'Enregistrer')
		{	
			$sql = 'SELECT * FROM moteurs';
				$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
				while($data = mysql_fetch_assoc($req))
				{	if($_POST[$data['id_os']] != ''){$clesprivilege = $clesprivilege.$_POST[$data['id_os']].';';}	}
			$encryptedPassword = sha1($_POST['username_pass']);
			$sqlIns = "INSERT INTO users (`firstname` ,`lastname` ,`pass` ,`privilege` ,`osAutorise`)VALUES ('".$_POST['NewFirstName']."', '".$_POST['NewLastName']."', '".$encryptedPassword."', '".$_POST['username_priv']."', '".$clesprivilege."')";
			$reqIns = mysql_query($sqlIns) or die('Erreur SQL !<br>'.$sqlIns.'<br>'.mysql_error());
			echo "Utilisateur Enregistré";
		} 
	//*****************************************************************
		if($_POST['cmd'] == 'Modifier')
		{
		$sql = 'SELECT * FROM moteurs';
				$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
				while($data = mysql_fetch_assoc($req))
				{	if($_POST[$data['id_os']] != ''){$clesprivilege = $clesprivilege.$_POST[$data['id_os']].';';}	}		
		$sqlUp = "UPDATE users SET `firstname` = '".$_POST['NewFirstName']."', `lastname` = '".$_POST['NewLastName']."', `privilege` = '".$_POST['username_priv']."', `osAutorise` = '".$clesprivilege."' WHERE `firstname` = '".$_POST['oldFirstName']."' AND `lastname` = '".$_POST['oldLastName']."'";	
		$reqUp = mysql_query($sqlUp) or die('Erreur SQL !<br>'.$sqlUp.'<br>'.mysql_error());
		echo "Utilisateur Modifié";		
		} 
	//*****************************************************************
		if($_POST['cmd'] == 'Supprimer')
		{	
		$sqlDel = "DELETE FROM users WHERE `firstname` = '".$_POST['oldFirstName']."' AND `lastname` = '".$_POST['oldLastName']."' ";	
		$reqDel = mysql_query($sqlDel) or die('Erreur SQL !<br>'.$sqlDel.'<br>'.mysql_error());
		echo "Utilisateur Supprimé";
		} 
}
//******************************************************
//******** lISTE DES UTILISATEURS **********************
//******************************************************	
	$sql = 'SELECT * FROM users';
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	$i=0;
	echo '<FORM METHOD=POST ACTION=""><INPUT TYPE="submit" VALUE="Ajouter" NAME="cmd" '.$btn.'> Permet d\'ajouter un nouvel Utilisateur.</FORM></center>';
	echo '<hr><table width="100%" BORDER=0><TR><tr><td>Privilege</td><td>Prénom</td><td>Nom</td><td>Mot de passe</td></tr><tr><td colspan=4><hr></td></tr>';
	while($data = mysql_fetch_assoc($req))
	{$i++;
			$privilegetxt1 = $privilegetxt2 = $privilegetxt3 = 0;
			$privilege = $data['privilege'];
			 $oldbtnN3 =  $btnN3;
			switch ($privilege) {
				case 1: $privilegetxt1 = "selected";break;
				case 2: $privilegetxt2 = "selected";break;
				case 3: $privilegetxt3 = "selected";break;
				case 4: 
				if($_SESSION['privilege']==4)
				{	$privilegetxt4 = "<option value='4' selected>Super Administrateur</option>";$block="";$btnN3 = "";	break;	}
				else{	$privilegetxt4 = "<option value='4' selected>Super Administrateur</option>";$block="disabled";$btnN3 = "disabled";	break;	}
			}
		echo '<tr>
		<FORM METHOD=POST ACTION=""><INPUT TYPE = "hidden" NAME = "oldFirstName" VALUE="'.$data['firstname'].'" ><INPUT TYPE = "hidden" NAME = "oldLastName" VALUE="'.$data['lastname'].'" >
		<tr>
			<td><select name="username_priv" '.$block.'><option value="1" '.$privilegetxt1.' >Invité - Privé</option><option value="2" '.$privilegetxt2.'>Gestionnaire</option><option value="3" '.$privilegetxt3.'>Administrateur</option>'.$privilegetxt4.'</select></td>
			<td><INPUT TYPE = "text" NAME = "NewFirstName" VALUE = "'.$data['firstname'].'" '.$btnN3.'></td>
			<td><INPUT TYPE = "text" NAME = "NewLastName" VALUE = "'.$data['lastname'].'" '.$btnN3.'></td>
			<td><INPUT TYPE = "submit" NAME = "cmd" VALUE = "reset password" '.$btnN3.'></td></tr>'; 
			if($data['privilege'] =="1")
			{
			echo'<tr><td colspan=4 align=center>MODE Invité => Laisser vide pour mode démo (Ne pas cocher de moteur) / Pour un usage mode Privé, cocher le(s) moteur(s) autorisé(s)<br>';
				$sql1 = 'SELECT * FROM moteurs';
				$req1 = mysql_query($sql1) or die('Erreur SQL !<br>'.$sql1.'<br>'.mysql_error());
				while($data1 = mysql_fetch_assoc($req1))
				{
				$moteursOK="";
				$osAutorise = explode(";", $data['osAutorise']);
					for($i=0;$i < count($osAutorise);$i++)
					{	if($data1['osAutorise'] == $osAutorise[$i]){$moteursOK="CHECKED";break;}    } 
			  	echo $data1['id_os'].'<input type="checkbox" name="'.$data1['id_os'].'" value="'.$data1['osAutorise'].'" '.$moteursOK.'>';		
				}
			echo '</td></tr>';
			}
			echo '<tr><td><INPUT TYPE = "submit" VALUE = "Modifier" NAME="cmd" '.$btnN3.'></td><td><INPUT TYPE = "submit" VALUE = "Supprimer" NAME="cmd" '.$btnN3.'></td>';
		echo '</FORM>		
		</tr><tr><td colspan=4><hr></td></tr>';
		 $btnN3 =  $oldbtnN3;$privilegetxt4="";$block="";
	}
	echo '</table><br><hr>';
echo '<u>Nb Utilisateurs :</u> '.$i.'</b><BR><br>';
mysql_close();
//******************************************************				
}else{header('Location: index.php');   }

?>