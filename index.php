<?php
//----------------------------------------------------------------------
//  CrawlProtect 3.2.5b
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
// file: index.php
//----------------------------------------------------------------------
//  Last update: 08/11/2013
//----------------------------------------------------------------------

// make sure PHP version  >= 4.3.2 is used (and even this version is waaaay too old, 29-May-2003)
if (version_compare(PHP_VERSION, '4.3.2', '<')) exit("Sorry, CrawlProtect needs at least PHP version 4.3.2 to run ! You are running version " . PHP_VERSION . " \n");

error_reporting(0);
//initialize array & data
$listlangcrawlt = array();
$numbquery = 0;
//function to count the number of mysql query
function db_query($sql) {
	global $numbquery;
	$numbquery++;
	return mysql_query($sql);
}
//function to measure the time used for the calculation
function getTime() {
	static $timer = false, $start;
	if ($timer === false) {
		$start = array_sum(explode(' ', microtime()));
		$timer = true;
		return NULL;
	} else {
		$timer = false;
		$end = array_sum(explode(' ', microtime()));
		return round(($end - $start), 3);
	}
}
getTime();
//if already install get all the config datas
if (file_exists('include/connection.php') ) {
	//connection file include
	require_once ("include/connection.php");

		$connexion = mysql_connect($crawlthost, $crawltuser, $crawltpassword) or die("MySQL connection to database problem toto");
		$selection = mysql_select_db($crawltdb) or die("MySQL database selection problem");
		
		unset($crawltpassword);
		unset($crawltuser);
		$sqlconfig = "SELECT * FROM crawlp_general_setting";
		$requeteconfig = @db_query($sqlconfig, $connexion);
		$nbrresult = @mysql_num_rows($requeteconfig);
		if ($nbrresult >= 1) {
			$ligne = mysql_fetch_assoc($requeteconfig);
			$crawltlang = $ligne['language'];
			$version = $ligne['version'];			
		}
		else {
		if(isset($_POST['lang']))
			{
			$crawltlang=$_POST['lang'];
			}
		else
			{
			$crawltlang='english';	
			}
		$navig=15;			
		}
		
if(isset($_POST['changelang']))
	{
	$changelang = $_POST['changelang'];
	}
else
	{
	$changelang = '';
	}
			
if($changelang=='ok')
	{
	if(isset($_POST['lang2']))
		{
		$lang2 = $_POST['lang2'];
		}
	else
		{
		$lang2 = 2;
		}			
	switch($lang2)
		{
		case 1:
			$crawltlang='french';
		break;
		case 2:
			$crawltlang='english';
		break;
		case 3:
			$crawltlang='italian';
		break;
		case 4:
			$crawltlang='russian';
		break;	
		case 5:
			$crawltlang='swedish';
		break;		
		case 6:
			$crawltlang='spanish';
		break;	
							
		}
	}
}
else
	{
	if(isset($_POST['lang']))
		{
		$crawltlang=$_POST['lang'];
		}
	else
		{
		$crawltlang='english';	
		}
	} 
require_once ("include/post.php");
require_once ("include/listlang.php");
require_once ("include/functions.php");

// do not modify
define('IN_CRAWLT', TRUE);
//check if cppf.php file is up to date (to modify it in case of multi site installation)
if (file_exists('include/cppf.php') )
	{
	$cppf=0;
	include ("include/cppf.php");
	//if not display for manual update	
	if($cppf==0 || $cppf==1 || $cppf==2)
		{
		$version='324';
		}
	}
	
//language file include
if (file_exists("language/" . $crawltlang . ".php") && in_array($crawltlang, $listlangcrawlt)) {
	require_once ("language/" . $crawltlang . ".php");
} else {
	exit('<h1>No language files available !!!!</h1>');
}

//version id
$versionid = '325';
// session start 'crawlt'
if (!isset($_SESSION['flag']) && $navig !=15) {
	session_name('crawlt');
	session_start();
	$_SESSION['flag'] = true;
}
//if already install
if (file_exists('include/connection.php') && $navig != 15) {
	//check if we are on the right server for the demanded site if not redirect to the right one
	$sqlsite = "SELECT id_site FROM crawlp_site_setting WHERE INSTR('".$_SERVER['HTTP_HOST']."',url) > 0";
	$requetesite = mysql_query($sqlsite, $connexion);
	$nbrresult = mysql_num_rows($requetesite);
	if ($nbrresult >= 1) {
		$ligne = mysql_fetch_assoc($requetesite);
		$idsitehost = $ligne['id_site'];
		if(	$site != $idsitehost && ($navig==1||$navig==2||$navig==3||$navig==4))
			{
			$sql = "SELECT url_crawlprotect FROM crawlp_site_setting WHERE id_site='" . sql_quote($site) . "'";
			$requete = mysql_query($sql, $connexion);				
			$ligne = mysql_fetch_assoc($requete);	
			$redirecturl= "http://".$ligne['url_crawlprotect'];					
			header("Location: ".$redirecturl."?navig=$navig&site=$site");
			exit;	
			}						
	}
	if ($navig == 0) {
		$main = ("include/index.php");
	} elseif ($navig == 1) {
		$main = ("include/admin.php");
	} elseif ($navig == 2) {
		$main = ("include/chmod.php");
	} elseif ($navig == 3) {
		$main = ("include/paramhtaccess.php");
	} elseif ($navig == 4) {
		$main = ("include/createhtaccess.php");	
	} elseif ($navig == 7) {
		$main = ("include/index.htm"); // to avoid notice error in Apache logs
		session_destroy();
		header("Location:index.php");
		exit;		
	} else {
		$main = ("include/index.php");
	}
	//  IF NO SESSION LOGIN
	if (!isset($_SESSION['userlogin'])) {
			//get values
			if (isset($_POST['userlogin'])) {
				$userlogin = htmlentities($_POST['userlogin']);
			} else {
				$userlogin = '';
			}
			if (isset($_POST['userpass'])) {
				$userpass = htmlentities($_POST['userpass']);
			} else {
				$userpass = '';
			}
			//access form
			include ("include/header.php");
			echo "<div class=\"content\">\n";

			echo "<h1>" . $language['restrited_access'] . "</h1>\n";

			
			if ($nocookie==1) {
			echo "<div class=\"alert2\">".$language['no_cookie']."</div>\n";
			}
			
			
			echo "<h2>" . $language['enter_login'] . "</h2>\n";
			echo "<div class=\"form\">\n";
			echo "<form action=\"php/login.php\" method=\"POST\" name=\"login\" >\n";
			echo "<table align=\"left\" width=\"400px\">\n";
			echo "<tr>\n";
			echo "<td >" . $language['login'] . "&nbsp;<input name='userlogin' value='$userlogin' type='text' maxlength='20' size='20'/></td></tr>\n";
			echo "<tr><td></td></tr>\n";
			echo "<tr><td>" . $language['password'] . "&nbsp;<input name='userpass'  value='$userpass' type='password' size='20'/></td></tr>\n";	
			echo "<input type=\"hidden\" name ='navig' value=\"$navig\">\n";
			echo "<input type=\"hidden\" name ='site' value=\"$site\">\n";
			echo "<tr><td><input name='ok' type='submit'  value='OK' size='20'></td></tr>\n";
			echo "</table></form>\n";
			echo "<script type=\"text/javascript\"> document.forms[\"login\"].elements[\"userlogin\"].focus()</script>\n";
			echo "<br><br><br><br><br>\n";
			echo "<div align='center'><br><iframe name=\"I1\" src=\"http://www.crawltrack.net/news/relcp.php?r=".$versionid."&p=".PHP_VERSION."&l=".$crawltlang."\" marginwidth=\"0\" marginheight=\"0\" scrolling=\0\" frameborder=\"0\" width=\"400px\" height=\"20px\"></iframe></div><br><br>\n";
			echo "</div>\n";
			include ("include/footer.php");
		
	} else {

		//check token
		//Thanks to François Lasselin (http://blog.nalis.fr/index.php?post/2009/09/28/Securisation-stateless-PHP-avec-un-jeton-de-session-%28token%29-protection-CSRF-en-PHP)
		$validity_time = 1800;
		$token_clair= $secret_key.$_SERVER['HTTP_HOST'].$_SERVER['HTTP_USER_AGENT'];
		$token = hash('md5', $token_clair.$_COOKIE["session_informations"]);
		if(strcmp($_COOKIE["session_token"], $token)==0)
			{
			list($date, $user) = preg_split('[-]', $_COOKIE["session_informations"]);
			if($date+ $validity_time>time() AND $date <=time())
				{
					//test to see if version is up-to-date
					if (!isset($version)) {
						$version = 100;
					}
					if ($version == $versionid) {
						//installation is up-to-date, display stats
						include ("include/header.php");
						include ("$main");
						include ("include/footer.php");
					} else {
						//update the installation
						include ("include/header.php");
						include ("include/updatecrawlprotect.php");
						include ("include/footer.php");
					}
				}
			else
				{
					
				unset($_SESSION['userlogin']);
				$crawlencode = urlencode($crawler);
				header("Location: index.php?navig=$navig&site=$site");
				exit;
				}
			}
		else
			{
			unset($_SESSION['userlogin']);
			$crawlencode = urlencode($crawler);
			header("Location: index.php?navig=$navig&site=$site");
			exit;
			}
	}
} else {
	//display install
	$navig = '';
	include ("include/header.php");
	include ("include/install.php");
	include ("include/footer.php");
}

echo "<div class='smalltextgrey'>" . $numbquery . " mysql query           " . getTime() . " s</div>";
echo "</body>\n";
echo "</html>\n";
mysql_close($connexion);
?>
