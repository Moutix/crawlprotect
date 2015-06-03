<?php
//----------------------------------------------------------------------
//  CrawlProtect 3.2.4
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
// file: paramhtaccess.php
//----------------------------------------------------------------------
//  Last update: 28/09/2013
//----------------------------------------------------------------------
if (!defined('IN_CRAWLT'))
	{
	echo"<h1>Hacking attempt !!!!</h1>";
	exit();
	}
unset($_SESSION['actualhtaccess']);	
//variables init	
$listUAautorise=array();
//collect htaccess existing parameter
$sql = "SELECT * FROM crawlp_site_setting WHERE id_site= '" . sql_quote($site) . "'";
$requete = mysql_query($sql, $connexion);
$nbrresult=mysql_num_rows($requete);
if ($nbrresult >= 1) {
	while ($ligne = mysql_fetch_object($requete)) {
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
		$actual = $ligne->actualhtaccess;
		$hotlinkok = $ligne->hotlinkok;
		$listindex = $ligne->listindex;
		$badfile = 	$ligne->badfile;
		$url_image= $ligne->url_image;				
	}
}
else
	{
		$scriptsused = '';
		$yourip = '0';
		$blockUA = '1';				
		$hotlink = '0';
		$shellsetting = 'all';
		$sqlsetting = 'all';
		$trustsites = 'www.example.com';		
		$trustip = '';		
		$trustuseragent = '';
		$trustvariable = 'variable-example';
		$forbiddenip = '';				
		$forbiddenurl = '';
		$forbiddenparameter = '';
		$forbiddenreferer = '';
		$forbiddenword = '';
		$autoprepend = '1';		
		$actual = '';	
		$hotlinkok = 'example.com';
		$listindex='0';	
		$badfile='0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0';
		$url_image='';
	}
//case php not module Apache ==> autoprepend impossible
$sapi_type = php_sapi_name();
if(!preg_match("/apache/i", $sapi_type)) {	
$autoprepend = '0';	
$testautoprepend='0';
}
else {
$testautoprepend='1';	
}
//forbiddenip case error
if(isset($_POST['forbiddenip']))
	{
	$forbiddenip=$_POST['forbiddenip'];
	}

//collect IP to block from index.php page	
if(isset($_POST['addiptoblock']))
	{
	$addiptoblock = $_POST['addiptoblock'];
	}
else
	{
	$addiptoblock = '0';
	}
//treatment of list of IP to block
$iptoadd='';	
if($addiptoblock==1)
	{
	//get  values
		$sql = "SELECT DISTINCT ip FROM crawlp_stats";
		$requete = db_query($sql, $connexion);
		$nbrresult=@mysql_num_rows($requete);
		if($nbrresult>=1)
			{
			while ($ligne = mysql_fetch_row($requete))
				{
				$listbadip[]=$ligne[0];
				}	
			foreach($listbadip as  $value)
				{
				$key3=ip2long($value);
				if(isset($_POST[$key3]) && $_POST[$key3]==1)
					{
					$iptoadd=$iptoadd.",".$value;
					}
				}
			$forbiddenip=$forbiddenip.$iptoadd;
								
			}	
	}
$forbiddenip=trim($forbiddenip,',');
//collect actual htaccess
if($actual == '')
	{
	if(file_exists('../.htaccess') )
		{
		if(function_exists('fopen'))
			{
			$file = fopen("../.htaccess", "r");
			$actual = fread($file, filesize("../.htaccess"));
			fclose($file);
			}
		else
			{
			$actual = "#---".$language['htaccessnotreadible'];
			}
		}
	else
		{
		$actual = '';
		}
		//check if there is already a CrawlProtect part include in the actual htaccess
		if(preg_match("/CrawlProtect/i", $actual)) {	
		//remove CrawlProtect part	
		
			if(preg_match("/noaccess30/i", $actual))
				{
				$explodehtaccess = explode('noaccess30.php  [L]', $actual);
				$explodehtaccess2 = explode('# CrawlProtect', $explodehtaccess[0]);
				$actual = $explodehtaccess2[0].ltrim($explodehtaccess[1]);
				}
			elseif(preg_match("/File created by CrawlProtect/i", $actual))
				{
				if(preg_match("/# Existing File/i", $actual))
					{
					$explodehtaccess = explode('# Existing File', $actual);
					$explodehtaccess2 = explode('# File created by CrawlProtect', $explodehtaccess[0]);
					$actual = $explodehtaccess2[0].ltrim($explodehtaccess[1]);
					}
				else
					{
					$actual='';	
					}	
				}		
			else
				{
				$explodehtaccess = explode('noaccess3.php  [L]', $actual);
				$explodehtaccess2 = explode('# CrawlProtect', $explodehtaccess[0]);
				$actual = $explodehtaccess2[0].ltrim($explodehtaccess[1]);
				}
		}	
				
	}

//check if removing of existing part is OK
if(preg_match("/CrawlProtect/", $actual)) {
$alreadycrawlprotect = '1';	
} else {
$alreadycrawlprotect = '0';	
}	
	
//collect list bad UA
$sql = "SELECT id_UA, UA FROM crawlp_uablock";
$requete = mysql_query($sql, $connexion);
$nbrresult=mysql_num_rows($requete);
$i=0;
if($nbrresult>=1)
	{
	while ($ligne = mysql_fetch_row($requete))
		{
		$baduseragent[($ligne[0]-1)]=$ligne[1];
		}
	}
else
	{
	$baduseragent=array();
	}
//build trust UA array
if($trustuseragent !='')
	{
	$tabUA = explode(',',$trustuseragent);
	foreach($tabUA as $key=>$value)
		{
		if($value==0)
			{
			$listUAautorise[]=$key;
			}
			
		}
		
	//temporary fix, need to see how to better manage UA list (size of column in the table)	
	$listUAautorise[]=129;	
		
					
	}	
//collect list scripts	
$sql = "SELECT DISTINCT name FROM crawlp_scripts";
$requete = mysql_query($sql, $connexion);
$nbrresult=mysql_num_rows($requete);
$i=0;
if($nbrresult>=1)
	{
	while ($ligne = mysql_fetch_row($requete))
		{
		$listscript[]=$ligne[0];
		}
	}
else
	{
	$listscript=array();
	}
//build scripts used array
if($scriptsused !='')
	{
	$listscriptsused = explode(',',$scriptsused);		
	}
else
	{
	$listscriptsused=array();
	}		
//own IP
if(isset($_POST['trustip']))
	{
	$trustip=$_POST['trustip'];
	}
elseif($trustip=='')
	{	
	$trustip=$_SERVER['REMOTE_ADDR'];
	}
		
//collect sql parameters
if($sqlsetting=='all')
	{
	for($i=1; $i<=7; $i++)
		{
		${'sq'.$i}=1;	
		}
	}
else
	{
	$tabsql=explode(',',$sqlsetting);
	for($i=1; $i<=7; $i++)
		{
		$j=$i-1;
		${'sq'.$i}=$tabsql[$j];			
		}		
		
	}
//collect shell parameters
if($shellsetting=='all')
	{
	for($i=1; $i<=28; $i++)
		{
		${'s'.$i}=1;	
		}
	}
else
	{
	$tabshell=explode(',',$shellsetting);
	for($i=1; $i<=28; $i++)
		{
		$j=$i-1;
		${'s'.$i}=$tabshell[$j];	
		}		
		
	}
//collect bad file parameters
$tabbf=explode(',',$badfile);
for($i=1; $i<=17; $i++)
	{
	$j=$i-1;
	${'bf'.$i}=$tabbf[$j];			
	}	
//----------------------------------------------------
//Display
	
//include menu
include ("include/menusite.php");
include ("include/menumain.php");	
echo"<div align=\"center\" style=\"padding:10px;\"><br>\n";
echo "<h1>".$language['paramhtaccess']."</h1><br>\n";
echo "<p>".$language['paramhtaccesstext1']."</p>\n";
echo "<div class=\"zonehtaccess\"><hr>\n";
echo "<h2>".$language['protectantihacking']."</h2>\n";
echo "<p>".$language['paramhtaccesstext2']."</p>\n";
echo"<table><tr><td align='left' style=\"padding-left:20px;\">\n";
echo"<form action=\"http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."\" method=\"POST\">\n";
echo"<input type=\"hidden\" name ='navig' value='4'>\n";
echo"<input type=\"hidden\" name ='logok' value='1'>\n";
echo "<p><b>".$language['trustip2']."</b></p>\n";
echo"<input type=\"radio\" name=\"yourip\" value=\"1\"";
if($yourip==1)
	{
	echo "checked";
	}
echo">".$language['yesip']."<br>\n";
echo "<p>".$language['yesip2']."</p>\n";
echo $language['trustip']." <input name='trustip'  value='".$trustip."' type='text' maxlength='70' size='70'/><br><br>\n";
echo"<input type=\"radio\" name=\"yourip\" value=\"0\"";
if($yourip==0)
	{
	echo "checked";
	}
echo ">".$language['noip']."<br><br>\n";

echo "<br><b>".$language['scripts_used']."</b>\n";
echo "<p>".$language['paramhtaccesstext20']."</p>\n";
$i=0;
echo"<table width=\"100%\"><tr>\n";
usort($listscript, "strcasecmp");
foreach ($listscript as $key => $value)
	{
	$namevalue =str_replace(' ','--',$value);			
	echo"<td><input type=\"checkbox\" name='".$namevalue."' value=\"1\"";
	if( in_array($value , $listscriptsused))
		{
		echo"checked";
		} 
 	echo">".$value."</td>\n";
	$i++;
	if($i % 4 == 0)
		{
		echo"</tr><tr>\n";	
		}	
	}
echo"</tr></table></div><br><br>\n";

echo "<p><b>".$language['nocodeinjection']."</b></p>\n";
echo "<p>".$language['blockurl']."</p>\n";	
echo "<p>".$language['paramhtaccesstext3']."</p>\n";
echo"<TEXTAREA name='trustsites' rows='4' cols=115>".$trustsites."</TEXTAREA><br><br>\n";
echo "<p><br>".$language['paramhtaccesstext4']."</p>\n";
echo"<TEXTAREA name='trustvariable' rows='4' cols=115>".$trustvariable."</TEXTAREA><br><br>\n";
echo "<p><b>".$language['nosqlinjection']."</b></p>\n";
echo "<p>".$language['blockvariable']."</p>\n";
echo "<table><tr><td>\n";
echo "<input type=\"checkbox\" name=\"sq1\" value=\"1\" ";
if($sq1==1)
	{
	echo "checked";
	}
echo ">select</td><td>\n";
echo "<input type=\"checkbox\" name=\"sq2\" value=\"1\"";
if($sq2==1)
	{
	echo "checked";
	}
echo ">insert</td><td>\n";
echo "<input type=\"checkbox\" name=\"sq3\" value=\"1\"";
if($sq3==1)
	{
	echo "checked";
	}
echo ">update</td><td>\n";
echo "<input type=\"checkbox\" name=\"sq4\" value=\"1\"";
if($sq4==1)
	{
	echo "checked";
	}
echo ">replace</td><td>\n";
echo "<input type=\"checkbox\" name=\"sq5\" value=\"1\"";
if($sq5==1)
	{
	echo "checked";
	}
echo ">where</td><td>\n";
echo "<input type=\"checkbox\" name=\"sq6\" value=\"1\"";
if($sq6==1)
	{
	echo "checked";
	}
echo ">like</td><td>\n";
echo "<input type=\"checkbox\" name=\"sq7\" value=\"1\"";
if($sq7==1)
	{
	echo "checked";
	}
echo ">or</td></tr></table>\n";
echo "<p>".$language['paramhtaccesstext5']."</p>\n";
echo "<p><b>".$language['noshell']."</b></p>\n";
echo "<p>".$language['blockvariable']."</p>\n";
echo "<table width=\"98%\">\n";
echo "<tr><td><input type=\"checkbox\" name=\"s1\" value=\"1\"";
if($s1==1)
	{
	echo "checked";
	}
echo ">chmod</td><td>\n";
echo "<input type=\"checkbox\" name=\"s2\" value=\"1\"";
if($s2==1)
	{
	echo "checked";
	}
echo ">chdir</td><td>\n";
echo "<input type=\"checkbox\" name=\"s3\" value=\"1\"";
if($s3==1)
	{
	echo "checked";
	}
echo ">mkdir</td><td>\n";
echo "<input type=\"checkbox\" name=\"s4\" value=\"1\"";
if($s4==1)
	{
	echo "checked";
	}
echo ">rmdir</td><td>\n";
echo "<input type=\"checkbox\" name=\"s5\" value=\"1\"";
if($s5==1)
	{
	echo "checked";
	}
echo ">clear</td><td>\n";
echo "<input type=\"checkbox\" name=\"s6\" value=\"1\"";
if($s6==1)
	{
	echo "checked";
	}
echo ">whoami</td></tr>\n";
echo "<tr><td><input type=\"checkbox\" name=\"s7\" value=\"1\"";
if($s7==1)
	{
	echo "checked";
	}
echo ">uname</td><td>\n";
echo "<input type=\"checkbox\" name=\"s8\" value=\"1\"";
if($s8==1)
	{
	echo "checked";
	}
echo ">unzip</td><td>\n";
echo "<input type=\"checkbox\" name=\"s9\" value=\"1\"";
if($s9==1)
	{
	echo "checked";
	}
echo ">gzip</td><td>\n";
echo "<input type=\"checkbox\" name=\"s10\" value=\"1\"";
if($s10==1)
	{
	echo "checked";
	}
echo ">gunzip</td><td>\n";
echo "<input type=\"checkbox\" name=\"s11\" value=\"1\"";
if($s11==1)
	{
	echo "checked";
	}
echo ">grep</td><td>\n";
echo "<input type=\"checkbox\" name=\"s12\" value=\"1\"";
if($s12==1)
	{
	echo "checked";
	}
echo ">umask</td></tr>\n";

echo "<tr><td><input type=\"checkbox\" name=\"s13\" value=\"1\"";
if($s13==1)
	{
	echo "checked";
	}
echo ">telnet</td><td>\n";
echo "<input type=\"checkbox\" name=\"s14\" value=\"1\"";
if($s14==1)
	{
	echo "checked";
	}
echo ">ssh</td><td>\n";
echo "<input type=\"checkbox\" name=\"s15\" value=\"1\"";
if($s15==1)
	{
	echo "checked";
	}
echo ">ftp</td><td>\n";
echo "<input type=\"checkbox\" name=\"s16\" value=\"1\"";
if($s16==1)
	{
	echo "checked";
	}
echo ">mkmode</td><td>\n";
echo "<input type=\"checkbox\" name=\"s17\" value=\"1\"";
if($s17==1)
	{
	echo "checked";
	}
echo ">logname</td><td>\n";
echo "<input type=\"checkbox\" name=\"s18\" value=\"1\"";
if($s18==1)
	{
	echo "checked";
	}
echo ">edit_file</td></tr>\n";
echo "<tr><td><input type=\"checkbox\" name=\"s19\" value=\"1\"";
if($s19==1)
	{
	echo "checked";
	}
echo ">search_text</td><td>\n";
echo "<input type=\"checkbox\" name=\"s20\" value=\"1\"";
if($s20==1)
	{
	echo "checked";
	}
echo ">find_text</td><td>\n";
echo "<input type=\"checkbox\" name=\"s21\" value=\"1\"";
if($s21==1)
	{
	echo "checked";
	}
echo ">php_eval</td><td>\n";
echo "<input type=\"checkbox\" name=\"s22\" value=\"1\"";
if($s22==1)
	{
	echo "checked";
	}
echo ">download_file</td><td>\n";
echo "<input type=\"checkbox\" name=\"s23\" value=\"1\"";
if($s23==1)
	{
	echo "checked";
	}
echo ">ftp_file_down</td><td>\n";
echo "<input type=\"checkbox\" name=\"s24\" value=\"1\"";
if($s24==1)
	{
	echo "checked";
	}
echo ">ftp_file_up</td></tr>\n";
echo "<tr><td><input type=\"checkbox\" name=\"s25\" value=\"1\"";
if($s25==1)
	{
	echo "checked";
	}
echo ">ftp_brute</td><td>\n";
echo "<input type=\"checkbox\" name=\"s26\" value=\"1\"";
if($s26==1)
	{
	echo "checked";
	}
echo ">mail_file</td><td>\n";
echo "<input type=\"checkbox\" name=\"s27\" value=\"1\"";
if($s27==1)
	{
	echo "checked";
	}
echo ">mysql_dump</td><td>\n";
echo "<input type=\"checkbox\" name=\"s28\" value=\"1\"";
if($s28==1)
	{
	echo "checked";
	}
echo ">db_query</td><td>\n";
echo "</td><td>";
echo "</td></tr>";
echo "</table>\n";
echo "<p>".$language['paramhtaccesstext6']."</p>\n";
echo "<br><b>".$language['ipblock']."</b>\n";
echo "<p>".$language['ipblacklist']."</p>\n";
echo"<TEXTAREA name='forbiddenip' rows='4' cols=115>".$forbiddenip."</TEXTAREA><br><br>\n";
if($addiptoblock==1)
	{
	echo "<table width='850px'><tr><td class='black'>".$language['badipadd']."<br><span class='red'>".str_replace(",","&nbsp;    &nbsp;&nbsp;",ltrim($iptoadd,","))."</span></td></tr></table>";
	}
else
	{
	echo "<br>";
	}
echo "<br><b>".$language['forbiddenurl']."</b><br>\n";
echo "<br>".$language['forbiddenurllist']."<br><br>\n";
echo"<TEXTAREA name='forbiddenurl' rows='4' cols=115>".$forbiddenurl."</TEXTAREA><br>\n";
echo "<br><b>".$language['forbiddenparameter']."</b><br>\n";
echo "<br>".$language['forbiddenparameterlist']."<br><br>\n";
echo"<TEXTAREA name='forbiddenparameter' rows='4' cols=115>".$forbiddenparameter."</TEXTAREA><br>\n";
echo "<p>".$language['paramhtaccesstext7']."</p></div>\n";
echo"</td></tr></table><br>\n";
//------------------------------------------------------------------------------------------------------------------
echo "<div class=\"zonehtaccess2\"><hr>\n";
echo"<table><tr><td align='left' style=\"padding-left:20px;\">\n";
echo "<h2>".$language['nocontentstolen']."</h2>\n";
echo "<p>".$language['paramhtaccesstext8']."</p>\n";
echo "<br><b>".$language['nobadbot']."</b><br>\n";
echo "<br>".$language['paramhtaccesstext9']."<br><br>\n";
$i=0;
echo"<div class='tablehauteurfixe'>\n";
echo"<table width=\"100%\"><tr>\n";
asort($baduseragent);
foreach ($baduseragent as $key => $value)
	{		
	$nameUA = "UA-".$key;
	
	if($key !=129 && $key !=293)  //to suppress LWP of the list & JCE bot
		{	
		echo"<td><input type=\"checkbox\" name=\"".$nameUA."\" value=\"1\"";
		if( !in_array($key , $listUAautorise))
			{
			echo"checked";
			} 
	 	echo">".stripslashes($value)."</td>\n";
		$i++;
		if($i % 5 == 0)
			{
			echo"</tr><tr>\n";	
			}
		
		}
	else
		{
		echo "<input type=\"hidden\" name ='UA-129' value='0'>\n";	
		}
		
	}
echo"</tr></table></div><br><br>\n";	
echo"<input type=\"radio\" name=\"blockUA\" value=\"1\"";
if($blockUA==1)
	{
	echo "checked";
	}
echo ">".$language['paramhtaccesstext10']."<br>\n";
echo"<input type=\"radio\" name=\"blockUA\" value=\"0\"";
if($blockUA==0)
	{
	echo "checked";
	}
echo ">".$language['paramhtaccesstext11']."<br>\n";
echo"<p>".$language['paramhtaccesstext12']."</p>\n";
echo "<br><b>".$language['nohotlinking']."</b><br><br>\n";
echo"<input type=\"radio\" name=\"hotlink\" value=\"1\"";
if($hotlink==1)
	{
	echo "checked";
	}
echo ">".$language['paramhtaccesstext13']."<br>\n";
echo"<input type=\"radio\" name=\"hotlink\" value=\"0\"";
if($hotlink==0)
	{
	echo "checked";
	}
echo ">".$language['paramhtaccesstext14']."<br>\n";


echo "<br>".$language['paramhtaccesstext26']."<br><br>\n";
echo"<TEXTAREA name='hotlinkok' rows='4' cols=115>".$hotlinkok."</TEXTAREA><br>\n";


echo "<br>".$language['alternatimage']."<br><br>\n";
echo"<TEXTAREA name='url_image' rows='4' cols=115>".$url_image."</TEXTAREA><br>\n";


echo"</td></tr></table><br></div>\n";
//------------------------------------------------------------------------------------------------------------------
echo "<div class=\"zonehtaccess2\"><hr>\n";
echo"<table><tr><td align='left' style=\"padding-left:20px;\">\n";
echo "<h2>".$language['nospammer']."</h2>\n";
echo "<br><b>".$language['blockword']."</b><br><br>\n";
echo "<p>".$language['paramhtaccesstext18']."</p>\n";
echo "<p>".$language['paramhtaccesstext19']."</p>\n";
echo"<TEXTAREA name='forbiddenword' rows='4' cols=115>".$forbiddenword."</TEXTAREA><br><br>\n";
echo "<input type=\"hidden\" name ='validkey' value='".$secret_key."'>\n";
echo"<input type=\"radio\" name=\"autoprepend\" value=\"1\"";
if($autoprepend==1)
	{
	echo "checked";
	}
echo ">".$language['paramhtaccesstext21']."<br>\n";
echo"<input type=\"radio\" name=\"autoprepend\" value=\"0\"";
if($autoprepend==0)
	{
	echo "checked";
	}
echo ">".$language['paramhtaccesstext22']."<br>\n";
if($testautoprepend==0) {
echo "<div class='alert2'>".$language['paramhtaccesstext23']."</div>\n";
}
echo "<br><b>".$language['blockreferer']."</b><br><br>\n";
echo "<p>".$language['paramhtaccesstext15']."</p>\n";
echo "<p>".$language['paramhtaccesstext16']."</p>\n";
echo"<TEXTAREA name='forbiddenreferer' rows='4' cols=115>".$forbiddenreferer."</TEXTAREA><br><br>\n";
echo"</td></tr></table><br></div>\n";
//------------------------------------------------------------------------------------------------------------------
echo "<hr><h2>".$language['others']."</h2>\n";
echo "<div align='left'>\n";
echo"<table><tr><td align='left' style=\"padding-left:20px;\">\n";


echo "<br><b>".$language['listindex']."</b><br><br>\n";
echo"<input type=\"radio\" name=\"listindex\" value=\"0\"";
if($listindex==0)
	{
	echo "checked";
	}
echo">".$language['listindexnook']."<br>\n";
echo"<input type=\"radio\" name=\"listindex\" value=\"1\"";
if($listindex==1)
	{
	echo "checked";
	}
echo">".$language['listindexok']."<br>\n";




echo "<br><br><b>".$language['badfileaccess']."</b><br><br>\n";
echo "<p>".$language['listbadfile']."</p>\n";
echo "<table><tr><td>\n";
echo "<input type=\"checkbox\" name=\"bf1\" value=\"1\" ";
if($bf1==1)
	{
	echo "checked";
	}
echo ">inc</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf2\" value=\"1\"";
if($bf2==1)
	{
	echo "checked";
	}
echo ">class</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf3\" value=\"1\"";
if($bf3==1)
	{
	echo "checked";
	}
echo ">sql</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf4\" value=\"1\"";
if($bf4==1)
	{
	echo "checked";
	}
echo ">ini</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf5\" value=\"1\"";
if($bf5==1)
	{
	echo "checked";
	}
echo ">conf</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf6\" value=\"1\"";
if($bf6==1)
	{
	echo "checked";
	}
echo ">exe</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf7\" value=\"1\"";
if($bf7==1)
	{
	echo "checked";
	}
echo ">dll</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf8\" value=\"1\"";
if($bf8==1)
	{
	echo "checked";
	}
echo ">bin</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf9\" value=\"1\"";
if($bf9==1)
	{
	echo "checked";
	}
echo ">tpl</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf10\" value=\"1\"";
if($bf10==1)
	{
	echo "checked";
	}
echo ">bkp</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf11\" value=\"1\"";
if($bf11==1)
	{
	echo "checked";
	}
echo ">dat</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf12\" value=\"1\"";
if($bf12==1)
	{
	echo "checked";
	}
echo ">c</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf13\" value=\"1\"";
if($bf13==1)
	{
	echo "checked";
	}
echo ">h</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf14\" value=\"1\"";
if($bf14==1)
	{
	echo "checked";
	}
echo ">py</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf15\" value=\"1\"";
if($bf15==1)
	{
	echo "checked";
	}
echo ">spd</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf16\" value=\"1\"";
if($bf16==1)
	{
	echo "checked";
	}
echo ">theme</td><td>\n";
echo "<input type=\"checkbox\" name=\"bf17\" value=\"1\"";
if($bf17==1)
	{
	echo "checked";
	}
echo ">module</td></tr></table>\n";


echo"</td></tr></table><br></div>\n";
//------------------------------------------------------------------------------------------------------------------
echo "<div class=\"zonehtaccess2\"><hr>\n";
echo "<h2>".$language['actualhtaccess']."</h2>\n";
echo "<p>".$language['paramhtaccesstext17']."</p>\n";
if($alreadycrawlprotect==1)
	{
	echo "<p>".$language['paramhtaccesstext25']."</p>\n";	
	}
echo"<table><tr><td align='left' style=\"padding-left:20px;\">\n";
echo"<TEXTAREA name='actual' rows='10' cols=115>".$actual."</TEXTAREA><br><br>\n";
echo"</td></tr></table><br></div>\n";
echo "<input type=\"hidden\" name ='validparam' value='ok'>\n";
echo"<table><tr><td align='left' style=\"padding-left:20px;\">\n";
echo"<br><br><div align='center'><input name='ok' type='submit'  value='".$language['validhtaccess']."' size='60' ></div>\n";
echo"</form>&nbsp;</td></tr></table><br></div>\n";
?>

