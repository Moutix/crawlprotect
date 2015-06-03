<?php
//----------------------------------------------------------------------
//  CrawlProtect 3.2.5b
//----------------------------------------------------------------------
// Protect your website from hackers
//----------------------------------------------------------------------
// Author: Jean-Denis Brun
//----------------------------------------------------------------------
// Website: www.crawltrack.net
//----------------------------------------------------------------------
// That script is distributed under GNU GPL license
//----------------------------------------------------------------------
// file: include/index.php
//----------------------------------------------------------------------
//  Last update: 22/09/2013
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
$noattack=0;
$oldestdate='';
//get variables-----------------------------------------
if(isset($_POST['checkrelease']))
	{
	$checkrelease = $_POST['checkrelease'];
	}
else
	{
	$checkrelease = 'no';
	}
//get info for selected log display	
if (isset($_POST['logdisplay'])) {
	$logdisplay = (int)$_POST['logdisplay'];
} else {
	$logdisplay = 0;
}
if($logdisplay==1)
	{
	if (isset($_POST['sqllog'])) {
		$sqllog = (int)$_POST['sqllog'];
	} else {
		$sqllog = 0;
	}
	if (isset($_POST['codelog'])) {
		$codelog = (int)$_POST['codelog'];
	} else {
		$codelog = 0;
	}
	if (isset($_POST['xsslog'])) {
		$xsslog = (int)$_POST['xsslog'];
	} else {
		$xsslog = 0;
	}
	if (isset($_POST['shelllog'])) {
		$shelllog = (int)$_POST['shelllog'];
	} else {
		$shelllog = 0;
	}
	if (isset($_POST['urllog'])) {
		$urllog = (int)$_POST['urllog'];
	} else {
		$urllog = 0;
	}
	if (isset($_POST['badbotlog'])) {
		$badbotlog = (int)$_POST['badbotlog'];
	} else {
		$badbotlog = 0;
	}
	if (isset($_POST['spammerlog'])) {
		$spammerlog = (int)$_POST['spammerlog'];
	} else {
		$spammerlog = 0;
	}
	if (isset($_POST['refererlog'])) {
		$refererlog = (int)$_POST['refererlog'];
	} else {
		$refererlog = 0;
	}
	if (isset($_POST['hotlinkinglog'])) {
		$hotlinkinglog = (int)$_POST['hotlinkinglog'];
	} else {
		$hotlinkinglog = 0;
	}
	if (isset($_POST['onlyip'])) {
		$onlyip = $_POST['onlyip'];
	} elseif (isset($_GET['onlyip'])) {
		$onlyip = $_GET['onlyip'];
	} else {
		$onlyip = 'toto';
	}
				
	}
else
	{
	$sqllog = 1;
	$codelog = 1;
	$xsslog = 1;
	$shelllog = 1;
	$urllog = 1;
	$badbotlog = 1;
	$spammerlog = 1;	
	$refererlog = 1;
	$hotlinkinglog = 1;
	if (isset($_GET['onlyip'])) {
		$onlyip = $_GET['onlyip'];
	} else {
		$onlyip = '';
	}
	
	}
//check if CrawlProtect  htaccess file is in place
if(!isset($_SESSION['verif']))
	{
	if(file_exists('../.htaccess') )
		{
		if(function_exists('fopen'))
			{
			$file = fopen("../.htaccess", "r");
			$existingfile = fread($file, filesize("../.htaccess"));
			fclose($file);
			if(preg_match("/Htaccess created by CrawlProtect/i", $existingfile) || preg_match("/File created by CrawlProtect/i", $existingfile))
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



		$codeinjection=0;
		$sqlinjection=0;
		$xss=0;				
		$badbot=0;
		$shell=0;
		$spammer=0;
		$spamreferer=0;		
		$hotlinking=0;
		$forbiddenurl=0;		
		$log='';
		$country2=array();
		$listip=array();
		$listcode=array();
		$sql = "SELECT * FROM crawlp_stats  WHERE id_site='" . sql_quote($site) . "' ORDER BY date DESC";			
		$requete = db_query($sql, $connexion);
		$nbrresult=@mysql_num_rows($requete);
		if($nbrresult>=1)
			{
			if(file_exists("./geoipdatabase/geoip.inc"))
				{
				$case=1;
				}
			else
				{
				$case=2;
				}
			// Test to see if the server is running a standalone version of GeoIP
			if(!function_exists('geoip_country_code_by_addr'))
				{
				if($case==1)
					{
					include("./geoipdatabase/geoip.inc");
					}
				else
					{
					include("../geoipdatabase/geoip.inc");
					}
				}
				$i=1;
			while($ligne = mysql_fetch_assoc($requete))
				{
					
				$oldestdate=$ligne['date'];	
				$oldestdate=date('j/m/Y',$ligne['date']);
				$ip=$ligne['ip'];
				if(isset($listcode[$ip]))
					{
					$code=$listcode[$ip];
					}
				elseif(isset($_SESSION[$ip]))
					{
					$code=$_SESSION[$ip];	
					$listcode[$ip]=$code;
					}	
				else
					{
					if($case==1)
						{
						$gi = geoip_open("./geoipdatabase/GeoIP.dat",GEOIP_STANDARD);
						}
					else
						{
						$gi = geoip_open("../geoipdatabase/GeoIP.dat",GEOIP_STANDARD);
						}
					$code = strtolower(str_replace("'"," ",geoip_country_code_by_addr($gi, $ip)));
					if($code==''||$code=='a1')
						{
						$code='xx';
						}
					$listcode[$ip]=$code;
					$_SESSION[$ip]=$code;	
					geoip_close($gi);
					}
				// to cut and wrap the url to avoid oversize display
				$length=100;
				$url=$ligne['url'];
				$urldisplaylength = strlen($url);

				$cutvalue = 0;
				$urldisplay='';
				while ($cutvalue <= $urldisplaylength)
				{
				$cutvalue2 = $cutvalue + $length;
				$urldisplay= $urldisplay.htmlspecialchars(substr($url,$cutvalue,$length));
				if ($cutvalue2 <= $urldisplaylength)
					{
					$urldisplay = $urldisplay.'<br>&nbsp;&nbsp;';
					$urlcut=1;
					}
				$cutvalue = $cutvalue2;
				}
				if(isset($country2[$code]))
					{
					$country2[$code]++;
					}
				else
					{
					$country2[$code]=1;
					}
				if(function_exists('filter_var'))
					{	
					if(	filter_var($ligne['ip'], FILTER_VALIDATE_IP))
						{
						if(isset($listip[$ligne['ip']]))
							{
							$listip[$ligne['ip']]++;
							}
						else
							{
							$listip[$ligne['ip']]=1;
							}
						}
					}
				else
					{
					if(isset($listip[$ligne['ip']]))
						{
						$listip[$ligne['ip']]++;
						}
					else
						{
						$listip[$ligne['ip']]=1;
						}						
					}
						
				$date=date('j/m/Y H:i',$ligne['date']);
				
				//log

				if($ligne['attack']=='xss')
					{
					$xss++;
					if($i<501 && $xsslog==1)
						{
						if($onlyip=='' || $ligne['ip']==$onlyip)
							{						
							$loginfo="<tr><td>".$date."</td><td align='left'><b>Xss:</b>&nbsp;".$urldisplay."</td><td>".$ligne['ip']."</td><td>".$country[$code]."</td></tr>\n";
							$log=$log.$loginfo;
							$i++;
							}
						}	
					}
				elseif($ligne['attack']=='forbiddenurl')
					{
					$forbiddenurl++;
					if($i<501 && $urllog==1)
						{
						if($onlyip=='' || $ligne['ip']==$onlyip)
							{						
							$loginfo="<tr><td>".$date."</td><td align='left'><b>Url:</b>&nbsp;".$urldisplay."</td><td>".$ligne['ip']."</td><td>".$country[$code]."</td></tr>\n";
							$log=$log.$loginfo;
							$i++;
							}
						}
					}
				elseif($ligne['attack']=='forbiddenparameter')
					{
					$forbiddenurl++;
					if($i<501 && $urllog==1)
						{
						if($onlyip=='' || $ligne['ip']==$onlyip)
							{						
							$loginfo="<tr><td>".$date."</td><td align='left'><b>Url:</b>&nbsp;".$urldisplay."</td><td>".$ligne['ip']."</td><td>".$country[$code]."</td></tr>\n";
							$log=$log.$loginfo;
							$i++;
							}
						}					
					}	
				elseif($ligne['attack']=='sqlinjection')
					{
					$sqlinjection++;
					if($i<501 && $sqllog==1)
						{
						if($onlyip=='' || $ligne['ip']==$onlyip)
							{						
							$loginfo="<tr><td>".$date."</td><td align='left'><b>Sqlinjection:</b>&nbsp;".$urldisplay."</td><td>".$ligne['ip']."</td><td>".$country[$code]."</td></tr>\n";
							$log=$log.$loginfo;
							$i++;
							}
						}					
					}	
				elseif($ligne['attack']=='codeinjection')
					{
					$codeinjection++;
					if($i<501 && $codelog==1)
						{
						if($onlyip=='' || $ligne['ip']==$onlyip)
							{											
							$loginfo="<tr><td>".$date."</td><td align='left'><b>Codeinjection:</b>&nbsp;".$urldisplay."</td><td>".$ligne['ip']."</td><td>".$country[$code]."</td></tr>\n";
							$log=$log.$loginfo;
							$i++;
							}
						}					
					}																		
				elseif($ligne['attack']=='badbot')
					{
					$badbot++;
					if($i<501 && $badbotlog==1)
						{
						if($onlyip=='' || $ligne['ip']==$onlyip)
							{						
							$loginfo="<tr><td>".$date."</td><td align='left'><b>BadBot:</b>&nbsp;".$urldisplay."</td><td>".$ligne['ip']."</td><td>".$country[$code]."</td></tr>\n";
							$log=$log.$loginfo;
							$i++;
							}
						}					
					}
				elseif($ligne['attack']=='shell')
					{
					$shell++;
					if($i<501 && $shelllog==1)
						{
						if($onlyip=='' || $ligne['ip']==$onlyip)
							{						
							$loginfo="<tr><td>".$date."</td><td align='left'><b>Shell:</b>&nbsp;".$urldisplay."</td><td>".$ligne['ip']."</td><td>".$country[$code]."</td></tr>\n";
							$log=$log.$loginfo;
							$i++;
							}
						}					
					}
				elseif($ligne['attack']=='spamreferer')
					{
					$spamreferer++;
					if($i<501 && $refererlog==1)
						{
						if($onlyip=='' || $ligne['ip']==$onlyip)
							{						
							$loginfo="<tr><td>".$date."</td><td align='left'><b>Referer:</b>&nbsp;".$urldisplay."</td><td>".$ligne['ip']."</td><td>".$country[$code]."</td></tr>\n";
							$log=$log.$loginfo;
							$i++;
							}
						}					
					}
				elseif($ligne['attack']=='spammer')
					{
					$spammer++;
					if($i<501 && $spammerlog==1)
						{
						if($onlyip=='' || $ligne['ip']==$onlyip)
							{						
							$loginfo="<tr><td>".$date."</td><td align='left'><b>Spam:</b>&nbsp;".$urldisplay."</td><td>".$ligne['ip']."</td><td>".$country[$code]."</td></tr>\n";
							$log=$log.$loginfo;
							$i++;
							}
						}					
					}					
				elseif($ligne['attack']=='hotlinking')
					{
					$hotlinking++;
					if($i<501 && $hotlinkinglog==1)
						{
						if($onlyip=='' || $ligne['ip']==$onlyip)
							{						
							$loginfo="<tr><td>".$date."</td><td align='left'><b>Hotlinking:</b>&nbsp;".$urldisplay."</td><td align='center'>".$language['notapplicable']."</td><td align='center'>".$language['notapplicable']."</td></tr>\n";
							$log=$log.$loginfo;
							$i++;
							}
						}					
					}
						
				
								
				}
			$countryserialize=serialize($country2);
			$listipserialize=serialize($listip);
			}
		else
			{
			$codeinjection=0;
			$sqlinjection=0;
			$xss=0;				
			$badbot=0;
			$shell=0;
			$spammer=0;
			$spamreferer=0;		
			$hotlinking=0;
			$forbiddenurl=0;
			$listipserialize='a:0:{}';
			$countryserialize='a:0:{}';
			}


	$datatransfert= unserialize($countryserialize);


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

//collect autoprepend existing parameter
$sql = "SELECT autoprepend, url, url_crawlprotect FROM crawlp_site_setting WHERE id_site= '" . sql_quote($site) . "'";
$requete = mysql_query($sql, $connexion);
$nbrresult=mysql_num_rows($requete);
if ($nbrresult >= 1) {
	while ($ligne = mysql_fetch_object($requete)) {
		$autoprepend = $ligne->autoprepend;
		$url = $ligne->url;
		$url_crawlprotect = $ligne->url_crawlprotect;					
	}
}
else
	{
		$autoprepend = '0';	
		$url = $_SERVER['HTTP_HOST'];
		$url_crawlprotect =$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];		
	}	

	
//display=====================================================================================================================
//include menu
include ("include/menusite.php");
include ("include/menumain.php");
echo"<div align=\"center\"><br>\n";
echo"<table width=\"100%\"><tr><td width=\"50%\" valign=\"top\" align=\"center\">\n";
if($sqlinjection+$codeinjection+$xss+$spammer+$spamreferer+$badbot+$shell+$forbiddenurl+$hotlinking > 0)
	{
	echo"<h2>".$language['CrawlProtect_has_blocked']." ".$oldestdate."</h2>\n";
	echo"<table style=\"font-size:14px;\"><tr><td align=\"right\">\n";
	echo numbdisp($sqlinjection)."</td><td align=\"left\">".$language['sqlinjection']."\n";
	echo"</td></tr><tr><td align=\"right\">\n";
	echo numbdisp($codeinjection)."</td><td align=\"left\">".$language['codeinjection']."\n";
	echo"</td></tr><tr><td align=\"right\">\n";	
	echo numbdisp($xss)."</td><td align=\"left\">".$language['xss']."\n";
	echo"</td></tr><tr><td align=\"right\">\n";	
	echo numbdisp($shell)."</td><td align=\"left\">".$language['shell']."\n";
	echo"</td></tr><tr><td align=\"right\">\n";		
	echo numbdisp($forbiddenurl)."</td><td align=\"left\">".$language['forbidden']."\n";		
	echo"</td></tr><tr><td align=\"right\">\n";			
	echo numbdisp($badbot)."</td><td align=\"left\">".$language['badbots']."\n";
	echo"</td></tr><tr><td align=\"right\">\n";
	echo numbdisp($spamreferer)."</td><td align=\"left\">".$language['spam']."\n";
	echo"</td></tr><tr><td align=\"right\">\n";
	echo numbdisp($spammer)."</td><td align=\"left\">".$language['spammer']."\n";
	echo"</td></tr><tr><td align=\"right\">\n";
	echo numbdisp($hotlinking)."</td><td align=\"left\">".$language['hotlinking']."\n";		
	echo"</td></tr></table>\n";
	
	echo"<h2>".numbdisp($sqlinjection+$codeinjection+$xss+$forbiddenurl+$badbot+$spamreferer+$spammer+$hotlinking)." ".$language['hackattempts']."</h2>\n";
	
	
	if(count($datatransfert)>0)
		{
		//ip
		echo"<br><br><h2>".$language['ip_used']."</h2>";
		//get  values
		$listip= unserialize($listipserialize);
		arsort($listip);
		//check IP already blocked
	$sql = "SELECT forbiddenip FROM crawlp_site_setting WHERE id_site=".$site."";
	$requete = mysql_query($sql, $connexion);
	$nbrresult=mysql_num_rows($requete);
	if($nbrresult>=1)
		{
		$ligne = mysql_fetch_assoc($requete);
		$badip = $ligne['forbiddenip'];
		if($badip !='')
			{
			$listbadip=explode(',',$badip);	
			}
		else
			{
			$listbadip=array();
			}
		}
	else
		{
		$listbadip=array();
		}
		echo"<form action=\"".$crawlprotecturl."\" method=\"POST\">";
		echo "<input type=\"hidden\" name ='navig' value='3'>\n";
		echo "<input type=\"hidden\" name =\"addiptoblock\" value=\"1\">\n";
		echo"<div class='listip'><table width='340px' style='font-size:13px;'><tr><td align='right'>";
		foreach ($listip as $key => $value)
			{
			$explodeip = explode('.',$key);	
			$ip1=$explodeip[0];
			$ip2=$explodeip[0].".".$explodeip[1];
			$ip3=$explodeip[0].".".$explodeip[1].".".$explodeip[2];
			$i=(5-strlen($value))*2;
			if($i<0)
				{
				$i=0;
				}
			$j=(16-strlen($key))*2;
			$value2=str_repeat("&nbsp;",$i).$value;
			$key2="<a href=\"index.php?onlyip=".$key."\">".$key."</a>".str_repeat("&nbsp;",$j);
			if(!in_array($key,$listbadip) && !in_array($ip1,$listbadip) && !in_array($ip2,$listbadip) && !in_array($ip3,$listbadip))
				{
				echo $key2."&nbsp;==>".$value2."&nbsp;&nbsp;".$language['attempts']."<input type=\"checkbox\" name=\"".ip2long($key)."\" value=\"1\">&nbsp;<a href=\"http://www.whois-search.com/whois/".$key."\" target=\"blank\"><img src='images/information.png'></a><br>\n";
				}
			else
				{
				echo "<span class='red'>".$key2."&nbsp;==>".$value2."&nbsp;&nbsp;".$language['attempts']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"http://www.whois-search.com/whois/".$key."\" target=\"blank\"><img src='images/information.png'></a></span><br>\n";
				}
			}
		echo"</td></tr></table></div>";
		echo"<br><input name='ok' type='submit'  value='".$language['blockip']."' size='20' >";
		echo"</form>\n";
		}	
	
		// graph
if($noattack==0 && function_exists("gd_info"))
	{		
	echo"<h2>".$language['coming_from']."</h2>\n";
	echo"<img src=\"./graphs/origine-graph.php?lang=".$crawltlang."&site=".$site."\" alt=\"Origin\" width=\"450px\" height=\"200px\"/>\n";
	echo"<div class=\"smalltext\">".$language['maxmind']."<a href='http://maxmind.com'>http://maxmind.com</a></div>\n";
	}

	
	
	

	}
else
	{
	echo"<br><br><h2>".$language['no_attack']."</h2>\n";
	$noattack=1;

	}
echo"</td><td valign=\"top\" align=\"center\">\n";
if($noattack==1)
	{
	echo "<br><br>";
	}
echo"<h2>".$language['check-set-up']."</h2>\n";


if($url!=$_SERVER['HTTP_HOST'])
	{
	echo $language['otherhost'];
	
	echo "<br><br><a href=\"http://".$url_crawlprotect."\">".$language['changehost']."</a>";
	
		
	}
else
	{	
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
	echo "<input type=\"hidden\" name ='chmodindex' value='1'>\n";
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
	echo "<input type=\"hidden\" name ='chmodindex' value='1'>\n";
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
		
		
		
			
		
		
	//tag for anti-spam
	if($autoprepend==0)
		{
	
		echo"<br><hr>\n";
		echo "<h2>".$language['tagforspam']."</h2>";
		echo "<div>";	
		echo"<table width=\"80%\"><tr><td align='left'>";	
		echo "<p>".$language['paramhtaccesstext24']."</p>";	
		echo "<div class=\"displaytag\"><b>require_once(\"".$path."/include/cppf.php\");</b></div>";
		echo"</td></tr></table><br></div>";
		}
	else
		{
		echo"<br><hr>\n";
		echo "<h2>".$language['tagforspam']."</h2>";
		echo "<div>";	
		echo"<table width=\"80%\"><tr><td align='left' >";	
		echo "<p>".$language['paramhtaccesstext21']."</p>";	
		echo"</td></tr></table><br></div>";			
		}	
		
	}	
	
echo"</td></tr><tr><td valign='top'>\n";

echo"</td></tr></table><br><br>\n";
if($codeinjection+$sqlinjection+$xss+$badbot+$shell+$forbiddenurl+$spammer+$spamreferer+$hotlinking > 0)
	{
	echo"<h2>".$language['log-recording']."</h2>";
	echo"<p>".$language['not-all-log']."</p>";		
	echo "<div width='70%' align='center'><form action=\"index.php\" method=\"POST\"  style=\" font-size:10px; font-weight:bold; color: #000080; font-family: Verdana,Geneva, Arial, Helvetica, Sans-Serif; \">\n";
	echo "<input type=\"hidden\" name ='navig' value=\"0\">\n";
	echo "<input type=\"hidden\" name ='site' value=\"".$site."\">\n";
	echo "<input type=\"hidden\" name ='logdisplay' value=\"1\">\n";								
	echo "<table>";
	if($sqllog==1)
		{
		echo "<tr><td>" . $language['sql'] . "</td><td><input type=\"checkbox\" name=\"sqllog\" value=\"1\" checked></td>\n";
		}
	else
		{
		echo "<tr><td>" . $language['sql'] . "</td><td><input type=\"checkbox\" name=\"sqllog\" value=\"1\"></td>\n";
		}			
	if($codelog==1)
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['code'] . "</td><td><input type=\"checkbox\" name=\"codelog\" value=\"1\" checked></td>\n";
		}
	else
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['code'] . "</td><td><input type=\"checkbox\" name=\"codelog\" value=\"1\"></td>\n";
		}
	if($xsslog==1)
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['xss2'] . "</td><td><input type=\"checkbox\" name=\"xsslog\" value=\"1\" checked></td>\n";
		}
	else
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['xss2'] . "</td><td><input type=\"checkbox\" name=\"xsslog\" value=\"1\"></td>\n";
		}	
	if($shelllog==1)
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['shell2'] . "</td><td><input type=\"checkbox\" name=\"shelllog\" value=\"1\" checked></td>\n";
		}
	else
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['shell2'] . "</td><td><input type=\"checkbox\" name=\"shelllog\" value=\"1\"></td>\n";
		}				
	if($urllog==1)
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['url'] . "</td><td><input type=\"checkbox\" name=\"urllog\" value=\"1\" checked></td>\n";
		}
	else
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['url'] . "</td><td><input type=\"checkbox\" name=\"urllog\" value=\"1\"></td>\n";
		}
	if($badbotlog==1)
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['badbot'] . "</td><td><input type=\"checkbox\" name=\"badbotlog\" value=\"1\" checked></td>\n";
		}
	else
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['badbot'] . "</td><td><input type=\"checkbox\" name=\"badbotlog\" value=\"1\"></td>\n";
		}
	if($refererlog==1)
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['referer'] . "</td><td><input type=\"checkbox\" name=\"refererlog\" value=\"1\" checked></td>\n";
		}
	else
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['referer'] . "</td><td><input type=\"checkbox\" name=\"refererlog\" value=\"1\"></td>\n";
		}
	if($spammerlog==1)
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['spammer2'] . "</td><td><input type=\"checkbox\" name=\"spammerlog\" value=\"1\" checked></td>\n";
		}
	else
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['spammer2'] . "</td><td><input type=\"checkbox\" name=\"spammerlog\" value=\"1\"></td>\n";
		}									
	if($hotlinkinglog==1)
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['hotlinking2'] . "</td><td><input type=\"checkbox\" name=\"hotlinkinglog\" value=\"1\" checked></td>\n";
		}
	else
		{
		echo "<td>&nbsp;&nbsp;&nbsp;" . $language['hotlinking2'] . "</td><td><input type=\"checkbox\" name=\"hotlinkinglog\" value=\"1\"></td>\n";
		}
		
		
		
		echo "<td>&nbsp;&nbsp;&nbsp;IP:</td><td><input name='onlyip'  value='".$onlyip."' type='text' maxlength='15' size='15'/></td>\n";
		
		
						
		echo "<td>&nbsp;&nbsp;&nbsp;<input name='ok' type='submit'  value=' OK ' size='20' ></td></tr>\n";			
		echo "</table></div>\n";		
		echo"</form><br>\n";
	echo"<div class='titres'><table width='950px'>";
	echo"<tr'><td width='150px'><h3>".$language['date']."</h3></td><td width='600px'><h3>".$language['why']."</h3></td><td width='100px'><h3>IP</h3></td><td width='100px'><h3>".$language['country']."</h3></td></tr></table></div>";
	echo"<div class='logs'><table width='950px' style=\"font-size:11px; \">";
	echo"<tr class='noshow'><td width='150px'>&nbsp;</td><td width='600px'>&nbsp;</td><td width='100px'>&nbsp;</td><td width='100px'>&nbsp;</td></tr>";
	echo $log;
	
	echo"</table></div><br><br>";
	}
?>
