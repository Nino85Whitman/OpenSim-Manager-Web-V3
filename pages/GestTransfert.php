<?php 
include 'config/variables.php';

if (isset($_SESSION['authentification']) && $_SESSION['privilege']>=3){ // v&eacute;rification sur la session authentification 
	if($_POST['OSSelect']){$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}
	echo '<HR>';
	$ligne1 = '<B>Gestion du Transfert des sauvegardes pour les moteurs Opensim.</B>';
	$ligne2 = '<br>>>> Destination: <b>'.INI_Conf_Moteur($_SESSION['opensim_select'],"address").'</b> <<<';
	echo '<div class="block" id="clean-gray"><button><CENTER>'.$ligne1.'<br>'.$ligne2.'</CENTER></button></div>';
	//echo '<hr>';
	//******************************************************
	$btnN1 = "disabled"; $btnN2 = "disabled"; $btnN3 = "disabled";
	if( $_SESSION['privilege']==4){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 4	
	if( $_SESSION['privilege']==3){$btnN1="";$btnN2="";$btnN3="";}		//  Niv 3
	if( $_SESSION['privilege']==2){$btnN1="";$btnN2="";}				//	Niv 2
	if( $_SESSION['privilege']==1){$btnN1="";}							//	Niv 1
	//******************************************************	
//*****************************************************************
if($_POST['cmd'])
{	//echo $_POST['cmd'].'<br>';
	if($_POST['cmd'] == 'Archiver Moteur')
	{
		echo "<b>ARCHIVAGE du moteur: ".$_POST['name_sim'].'</b><br>';
		extract($_POST);
		echo "Nombre de fichier archiv�(s) : ".count($matrice).'<hr>';
		for ($i = 0; $i < count($_POST["matrice"]); $i++)
		{
		$ftp_server = $_POST["ftpserver"];
		$login = $_POST["ftplogin"];
		$password = $_POST["ftppass"];
		$destination_file = $_POST["ftppath"].'/'.$_POST["matrice"][$i];
		$source_file = INI_Conf_Moteur($_SESSION['opensim_select'],"address").$_POST["matrice"][$i];		
			$connect = ftp_connect($ftp_server);
			if (ftp_login($connect, $login, $password)) {
			echo "Connect� en tant que $login sur $ftp_server<br>";
			} else {
			echo "Connexion impossible en tant que ".$login."<br>";
			}
			$upload = ftp_put($connect, "$destination_file", "$source_file", FTP_ASCII);
			if (!$upload) {
			echo "Le transfert Ftp a �chou�!<br>";
			} else {
			echo "T�l�chargement de <b><u>".$_POST["matrice"][$i]."</u></b> >>>>>> ".$destination_file."<hr>";
			}
		}
	}	
}

if($_GET['g']){  $commande = "cd ".INI_Conf_Moteur($_SESSION['opensim_select'],"address").";rm ".$_GET['g'];}
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

//******************************************************
// Debut Affichage page principale
//******************************************************

	echo'<hr>';
	//*************** Formulaire de choix du moteur a selectionn� *****************
		// on se connecte � MySQL
	$db = mysql_connect($hostnameBDD, $userBDD, $passBDD);
	mysql_select_db($database,$db);
	$sql = 'SELECT * FROM moteurs';
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	echo '<CENTER><FORM METHOD=POST ACTION="">
		<select name="OSSelect">';
	while($data = mysql_fetch_assoc($req))
		{$sel="";
		 if($data['id_os'] == $_SESSION['opensim_select']){$sel="selected";}
			echo '<option value="'.$data['id_os'].'" '.$sel.'>'.$data['name'].' - '.$data['version'].'</option>';
		}
	mysql_close();	
	echo'</select><INPUT TYPE="submit" VALUE="Choisir" ></FORM></CENTER><hr>';
	//**************************************************************************
	
	
		//**************************************************************************	
	// *** Lecture Fichier Region.ini ***
	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."Regions/".$FichierINIRegions;	 
	if (file_exists($filename2)) 
		{//echo "Le fichier $filename2 existe.<br>";
		$filename = $filename2 ;
		}else {//echo "Le fichier $filename2 n'existe pas.<br>";
		}
	$tableauIni = parse_ini_file($filename, true);
	if($tableauIni == FALSE){echo 'prb lecture ini $filename<br>';}
	
	// *** Lecture Fichier OpenSimDefaults ***
	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'],"address").$FichierINIOpensim;		
	if (file_exists($filename2)) 
		{//echo "Le fichier $filename2 existe.<br>";
		$filename = $filename2 ;
		}else {//echo "Le fichier $filename2 n'existe pas.<br>";
		}

// **** Recuperation du port http du serveur ******		
	if (!$fp = fopen($filename,"r")) 
	{echo "Echec de l'ouverture du fichier $filename";}		
	$tabfich=file($filename); 
	for( $i = 1 ; $i < count($tabfich) ; $i++ )
	{
	//echo $tabfich[$i]."</br>";
	$porthttp = strstr($tabfich[$i],"http_listener_port");
		if($porthttp)
		{
			$posEgal = strpos($porthttp,'=');
			$longueur = strlen($porthttp);
			$srvOS = substr($porthttp, $posEgal + 1);
		}
	}
	fclose($fp);
		
//******************************************************
//  Contenu Affichage page principale
//******************************************************	
			
/* racine */
$cheminPhysique = INI_Conf_Moteur($_SESSION['opensim_select'],"address");
$Address = $hostnameSSH;		
		
		
		/* infos � extraire */
function addScheme($entry,$base,$type) {
  $tab['name'] = $entry;
  $tab['type'] = filetype($base."/".$entry);
  $tab['date'] = filemtime($base."/".$entry);
  $tab['size'] = filesize($base."/".$entry);
  $tab['perms'] = fileperms($base."/".$entry);
  $tab['access'] = fileatime($base."/".$entry);
  $t = explode(".", $entry);
  $tab['ext'] = $t[count($t)-1];
  return $tab;
}
/* liste des dossiers */
function list_dir($base, $cur, $level=0) {
  global $PHP_SELF, $order, $asc;
  if ($dir = opendir($base)) {
    $tab = array();
    while($entry = readdir($dir)) {
      if(is_dir($base."/".$entry) && !in_array($entry, array(".",".."))) {
        $tab[] = addScheme($entry, $base, 'dir');
      }
    }
    /* tri */
    usort($tab,"cmp_name");
    foreach($tab as $elem) {
      $entry = $elem['name'];
      /* chemin relatif � la racine */
      $file = $base."/".$entry;
     /* marge gauche */
      for($i=1; $i<=(4*$level); $i++) {
        echo "&nbsp;";
      }
      /* l'entree est-elle le dossier courant */
      if($file == $cur) {
        echo "<img src='images/hippo.gif' />&nbsp;$entry<br />\n";
      } else {
        echo "<img src='images/hippo.gif' />&nbsp;<a href=\"$PHP_SELF?dir=". rawurlencode($file) ."&order=$order&asc=$asc\">$entry</a><br />\n";
      }
      /* l'entree est-elle dans la branche dont le dossier courant est la feuille */
      if(ereg($file."/",$cur."/")) {
        list_dir($file, $cur, $level+1);
      }
    }
    closedir($dir);
  }
}
/* liste des fichiers */
function list_file($cur) {
  global $PHP_SELF, $order, $asc, $order0;
  if ($dir = opendir($cur)) {
    /* tableaux */
    $tab_dir = array();
    $tab_file = array();
    /* extraction */
    while($file = readdir($dir)) {
      if(is_dir($cur."/".$file)) {
        if(!in_array($file, array(".",".."))) {
          $tab_dir[] = addScheme($file, $cur, 'dir');
        }
      } else {
          $tab_file[] = addScheme($file, $cur, 'file');
      }
    }
    /* tri */
    usort($tab_dir,"cmp_".$order);
    usort($tab_file,"cmp_".$order);
    /* affichage */

    echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
    echo "<tr style=\"font-size:8pt;font-family:arial;\">
    <th>".(($order=='name')?(($asc=='a')?'/\\ ':'\\/ '):'')."Nom</th><td>&nbsp;</td>
    <th>".(($order=='size')?(($asc=='a')?'/\\ ':'\\/ '):'')."Taille</th><td>&nbsp;</td>
	<th>".(($order=='date')?(($asc=='a')?'/\\ ':'\\/ '):'')."Derniere modification</th><td>&nbsp;</td>
	</tr>";
//*********************************************************************************************************
//*********************************************************************************************************
    foreach($tab_file as $elem) 
	{
	// http://www.yoursite.com/force-download.php?file=filepath
	global  $cheminPhysique, $cheminAppli , $Address, $moteursOK, $matrice;
	$cheminAppli = INI_Conf($_SESSION['opensim_select'],"cheminAppli");
	$cheminPhysique = INI_Conf_Moteur($_SESSION['opensim_select'],"address");
	$Address = INI_Conf_Moteur($_SESSION['opensim_select'],"cheminAppli");
	
	if($_SESSION['privilege']==1){$cheminWeb ="#";}else{$cheminWeb = "force-download.php?file=".$cheminPhysique.$elem['name'];}
	if($moteursOK == "OK"){$cheminWeb = "force-download.php?file=".$cheminPhysique.$elem['name'];}
		if(assocExt($elem['ext']) <> 'inconnu')
		{
		  echo "<tr><td>";
		  echo '&nbsp;&nbsp;&nbsp;'.$elem['name'].'&nbsp;&nbsp;&nbsp;';
		  echo "</td><td>&nbsp;</td>
		  <td align=\"right\">".formatSize($elem['size'])."</td><td>&nbsp;</td>
		  <td>".date("d/m/Y H:i:s", $elem['date'])."</td><td>&nbsp;</td>
		  <td><input type='checkbox' name='matrice[]' value='".$elem['name']."'></td><td>&nbsp;</td></tr>";
		}
    }
    echo "</table>";
    closedir($dir);
  }
}

//*********************************************************************************************************
//*********************************************************************************************************

/* formatage de la taille */
function formatSize($s) {
  /* unites */
  $u = array('octets','Ko','Mo','Go','To');
  /* compteur de passages dans la boucle */
  $i = 0;
  /* nombre � afficher */
  $m = 0;
  /* division par 1024 */
  while($s >= 1) {
    $m = $s;
    $s /= 1024;
    $i++;
  }
  if(!$i) $i=1;
  $d = explode(".",$m);
  /* s'il y a des decimales */
  if($d[0] != $m) {
    $m = number_format($m, 2, ",", " ");
  }
  return $m." ".$u[$i-1];
}
/* formatage du type */
function assocType($type) {
  /* tableau de conversion */
  $t = array(
    'fifo' => "file",
    'char' => "fichier special en mode caractere",
    'dir' => "dossier",
    'block' => "fichier special en mode bloc",
    'link' => "lien symbolique",
    'file' => "fichier",
    'unknown' => "inconnu"
  );
  return $t[$type];
}
/* description de l'extention */
function assocExt($ext) {
  $e = array(
    '' => "inconnu",
	'oar' => "Archive OS OAR",
	'iar' => "Archive OS IAR",
	'xml2' => "Archive OS XML2",
	'jpg' => "Image JPG",
	'bmp' => "Image BMP",
	'gz' => "Backup OSMW",
	'raw' => "Terrain OS"
  );
  if(in_array($ext, array_keys($e))) {
    return $e[$ext];
  } else {
    return $e[''];
  }
}
function cmp_name($a,$b) {
    global $asc;
    if ($a['name'] == $b['name']) return 0;
    if($asc == 'a') {
        return ($a['name'] < $b['name']) ? -1 : 1;
    } else {
        return ($a['name'] > $b['name']) ? -1 : 1;
    }
}
function cmp_size($a,$b) {
    global $asc;
    if ($a['size'] == $b['size']) return cmp_name($a,$b);
    if($asc == 'a') {
        return ($a['size'] < $b['size']) ? -1 : 1;
    } else {
        return ($a['size'] > $b['size']) ? -1 : 1;
    }
}
function cmp_date($a,$b) {
    global $asc;
    if ($a['date'] == $b['date']) return cmp_name($a,$b);
    if($asc == 'a') {
        return ($a['date'] < $b['date']) ? -1 : 1;
    } else {
        return ($a['date'] > $b['date']) ? -1 : 1;
    }
}
function cmp_access($a,$b) {
    global $asc;
    if ($a['access'] == $b['access']) return cmp_name($a,$b);
    if($asc == 'a') {
        return ($a['access'] < $b['access']) ? -1 : 1;
    } else {
        return ($a['access'] > $b['access']) ? -1 : 1;
    }
}
function cmp_perms($a,$b) {
    global $asc;
    if ($a['perms'] == $b['perms']) return cmp_name($a,$b);
    if($asc == 'a') {
        return ($a['perms'] < $b['perms']) ? -1 : 1;
    } else {
        return ($a['perms'] > $b['perms']) ? -1 : 1;
    }
}
function cmp_type($a,$b) {
    global $asc;
    if ($a['type'] == $b['type']) return cmp_name($a,$b);
    if($asc == 'a') {
        return ($a['type'] < $b['type']) ? -1 : 1;
    } else {
        return ($a['type'] > $b['type']) ? -1 : 1;
    }
}
function cmp_ext($a,$b) {
    global $asc;
    if ($a['ext'] == $b['ext']) return cmp_name($a,$b);
    if($asc == 'a') {
        return ($a['ext'] < $b['ext']) ? -1 : 1;
    } else {
        return ($a['ext'] > $b['ext']) ? -1 : 1;
    }
}

echo '<FORM METHOD=POST ACTION="">';
echo '<table border="1" cellspacing="0" cellpadding="10" bordercolor="gray"><tr valign="top">';
//<!-- liste des fichiers -->
/* repertoire initial � lister */
if(!$dir) {  $dir = INI_Conf_Moteur($_SESSION['opensim_select'],"address");} 
list_file(rawurldecode($dir)); 
echo '</td></tr></table><HR>';
echo 'Transfert vers un serveur ftp externe, archivage des fichiers.<br>';
echo 'Serveur FTP: <INPUT TYPE="text" NAME="ftpserver" VALUE=""> Login: <INPUT TYPE="text" NAME="ftplogin" VALUE=""> Password: <INPUT TYPE="password" NAME="ftppass" VALUE=""> Chemin: <INPUT TYPE="text" NAME="ftppath" VALUE="">';
echo '<INPUT TYPE="submit" VALUE="Archiver Moteur" NAME="cmd" ><INPUT TYPE="hidden" VALUE="'.$_SESSION['opensim_select'].'" NAME="name_sim">';
echo '</FORM>';	
//***********************************************************************************************	

//******************************************************		
mysql_close();			
}else{header('Location: index.php');   }
?>