<?php
include 'variables.php';

if (session_is_registered("authentification")){ // v&eacute;rification sur la session authentification 
	echo '<HR>';
	$ligne1 = '<B>Gestion des fichiers sauvegardes du Serveur.</B>';
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
	//******************************************************//******************************************************
	
/* racine */
$cheminPhysique = INI_Conf_Moteur($_SESSION['opensim_select'],"address");
$Address = $hostnameSSH;

//******************************************************
// CONSTRUCTION de la commande pour ENVOI sur la console via  SSH
//******************************************************
// exemple "cd ".INI_Conf_Moteur($_SESSION['opensim_select'],"address").";chmod 777 OpenSim.log;rm OpenSim.log";
if($_GET['f']){  $commande = $cmd_SYS_Delete_file.$_GET['f'].";rm ".$_GET['f'];}	 

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


/* infos à extraire */
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
      /* chemin relatif à la racine */
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
    <th>".(($order=='ext')?(($asc=='a')?'/\\ ':'\\/ '):'')."Extention</th><td>&nbsp;</td></tr>";
//*********************************************************************************************************
//*********************************************************************************************************
    foreach($tab_file as $elem) 
	{
	// http://www.yoursite.com/force-download.php?file=filepath
	global  $cheminPhysique, $cheminAppli , $Address, $moteursOK;
	$cheminAppli = INI_Conf($_SESSION['opensim_select'],"cheminAppli");
	$cheminPhysique = INI_Conf_Moteur($_SESSION['opensim_select'],"address");
	$Address = INI_Conf_Moteur($_SESSION['opensim_select'],"cheminAppli");
	
	if($_SESSION['privilege']==1){$cheminWeb ="#";}else{$cheminWeb = "force-download.php?file=".$cheminPhysique.$elem['name'];}
	if($moteursOK == "OK"){$cheminWeb = "force-download.php?file=".$cheminPhysique.$elem['name'];}
		if(assocExt($elem['ext']) <> 'inconnu')
		{
		  echo "<tr>";
		  echo "<td><img src='images/hippo.gif' />&nbsp;<a href = '".$cheminWeb."'>".$elem['name']."</a></td>
		  <td>&nbsp;&nbsp;&nbsp;</td>";
		  echo "<td align=\"right\">".formatSize($elem['size'])."</td>
		  <td>&nbsp;&nbsp;&nbsp;</td>
		  <td>".date("d/m/Y H:i:s", $elem['date'])."</td><td>&nbsp;&nbsp;</td>
		  <td>".assocExt($elem['ext'])."</td>
		  <td>&nbsp;&nbsp;&nbsp;</td>
		  <td>";
		  if( $_SESSION['privilege']>=3 xor $moteursOK == "OK"){echo "<a href = '?f=".$elem['name']."'>Supprimer</a>";}
		  echo " </td></tr>\n";
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
  /* nombre à afficher */
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


echo '<table border="1" cellspacing="0" cellpadding="10" bordercolor="gray"><tr valign="top">';
//<!-- liste des fichiers -->

/* repertoire initial à lister */
if(!$dir) {  $dir = INI_Conf_Moteur($_SESSION['opensim_select'],"address");} 
list_file(rawurldecode($dir)); 
echo '</td></tr></table><HR>';

}else{header('Location: index.php');   }
