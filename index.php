<html>
<head>

<style type="text/css">
* {font-size: 10pt;}
</style>
<link rel="stylesheet" href="menu_style.css" type="text/css" />
</head>
<body>
<?php 

include 'config/fonctions.php';
include 'config/variables.php';
include 'config/osmw_conf.php';

if($_GET['a']=='logout'){$_SESSION = array();session_destroy();session_unset();}

session_start(); // On relaye la session
	//******************************************************
	$btnN1 = "disabled"; $btnN2 = "disabled"; $btnN3 = "disabled";
	if( $_SESSION['privilege']==4){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 4	Super Administrateur
	if( $_SESSION['privilege']==3){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 3	Administrateur
	if( $_SESSION['privilege']==2){$btnN1="";$btnN2="";}				//	Niv 2	Gestionnaire (sauvegarde)
	if( $_SESSION['privilege']==1){$btnN1="";}							//	Niv 1	Utilisateurs
	//******************************************************

//****************************************************************************************	
//	IDENTIFICATION ET INITIALISATION Variable OPENSIM[SELECT]
//****************************************************************************************
if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['pass']))
{	
	$_SESSION['login'] = $_POST['firstname'].' '. $_POST['lastname']; // Son Login	
    $auth = false; // On part du principe que l'utilisateur n'est pas authentifi�
	$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
	
	mysql_select_db($database,$db);	
	$passwordHash = sha1($_POST['pass']);
	// on se connecte � MySQL
	$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
	mysql_select_db($database,$db);
	$sql = 'SELECT * FROM users';
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	while($data = mysql_fetch_assoc($req))
		{
			if($data['firstname'] == $_POST['firstname'] and $data['lastname'] == $_POST['lastname'] and $data['pass'] == $passwordHash)
			{
			$auth = true;
			$_SESSION['privilege'] = $data['privilege'];
			$_SESSION['osAutorise'] = $data['osAutorise'];
			$_SESSION['authentification']=true;
			break;
			}
		}
	
    if ( ! $auth ) {echo 'Vous ne pouvez pas acc�der � cette page'; header('Location: index.php?erreur=login');	}
    else {
        echo 'Bienvenue sur la page administration du site';
		// on se connecte � MySQL
		$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
		mysql_select_db($database,$db);
		$sql = 'SELECT * FROM moteurs';
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		while($data = mysql_fetch_assoc($req))
			{$_SESSION['opensim_select'] = $data['id_os']; break;}
    }
	mysql_close();
}

//****************************************************************************************
//****************************************************************************************
//**************************** PAGE EN ACCES SECURISE ************************************
//**************** DEBUT *****************************
if (isset($_SESSION['authentification'])){ // v�rification sur la session authentification 
	// ********** SI le moteur selectionn� � chang�
	if($_POST['OSSelect']){$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}
	
	// Menu
	echo $MENU_LATTERALE.'<HR>';
	
	// REDIRECTION DES PAGES *************************************************************
	if($_POST['a'] or $_GET['a']){
		if($_POST['a']){$vers = $_POST['a'];}
		if($_GET['a']){$vers = $_GET['a'];}
			//***************************************************************************
			// 	 				index.php																				
			if($vers =="1"){include('pages/GestSims.php');}								// 	# Gestion sim
			if($vers =="2"){include('pages/GestSaveRestore.php');}						// 	# Gestion backup sim
			if($vers =="3"){include('pages/GestTerrain.php');}							// 	# Gestion Terrain
			if($vers =="4"){include('pages/GestImportExport.php');}  					//  # Exporter un inventaire
			if($vers =="5"){include('pages/GestOpensim.php');}			// admin		// 	# Edition des fichiers de conf Opensim propre au moteur
			if($vers =="6"){include('pages/GestRegion.php');}			// admin		// 	# Gestion des Regions par moteur
			if($vers =="7"){include('pages/GestLog.php');}								// 	# Gestion du Log
			if($vers =="8"){include('pages/GestAdminServ.php');}		// admin		// 	# Gestion du serveur
			if($vers =="9"){include('pages/contact.php');}								// 	# Helpdesk Utilisateur
			if($vers =="10"){include('pages/GestDirectory.php');}						// 	# Gestion des Fichiers
			if($vers =="11"){include('pages/map.php');}									// 	# MAP
			if($vers =="12"){include('pages/GestIdentite.php');}		// admin		// 	# Connection a Admin Grille OSMW 
			if($vers =="13"){include('pages/Aide.php');}  								// 	# Aide
			if($vers =="14"){include('pages/Apropos.php');}  							// 	# Les remerciements
			if($vers =="15"){include('pages/GestUsers.php');}			// admin		// 	# Gestion des utilisateurs
			if($vers =="16"){include('pages/GestBackup.php');}			// admin		// 	# Gestion des sauvegardes
			if($vers =="17"){include('pages/GestMoteur.php');}			// admin		// 	# Gestion des moteurs
			if($vers =="18"){include('pages/GestConfig.php');}			// admin		// 	# Configuration de OSMW
		//	if($vers =="19"){include('GestXMLRPC.php');}				// admin		//	#	experimental
			if($vers =="20"){include('pages/GestTransfert.php');}		// admin		// 	# Permet le transfert de fichier 
			if($vers =="21"){include('pages/GestHypergrid.php');}						// 	# Gestion des liens Hypergrid
			if($vers =="logout"){session_start();$_SESSION = array();session_destroy();session_unset();header('Location: index.php');  }	
	}
	else
		{

	//********************************************************************************************
	// ************************   ***************   ****  Affichage Principal ********************
	//********************************************************************************************
	// **************   Choix du moteur Opensim
	//********************************************************************************************
	$ligne1 = '<b>Bienvenue &quot;'.$_SESSION['login'].'&quot; dans votre espace s&eacute;curis&eacute;. Niv:'.$_SESSION['privilege'].'</b>';
	$ligne2 = '*** <u>Moteur OpenSim selectionne: </u>'.$_SESSION['opensim_select'].' - '.INI_Conf_Moteur($_SESSION['opensim_select'],"version").' ***';
	echo '<div class="block" id="clean-gray"><button><CENTER>'.$ligne1.'<br>'.$ligne2.'</CENTER></button></div>';
	echo '<hr>';
	//********************************************************************************************
	//*************** Formulaire de choix du moteur a selectionn� *****************
	//********************************************************************************************
	// Si NIV 1 - Verification Moteur Autoris� ************
	if($_SESSION['osAutorise'] != '')
	{
	$osAutorise = explode(";", $_SESSION['osAutorise']);
	//echo count($osAutorise);
	//echo $_SESSION['osAutorise'];
		for($i=0;$i < count($osAutorise);$i++)
		{	if(INI_Conf_Moteur($_SESSION['opensim_select'],"osAutorise") == $osAutorise[$i]){$moteursOK="OK";}    } 
	}
	//*****************************************************
	// PARCOURS DE TOUS LES MOTEURS
	//*****************************************************
			// on se connecte � MySQL
		$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
		mysql_select_db($database,$db);
		$sql = 'SELECT * FROM moteurs';
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		
		//$_SESSION['opensim_select']
		echo '<CENTER><FORM METHOD=POST ACTION="">
			<select name="OSSelect">';
		while($data = mysql_fetch_assoc($req))
			{
			//if($data['osAutorise'] != ''){echo $data['osAutorise'];}else{$osAutorise = explode(";", $data['osAutorise']);echo count($osAutorise);}
				$sel="";
				 if($data['id_os'] == $_SESSION['opensim_select']){$sel="selected";}
			echo '<option value="'.$data['id_os'].'" '.$sel.'>'.$data['name'].' - '.$data['version'].'</option>';
			}
		mysql_close();	
		echo'</select><INPUT TYPE="submit" VALUE="Choisir" ></FORM></CENTER>';
	//********************************************************************************************
	//  ********** MENU  ***************
	//********************************************************************************************
	echo '<HR>';
	echo '<center>';
	echo '<div class="block" id="clean-gray"><button>Section Utilisateur</button></div><br>';
	echo '<a href="?a=1"><div class="block" id="pale-blue"><button>Gestion des Sims.</button></div></a>';	
	echo '<div class="block" id="pale-blue"><a href="?a=21"><button>Raccourcis Hypergrid.</button></a></div>';	
	echo '<div class="block" id="pale-blue"><a href="?a=2"><button>Gestion des Sauvegardes.</button></a></div>';		
	echo '<div class="block" id="pale-blue"><a href="?a=3"><button>Gestion des Terrains.</button></a></div>';		
	echo '<div class="block" id="pale-blue"><a href="?a=10"><button>Gestion des fichiers de sauvegardes.</button></a></div>';		
	echo '<div class="block" id="pale-blue"><a href="?a=4"><button>Exporter son inventaire.</button></a></div>';		
	echo '<div class="block" id="pale-blue"><a href="?a=7"><button>Gestion du Log.</button></a></div>';	
	echo '<div class="block" id="pale-blue"><a href="?a=11"><button>Cartographie des Regions.</button></a></div>';
	echo '<div class="block" id="pale-blue"><a href="?a=9"><button>Contacter l\'assistance.</button></a></div>';		
	echo '<div class="block" id="pale-blue"><a href="?a=13"><button>Aide.</button></a></div>';	
	echo '<div class="block" id="pale-blue"><a href="?a=14"><button>A Propos.</button></a></div>';	
	echo '<hr>';
	//*********** Menu administrateur *************
	if( $_SESSION['privilege']>=3){
		echo '<div class="block" id="clean-gray"><button>Section Administrateur</button></div><br>';
		echo '<div class="block" id="pale-blue"><a href="?a=8"><button>Gestion du Serveur.</button></a></div>';
		echo '<div class="block" id="pale-blue"><a href="?a=17"><button>Gestion des Moteurs.</button></a></div>';
		echo '<div class="block" id="pale-blue"><a href="?a=6"><button>Gestion des Regions.</button></a></div>';	
		echo '<div class="block" id="pale-blue"><a href="?a=5"><button>Editer la configuration Opensim.</button></a></div>';
		echo '<div class="block" id="pale-blue"><a href="?a=18"><button>Configuration de OpenSim Manager Web.</button></a></div>';	
		echo '<div class="block" id="pale-blue"><a href="?a=15"><button>Gestion des Utilisateurs.</button></a></div>';
		echo '<div class="block" id="pale-blue"><a href="?a=12"><button>Connectivit� du Serveur OSMW.</button></a></div>';
		echo '<div class="block" id="pale-blue"><a href="?a=16"><button>Sauvegardes config Opensim.</button></a></div>';
		echo '<div class="block" id="pale-blue"><a href="?a=20"><button>Transfert des Sauvegardes.</button></a></div>';
	//	echo '<div class="block" id="pale-blue"><a href="?a=19"><button>Gestion XMLRPC.*</button></a></div>';
		echo '<HR>';
	}
	echo '<div class="block" id="pale-blue"><a href="?a=logout"><button>D&eacute;connexion.</button></a></div>';	
	echo '</center>';
	}
// FIN REDIRECTION DES PAGES *************************************************************
}
// FIN *****************************
else{		session_start();$_SESSION = array();session_destroy();session_unset();
?>			
<span>
	<CENTER><div class="block" id="fat-blue"><button>OpenSim Manager Web</button></div></CENTER>
<form action="" method="post" name="connect">
	  <p align="center" ><strong>      
		  <?php if(isset($_GET['erreur']) && ($_GET['erreur'] == "login")) { // Affiche l'erreur  ?>
		  <span class="Style5">Echec d'authentification !!! &gt; login ou mot de passe incorrect</span>    <?php } ?>
		  <?php if(isset($_GET['erreur']) && ($_GET['erreur'] == "delog")) { // Affiche l'erreur ?>
		  <span class="Style2">D&eacute;connexion r&eacute;ussie... A bient&ocirc;t !</span>    <?php } ?>
		  <?php if(isset($_GET['erreur']) && ($_GET['erreur'] == "intru")) { // Affiche l'erreur ?>
		  <span class="Style5">Echec d'authentification !!! &gt; Aucune session n'est ouverte</span>
		  <span class="Style5">ou vous n'avez pas les droits pour afficher cette page </span>
		  <?php } ?></strong></p>
	<CENTER><table  border="0" cellpadding="10" cellspacing="0"  border="1" >
		<tr><td colspan="2" align="center">
			<img src="images/logoserver.png"  BORDER=1>
			<br><br><div class="block" id="pale-blue"><button>Authentification</button></div>
		</td></tr>
		<tr><td>&nbsp; Firstname </td><td><input name="firstname" type="text" id="firstname"></td></tr>
		<tr><td>&nbsp; Lastname </td><td><input name="lastname" type="text" id="lastname"></td></tr>
		<tr><td>&nbsp; MOT DE PASSE </td><td><input name="pass" type="password" id="pass"></td></tr>
		<tr><td height="34" colspan="2"><CENTER><input type="submit" name="Submit" value="Se connecter"></CENTER></td></tr>
	</table><CENTER>
</FORM>
</span>
<?}
echo $PIED_DE_PAGE;
?>
</body>
</html>