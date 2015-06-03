<?php
//----------------------------------------------------------------------
//  CrawlProtect 3.2.4
//----------------------------------------------------------------------
// Protect your website from hackers
//----------------------------------------------------------------------
// Author: Jean-Denis Brun
//----------------------------------------------------------------------
// Website: www.crawltrack.net/crawlprotect/
//----------------------------------------------------------------------
// That script is distributed under GNU GPL license
//----------------------------------------------------------------------
// file: noaccess/index.php
//----------------------------------------------------------------------
//  Last update: 28/09/2013
//----------------------------------------------------------------------
error_reporting(0);
include "../include/connection.php";
include "../include/functions.php";	
$connexion = @mysql_connect($crawlthost,$crawltuser,$crawltpassword);
$selection = @mysql_select_db($crawltdb);
unset($crawltpassword);
unset($crawltuser);
//get site value
if (isset($_GET['crawlprotecttype']))
	{
	$type = htmlspecialchars($_GET['crawlprotecttype']);
	}

	
if($type=='spammer')
	{
	//get site id
	$sqlsite = "SELECT id_site FROM crawlp_site_setting WHERE INSTR('".$_SERVER['HTTP_HOST']."',url) > 0";
	$requetesite = mysql_query($sqlsite, $connexion);
	$nbrresult = mysql_num_rows($requetesite);
	if ($nbrresult >= 1)
		{
		while ($ligne = mysql_fetch_object($requetesite))
			{
			$site = $ligne->id_site;
			}
		}
	//get content value
	if (isset($_GET['crawlprotectcontent']))
		{
		$content = htmlspecialchars($_GET['crawlprotectcontent']);
		}
	//get content value
	if (isset($_GET['crawlprotecturl']))
		{
		$url = htmlspecialchars($_GET['crawlprotecturl']);
		}
	$content=$content."   ".$url;										
	}	
elseif($type=='spamreferer')
	{
	//get site value
	if (isset($_GET['crawlprotectsite']))
		{
		$site = (int)$_GET['crawlprotectsite'];
		}
	else
		{
		$sqlsite = "SELECT id_site FROM crawlp_site_setting WHERE INSTR('".$_SERVER['HTTP_HOST']."',url) > 0";
		$requetesite = mysql_query($sqlsite, $connexion);
		$nbrresult = mysql_num_rows($requetesite);
		if ($nbrresult >= 1)
			{
			while ($ligne = mysql_fetch_object($requetesite))
				{
				$site = $ligne->id_site;
				}
			}
		}			
	//get url value
	if (isset($_GET['crawlprotecturl']))
		{
		$url = htmlspecialchars($_GET['crawlprotecturl']);
		}
	$content =$url;				
	}	
else
	{
	//get site value
	if (isset($_GET['crawlprotectsite']))
		{
		$site = (int)$_GET['crawlprotectsite'];
		$sqlsite = "SELECT url_image FROM crawlp_site_setting WHERE id_site='".$site."'";
		$requetesite = mysql_query($sqlsite, $connexion);
		$nbrresult = mysql_num_rows($requetesite);
		if ($nbrresult >= 1)
			{
			while ($ligne = mysql_fetch_object($requetesite))
				{
				$url_image = $ligne->url_image;
				}
			}		
		}
	else
		{
		$sqlsite = "SELECT id_site, url_image FROM crawlp_site_setting WHERE INSTR('".$_SERVER['HTTP_HOST']."',url) > 0";
		$requetesite = mysql_query($sqlsite, $connexion);
		$nbrresult = mysql_num_rows($requetesite);
		if ($nbrresult >= 1)
			{
			while ($ligne = mysql_fetch_object($requetesite))
				{
				$site = $ligne->id_site;
				$url_image = $ligne->url_image;
				}
			}
		}		
	//get url value
	$rawurl=@$_SERVER['REQUEST_URI'];
	$urltab =explode('crawlprotect2content',$rawurl);
	if (isset($_GET['crawlprotecturl']))
		{
		$url = htmlspecialchars($_GET['crawlprotecturl']);
		}
	$content =$url."?".ltrim($urltab[1],'&');
	$content =rtrim($content,'?');				
	}


if(isset($site) && isset($type))
	{
	$ip = @$_SERVER['REMOTE_ADDR'];
	$date = time();

	$content=htmlspecialchars($content);
	switch($type)
		{
		case "spamreferer":
		$sql ="INSERT INTO crawlp_stats (date,id_site, attack, url, ip) VALUES ( '".sql_quote($date)."','".sql_quote($site)."','".sql_quote($type)."','".sql_quote($content)."','".sql_quote($ip)."')";
		$message="<!-Referer= ".$content."  -->\n";
		break;
		case "forbiddenurl":
		$sql ="INSERT INTO crawlp_stats (date,id_site, attack, url, ip) VALUES ( '".sql_quote($date)."','".sql_quote($site)."','".sql_quote($type)."','".sql_quote($content)."','".sql_quote($ip)."')";
		$message="<!-Url= ".$content."  -->\n";
		break;		
		case "forbiddenparameter":
		$sql ="INSERT INTO crawlp_stats (date,id_site, attack, url, ip) VALUES ( '".sql_quote($date)."','".sql_quote($site)."','".sql_quote($type)."','".sql_quote($content)."','".sql_quote($ip)."')";
		$message="<!-Url= ".$content."  -->\n";
		break;	
		case "xss":
		$sql ="INSERT INTO crawlp_stats (date,id_site, attack, url, ip) VALUES ( '".sql_quote($date)."','".sql_quote($site)."','".sql_quote($type)."','".sql_quote($content)."','".sql_quote($ip)."')";
		$message="<!-Xss= ".$content."  -->\n";
		break;
		case "sqlinjection":
		$sql ="INSERT INTO crawlp_stats (date,id_site, attack, url, ip) VALUES ( '".sql_quote($date)."','".sql_quote($site)."','".sql_quote($type)."','".sql_quote($content)."','".sql_quote($ip)."')";
		$message="<!-Sql injection= ".$content."  -->\n";
		break;		
		case "codeinjection":
		$sql ="INSERT INTO crawlp_stats (date,id_site, attack, url, ip) VALUES ( '".sql_quote($date)."','".sql_quote($site)."','".sql_quote($type)."','".sql_quote($content)."','".sql_quote($ip)."')";
		$message="<!-Code injection= ".$content."  -->\n";
		break;		
		case "badbot":
		$content=@$_SERVER['HTTP_USER_AGENT'];
		$sql ="INSERT INTO crawlp_stats (date,id_site, attack, url, ip) VALUES ( '".sql_quote($date)."','".sql_quote($site)."','".sql_quote($type)."','".sql_quote($content)."','".sql_quote($ip)."')";
		$message="<!-Badbot= ".$content."  -->\n";
		break;		
		case "shell":
		$sql ="INSERT INTO crawlp_stats (date,id_site, attack, url, ip) VALUES ( '".sql_quote($date)."','".sql_quote($site)."','".sql_quote($type)."','".sql_quote($content)."','".sql_quote($ip)."')";
		$message="<!-Shell= ".$content."  -->\n";
		break;		
		case "hotlinking":
		$sql ="INSERT INTO crawlp_stats (date,id_site, attack, url, ip) VALUES ( '".sql_quote($date)."','".sql_quote($site)."','".sql_quote($type)."','".sql_quote($content)."','')";
		$message="<!-Hotlinking= ".$content."  -->\n";
		break;		
		case "spammer":		
		$sql ="INSERT INTO crawlp_stats (date,id_site, attack, url, ip) VALUES ( '".sql_quote($date)."','".sql_quote($site)."','".sql_quote($type)."','".sql_quote($content)."','".sql_quote($ip)."')";
		$message="<!-Spam= ".$content."  -->\n";
		break;
		case "hack":		
		$sql ="INSERT INTO crawlp_stats (date,id_site, attack, url, ip) VALUES ( '".sql_quote($date)."','".sql_quote($site)."','".sql_quote($type)."','".sql_quote($content)."','".sql_quote($ip)."')";
		$message="<!-Hack= ".$content."  -->\n";
		break;			
		}

	$requete = mysql_query($sql, $connexion);
	if($type =='hotlinking' && $url_image !='')
		{
			
		$redirect= "Location: http://".$url_image."";		
		header($redirect);
		exit;	
		}
	header( "Status: 403 request forbidden", false, 403);
	echo $message;
	}
else
	{
	header( "Status: 403 request forbidden", false, 403);	
	}	
?>
<html>
<head>
<title>CrawlProtect 3-0-0</title>
<meta http-equiv="Content-Language" content="en">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body style="background-color:#ff0000;" ><br><br>
<br><br><br><br><br><br><br><br><br>
<br><br><br>
<div align="center" style="background-color:#ffffff; width:60%; margin:auto;">
<h1>This site is protected by CrawlProtect !!!</h1>
<h1>Your access attempt has been blocked.</h1>
<p>If you think that this shouldn't have been the case, please contact the webmaster.</p>
</div>
<br><br><br>
<br><br><br><br><br><br><br><br>
<div align="center">
<p>Ce site est protégé par CrawlProtect !!! Votre tentative d'accès a été bloquée. Si vous pensez que cela ne devrait pas avoir été le cas, s'il vous plaît contactez le webmaster.</p>


<p>Questo sito &egrave; protetto da CrawlProtect !!! Il tuo tentativo di accesso &egrave; stato bloccato.Se pensi che questo non dovrebbe accadere, contatta il webmaster.</p>


<p>Этот сайт защищен CrawlProtect !!! Ваши попытки доступ был заблокирован.Если вы думаете, что это не должно было быть так, пожалуйста, свяжитесь с вебмастером.</p>
<p>Den här webbplatsen skyddas av CrawlProtect! Din åtkomstförsök blockerades. Om du tror att detta inte borde ha varit fallet, kontakta webmaster.</p>
<p>Este sitio está protegido por CrawlProtect! Su intento de acceso fue bloqueado. Si usted piensa que esto no debería haber sido el caso, por favor póngase en contacto con el webmaster.</p>

</div><br></body>
</html>
