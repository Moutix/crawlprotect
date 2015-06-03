<?php
//----------------------------------------------------------------------
//  CrawlProtect 3.1.0
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
// file: login.php
//----------------------------------------------------------------------
//  Last update: 14/10/2012
//----------------------------------------------------------------------
error_reporting(0);
//database connection
include ("../include/connection.php");
include ("../include/post.php");
$crawlencode = urlencode($crawler);
//get the functions files
$times = 0;
include ("../include/functions.php");

$connexion = mysql_connect($crawlthost, $crawltuser, $crawltpassword) or die("MySQL connection to database problem tata");
$selection = mysql_select_db($crawltdb) or die("MySQL database selection problem");


//get values
if (isset($_POST['userlogin'])) {
	$userlogin = $_POST['userlogin'];
} else {
	$userlogin = '';
}
if (isset($_POST['userpass'])) {
	$userpass = $_POST['userpass'];
} else {
	$userpass = '';
}


//mysql query
$sqllogin = "SELECT * FROM crawlp_login";
$requetelogin = mysql_query($sqllogin, $connexion) or die("MySQL query error");

//mysql connexion close
mysql_close($connexion);
$validuser = 0;
$userpass2 = md5($userpass);
while ($ligne = mysql_fetch_object($requetelogin)) {
	$user = $ligne->crawlp_user;
	$passw = $ligne->crawlp_password;
	if ($user == $userlogin && $passw == $userpass2) {
		$validuser = 1;
	}
}

if ($validuser == 1) {
	// session start 'crawlt'
	if (!isset($_SESSION['flag'])) {
		session_name('crawlt');
		session_start();
		$_SESSION['flag'] = true;
	}

//create token
//Thanks to François Lasselin (http://blog.nalis.fr/index.php?post/2009/09/28/Securisation-stateless-PHP-avec-un-jeton-de-session-%28token%29-protection-CSRF-en-PHP)
$validity_time = 1800;
$token_clair=$secret_key.$_SERVER['HTTP_HOST'].$_SERVER['HTTP_USER_AGENT'];
$informations=time()."-".$user;
$token = hash('md5', $token_clair.$informations);
setcookie("session_token", $token, time()+$validity_time,'/');
setcookie("session_informations", $informations, time()+$validity_time,'/');

// we define session variables
$_SESSION['cookie'] = 1;
$_SESSION['userlogin'] = $userlogin;
$_SESSION['rightspamreferer'] = 1;
if (!isset($_SESSION['clearcache'])) {
	$_SESSION['clearcache'] = "0";
}

header("Location: ../index.php?navig=$navig&site=$site&nocookie=1");
exit;

} else {
	header("Location: ../index.php?navig=$navig&site=$site");
	exit;
}
?>
