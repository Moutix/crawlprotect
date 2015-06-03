<?php
//----------------------------------------------------------------------
//  CrawlProtect 3.2.5
//----------------------------------------------------------------------
// Security for website
//----------------------------------------------------------------------
// Author: Jean-Denis Brun
//----------------------------------------------------------------------
// Code cleaning: Philippe Villiers
//----------------------------------------------------------------------
// Website: www.crawltrack.net
//----------------------------------------------------------------------
// That script is distributed under GNU GPL license
//----------------------------------------------------------------------
// file: chmod.php
//----------------------------------------------------------------------
//  Last update: 04/10/2013
//----------------------------------------------------------------------
if (!defined('IN_CRAWLT'))
	{
	echo"<h1>Hacking attempt !!!!</h1>";
	exit();
	}

//variables init------------------------------------
$indexlist=array();
$dirlist=array();
$headerlist=array();
$footerlist=array();
$configlist=array();
$listallfolders=array();
$errorchangelogin=0;
$noattack=0;
$goodfilechmod=array();
$goodfilechmod[0]='0404';
$goodfilechmod[1]='0444';
$goodfilechmod[2]='0400';
$goodfilechmod[3]='0000';
$goodfilechmod[4]='2404';
$goodfilechmod[5]='2444';
$goodfilechmod[6]='2400';
$goodfilechmod[7]='2000';
$goodfilechmod[8]='0440';
$goodfilechmod[9]='2440';
$correctfilechmod=array();
$correctfilechmod[0]='0604';
$correctfilechmod[1]='0644';
$correctfilechmod[2]='0600';
$correctfilechmod[3]='2604';
$correctfilechmod[4]='2644';
$correctfilechmod[5]='2600';
$goodfolderchmod=array();
$goodfolderchmod[0]='0505';
$goodfolderchmod[1]='0555';
$goodfolderchmod[2]='0500';
$goodfolderchmod[3]='0510';
$goodfolderchmod[4]='0501';
$goodfolderchmod[5]='0511';
$goodfolderchmod[6]='0541';
$goodfolderchmod[7]='0514';
$goodfolderchmod[8]='0544';
$goodfolderchmod[9]='2505';
$goodfolderchmod[10]='2555';
$goodfolderchmod[11]='2500';
$goodfolderchmod[12]='2510';
$goodfolderchmod[13]='2501';
$goodfolderchmod[14]='2511';
$goodfolderchmod[15]='2541';
$goodfolderchmod[16]='2514';
$goodfolderchmod[17]='2544';
$goodfolderchmod[18]='0550';
$goodfolderchmod[19]='2550';
$correctfolderchmod=array();
$correctfolderchmod[0]='0705';
$correctfolderchmod[1]='0755';
$correctfolderchmod[2]='0700';
$correctfolderchmod[3]='0710';
$correctfolderchmod[4]='0750';
$correctfolderchmod[5]='0751';
$correctfolderchmod[6]='0711';
$correctfolderchmod[7]='0715';
$correctfolderchmod[8]='2705';
$correctfolderchmod[9]='2755';
$correctfolderchmod[10]='2700';
$correctfolderchmod[11]='2710';
$correctfolderchmod[12]='2750';
$correctfolderchmod[13]='2751';
$correctfolderchmod[14]='2711';
$correctfolderchmod[15]='2715';
//get variables-----------------------------------------
if(isset($_POST['datedisplay']))
	{
	$datedisplay = $_POST['datedisplay'];
	}
else
	{
	$datedisplay = 1;
	}
if(isset($_POST['refresh']))
	{
	$refresh = $_POST['refresh'];
	}
else
	{
	$refresh = 'no';
	}
if(isset($_POST['sort']))
	{	
	$sort = $_POST['sort'];
	}
else
	{
	$sort = 1;
	}
if(isset($_POST['sortf']))
	{
	$sortf = $_POST['sortf'];
	}
else
	{
	$sortf = 1;
	}
//chmod change
if(isset($_POST['changechmod']))
	{
	$changechmod = $_POST['changechmod'];
	}
else
	{
	$changechmod = 0;
	}

if(isset($_POST['min']))
	{
	$min = $_POST['min'];
	}
else
	{
	$min = 1;
	}
$max=$min+100;
if(isset($_POST['minf']))
	{
	$minf = $_POST['minf'];
	}
else
	{
	$minf = 1;
	}
$maxf=$minf+100;
if(isset($_POST['chmodindex']))
	{
	$chmodindex = $_POST['chmodindex'];
	}
else
	{
	$chmodindex = 0;
	}
if(isset($_POST['chmodadmin']))
	{
	$chmodadmin = $_POST['chmodadmin'];
	}
else
	{
	$chmodadmin = 0;
	}
//test to see if chmod is permitted
if(function_exists('chmod') && @chmod('./cache/',0755))
	{
	$chmodpossible=1;
	}
else
	{
	$chmodpossible=0;
	}
//include menu
include ("include/menusite.php");
include ("include/menumain.php");		
//lock folders and files list-----------------------------------------------------------
$sql = "SELECT * FROM crawlp_site_setting WHERE id_site= '" . sql_quote($site) . "'";
$requete = mysql_query($sql, $connexion);
$nbrresult=mysql_num_rows($requete);
if ($nbrresult >= 1) {
	while ($ligne = mysql_fetch_object($requete)) {
		$whichfile = $ligne->whichfile;
		$nologs = $ligne->nologs;			
		$nocache = $ligne->nocache;
		$nostats = $ligne->nostats;			
		$folderlevel = $ligne->folderlevel;
		$justbad = $ligne->justbad;
		$listfiledontchangeserialize = $ligne->listfiledontchangeserialize;
		$listfolderdontchangeserialize = $ligne->listfolderdontchangeserialize;
		$scriptsused = $ligne->scriptsused;
		$yourip = $ligne->yourip;
		$blockUA = $ligne->blockUA;				
		$hotlink = $ligne->hotlink;
		$shellsetting = $ligne->shellsetting;
		$sqlsetting = $ligne->sqlsetting;
		$trustsites = $ligne->trustsites;		
		$trustip = $ligne->trustip;		
		$trustuseragent = $ligne->trustuseragent;
		$trustvariable = $ligne->trustvariable;	
		$forbiddenip = $ligne->forbiddenip;		
		$forbiddenurl = $ligne->forbiddenurl;
		$forbiddenparameter = $ligne->forbiddenparameter;
		$forbiddenreferer = $ligne->forbiddenreferer;
		$forbiddenword = $ligne->forbiddenword;
		$autoprepend = $ligne->autoprepend;		
		$actualhtaccess = $ligne->actualhtaccess;							
	}
$listfolderdontchange=unserialize($listfolderdontchangeserialize);
if(!is_array($listfolderdontchange))
	{
	$listfolderdontchange=array();
	}
$listfiledontchange=unserialize($listfiledontchangeserialize);
if(!is_array($listfiledontchange))
	{
	$listfiledontchange=array();
	}		
}
else
	{
		$whichfile = 'all';
		$nologs = '1';			
		$nocache = '1';
		$nostats = '1';			
		$folderlevel = 'all';
		$justbad = '0';
		$listfiledontchangeserialize = '';
		$listfolderdontchangeserialize = '';		
		$scriptsused = '';
		$yourip = '0';
		$blockUA = '1';				
		$hotlink = '0';
		$shellsetting = 'all';
		$sqlsetting = 'all';
		$trustsites = 'www.example';		
		$trustip = '';		
		$trustuseragent = '';
		$trustvariable = 'variable-example';
		$forbiddenip = '';				
		$forbiddenurl = '';
		$forbiddenparameter = '';
		$forbiddenreferer = '';
		$forbiddenword = '';
		$autoprepend = '1';		
		$actualhtaccess = '';
		$listfolderdontchange=array();
		$listfiledontchange=array();				
	}

//refresh
if($refresh=='ok')
	{
	if(isset($_SESSION['nofile']))
		{
		unset($_SESSION['nofile']);
		}
	if(isset($_SESSION['yourrelease']))
		{
		unset($_SESSION['yourrelease']);
		}
	if(isset($_SESSION['verif']))
		{
		unset($_SESSION['verif']);
		}
	}
//file modification follow up
//building the list of folder
$today=strtotime("today");
if(!isset($_SESSION['dirlist']))
	{
	$dirlist= lookfolder('../', $folderlevel,$justbad,$nocache,$nostats,$nologs);
	$_SESSION['dirlist']=$dirlist;
	$_SESSION['dirlistall']=$listallfolders;
	}
else
	{
	$dirlist=$_SESSION['dirlist'];
	$listallfolders=$_SESSION['dirlistall'];
	if(count($dirlist)==0)
		{
		$dirlist= lookfolder('../', $folderlevel,$justbad,$nocache,$nostats,$nologs);
		$_SESSION['dirlist']=$dirlist;
		$_SESSION['dirlistall']=$listallfolders;
		}
	}
//building the list of files
if(!isset($_SESSION['filelist']))
	{
	$filelist= lookfile('../', $whichfile,$justbad,$nocache,$nostats,$nologs);
	foreach($listallfolders as $value=>$time)
		{
		$dirname="../".$value;
		$filelist= array_merge($filelist, lookfile($dirname, $whichfile,$justbad,$nocache,$nostats,$nologs));
		}
	$_SESSION['filelist']=$filelist;
	}
else
	{
	$filelist=$_SESSION['filelist'];
	if(count($filelist)==0)
		{
		$filelist= lookfile('../', $whichfile,$justbad,$nocache,$nostats,$nologs);
		foreach($listallfolders as $value=>$time)
			{
			$dirname="../".$value;
			$filelist= array_merge($filelist, lookfile($dirname, $whichfile,$justbad,$nocache,$nostats,$nologs));
			}
		$_SESSION['filelist']=$filelist;
		}
	}
//number of folders & files
$nbrfolders= count($dirlist);
$nbrfiles= count($filelist);
//date display or chmod change?
if($changechmod==1)
	{
	if(isset($_POST['type2']))
		{
		$type2 = $_POST['type2'];
		}
	else
		{
		$type2 = '';
		}
	if(isset($_POST['filedir']))
		{
		$filedir = $_POST['filedir'];
		}
	else
		{
		$filedir = '';
		}
	if(isset($_POST['chmod']))
		{
		$chmod = $_POST['chmod'];
		}
	else
		{
		$chmod = '';
		}
	if(isset($_POST['validchmod']))
		{
		$validchmod = $_POST['validchmod'];
		}
	else
		{
		$validchmod = '';
		}
	if(isset($_POST['dontchange']))
		{
		$dontchange = $_POST['dontchange'];
		}
	else
		{
		$dontchange = '';
		}
		
		
		
		
		
	//add to dontchange list if request
	if($dontchange=='ok')
		{
		if($type2=='file' && !in_array($filedir, $listfiledontchange))
			{
			$listfiledontchange[]=$filedir;
			$listfiledontchangeserialize= serialize($listfiledontchange);
			$sql ="UPDATE crawlp_site_setting SET  listfiledontchangeserialize='".sql_quote($listfiledontchangeserialize)."',listfolderdontchangeserialize='".sql_quote($listfolderdontchangeserialize)."' WHERE id_site= '" . sql_quote($site) . "' ";
			$requete = mysql_query($sql, $connexion);
			}
		elseif($type2=='folder' && !in_array($filedir, $listfolderdontchange))
			{
			$listfolderdontchange[]=$filedir;
			$listfolderdontchangeserialize= serialize($listfolderdontchange);
			$sql ="UPDATE crawlp_site_setting SET  listfiledontchangeserialize='".sql_quote($listfiledontchangeserialize)."',listfolderdontchangeserialize='".sql_quote($listfolderdontchangeserialize)."' WHERE id_site= '" . sql_quote($site) . "' ";
			$requete = mysql_query($sql, $connexion);
			}
		}
	else
		{
		if($type2=='file' && in_array($filedir, $listfiledontchange))
			{
			foreach($listfiledontchange as $key=>$value)
				{
				if($value==$filedir)
					{
					unset($listfiledontchange[$key]);
					}
				}
			$listfiledontchangeserialize= serialize($listfiledontchange);
			$sql ="UPDATE crawlp_site_setting SET  listfiledontchangeserialize='".sql_quote($listfiledontchangeserialize)."',listfolderdontchangeserialize='".sql_quote($listfolderdontchangeserialize)."' WHERE id_site= '" . sql_quote($site) . "' ";
			$requete = mysql_query($sql, $connexion);
			}
		elseif($type2=='folder' && in_array($filedir, $listfolderdontchange))
			{
			foreach($listfolderdontchange as $key=>$value)
				{
				if($value==$filedir)
					{
					unset($listfolderdontchange[$key]);
					}
				}
			$listfolderdontchangeserialize= serialize($listfolderdontchange);
			$sql ="UPDATE crawlp_site_setting SET  listfiledontchangeserialize='".sql_quote($listfiledontchangeserialize)."',listfolderdontchangeserialize='".sql_quote($listfolderdontchangeserialize)."' WHERE id_site= '" . sql_quote($site) . "' ";
			$requete = mysql_query($sql, $connexion);
			}
		}
	change_chmod($type2, $filedir, $chmod, $validchmod);
	

	
	
	}
if($chmodindex==1)
	{
	?>
	<SCRIPT LANGUAGE="JavaScript">
	document.location.href="index.php"
	</SCRIPT>
	<?php
	}
elseif($chmodadmin==1)
	{
	?>
	<SCRIPT LANGUAGE="JavaScript">
	document.location.href="index.php?navig=1"
	</SCRIPT>
	<?php
	}		
//display=====================================================================================================================
echo"<div align=\"center\"><A NAME=\"folders\">&nbsp;</A>\n";

echo"<h2>".$language['crawlprotectcheck'].numbdisp($nbrfolders).$language['crawlprotectcheck2'].numbdisp($nbrfiles).$language['crawlprotectcheck3']."</h2>\n";
echo"</div>\n";
echo"<hr><hr>\n";
if($folderlevel=='restricted' OR $justbad=='1' OR $whichfile=='hihfc' OR $nocache=='1' OR $nostats=='1')
	{
	echo"<div align='center'><p class=\"red\"><b>".$language['main_file']."</b><br>";
	if($whichfile=='hihfc')
		{
		echo $language['selectfiles']."<br>";
		}
	if($justbad=='1')
		{
		echo $language['justbaddisplay']."<br>";
		}
	if($nocache=='1')
		{
		echo $language['nocache']."<br>";
		}
	if($nostats=='1')
		{
		echo $language['nostats']."<br>";
		}
	if($nologs=='1')
		{
		echo $language['nologs']."<br>";
		}
	if($folderlevel=='limit')
		{
		echo $language['folderlevelrestricted']."<br>";
		}
	echo"</p></div>";
	}
else
	{
	echo"<div align='center'><p>".$language['totalprotection']."</p></div>";
	}
echo"<hr><hr>\n";
echo"<h2>".$language['folder_modification']."</h2>\n";
echo"<div align=\"center\">\n";
//sorting choice
echo"<form action=\"index.php#folders\" method=\"POST\" >\n";
echo "<input type=\"hidden\" name ='navig' value='2'>\n";
echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>\n";
echo "<input type=\"hidden\" name ='min' value='$min'>\n";
echo "<input type=\"hidden\" name ='minf' value='$minf'>\n";
echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
if($sort==1)
	{
	echo"<p><input type=\"radio\" name=\"sort\" value=\"1\" checked>".$language['filename']."&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"sort\" value=\"2\">".$language['filedate']."&nbsp;&nbsp;\n";
	}
else 
	{
	echo"<p><input type=\"radio\" name=\"sort\" value=\"1\">".$language['filename']."&nbsp;&nbsp;\n"; 
	echo"<input type=\"radio\" name=\"sort\" value=\"2\" checked>".$language['filedate']."&nbsp;&nbsp;\n";
	}
echo"<input name='ok' type='submit'  value='OK' size='20' >\n";
echo"</p></form>&nbsp;\n";
echo"</div>\n";
echo"<div align=\"center\">\n";
if($chmodpossible==1)
	{
	echo"<form action=\"index.php#folders\" method=\"POST\">\n";
	echo "<input type=\"hidden\" name ='navig' value='2'>\n";
	echo "<input type=\"hidden\" name ='min' value='$min'>";
	echo "<input type=\"hidden\" name ='minf' value='$minf'>";
	echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
	echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
	if($datedisplay==1)
		{
		$text5=$language['chmod_change_menu'];
		echo "<input type=\"hidden\" name ='datedisplay' value='0'>\n";
		}
	else
		{
		$text5=$language['datedisplay'];
		echo "<input type=\"hidden\" name ='datedisplay' value='1'>\n";
		}
	echo"<input name='ok' type='submit'  value='$text5' size='20' >\n";
	echo"</form>\n";
	}
else
	{
	echo "<p style='font-size:12px; font-style:italic;'>".$language['nochmod']."</p>";
	}
echo"<br>(=> <A HREF=\"#files\">".$language['file_modification']."</A> )<br><br><hr>\n";
if($nbrfolders > 300)
	{
	echo"<div align='center'><table><tr><td>";
	if($minf>101)
		{
		$buttonf0="1 ==> 100";
		echo"<form action=\"index.php#folders\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name ='navig' value='2'>\n";
		echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
		echo "<input type=\"hidden\" name ='minf' value=1>\n";
		echo "<input type=\"hidden\" name ='min' value=$min>\n";
		echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
		echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
		echo"<input name='ok' type='submit'  value='$buttonf0' size='20' >\n";
		echo"</form></td><td>\n";
		}
	if($minf>1)
		{
		$minf1=$minf-100;
		$maxf1=$minf-1;
		$buttonf1=$minf1." ==> ".$maxf1;
		echo"<form action=\"index.php#folders\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name ='navig' value='2'>\n";
		echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
		echo "<input type=\"hidden\" name ='minf' value=$minf1>\n";
		echo "<input type=\"hidden\" name ='min' value=$min>\n";
		echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
		echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
		echo"<input name='ok' type='submit'  value='$buttonf1' size='20' >\n";
		echo"</form>\n";
		}
	else
		{
		echo"&nbsp;";
		}
	echo"</td><td>";
	$lastf=$maxf-1;
	if($lastf> $nbrfolders)
		{
		$lastf=$nbrfolders;
		}
	echo "<h2>".$minf." ==> ".$lastf."</h2>";
	echo"</td><td>";
	$minf2= $minf+100;
	if($minf2< $nbrfolders)
		{
		$maxf2=$minf2+99;
		if($maxf2> $nbrfolders)
			{
			$maxf2=$nbrfolders;
			}
		$buttonf2=$minf2." ==> ".$maxf2;
		echo"<form action=\"index.php#folders\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name ='navig' value='2'>\n";
		echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
		echo "<input type=\"hidden\" name ='minf' value=$minf2>\n";
		echo "<input type=\"hidden\" name ='min' value=$min>\n";
		echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
		echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
		echo"<input name='ok' type='submit'  value='$buttonf2' size='20' >\n";
		echo"</form>\n";
		}
	echo"</td></tr></table></div>";
	}
echo"<table width='90%' cellpadding='0px' cellspacing='0'>\n";
echo"<tr><th class='titleleft'>".$language['folder']."</th><th class='titleleft' width='100px'>".$language['chmod']."</th><th class='titleright' width='300px'>\n";
if($datedisplay==1)
	{
 	echo $language['datemod'];
	}
else
	{
 	echo $language['changechmod'];
	}
echo"</th></tr>\n";
//folders list
if(count($dirlist)>0)
	{
	if($datedisplay==0)
		{
		echo "<tr class=\"title\"><td class=\"underline\" colspan='2'>";
		echo $language['change_all_folders']."</td>";
		echo"<td class=\"underline2\"><form action=\"index.php#folders\" method=\"POST\" >\n";
		echo "<input type=\"hidden\" name ='navig' value='2'>\n";
		echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
		echo "<input type=\"hidden\" name ='min' value='$min'>";
		echo "<input type=\"hidden\" name ='minf' value='$minf'>";
		echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
		echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
		echo "<input type=\"hidden\" name ='datedisplay' value='0'>\n";
		echo "<input type=\"hidden\" name ='changechmod' value='1'>\n";
		echo "<input type=\"hidden\" name ='filedir' value='changeallfolders'>\n";
		echo "<input type=\"hidden\" name ='type2' value='folder'>\n";
		echo"<input type=\"radio\" name=\"chmod\" value=\"1\">0555&nbsp;&nbsp;\n";
		echo"<input type=\"radio\" name=\"chmod\" value=\"0\">0755&nbsp;&nbsp;\n";
		echo"<input name='ok' type='submit'  value='OK' size='20' >\n";
		echo"</form></td></tr>\n";
		}
	//sorting
	if($sort==1)
		{
		ksort($dirlist);
		}
	else
		{
		arsort($dirlist);
		}
	$j=1;
	foreach($dirlist as $value=>$timedir)
		{
		if(($j>=$minf && $j<$maxf) OR $nbrfolders < 301)
			{
			$dir="../".$value;
			if($today-$timedir< 604800)
				{
				$modifrecently='ok';
				}
			else
				{
				$modifrecently='no';
				}
			if($timedir)
				{
				$perms=fileperms($dir);
				$chmod=substr(sprintf('%o', $perms), -4);
				if(in_array($chmod, $correctfolderchmod))
					{
					$image='images/ok.png';
					}
				elseif(in_array($chmod, $goodfolderchmod))
					{
					$image='images/super.png';
					}
				else
					{
					$image='images/nook.png';
					}
				if($datedisplay==1)
					{
					if($today-$timedir< 604800)
						{
						echo"<tr class=\"red2\">\n";
						}
					else
						{
						echo"<tr class=\"black\">\n";
						}
					if($datedisplay==1)
						{
						echo "<td>";
						}
					else
						{
						echo "<td class=\"underline\">";
						}
					echo crawltcuturl($value,80)."</td><td>".$chmod."&nbsp;<img src='".$image."'>";
					if(in_array($dir, $listfolderdontchange))
						{
						echo"&nbsp;&nbsp;<img src=\"./images/lock.png\" width=\"16px\" height=\"16px\" border=\"0\" >\n";
						}
					echo"</td>";
					if($crawltlang=='french')
						{
						setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
						echo "<td align='right'>". strftime("%d %B %Y  %H:%M:%S", $timedir)."</td></tr>";
						}
					else
						{
						echo "<td align='right' >".date ("F d Y  H:i:s", $timedir)."</td></tr>";
						}
					}
				else
					{
					if($today-$timedir< 604800)
						{
						echo"<tr class=\"red2\">\n";
						}
					else
						{
						echo"<tr class=\"black\">\n";
						}
					if($datedisplay==1)
						{
						echo "<td>";
						}
					else
						{
						echo "<td class=\"underline\">";
						}
					echo crawltcuturl($value,80)."</td><td class=\"underline\">".$chmod."&nbsp;<img src='".$image."'></td>";
					echo"<td class=\"underline2\"><form action=\"index.php#folders\" method=\"POST\" >\n";
					echo "<input type=\"hidden\" name ='navig' value='2'>\n";
					echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
					echo "<input type=\"hidden\" name ='min' value='$min'>";
					echo "<input type=\"hidden\" name ='minf' value='$minf'>";
					echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
					echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
					echo "<input type=\"hidden\" name ='datedisplay' value='0'>\n";
					echo "<input type=\"hidden\" name ='changechmod' value='1'>\n";
					echo "<input type=\"hidden\" name ='filedir' value='$dir'>\n";
					echo "<input type=\"hidden\" name ='type2' value='folder'>\n";
					if(in_array($dir, $listfolderdontchange))
						{
						echo"<input type=\"checkbox\" name=\"dontchange\" value=\"ok\" checked><img src=\"./images/lock.png\" width=\"16px\" height=\"16px\" border=\"0\" >&nbsp;&nbsp;\n";
						}
					else
						{
						echo"<input type=\"checkbox\" name=\"dontchange\" value=\"ok\"><img src=\"./images/lock.png\" width=\"16px\" height=\"16px\" border=\"0\" >&nbsp;&nbsp;\n";
						}
					if($perms==16709)
						{
						echo"<input type=\"radio\" name=\"chmod\" value=\"1\" checked>0555&nbsp;&nbsp;\n";
						echo"<input type=\"radio\" name=\"chmod\" value=\"0\">0755&nbsp;&nbsp;\n";
						}
					elseif($perms==16877 OR $perms==16837) 
						{
						echo"<input type=\"radio\" name=\"chmod\" value=\"1\">0555&nbsp;&nbsp;\n";
						echo"<input type=\"radio\" name=\"chmod\" value=\"0\" checked>0755&nbsp;&nbsp;\n";
						}
					else
						{
						echo"<input type=\"radio\" name=\"chmod\" value=\"1\">0555&nbsp;&nbsp;\n";
						echo"<input type=\"radio\" name=\"chmod\" value=\"0\">0755&nbsp;&nbsp;\n";
						}
					echo"<input name='ok' type='submit'  value='OK' size='20' >\n";
					echo"</form></td></tr>\n";
					}
				}
			}
		$j++;
		}
	}
echo"</table>\n";
if($nbrfolders > 300)
	{
	echo"<div align='center'><br><table><tr><td>";
	if($minf>101)
		{
		$buttonf0="1 ==> 100";
		echo"<form action=\"index.php#folders\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name ='navig' value='2'>\n";
		echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
		echo "<input type=\"hidden\" name ='minf' value=1>\n";
		echo "<input type=\"hidden\" name ='min' value=$min>\n";
		echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
		echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
		echo"<input name='ok' type='submit'  value='$buttonf0' size='20' >\n";
		echo"</form></td><td>\n";
		}
	if($minf>1)
		{
		$minf1=$minf-100;
		$maxf1=$minf-1;
		$buttonf1=$minf1." ==> ".$maxf1;
		echo"<form action=\"index.php#folders\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name ='navig' value='2'>\n";
		echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
		echo "<input type=\"hidden\" name ='minf' value=$minf1>\n";
		echo "<input type=\"hidden\" name ='min' value=$min>\n";
		echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
		echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
		echo"<input name='ok' type='submit'  value='$buttonf1' size='20' >\n";
		echo"</form>\n";
		}
	else
		{
		echo"&nbsp;";
		}
	echo"</td><td>";
	$lastf=$maxf-1;
	if($lastf> $nbrfolders)
		{
		$lastf=$nbrfolders;
		}
	echo "<h2>".$minf." ==> ".$lastf."</h2>";
	echo"</td><td>";
	$minf2= $minf+100;
	if($minf2< $nbrfolders)
		{
		$maxf2=$minf2+99;
		if($maxf2> $nbrfolders)
			{
			$maxf2=$nbrfolders;
			}
		$buttonf2=$minf2." ==> ".$maxf2;
		echo"<form action=\"index.php#folders\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name ='navig' value='2'>\n";
		echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
		echo "<input type=\"hidden\" name ='minf' value=$minf2>\n";
		echo "<input type=\"hidden\" name ='min' value=$min>\n";
		echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
		echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
		echo"<input name='ok' type='submit'  value='$buttonf2' size='20' >\n";
		echo"</form>\n";
		}
	echo"</td></tr></table></div>";
	}
echo"</div>\n";
echo"<A NAME=\"files\">&nbsp;</A>\n";
echo"<hr><hr>\n";
echo"<h2>".$language['file_modification']."</h2>\n";
echo"<div align=\"center\">\n";
//sorting choice
echo"<form action=\"index.php#files\" method=\"POST\" >\n";
echo "<input type=\"hidden\" name ='navig' value='2'>\n";
echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>\n";
echo "<input type=\"hidden\" name ='min' value='$min'>\n";
echo "<input type=\"hidden\" name ='minf' value='$minf'>\n";
echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
if($sortf==1)
	{
	echo"<p><input type=\"radio\" name=\"sortf\" value=\"1\" checked>".$language['filename']."&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"sortf\" value=\"2\">".$language['filedate']."&nbsp;&nbsp;\n";
	}
else 
	{
	echo"<p><input type=\"radio\" name=\"sortf\" value=\"1\">".$language['filename']."&nbsp;&nbsp;\n"; 
	echo"<input type=\"radio\" name=\"sortf\" value=\"2\" checked>".$language['filedate']."&nbsp;&nbsp;\n";
	}
echo"<input name='ok' type='submit'  value='OK' size='20' >\n";
echo"</p></form>&nbsp;\n";
echo"</div>\n";
echo"<div align=\"center\">\n";
if(function_exists('chmod'))
	{
	echo"<form action=\"index.php#files\" method=\"POST\">\n";
	echo "<input type=\"hidden\" name ='navig' value='2'>\n";
	echo "<input type=\"hidden\" name ='min' value='$min'>";
	echo "<input type=\"hidden\" name ='minf' value='$minf'>";
	echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
	echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
	if($datedisplay==1)
		{
		$text5=$language['chmod_change_menu'];	
		echo "<input type=\"hidden\" name ='datedisplay' value='0'>\n";
		}
	else
		{
		$text5=$language['datedisplay'];
		echo "<input type=\"hidden\" name ='datedisplay' value='1'>\n";
		}
	echo"<input name='ok' type='submit'  value='$text5' size='20' >\n";
	echo"</form>\n";
	}
else
	{
	echo "<p style='font-size:12px; font-style:italic;'>".$language['nochmod']."</p>";
	}
echo"<br>(=> <A HREF=\"#folders\">".$language['folder_modification']."</A> )<br><br><hr>\n";
if($nbrfiles > 300)
	{
	echo"<div align='center'><table><tr><td>";
	if($min>101)
		{
		$button0="1 ==> 100";
		echo"<form action=\"index.php#files\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name ='navig' value='2'>\n";
		echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
		echo "<input type=\"hidden\" name ='min' value=1>\n";
		echo "<input type=\"hidden\" name ='minf' value=$minf>\n";
		echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
		echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
		echo"<input name='ok' type='submit'  value='$button0' size='20' >\n";
		echo"</form></td><td>\n";
		}
	if($min>1)
		{
		$min1=$min-100;
		$max1=$min-1;
		$button1=$min1." ==> ".$max1;
		echo"<form action=\"index.php#files\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name ='navig' value='2'>\n";
		echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
		echo "<input type=\"hidden\" name ='min' value=$min1>\n";
		echo "<input type=\"hidden\" name ='minf' value=$minf>\n";
		echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
		echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
		echo"<input name='ok' type='submit'  value='$button1' size='20' >\n";
		echo"</form>\n";
		}
	else
		{
		echo"&nbsp;";
		}
	echo"</td><td>";
	$last=$max-1;
	if($last> $nbrfiles)
		{
		$last=$nbrfiles;
		}
	echo "<h2>".$min." ==> ".$last."</h2>";
	echo"</td><td>";
	$min2= $min+100;
	if($min2< $nbrfiles)
		{
		$max2=$min2+99;
		if($max2> $nbrfiles)
			{
			$max2=$nbrfiles;
			}
		$button2=$min2." ==> ".$max2;
		echo"<form action=\"index.php#files\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name ='navig' value='2'>\n";
		echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
		echo "<input type=\"hidden\" name ='min' value=$min2>\n";
		echo "<input type=\"hidden\" name ='minf' value=$minf>\n";
		echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
		echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
		echo"<input name='ok' type='submit'  value='$button2' size='20' >\n";
		echo"</form>\n";
		}
	echo"</td></tr></table></div>";
	}
echo"<table width='90%' cellpadding='0px' cellspacing='0'>\n";
echo"<tr><th class='titleleft'>".$language['file']."</th><th class='titleleft' width='100px'>".$language['chmod']."</th><th class='titleright' width='300px'>\n"; 
if($datedisplay==1)
	{
 	echo $language['datemod'];
	}
else
	{
 	echo $language['changechmod'];
	}
echo"</th></tr>\n";
if($datedisplay==0)
	{
	echo "<tr class=\"title\"><td class=\"underline\" colspan='2'>";
	echo $language['change_all_files']."</td>";
	echo"<td class=\"underline2\"><form action=\"index.php#files\" method=\"POST\" >\n";
	echo "<input type=\"hidden\" name ='navig' value='2'>\n";
	echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
	echo "<input type=\"hidden\" name ='min' value='$min'>";
	echo "<input type=\"hidden\" name ='minf' value='$minf'>";
	echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
	echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
	echo "<input type=\"hidden\" name ='datedisplay' value='0'>\n";
	echo "<input type=\"hidden\" name ='changechmod' value='1'>\n";
	echo "<input type=\"hidden\" name ='filedir' value='changeallfiles'>\n";
	echo "<input type=\"hidden\" name ='type2' value='file'>\n";
	echo"<input type=\"radio\" name=\"chmod\" value=\"1\">0444&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"chmod\" value=\"0\">0644&nbsp;&nbsp;\n";
	echo"<input name='ok' type='submit'  value='OK' size='20' >\n";
	echo"</form></td></tr>\n";
	}
//files
//sorting
if($sortf==1)
	{
	ksort($filelist);
	}
else
	{
	arsort($filelist);
	}
$i=1;
if(count($filelist)>0)
	{
	foreach($filelist as $value=>$timeindex)
		{
		if(($i>=$min && $i<$max) OR $nbrfiles < 301)
			{
			$index=$value;
			$index2=ltrim($value,'./');
			if($today-$timeindex< 604800)
				{
				$modifrecently='ok';
				}
			else
				{
				$modifrecently='no';
				}
			if($timeindex)
				{
				$perms=fileperms($value);
				$chmod=substr(sprintf('%o', $perms), -4);
				if(in_array($chmod, $correctfilechmod))
					{
					$image='images/ok.png';
					}
				elseif(in_array($chmod, $goodfilechmod))
					{
					$image='images/super.png';
					}
				else
					{
					$image='images/nook.png';
					}
				if($datedisplay==1)
					{
					if($today-$timeindex< 604800)
						{
						echo"<tr class=\"red2\">\n";
						}
					else
						{
						echo"<tr class=\"black\">\n";
						}
					if($datedisplay==1)
						{
						echo "<td>";
						}
					else
						{
						echo "<td class=\"underline\">";
						}
					echo crawltcuturl($index2,80)."</td><td>".$chmod."&nbsp;<img src='".$image."'>";
					if(in_array($value, $listfiledontchange))
						{
						echo"&nbsp;&nbsp;<img src=\"./images/lock.png\" width=\"16px\" height=\"16px\" border=\"0\" >\n";
						}
					echo"</td><td align='right'>";
					if($crawltlang=='french')
						{
						setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
						echo strftime("%d %B %Y  %H:%M:%S", $timeindex)."</td></tr>";
						}
					else
						{
						echo date ("F d Y  H:i:s", $timeindex)."</td></tr>";
						}
					}
				else
					{
					if($today-$timeindex< 604800)
						{
						echo"<tr class=\"red2\">\n";
						}
					else
						{
						echo"<tr class=\"black\">\n";
						}
					if($datedisplay==1)
						{
						echo "<td>";
						}
					else
						{
						echo "<td class=\"underline\">";
						}
					echo crawltcuturl($index2,80)."</td><td class=\"underline\">".$chmod."&nbsp;<img src='".$image."'></td>";
					echo"<td class=\"underline2\"><form action=\"index.php#files\" method=\"POST\" >\n";
					echo "<input type=\"hidden\" name ='navig' value='2'>\n";
					echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
					echo "<input type=\"hidden\" name ='min' value='$min'>";
					echo "<input type=\"hidden\" name ='minf' value='$minf'>";
					echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
					echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
					echo "<input type=\"hidden\" name ='datedisplay' value='0'>\n";
					echo "<input type=\"hidden\" name ='changechmod' value='1'>\n";
					echo "<input type=\"hidden\" name ='filedir' value='$value'>\n";
					echo "<input type=\"hidden\" name ='type2' value='file'>\n";
					if(in_array($value, $listfiledontchange))
						{
						echo"<input type=\"checkbox\" name=\"dontchange\" value=\"ok\" checked><img src=\"./images/lock.png\" width=\"16px\" height=\"16px\" border=\"0\" >&nbsp;&nbsp;\n";
						}
					else
						{
						echo"<input type=\"checkbox\" name=\"dontchange\" value=\"ok\"><img src=\"./images/lock.png\" width=\"16px\" height=\"16px\" border=\"0\" >&nbsp;&nbsp;\n";
						}
					if($perms==33028)
						{
						echo"<input type=\"radio\" name=\"chmod\" value=\"1\" checked>0444&nbsp;&nbsp;\n";
						echo"<input type=\"radio\" name=\"chmod\" value=\"0\">0644&nbsp;&nbsp;\n";
						}
					elseif($perms==33188) 
						{
						echo"<input type=\"radio\" name=\"chmod\" value=\"1\">0444&nbsp;&nbsp;\n";
						echo"<input type=\"radio\" name=\"chmod\" value=\"0\" checked>0644&nbsp;&nbsp;\n";        
						}
					else
						{
						echo"<input type=\"radio\" name=\"chmod\" value=\"1\">0444&nbsp;&nbsp;\n";
						echo"<input type=\"radio\" name=\"chmod\" value=\"0\">0644&nbsp;&nbsp;\n";        
						}
					echo"<input name='ok' type='submit'  value='OK' size='20' >\n";
					echo"</form></td></tr>\n";
					}
				}
			}
		$i++;
		}
	}
echo"</table>\n";
if($nbrfiles > 300)
	{
	echo"<div align='center'><br><table><tr><td>";
	if($min>101)
		{
		$button0="1 ==> 100";
		echo"<form action=\"index.php#folders\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name ='navig' value='2'>\n";
		echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
		echo "<input type=\"hidden\" name ='min' value=1>\n";
		echo "<input type=\"hidden\" name ='minf' value=$minf>\n";
		echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
		echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
		echo"<input name='ok' type='submit'  value='$button0' size='20' >\n";
		echo"</form></td><td>\n";
		}
	if($min>1)
		{
		$min1=$min-100;
		$max1=$min-1;
		$button1=$min1." ==> ".$max1;
		echo"<form action=\"index.php#files\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name ='navig' value='2'>\n";
		echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
		echo "<input type=\"hidden\" name ='min' value=$min1>\n";
		echo "<input type=\"hidden\" name ='minf' value=$minf>\n";
		echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
		echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
		echo"<input name='ok' type='submit'  value='$button1' size='20' >\n";
		echo"</form>\n";
		}
	else
		{
		echo"&nbsp;";
		}
	echo"</td><td>";
	$last=$max-1;
	if($last> $nbrfiles)
		{
		$last=$nbrfiles;
		}
	echo "<h2>".$min." ==> ".$last."</h2>";
	echo"</td><td>";
	$min2= $min+100;
	if($min2< $nbrfiles)
		{
		$max2=$min2+99;
		if($max2> $nbrfiles)
			{
			$max2=$nbrfiles;
			}
		$button2=$min2." ==> ".$max2;
		echo"<form action=\"index.php#files\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name ='navig' value='2'>\n";
		echo "<input type=\"hidden\" name ='datedisplay' value='$datedisplay'>";
		echo "<input type=\"hidden\" name ='min' value=$min2>\n";
		echo "<input type=\"hidden\" name ='minf' value=$minf>\n";
		echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";
		echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
		echo"<input name='ok' type='submit'  value='$button2' size='20' >\n";
		echo"</form>\n";
		}
	echo"</td></tr></table></div>\n";
	}
echo"<hr></div>\n";
echo"</td></tr><tr><td></td><td>\n";
echo"<div  align=\"left\" style=\"padding-left:250px;\"><br>\n";
echo"<p class=\"red\">".$language['file_modification2']."</p>\n";
echo"<p><img src='images/super.png'>&nbsp;".$language['chmod_super']."<br>\n";
echo"<img src='images/ok.png'>&nbsp;".$language['chmod_ok']."<br>\n";
echo"<img src='images/nook.png' >&nbsp;".$language['chmod_nook']."</p>\n";
echo"</div><div align=\"center\">\n";
echo"<h2>".$language['chmod_advices']."</h2>\n";
echo"<table width=\"80%\" cellpadding='0px' cellspacing='0'>\n";
echo"<tr><td class=\"tableau0\">&nbsp;</td>\n";
echo"<td class=\"tableau1\"><img src='images/super.png'>&nbsp;".$language['chmod_safe']."*</td>\n";
echo"<td class=\"tableau2\"><img src='images/ok.png'>&nbsp;".$language['chmod_mini']."</td></tr>\n";
echo"<tr><td class=\"tableau3\">".$language['folder']."&nbsp;&nbsp;</td>\n";
echo"<td class=\"tableau4\" >0555</td>\n";
echo"<td class=\"tableau5\">0755</td></tr>\n";
echo"<tr><td class=\"tableau6\">".$language['file']."&nbsp;&nbsp;</td>\n";
echo"<td class=\"tableau7\">0444</td>\n";
echo"<td class=\"tableau5\">0644</td></tr>\n";
echo"<tr><td colspan=\"3\" style=\"padding-left:20px;\"><br>\n";
echo"<p>".$language['nota']."</p>\n";
echo"</td></tr></table><br>\n";

?>
