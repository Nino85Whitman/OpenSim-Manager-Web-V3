<?php 
if (isset($_SESSION['authentification'])){ // v&eacute;rification sur la session authentification 
	echo '<HR>';
	$ligne1 = '<B>Aide sur OpenSim Manager Web.</B>';
	//$ligne2 = '*** <u>Moteur OpenSim selectionne: </u>'.INI_Conf_Moteur($_SESSION['opensim_select'],"name").' - '.INI_Conf_Moteur($_SESSION['opensim_select'],"version").' ***';
	
	$ligne2 = '';
	echo '<div class="block" id="clean-gray"><button><CENTER>'.$ligne1.'<br>'.$ligne2.'</CENTER></button></div>';
	echo '<hr>';

	//******************************************************
	$btnN1 = "disabled"; $btnN2 = "disabled"; $btnN3 = "disabled";
	if( $_SESSION['privilege']==4){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 4	
	if( $_SESSION['privilege']==3){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 3
	if( $_SESSION['privilege']==2){$btnN1="";$btnN2="";}				//	Niv 2
	if( $_SESSION['privilege']==1){$btnN1="";}							//	Niv 1
	//******************************************************

	
echo '<center><b><a href="?a=13&hp=fr">FR<a> | <a href="?a=13&hp=en">EN</a></b><br>';
if($_GET['hp']== "fr"){echo '<iframe src="http://www.fgagod.net/HELP_OSMW-fr.pdf" width="90%" height="100%" >';}
if($_GET['hp']== "en"){echo '<iframe src="http://www.fgagod.net/HELP_OSMW-en.pdf" width="80%" height="100%" >';}
echo '</center>';
	
//******************************************************				
}else{header('Location: index.php');   }
?>