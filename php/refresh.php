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
// file: refresh.php
//----------------------------------------------------------------------
//  Last update: 28/09/2013
//----------------------------------------------------------------------
error_reporting(0);


//get url data
if (isset($_GET['navig'])) {
	$navig = (int)$_GET['navig'];
} else {
	exit('<h1>Hacking attempt !!!!</h1>');
}

if (isset($_GET['site'])) {
	$site = (int)$_GET['site'];
} else {
	exit('<h1>Hacking attempt !!!!</h1>');
}
session_name('crawlt');
session_start();
unset($_SESSION['veriftag']);
unset($_SESSION['verif']);
unset($_SESSION['filelist']);
unset($_SESSION['dirlist']);

//call back the page
$urlrefresh = "../index.php?navig=$navig&site=$site";
header("Location: $urlrefresh");
exit;
