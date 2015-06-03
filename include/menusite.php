<?php
//----------------------------------------------------------------------
//  CrawlProtect 3.0.0
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
// file: menusite.php
//----------------------------------------------------------------------
//  Last update: 19/02/2011
//----------------------------------------------------------------------
if (!defined('IN_CRAWLT')) {
	exit('<h1>Hacking attempt !!!!</h1>');
}

//initialize array
$listsites = array();
$urlsite = array();
$listidsite = array();
$nbrpagestotal = 0;

	//mysql query
	$sqlsite = "SELECT * FROM crawlp_site_setting";
	$requetesite = db_query($sqlsite, $connexion);
	$nbrresult = mysql_num_rows($requetesite);
	
	if ($nbrresult >= 1) {
		while ($ligne = mysql_fetch_object($requetesite)) {
			$sitename = $ligne->name;
			$siteurl = $ligne->url;
			$siteid = $ligne->id_site;
			$crawlprotecturl2 = "http://".$ligne->url_crawlprotect;
			$listsites[$siteid] = $sitename;
			$urlsite[$siteid] = $siteurl;
			$listsiteid[] = $siteid;
			$urlcrawlprotect[$siteid] = $crawlprotecturl2;
		}
		//case site 1 not in the base
		if (!in_array($site, $listsiteid)) {
			$site = min($listsiteid);
		}
		$sitename2 = $listsites[$site];
		
		//to avoid problem if the url is entered in the database with http://
		if (!preg_match('#^http://#i', $urlsite[$site])) {
			$hostsite = "http://" . $urlsite[$site];
		} else {
			$hostsite = $urlsite[$site];
		}
		$crawlprotecturl=$urlcrawlprotect[$site];
		//display
		echo "<div class=\"menusite\" align=\"center\">\n";
		echo "<form action=\"index.php\" method=\"POST\">\n";		
		echo "<input type=\"hidden\" name ='navig' value=\"$navig\">\n";
		echo "<input type=\"hidden\" name ='validform' value=\"$validform\">\n";
		echo "<select onchange=\"form.submit()\" size=\"1\" name=\"site\"  style=\" font-size:13px; font-weight:bold; color: #003399;
		font-family: Verdana,Geneva, Arial, Helvetica, Sans-Serif; \">\n";
		
		asort($listsites);
		foreach ($listsites as $id => $sitename3) {
			if ($id == $site ) {
				echo "<option value=\"$id\" selected style=\" font-size:13px; font-weight:bold; color: #003399;
				font-family: Verdana,Geneva, Arial, Helvetica, Sans-Serif;\">" . $sitename3 . "</option>\n";
				} else {
				echo "<option value=\"$id\" style=\" font-size:13px; font-weight:bold; color: #003399;
				font-family: Verdana,Geneva, Arial, Helvetica, Sans-Serif;\">" . $sitename3 . "</option>\n";
			}
		}

		echo "</select></form></div>\n";
	}


?>
