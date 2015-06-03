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
// file: admin.php
//----------------------------------------------------------------------
//  Last update: 19/10/2013
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
$errorchangelogin=0;
$goodfilechmod=array();
$goodfilechmod[0]='0404';
$goodfilechmod[1]='0444';
$correctfilechmod=array();
$correctfilechmod[0]='0604';
$correctfilechmod[1]='0644';
$goodfolderchmod=array();
$goodfolderchmod[0]='0505';
$goodfolderchmod[1]='0555';
$correctfolderchmod=array();
$correctfolderchmod[0]='0705';
$correctfolderchmod[1]='0755';
//collect htaccess existing parameter
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
		$hotlinkok = $ligne->hotlinkok;							
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
		$trustsites = '';		
		$trustip = '';		
		$trustuseragent = '';
		$trustvariable = '';
		$forbiddenip = '';				
		$forbiddenurl = '';
		$forbiddenparameter = '';
		$forbiddenreferer = '';
		$forbiddenword = '';
		$autoprepend = '0';		
		$actualhtaccess = '';
		$hotlinkok = '';		
	}	
//Treat trustip
if($trustip !='')
	{
	$listtrustip=explode(',',$trustip);	
	}
else
	{
	$listtrustip=array();
	}
sort($listtrustip);
//Treat scriptsused		
if($scriptsused !='')
	{
	$listscriptsused=explode(',',$scriptsused);	
	}
else
	{
	$listscriptsused=array();
	}
sort($listscriptsused);		
//Treat trustsites
if($trustsites !='')
	{
	$listtrustsites=explode(',',$trustsites);	
	}
else
	{
	$listtrustsites=array();
	}
sort($listtrustsites);	
//Treat trustvariable
if($trustvariable !='')
	{
	$listtrustvariable=explode(',',$trustvariable);	
	}
else
	{
	$listtrustvariable=array();
	}
sort($listtrustvariable);		
//Treat forbiddenip
if($forbiddenip !='')
	{
	$listforbiddenip=explode(',',$forbiddenip);	
	}
else
	{
	$listforbiddenip=array();
	}
sort($listforbiddenip);	
//Treat forbiddenreferer
if($forbiddenreferer !='')
	{
	$listforbiddenreferer=explode(',',$forbiddenreferer);
	}
else
	{
	$listforbiddenreferer=array();
	}
sort($listforbiddenreferer);
//Treat forbiddenurl
if($forbiddenurl !='')
	{
	$listforbiddenurl=explode(',',$forbiddenurl);
	}
else
	{
	$listforbiddenurl=array();
	}
sort($listforbiddenurl);
//Treat forbiddenparameter
if($forbiddenparameter !='')
	{
	$listforbiddenparameter=explode(',',$forbiddenparameter);
	}
else
	{
	$listforbiddenparameter=array();
	}
sort($listforbiddenparameter);
//Treat forbiddenword
if($forbiddenword !='')
	{
	$listforbiddenword=explode(',',$forbiddenword);
	}
else
	{
	$listforbiddenword=array();
	}
sort($listforbiddenword);
//Treat hotlinkok
if($hotlinkok !='')
	{
	$listhotlinkok=explode(',',$hotlinkok);	
	}
else
	{
	$listhotlinkok=array();
	}
sort($listhotlinkok);
//Treat actualhtaccess
$htaccessdisplay=htmlentities($actualhtaccess);
//change file type to display
if($changefile=='ok')
	{
	//get file & folder setting in case of changement	
	if(isset($_POST['folderlevel']))
		{
		$folderlevel = $_POST['folderlevel'];
		}
	else
		{
		$folderlevel = 'all';
		}
	if(isset($_POST['justbad']))
		{
		$justbad = $_POST['justbad'];
		}
	else
		{
		$justbad = '0';
		}
	if(isset($_POST['whichfile']))
		{
		$whichfile = $_POST['whichfile'];
		}
	else
		{
		$whichfile = 'all';
		}
	if(isset($_POST['nocache']))
		{	
		$nocache = $_POST['nocache'];
		}
	else
		{
		$nocache = '0';
		}
	if(isset($_POST['nostats']))
		{
		$nostats = $_POST['nostats'];
		}
	else
		{
		$nostats = '0';
		}
	if(isset($_POST['nologs']))
		{
		$nologs = $_POST['nologs'];
		}
	else
		{
		$nologs = '0';
		}		
	unset($_SESSION['filelist']);
	unset($_SESSION['dirlist']);
	$sql ="UPDATE crawlp_site_setting SET whichfile='".sql_quote($whichfile)."', folderlevel='".sql_quote($folderlevel)."', justbad='".sql_quote($justbad)."', nocache='".sql_quote($nocache)."',nostats='".sql_quote($nostats)."', nologs='".sql_quote($nologs)."'  WHERE id_site= '" . sql_quote($site) . "' ";
	$requete = mysql_query($sql, $connexion) or die(mysql_error());
	}
	
//CHMOD quick link--------------------------------------------------------------



//check CHMOD level
//files
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

$checkchmodok=0;

//determine the path to the file
if (isset($_SERVER['SCRIPT_FILENAME']) && !empty($_SERVER['SCRIPT_FILENAME'])) {
	$path = dirname($_SERVER['SCRIPT_FILENAME']);
} elseif (isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['PHP_SELF']) && !empty($_SERVER['PHP_SELF'])) {
	$path = dirname($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']);
} else {
	$path = '..';
}
$value=$path."/index.php";
$perms=fileperms($value);
$chmod=substr(sprintf('%o', $perms), -4);
if(in_array($chmod,$goodfilechmod))
	{
	$checkchmodok=1;		
	}
//folders
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
$checkchmod2ok=0;

$perms=fileperms($path);
$chmod=substr(sprintf('%o', $perms), -4);
if(in_array($chmod,$goodfolderchmod))
	{
	$checkchmod2ok=1;		
	}
	
	
	
	
//change language------------------------------------------------------
if($changelang=='ok')
	{				
	$sql ="UPDATE crawlp_general_setting SET  language='".sql_quote($crawltlang)."'";
	$requete = mysql_query($sql, $connexion);		
	}
//check if CrawlProtect htaccess file is in place
if(!isset($_SESSION['verif']))
	{
	if(file_exists('../.htaccess') )
		{
		if(function_exists('fopen'))
			{
			$file = fopen("../.htaccess", "r");
			$existingfile = fread($file, filesize("../.htaccess"));
			fclose($file);
			if(preg_match("/File created by CrawlProtect/i",$existingfile))
				{
				$_SESSION['verif']='ok';
				}
			else
				{
				$_SESSION['verif']='nook';
				}
			}
		else
			{
			$_SESSION['verif']='notpossible';
			}
		}
	else
		{
		$_SESSION['verif']='nook';
		}
	}
	
//check if CrawlProtect  tag is in place
if(!isset($_SESSION['veriftag']))
	{
	if(isset($_COOKIE["crawlprotecttag"]))
		{
		$_SESSION['veriftag']='ok';	
		}
	else
		{
		$_SESSION['veriftag']='nook';	
		}		
	}	
	
	
	
//include menu
include ("include/menusite.php");
include ("include/menumain.php");	
//change login---------------------------------------------------------
if($changelogin=='ok')
	{
	define('IN_CRAWLT_ADMIN', TRUE);
	include("include/adminchangepassword.php");
	}	
	
//reset data--------------------------------------------------------------
if($resetdata=='ok')
	{
	$text1=$language['suppress'];
	$text2=$language['keep'];
	echo"<hr><h2>".$language['confirm_zero']."</h2><br>";
	echo "<div class=\"form5\">\n";
	echo"<form action=\"index.php\" method=\"POST\">\n";
	echo"<input type=\"hidden\" name ='navig' value='1'>\n";
	echo"<input type=\"hidden\" name ='resetdata2' value='ok'>\n";	
	echo"<input name='ok' type='submit' class='widebutton' value='$text1' size='20' >\n";
	echo"</form>&nbsp;\n";
	echo"<form action=\"index.php\" method=\"POST\">\n";
	echo "<input type=\"hidden\" name ='navig' value='1'>\n";
	echo "<input type=\"hidden\" name ='resetdata2' value='nook'>\n";
	echo"<input name='ok' type='submit' class='widebutton' value='$text2' size='20' >\n";
	echo"</form>&nbsp;\n";
	echo"</div><br><hr>\n";

	}
if($resetdata2=='ok')
	{
	$sql = "DELETE FROM crawlp_stats WHERE  id_site= '" . sql_quote($site) . "'";
	$requete = mysql_query($sql, $connexion);
	}
//reset htaccess--------------------------------------------------------------
if($changehtaccess=='ok')
	{
	$text1=$language['suppress2'];
	$text2=$language['keep2'];
	echo"<hr><h2>".$language['confirm_remove_htaccess']."</h2><br>";
	echo "<div class=\"form5\">\n";
	echo"<form action=\"index.php\" method=\"POST\">\n";
	echo"<input type=\"hidden\" name ='navig' value='1'>\n";
	echo"<input type=\"hidden\" name ='changehtaccess2' value='ok'>\n";	
	echo"<input name='ok' type='submit' class='widebutton' value='$text1' size='20' >\n";
	echo"</form>&nbsp;\n";
	echo"<form action=\"index.php\" method=\"POST\">\n";
	echo "<input type=\"hidden\" name ='navig' value='1'>\n";
	echo "<input type=\"hidden\" name ='changehtaccess2' value='nook'>\n";
	echo"<input name='ok' type='submit' class='widebutton' value='$text2' size='20' >\n";
	echo"</form>&nbsp;\n";
	echo"</div><br><hr>\n";

	}
if($changehtaccess2=='ok')
	{
	$actualhtaccessdisplay=htmlentities($actualhtaccess);	
	//put in place the htaccess
	$filename=$_SERVER['DOCUMENT_ROOT']."/.htaccess";
	$filedir=$_SERVER['DOCUMENT_ROOT'];
	//chmod the file if already exist
	if(function_exists('chmod'))
		{
		@chmod($filename,0644);
		}
	
	if ( $file = @fopen($filename,"w") )
		{
		fwrite($file, $actualhtaccess);
		fclose($file);
		$filereplace=1;
		unset($_SESSION['verif']);
		}
	else
		{
		$filereplace=0;
		}
			
	if(function_exists('chmod'))
		{
		@chmod($filename, 0404);
		}
	if($filereplace==1)
		{
		echo "<h2>".$language['removehtaccessok']."</h2>\n";
		$_SESSION['verif']='nook';			
		}
	else
		{
		echo "<div class='alert3'>".$language['removehtaccessnook']."</div><br><br>\n";
		echo"<div class='htaccess'><pre>\n";
		echo $actualhtaccessdisplay;
		echo"</pre></div><br><br>\n";
		$_SESSION['verif']='notpossible';					
		}
	}
echo"<table width=\"100%\"><tr><td width=\"50%\" valign=\top\" align=\"center\">\n";
echo"<h2>".$language['admin']."</h2>\n";
$text1=$language['reset_zero'];
echo"<form action=\"index.php\" method=\"POST\">\n";
echo "<input type=\"hidden\" name ='navig' value='1'>\n";
echo "<input type=\"hidden\" name ='resetdata' value='ok'>\n";	
echo"<input name='ok' type='submit' class='widebutton' value='$text1' size='20' >\n";
echo"</form>&nbsp;\n";
$text2=$language['change_password'];
echo"<form action=\"index.php\" method=\"POST\">\n";
echo "<input type=\"hidden\" name ='navig' value='1'>\n";
echo "<input type=\"hidden\" name ='changelogin' value='ok'>\n";
echo"<input name='ok' type='submit' class='widebutton' value='$text2' size='20' >\n";
echo"</form>&nbsp;\n";
$text3=$language['change_htaccess'];
echo"<form action=\"index.php\" method=\"POST\">\n";
echo "<input type=\"hidden\" name ='navig' value='1'>\n";
echo "<input type=\"hidden\" name ='changehtaccess' value='ok'>\n";
echo"<input name='ok' type='submit' class='widebutton' value='$text3' size='20' >\n";
echo"</form>\n";
//file selection
echo"<br><form action=\"index.php\" method=\"POST\" >\n";
echo "<input type=\"hidden\" name ='navig' value='1'>\n";
echo"<h2>".$language['file_modification']."</h2>";
echo "<input type=\"hidden\" name ='changefile' value='ok'>\n";	
echo"<div align='left' style='padding-left:90px;'>";
if($whichfile=='hihfc')
	{
	echo"<input type=\"checkbox\" name=\"whichfile\" value=\"hihfc\" checked>".$language['selectfiles']."<br><br>\n";
	}
else
	{
	echo"<input type=\"checkbox\" name=\"whichfile\" value=\"hihfc\">".$language['selectfiles']."<br><br>\n";
	}
if($justbad=='1')
	{
	echo"<input type=\"checkbox\" name=\"justbad\" value=\"1\" checked>".$language['justbaddisplay']."<br><br>\n";
	}
else
	{
	echo"<input type=\"checkbox\" name=\"justbad\" value=\"1\">".$language['justbaddisplay']."<br><br>\n";
	}
if($nocache=='1')
	{
	echo"<input type=\"checkbox\" name=\"nocache\" value=\"1\" checked>".$language['nocache']."<br><br>\n";
	}
else
	{
	echo"<input type=\"checkbox\" name=\"nocache\" value=\"1\">".$language['nocache']."<br><br>\n";
	}
if($nostats=='1')
	{
	echo"<input type=\"checkbox\" name=\"nostats\" value=\"1\" checked>".$language['nostats']."<br><br>\n";
	}
else
	{
	echo"<input type=\"checkbox\" name=\"nostats\" value=\"1\">".$language['nostats']."<br><br>\n";
	}
if($nologs=='1')
	{
	echo"<input type=\"checkbox\" name=\"nologs\" value=\"1\" checked>".$language['nologs']."<br><br>\n";
	}
else
	{
	echo"<input type=\"checkbox\" name=\"nologs\" value=\"1\">".$language['nologs']."<br><br>\n";
	}
if($folderlevel=='limit')
	{
	echo"<input type=\"checkbox\" name=\"folderlevel\" value=\"limit\" checked>".$language['folderlevelrestricted']."<br><br>\n";
	}
else
	{
	echo"<input type=\"checkbox\" name=\"folderlevel\" value=\"limit\">".$language['folderlevelrestricted']."<br><br>\n";
	}
echo"</div>";
echo"<input name='ok' type='submit'  value='OK' class='widebutton' size='60px' >\n";
echo"</p></form>&nbsp;\n";
//language selection
echo"<br><br><form action=\"index.php\" method=\"POST\" >\n";
echo "<input type=\"hidden\" name ='navig' value='1'>\n";
echo"<h2>".$language['change_language']."</h2>";
echo "<input type=\"hidden\" name ='changelang' value='ok'>\n";	
if($crawltlang=='french')
	{
	echo"<p><input type=\"radio\" name=\"lang2\" value=\"1\" checked>Français&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"2\">Anglais&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"3\">Italien&nbsp;&nbsp;<br>\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"4\">Russe&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"5\">Suédois&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"6\">Espagnol&nbsp;&nbsp;\n";	
	
		
	}
elseif($crawltlang=='italian')
	{
	echo"<p><input type=\"radio\" name=\"lang2\" value=\"1\" >Francese&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"2\">Inglese&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"3\" checked>Italiano&nbsp;&nbsp;<br>\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"4\">Russo&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"5\">Svedese&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"6\">Spagnolo&nbsp;&nbsp;\n";			
	}
elseif($crawltlang=='russian')
	{
	echo"<p><input type=\"radio\" name=\"lang2\" value=\"1\" >французский&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"2\">английский&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"3\">итальянский&nbsp;&nbsp;<br>\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"4\" checked>pусский&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"5\">шведский&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"6\">испанский&nbsp;&nbsp;\n";			
	}
elseif($crawltlang=='swedish')
	{
	echo"<p><input type=\"radio\" name=\"lang2\" value=\"1\" >Franska&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"2\">Engelska&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"3\">Italienska&nbsp;&nbsp;<br>\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"4\">Ryska&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"5\" checked>Svenska&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"6\">Spanska&nbsp;&nbsp;\n";			
	}
elseif($crawltlang=='spanish')
	{
	echo"<p><input type=\"radio\" name=\"lang2\" value=\"1\" >Francés&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"2\">Inglés&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"3\">Italiano&nbsp;&nbsp;<br>\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"4\">Ruso&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"5\">Sueco&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"6\" checked>Español&nbsp;&nbsp;\n";			
	}				
	else 
	{
	echo"<p><input type=\"radio\" name=\"lang2\" value=\"1\">French&nbsp;&nbsp;\n"; 
	echo"<input type=\"radio\" name=\"lang2\" value=\"2\" checked>English&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"3\">Italian&nbsp;&nbsp;<br>\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"4\">Russian&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"5\">Swedish&nbsp;&nbsp;\n";
	echo"<input type=\"radio\" name=\"lang2\" value=\"6\">Spanish&nbsp;&nbsp;\n";			
	}
echo"<input name='ok' type='submit'  value='OK' size='20' >\n";
echo"</p></form>&nbsp;\n";

echo"</td><td valign=\"top\" align=\"center\">\n";
echo"<h2>".$language['check-set-up']."</h2>\n";


echo"<table width=\"80%\" cellpadding='0px' cellspacing='0'>\n";
echo"<tr><td >".$language['check-htaccess-crawlprotect']."&nbsp;&nbsp;</td>\n";
if($_SESSION['verif']=='ok')
	{
	echo"<td ><img src='images/ok.png'></td></tr>\n";
	}
elseif($_SESSION['verif']=='nook')
	{
	echo"<td ><img src='images/nook.png'></td></tr>\n";
	}
elseif($_SESSION['verif']=='notpossible')
	{
	echo"<td >".$language['verifnotpossible']."</td></tr>\n";	
	}	
echo"<tr><td >".$language['check-tag-crawlprotect']."&nbsp;&nbsp;</td>\n";
if($_SESSION['veriftag']=='ok' || $autoprepend==1)
	{
	echo"<td ><img src='images/ok.png'></td></tr>\n";
	}
elseif($_SESSION['veriftag']=='nook')
	{
	echo"<td ><img src='images/nook.png'></td></tr>\n";
	}
elseif($_SESSION['veriftag']=='notpossible')
	{
	echo"<td >".$language['verifnotpossible']."</td></tr>\n";	
	}	



echo"<tr><td >".$language['chmod-sample2-ok']."&nbsp;&nbsp;</td>\n";

	if($checkchmod2ok==0)
	{
	echo"<td ><img src='images/nook.png'></td></tr>\n";
	}
else
	{
	echo"<td ><img src='images/ok.png'></td></tr>\n";
	}	
	
echo"<tr><td >".$language['chmod-sample-ok']."&nbsp;&nbsp;</td>\n";

	if($checkchmodok==0)
	{
	echo"<td ><img src='images/nook.png'></td></tr>\n";
	}
else
	{
	echo"<td ><img src='images/ok.png'></td></tr>\n";
	}	
	
echo"</table>\n";		
if(	$_SESSION['verif']=='ok' && ($_SESSION['veriftag']=='ok' || $autoprepend==1) && $checkchmod2ok==1 && $checkchmodok==1)
	{
	echo "<p>".$language['message-security2']."</p>";	
	}
else
	{	
	echo "<p class=\"red\">".$language['message-security']."</p>";
	}	
echo "<div class='smalltext'>*".$language['notachmod']."</div>";
		
echo "<br><br><h2>".$language['quickchangechmod']."</h2>";
if(($checkchmodok+$checkchmod2ok)>0)
	{
	echo "<p>".$language['changechmodforupdate']."</p>";
	}
echo "<table width='400px'><tr class=\"title\"><td class=\"underline\" width='350px'>";
	if($checkchmod2ok==0)
		{	
		echo "&nbsp;&nbsp;".$language['change_all_folders_high_security']."*</td>";
		}
	else
		{
		echo "&nbsp;&nbsp;".$language['change_all_folders_std_security']."*</td>";
		}					
	echo "<td class=\"underline2\"><form action=\"index.php#folders\" method=\"POST\" >\n";
	echo "<input type=\"hidden\" name ='navig' value='2'>\n";
	echo "<input type=\"hidden\" name ='chmodadmin' value='1'>\n";
	echo "<input type=\"hidden\" name ='datedisplay' value='0'>\n";
	echo "<input type=\"hidden\" name ='changechmod' value='1'>\n";
	echo "<input type=\"hidden\" name ='filedir' value='changeallfolders'>\n";
	echo "<input type=\"hidden\" name ='type2' value='folder'>\n";
	if($checkchmod2ok==0)
		{
		echo"<input type=\"hidden\" name=\"chmod\" value=\"1\">\n";
		}
	else
		{
		echo"<input type=\"hidden\" name=\"chmod\"  value=\"0\">\n";
		}	
	echo"<input name='ok' type='submit'  value='OK' size='20' >\n";
	echo"</form></td></tr></table>\n";	
	
	
	echo "<table width='400px'><tr class=\"title\"><td class=\"underline\" width='350px'>";
	if($checkchmodok==0)
		{	
		echo "&nbsp;&nbsp;".$language['change_all_files_high_security']."*</td>";
		}
	else
		{
		echo "&nbsp;&nbsp;".$language['change_all_files_std_security']."*</td>";
		}
	echo"<td class=\"underline2\"><form action=\"index.php#files\" method=\"POST\" >\n";
	echo "<input type=\"hidden\" name ='navig' value='2'>\n";
	echo "<input type=\"hidden\" name ='chmodadmin' value='1'>\n";
	echo "<input type=\"hidden\" name ='datedisplay' value='0'>\n";
	echo "<input type=\"hidden\" name ='changechmod' value='1'>\n";
	echo "<input type=\"hidden\" name ='filedir' value='changeallfiles'>\n";
	echo "<input type=\"hidden\" name ='type2' value='file'>\n";
	if($checkchmodok==0)
		{
		echo"<input type=\"hidden\" name=\"chmod\"  value=\"1\">\n";
		}
	else
		{
		echo"<input type=\"hidden\" name=\"chmod\" value=\"0\">\n";
		}	
	echo"<input name='ok' type='submit'  value='OK' size='20' >\n";
	echo"</form></td></tr></table>\n";	
	
	echo"<table width='400px'><tr><td class='smalltext' >".$language['infolist']."</td></tr></table>\n";
		

if ($crawltlang == 'french') {
	echo "<br><br><h2>Infos<br><br><iframe name=\"I1\" src=\"http://www.crawltrack.net/news/crawltrack-news-fr.php\" marginwidth=\"1\" marginheight=\"1\" scrolling=\"auto\" border=\"1\" bordercolor=\"#003399\" frameborder=\"1px\" width=\"310px\" height=\"150px\"></iframe></h2>\n";
} else {
	echo "<br><br><h2>News<br><br><iframe name=\"I1\" src=\"http://www.crawltrack.net/news/crawltrack-news-en.php\" marginwidth=\"1\" marginheight=\"1\" scrolling=\"auto\" border=\"1\" bordercolor=\"#003399\" frameborder=\"1px\" width=\"310px\" height=\"150px\"></iframe></h2>\n";
}
echo"<br><br>\n";
echo"</td></tr></table>\n";
//tag for anti-spam
if($autoprepend==0)
	{
	//determine the path to the file
	if (isset($_SERVER['SCRIPT_FILENAME']) && !empty($_SERVER['SCRIPT_FILENAME'])) {
		$path = dirname($_SERVER['SCRIPT_FILENAME']);
	} elseif (isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['PHP_SELF']) && !empty($_SERVER['PHP_SELF'])) {
		$path = dirname($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']);
	} else {
		$path = '..';
	}
	echo"<br><hr>\n";
	echo "<h2>".$language['tagforspam']."</h2>";
	echo "<div class=\"zonehtaccess2\">";	
	echo"<table><tr><td align='left' style=\"padding:20px;\">";	
	echo "<p>".$language['paramhtaccesstext24']."</p>";	
	echo "<div class=\"displaytag2\"><b>require_once(\"".$path."/include/cppf.php\");</b></div>";
	echo"</td></tr></table><br></div>";
	}
else
	{
	echo"<br><hr>\n";
	echo "<h2>".$language['tagforspam']."</h2>";
	echo "<div class=\"zonehtaccess2\">";	
	echo"<table><tr><td align='left' style=\"padding:20px;\">";	
	echo "<p>".$language['paramhtaccesstext21']."</p>";	
	echo"</td></tr></table><br></div>";			
	}


echo"<br><hr>\n";
echo "<h2>".$language['parametersused']."</h2>";
echo"<div align='center'><table width=\"970px\"><tr><td align='center'>\n";
echo "<h2>".$language['trustip2']."</h2>";
echo"<div class='listip'>";
foreach ($listtrustip as $value)
	{
	echo $value."<br>";
	}
echo"</div>";
echo"</td><td align='center'>\n";
echo "<h2>".$language['scripts_used2']."</h2>";
//check referer already blocked
echo"<div class='listip'>";
foreach ($listscriptsused as $value)
	{
	echo $value."<br>";
	}
echo"</div>";
echo"</td></tr><tr><td align='center'>\n";

echo "<h2>".$language['trustsite']."</h2>";
echo"<div class='listip'>";
foreach ($listtrustsites as $value)
	{
	if($value !='www.example.com')
		{
		echo $value."<br>";
		}
	}
echo"</div>";
echo"</td><td align='center'>\n";
echo "<h2>".$language['trustvariable']."</h2>";
//check referer already blocked
echo"<div class='listip'>";
foreach ($listtrustvariable as $value)
	{
	if($value !='variable-example')
		{
		echo $value."<br>";
		}
	}
echo"</div>";
echo"</td></tr><tr><td align='center'>\n";
echo "<h2>".$language['ipblocked']."</h2>";
echo"<div class='listip'>";
foreach ($listforbiddenip as $value)
	{
	echo $value."<br>";
	}
echo"</div>";
echo"</td><td align='center'>\n";
echo "<h2>".$language['refererblocked']."</h2>";
//check referer already blocked
echo"<div class='listip'>";
foreach ($listforbiddenreferer as $value)
	{
	echo $value."<br>";
	}
echo"</div>";
echo"</td></tr><tr><td align='center'>\n";
echo "<h2>".$language['urlforbidden']."</h2>";
//check Url already blocked
echo"<div class='listip'>";
foreach ($listforbiddenurl as $value)
	{
	echo $value."<br>";
	}
echo"</div>";
echo"</td><td align='center'>\n";
echo "<h2>".$language['parameterforbidden']."</h2>";
//check Parameter already blocked
echo"<div class='listip'>";
foreach ($listforbiddenparameter as $value)
	{
	echo $value."<br>";
	}
echo"</div>";
echo"</td></tr><tr><td align='center'>\n";
echo "<h2>".$language['wordforbidden']."</h2>";
//check Url already blocked
echo"<div class='listip'>";
foreach ($listforbiddenword as $value)
	{
	echo $value."<br>";
	}
echo"</div>";
echo"</td><td align='center'>\n";
echo "<h2>".$language['hotlinking']."</h2>";
//check Parameter already blocked
echo"<div class='listip'>";
if($hotlink==1)
	{
	if(count($listhotlinkok) > 0)
		{
		echo $language['hotlinkingok'];
		echo "<br><br>".$language['trustsite'].":<br>";
		foreach ($listhotlinkok as $value)
			{
			echo $value."<br>";
			}
		}
	else
		{
		echo "<br><br>".$language['hotlinkingok'];	
		}			
	}
else
	{
	echo "<br><br>".$language['hotlinkingnook'];	
	}	
echo"</div>";
echo"</td></tr><tr><td colspan='2'  align='center'>\n";

echo "<h2>".$language['actualhtaccess2']."</h2>\n";

echo"<div class='htaccess'><pre>";
echo $htaccessdisplay;
echo"</pre></div>\n";


echo"</td></tr></table>\n";
echo"<br><br><br><br>\n";

?>
