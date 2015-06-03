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
// file: createhtaccess.php
//----------------------------------------------------------------------
//  Last update: 20/10/2013
//----------------------------------------------------------------------
if (!defined('IN_CRAWLT'))
	{
	echo"<h1>Hacking attempt !!!!</h1>";
	exit();
	}

//variables init
$erreur=0;
$checktrustip=1;
$checkforbiddenip=1;
$checknomhtaccess=1;
$forbiddeniplist=array();
$forbiddenurllist=array();
$forbiddenparameterlist=array();
$listscriptused=array();
$forbiddenrefererlisttreat=array();
$forbiddenwordlisttreat=array();
$xssname=array();
$xsstype=array();
$xsscontent=array();	
$sqlinjectname=array();
$sqlinjecttype=array();
$sqlinjectcontent=array();	
$codeinjectname=array();
$codeinjecttype=array();
$codeinjectcontent=array();	
$shellname=array();
$shelltype=array();
$shellcontent=array();
$hotlinkoklist=array();
//-------------------------------------------------------------------	
//get okcreatehtaccess value
if (isset($_POST['okcreate']))
	{
	$okcreate = (int)$_POST['okcreate'];
	}
else
	{
	$okcreate = 0;
	}
//-------------------------------------------------------------------	
//get yourip value
if (isset($_POST['yourip']))
	{
	$yourip = (int)$_POST['yourip'];
	}
else
	{
	$yourip = 0;
	}
//-------------------------------------------------------------------
//get trust ip list	
if( $yourip==1)
	{
	if (isset($_POST['trustip']))
		{
		$wrongtrustip='';	
		$trustip = rtrim(htmlspecialchars($_POST['trustip']),",");
		if(preg_match("/,/i", $trustip))
			{
			$trustiplist = explode(',',$trustip);		
			foreach($trustiplist as $value)
				{
				if(function_exists('filter_var'))
					{	
					if (!filter_var($value, FILTER_VALIDATE_IP))
						{					
						$checktrustip=0;
						$wrongtrustip=$wrongtrustip."--".$value;
						}
					}
				}
			}
		else
			{
			if ($trustip!='')
				{
				$trustiplist[]=$trustip;
				}
			else
				{
				$trustiplist=array();	
				}				
			if(function_exists('filter_var'))
				{				
				if (!filter_var($trustip, FILTER_VALIDATE_IP))
					{				
					$checktrustip=0;
					$wrongtrustip=$trustip;
					}
				}				
			}	
		} 
	else
		{
		$trustip = '';
		$yourip=0;
		$trustiplist=array();
		}		
	}
else
	{
	$trustip='';	
	}

//-------------------------------------------------------------------
//get the list of scripts used	
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
	
$scriptsused='';
$scriptsusedsql='';
foreach($listscript as $key => $value)
	{	
	$namevalue =str_replace(' ','--',$value);			
	if (isset($_POST[$namevalue]))
		{
		if(	$_POST[$namevalue]==1)
			{
			$scriptsused = $scriptsused.",".$value;
			$scriptsusedsql = $scriptsusedsql.",'".$value."'";
			$listscriptused[] = $value;	
			}		
		}

	}
$scriptsused =ltrim($scriptsused,',');
$scriptsusedsql =ltrim($scriptsusedsql,',');
//-------------------------------------------------------------------
//prepare the parameters for scripts used
if(count($listscriptused)>0)
	{
	$sql = "SELECT name, type, content, xss, sqlinject, codeinject, shell FROM crawlp_scripts WHERE name IN (".$scriptsusedsql.")";
	$requete = mysql_query($sql, $connexion);
	$nbrresult=mysql_num_rows($requete);

	if($nbrresult>=1)
		{
		while ($ligne = mysql_fetch_row($requete))
			{
			if( $ligne[3]==1)
				{
				$xssname[]= $ligne[0];
				$xsstype[]= $ligne[1];
				$xsscontent[]= $ligne[2];	
				}	
			if( $ligne[4]==1)
				{
				$sqlinjectname[]= $ligne[0];
				$sqlinjecttype[]= $ligne[1];
				$sqlinjectcontent[]= $ligne[2];	
				}				
			if( $ligne[5]==1)
				{
				$codeinjectname[]= $ligne[0];
				$codeinjecttype[]= $ligne[1];
				$codeinjectcontent[]= $ligne[2];	
				}				
			if( $ligne[6]==1)
				{
				$shellname[]= $ligne[0];
				$shelltype[]= $ligne[1];
				$shellcontent[]= $ligne[2];	
				}	
			}
		}	
	}
//-------------------------------------------------------------------
//get trust sites list
if (isset($_POST['trustsites']))
	{
	$trustsites = rtrim(htmlspecialchars($_POST['trustsites']),",");	
	if(preg_match("/,/i", $trustsites))
		{
		$trustsiteslist = explode(',',$trustsites);	
		}
	else
		{
		if ($trustsites!='' && $trustsites !='www.example.com')
			{
			$trustsiteslist[]=$trustsites;
			}
		else
			{
			$trustsiteslist=array();	
			}			
		}		
	} 
else
	{
	$trustsites = '';
	$trustsiteslist=array();	
	}
//-------------------------------------------------------------------
//get trust variables list
if (isset($_POST['trustvariable']))
	{
	$trustvariable = rtrim(htmlspecialchars($_POST['trustvariable']),",");	
	if(preg_match("/,/i", $trustvariable))
		{
		$trustvariablelist = explode(',',$trustvariable);	
		}
	else
		{
		if ($trustvariable!='' && $trustvariable !='variable-example')
			{
			$trustvariablelist[]=$trustvariable;
			}
		else
			{
			$trustvariablelist=array();	
			}			
		}		
	} 
else
	{
	$trustvariable = '';
	$trustvariablelist=array();	
	}
//-------------------------------------------------------------------
$sqlsetting='';
	for($i=1; $i<=7; $i++)
		{
		$sql="sq".$i;
		if(isset($_POST[$sql]))
			{
			${'sq'.$i}=(int)$_POST[$sql];
			}
		else
			{
			${'sq'.$i}=0;
			}			
		$sqlsetting = $sqlsetting.",".${'sq'.$i};
		}
$sqlsetting =ltrim($sqlsetting,',');		
//-------------------------------------------------------------------
$shellsetting='';
	for($i=1; $i<=28; $i++)
		{
		$shell="s".$i;	
		if(isset($_POST[$shell]))
			{
			${'s'.$i}=(int)$_POST[$shell];
			}
		else
			{
			${'s'.$i}=0;
			}			
		$shellsetting = $shellsetting.",".${'s'.$i};
		}
$shellsetting =ltrim($shellsetting,',');		
//-------------------------------------------------------------------
//get forbidden user agent list
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
	$valid = array();
	$countUA = 0;
	$trustuseragent='';	
	foreach($baduseragent as $key => $value)
		{
		$nameUA = "UA-".$key;		
		if (!isset($_POST[$nameUA]) || $_POST[$nameUA]==0)
			{
			$valid[$nameUA] = 0;

			$trustuseragent=$trustuseragent.",0";
			}
		else
			{
			$valid[$nameUA] = 1;
			$countUA++;			
			$trustuseragent=$trustuseragent.",1";
			}
		}
	$trustuseragent =ltrim($trustuseragent,',');				
	}
else
	{
	$countUA = 0;
	}
//-------------------------------------------------------------------
//get fordidden ip list
if (isset($_POST['forbiddenip']))
	{
	$forbiddenip = rtrim(htmlspecialchars($_POST['forbiddenip']),",");
	if($forbiddenip !='')
		{
		$wrongbadip='';	
		if(preg_match("/,/i", $forbiddenip))
			{
			$forbiddeniplist = explode(',',$forbiddenip);
			if(function_exists('filter_var'))
				{	
				foreach($forbiddeniplist as $value)
					{
					if (!filter_var($value, FILTER_VALIDATE_IP))
						{
						$value2= $value.".0";
						if (!filter_var($value2, FILTER_VALIDATE_IP))
							{
							$value3= $value2.".0";						
							if (!filter_var($value3, FILTER_VALIDATE_IP))
								{				
								$checkforbiddenip=0;
								$wrongbadip=$wrongbadip."--".$value;
								}
							}
						}
					}
				}
			}
		else
			{
			if(function_exists('filter_var'))
				{
				if (!filter_var($forbiddenip, FILTER_VALIDATE_IP))
					{
						$value2= $forbiddenip.".0";
						if (!filter_var($value2, FILTER_VALIDATE_IP))
							{
							$value3= $value2.".0";						
							if (!filter_var($value3, FILTER_VALIDATE_IP))
								{				
								$checkforbiddenip=0;
								$wrongbadip=$forbiddenip;
								}
							}
					}
				}
			$forbiddeniplist[]=$forbiddenip;				
			}
		}	
	} 
else
	{
	$forbiddenip = '';
	$forbiddeniplist=array();
	}
//-------------------------------------------------------------------
//get forbidden url list	
if (isset($_POST['forbiddenurl']))
	{
	$forbiddenurl = rtrim(htmlspecialchars($_POST['forbiddenurl']),",");
	if(preg_match("/,/i", $forbiddenurl))
		{
		$forbiddenurllist = explode(',',$forbiddenurl);	
		}
	else
		{
		if ($forbiddenurl!='')
			{
			$forbiddenurllist[]=$forbiddenurl;
			}
		else
			{
			$forbiddenurllist=array();	
			}		
		
		}
	foreach($forbiddenurllist as $url)
		{
		$forbiddenurllisttreat[]=str_replace(".","\.",trim($url));
		}		
	} 
else
	{
	$forbiddenurl = '';
	$forbiddenurllisttreat = array();
	}
//-------------------------------------------------------------------
//get forbidden parameter list	
if (isset($_POST['forbiddenparameter']))
	{
	$forbiddenparameter = rtrim(htmlspecialchars($_POST['forbiddenparameter']),",");
	if(preg_match("/,/i", $forbiddenparameter))
		{
		$forbiddenparameterlist = explode(',',$forbiddenparameter);	
		}
	else
		{
		if ($forbiddenparameter!='')
			{
			$forbiddenparameterlist[]=$forbiddenparameter;
			}
		else
			{
			$forbiddenparameterlist=array();	
			}		
		}
		
		foreach($forbiddenparameterlist as $parameter)
			{
			$forbiddenparameterlisttreat[]=str_replace(".","\.",trim($parameter));
			}		
			
	} 
else
	{
	$forbiddenparameter = '';
	$forbiddenparameterlisttreat = array();
	}
//-------------------------------------------------------------------
//get blockUA value
if (isset($_POST['blockUA']))
	{
	$blockUA = (int)$_POST['blockUA'];
	}
else
	{
	$blockUA = 0;
	}

//-------------------------------------------------------------------
//get hotlink value
if (isset($_POST['hotlink']))
	{
	$hotlink = (int)$_POST['hotlink'];
	}
else
	{
	$hotlink = 0;
	}
//-------------------------------------------------------------------
//get forbidden referer list	
if (isset($_POST['forbiddenreferer']))
	{
	$forbiddenreferer = rtrim(htmlspecialchars($_POST['forbiddenreferer']),",");
			if(preg_match("/,/i", $forbiddenreferer))
				{
				$forbiddenrefererlist = explode(',',$forbiddenreferer);
				}
			else
				{
				if ($forbiddenreferer!='')
					{
					$forbiddenrefererlist[]=$forbiddenreferer;
					}
				else
					{
					$forbiddenrefererlist=array();	
					}	
				}
				
			foreach($forbiddenrefererlist as $referer)
				{
				$forbiddenrefererlisttreat[]=str_replace(".","\.",trim($referer));
				}							
	} 
else
	{
	$forbiddenreferer = '';
	$forbiddenrefererlisttreat=array();
	}
//-------------------------------------------------------------------
//get forbidden word list	
if (isset($_POST['forbiddenword']))
	{
	$forbiddenword = rtrim(htmlspecialchars($_POST['forbiddenword']),",");
			if(preg_match("/,/i", $forbiddenword))
				{
				$forbiddenwordlist = explode(',',$forbiddenword);
				}
			else
				{
				if ($forbiddenword!='')
					{
					$forbiddenwordlist[]=$forbiddenword;
					}
				else
					{
					$forbiddenwordlist=array();	
					}	
				}
				
			foreach($forbiddenwordlist as $word)
				{				
				$forbiddenwordlisttreat[]=str_replace(".","\.",trim($word));
				}							
	} 
else
	{
	$forbiddenword = '';
	$forbiddenwordlisttreat=array();
	}
//-------------------------------------------------------------------
//get autoprepend value
if (isset($_POST['autoprepend']))
	{
	$autoprepend = (int)$_POST['autoprepend'];
	}
else
	{
	$autoprepend = 1;
	}
//case php not module Apache ==> autoprepend impossible
$sapi_type = php_sapi_name();
if(!preg_match("/apache/i", $sapi_type)) {	
$autoprepend = '0';	
}		
//-------------------------------------------------------------------
//get actual htaccess content
if(!isset($_SESSION['actual']))
	{	
	if (isset($_POST['actual']))
		{
		$actual = $_POST['actual'];
		} 
	else
		{
		$actual = '';
		}
	$_SESSION['actual']=$actual;
	}
else
	{
	if (isset($_POST['actual']) && $_POST['actual']!=$_SESSION['actual'])
		{	
		$actual = $_POST['actual'];
		$_SESSION['actual']=$actual;
		}
	else
		{
		$actual=$_SESSION['actual'];	
		}		
	}
//-------------------------------------------------------------------
//get hotlinkok sites list
if (isset($_POST['hotlinkok']))
	{
	$hotlinkok = rtrim(htmlspecialchars($_POST['hotlinkok']),",");	
	if(preg_match("/,/i", $hotlinkok))
		{
		$hotlinkoklist = explode(',',$hotlinkok);	
		}
	else
		{
		if ($hotlinkok!='' && $hotlinkok !='example.com')
			{
			$hotlinkoklist[]=$hotlinkok;
			}
		else
			{
			$hotlinkoklist=array();	
			}			
		}		
	} 
else
	{
	$hotlinkok = '';
	$hotlinkoklist=array();	
	}	
	
//-------------------------------------------------------------------	
//get listindex value
if (isset($_POST['listindex']))
	{
	$listindex = (int)$_POST['listindex'];
	}
else
	{
	$listindex = 0;
	}		
//-------------------------------------------------------------------
$badfile='';
	for($i=1; $i<=17; $i++)
		{
		$bf="bf".$i;
		if(isset($_POST[$bf]))
			{
			${'bf'.$i}=(int)$_POST[$bf];
			}
		else
			{
			${'bf'.$i}=0;
			}			
		$badfile = $badfile.",".${'bf'.$i};
		}
$badfile =ltrim($badfile,',');		
//-------------------------------------------------------------------
//get hotlinkok alternative image url
if (isset($_POST['url_image']))
	{
	$url_image = rtrim(htmlspecialchars($_POST['url_image']),",");		
	}
else
	{
	$url_image = '';
	}
		
//-------------------------------------------------------------------
//error management
if($checktrustip==0 ||$checkforbiddenip==0)
	{
	//include menu
	include ("include/menusite.php");
	include ("include/menumain.php");
	echo "<div id='centre'>";
	echo "<h1>".$language['yourownhtaccess']."</h1><br>";	
	if($checktrustip==0)
		{
		echo "<div class='alert3'>".$language['errortrustip']."<br>";
		$wrongtrustip = ltrim($wrongtrustip,'--');
		echo "<b>".$wrongtrustip."</b></div><br>";		
		}
	if($checkforbiddenip==0)
		{
		echo "<div class='alert3'>".$language['errorforbiddenip']."<br>";	
		$wrongbadip = ltrim($wrongbadip,'--');
		echo "<b>".$wrongbadip."</b></div><br>";
			
		}
	echo"<form action=\"index.php\" method=\"POST\">\n";
	echo"<input type=\"hidden\" name ='navig' value='3'>\n";	
	echo"<input type=\"hidden\" name ='trustip' value='".$trustip."'>\n";		
	echo"<input type=\"hidden\" name ='forbiddenip' value='".$forbiddenip."'>\n";		
	echo"<br><br><div align='center'><input name='ok' type='submit'  value='".$language['returntoform']."' size='60' ></div><br><br>\n";					
	}	
else
	{
//-------------------------------------------------------------------		
//enter data in the database
	if(isset($_POST['logok'])) //test to avoid issue in case of lost of connection during htaccess update
		{		
	$sql = "UPDATE crawlp_site_setting SET scriptsused='" . sql_quote($scriptsused) . "', yourip='" . sql_quote($yourip) . "', blockUA='" . sql_quote($blockUA) . "', hotlink='" . sql_quote($hotlink) . "', shellsetting='" . sql_quote($shellsetting) . "', sqlsetting='" . sql_quote($sqlsetting) . "', trustsites='" . sql_quote($trustsites) . "', trustip='" . sql_quote($trustip) . "', trustuseragent='" . sql_quote($trustuseragent) . "',trustvariable='" . sql_quote($trustvariable) . "', forbiddenip='" . sql_quote($forbiddenip) . "', forbiddenurl='" . sql_quote($forbiddenurl) . "', forbiddenparameter='" . sql_quote($forbiddenparameter) . "', forbiddenreferer='" . sql_quote($forbiddenreferer) . "', forbiddenword='" . sql_quote($forbiddenword) . "',autoprepend='" . sql_quote($autoprepend) . "', actualhtaccess='" . sql_quote($actual) . "', hotlinkok='" . sql_quote($hotlinkok) . "', listindex='" . sql_quote($listindex) . "', badfile='" . sql_quote($badfile) . "', url_image='" . sql_quote($url_image) . "' WHERE id_site='" . sql_quote($site) . "'";
	$query = db_query($sql, $connexion);
	}				
//-------------------------------------------------------------------
//redirection url calculation
$redirecturl=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$redirecturl= str_replace('index.php','noaccess/index.php',$redirecturl);
$redirecturl="http://".$redirecturl."?crawlprotectsite=".$site;
//-------------------------------------------------------------------		
//create htaccess		
$date=	date('l jS \of F Y h:i:s A');
	$crawlprotect ="#######################################################################################\n";
	$crawlprotect.="# File created by CrawlProtect\n";
	$crawlprotect.="#######################################################################################\n";
	$crawlprotect.="# Protect you website from hackers\n";
	$crawlprotect.="#######################################################################################\n";
	$crawlprotect.="# Author: Jean-Denis Brun\n";
	$crawlprotect.="#######################################################################################\n";
	$crawlprotect.="# Website: www.crawltrack.net/crawlprotect\n";
	$crawlprotect.="#######################################################################################\n";
	$crawlprotect.="# That file is distributed under GNU GPL license\n";
	$crawlprotect.="#######################################################################################\n";
	$crawlprotect.="# File created on: $date\n";
	$crawlprotect.="#######################################################################################\n";
	$crawlprotect.="#######################################################################################\n";	
	$crawlprotect.="#Block access to that file\n";
	$crawlprotect.="#######################################################################################\n";		
	$crawlprotect.="<Files .htaccess>\n";
    $crawlprotect.="order allow,deny\n";
    $crawlprotect.="deny from all\n";
	$crawlprotect.="</Files>\n";
	if($listindex==0)
		{
		$crawlprotect.="#######################################################################################\n";	
		$crawlprotect.="#Block listing of folder without index file\n";
		$crawlprotect.="#######################################################################################\n";		
		$crawlprotect.="<IfModule mod_rewrite.c>\n";	
		$crawlprotect.="RewriteEngine On\n";
		$crawlprotect.="<IfModule mod_autoindex.c>\n";	
		$crawlprotect.="IndexIgnore *\n";
		$crawlprotect.="</ifModule>\n";	
		$crawlprotect.="</ifModule>\n";			
		}
	$totbf= $bf1+$bf2+$bf3+$bf4+$bf5+$bf6+$bf7+$bf8+$bf9+$bf10+$bf11+$bf12+$bf13+$bf14+$bf15+$bf16+$bf17;
	if($totbf < 17)
		{	
		$crawlprotect.="#######################################################################################\n";	
		$crawlprotect.="#Block access to files which should not be displayed\n";
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="<Files ~ '\.(";
		
		$ibf=1;
		$totbf= 17 - $totbf;

		if($bf1==0)
			{
			$crawlprotect.="inc";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
		if($bf2==0)
			{
			$crawlprotect.="class";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}		
		if($bf3==0)
			{
			$crawlprotect.="sql";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
		if($bf4==0)
			{
			$crawlprotect.="ini";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
						
		if($bf5==0)
			{
			$crawlprotect.="conf";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
		
		if($bf6==0)
			{
			$crawlprotect.="exe";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
			
		if($bf7==0)
			{
			$crawlprotect.="dll";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
		if($bf8==0)
			{
			$crawlprotect.="bin";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
		if($bf9==0)
			{
			$crawlprotect.="tpl";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
		if($bf10==0)
			{
			$crawlprotect.="bkp";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
		if($bf11==0)
			{
			$crawlprotect.="dat";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
		if($bf12==0)
			{
			$crawlprotect.="c";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
		if($bf13==0)
			{
			$crawlprotect.="h";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
		if($bf14==0)
			{
			$crawlprotect.="py";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
		if($bf15==0)
			{
			$crawlprotect.="spd";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
		if($bf16==0)
			{
			$crawlprotect.="theme";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
		if($bf17==0)
			{
			$crawlprotect.="module";
			if($totbf> $ibf)
				{
				$crawlprotect.="|";	
				$ibf++;
				}
			}
		$crawlprotect.= ")$'>\n";																																						
		$crawlprotect.= "deny from all\n";
		$crawlprotect.= "</Files>\n";
		}
	
	$crawlprotect.="#######################################################################################\n";	
	$crawlprotect.="#Avoid display of Apache version\n";
	$crawlprotect.="#######################################################################################\n";	
	$crawlprotect.="ServerSignature Off\n";
	$crawlprotect.="#######################################################################################\n";	
	$crawlprotect.="#Deflate files to fasten the loading (setting proposed by http://support.netdna.com/tutorials/htaccess-examples/)\n";
	$crawlprotect.="#######################################################################################\n";		
	$crawlprotect.="<IfModule mod_deflate.c>\n";	
    $crawlprotect.="SetOutputFilter DEFLATE\n";
    $crawlprotect.="AddOutputFilterByType DEFLATE application/x-httpd-php text/html text/xml text/plain text/css text/javascript application/javascript application/x-javascript image/jpeg image/jpg image/png image/gif font/ttf font/eot font/otf\n";
	$crawlprotect.="</IfModule>\n";
	$crawlprotect.="<IfModule mod_headers.c>\n";
    $crawlprotect.="# properly handle requests coming from behind proxies\n";
    $crawlprotect.="Header append Vary User-Agent\n";
	$crawlprotect.="</IfModule>\n";
	$crawlprotect.="<IfModule mod_setenvif.c>\n";	
	$crawlprotect.="<IfModule mod_deflate.c>\n";
    $crawlprotect.="# Properly handle old browsers that do not support compression\n";
    $crawlprotect.="BrowserMatch ^Mozilla/4 gzip-only-text/html\n";
    $crawlprotect.="BrowserMatch ^Mozilla/4\.0[678] no-gzip\n";
    $crawlprotect.="BrowserMatch \bMSIE !no-gzip !gzip-only-text/html\n";
    $crawlprotect.="# Explicitly exclude binary files from compression just in case\n";
    $crawlprotect.="SetEnvIfNoCase Request_URI \.(?:gif|jpe?g|png|pdf|swf|ico|zip|ttf|eot|svg)$ no-gzip\n";
	$crawlprotect.="</IfModule>\n";
	$crawlprotect.="</IfModule>\n";	
	//fordidden word
	if(count($forbiddenwordlisttreat) >0 && $autoprepend==1)
		{
		//determine the path to the file
		if (isset($_SERVER['SCRIPT_FILENAME']) && !empty($_SERVER['SCRIPT_FILENAME'])) {
			$path = dirname($_SERVER['SCRIPT_FILENAME']);
		} elseif (isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['PHP_SELF']) && !empty($_SERVER['PHP_SELF'])) {
			$path = dirname($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']);
		} else {
			$path = '..';
		}			
		$crawlprotect.="#######################################################################################\n";	
		$crawlprotect.="#Block spammer\n";
		$crawlprotect.="#######################################################################################\n";	
		$crawlprotect.="<ifModule mod_php5.c>\n";
		$crawlprotect.="php_value auto_prepend_file \"".$path."/include/cppf.php\"\n";
		$crawlprotect.="</ifModule>\n";	
		$crawlprotect.="<ifModule mod_php4.c>\n";
		$crawlprotect.="php_value auto_prepend_file \"".$path."/include/cppf.php\"\n";
		$crawlprotect.="</ifModule>\n";			
		$crawlprotect.="#######################################################################################\n";			
		}
	//fordidden IP
	if(count($forbiddeniplist) >0)
		{
		$crawlprotect.="#######################################################################################\n";	
		$crawlprotect.="#Block bad IP\n";
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="order allow,deny\n";
		$crawlprotect.="deny from ";
		foreach($forbiddeniplist as $value)
			{
			$crawlprotect.=" ".$value;	
			}
		$crawlprotect.="\n";
		$crawlprotect.="allow from all\n";
		$crawlprotect.="#######################################################################################\n";			
		}			
	//start rewriting engine
	$crawlprotect.="<IfModule mod_rewrite.c>\n";	

	
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="# Block TRACE request to avoid risk of XST\n";
		$crawlprotect.="#######################################################################################\n";		
		$crawlprotect.="RewriteCond %{REQUEST_METHOD} ^TRACE [NC]\n";
		if($yourip==1)
			{
			foreach($trustiplist as $value)
				{
				$crawlprotect.="RewriteCond %{REMOTE_ADDR} !^".$value."\n";
				}
			}		
		$crawlprotect.="RewriteRule .* - [F]\n";
	
	//JCE Exploit
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="#Block JCE Exploit\n";
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="RewriteCond %{REQUEST_METHOD} (GET|POST) [NC]\n";
		$crawlprotect.="RewriteCond %{HTTP_USER_AGENT} BOT/0\.1\ \(BOT\ for\ JCE\)[NC]\n";
		$crawlprotect.="RewriteRule (.*) ".$redirecturl."&crawlprotecttype=codeinjection&crawlprotecturl=JCE_Exploit   [L,QSA]\n";
		
	//PHP CGI Argument Injection Remote Exploit
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="#Block PHP CGI Argument Injection Remote Exploit\n";
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="RewriteCond %{REQUEST_METHOD} (GET|POST) [NC]\n";
		$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)/?-d+allow_url_include%3d1+-d+auto_prepend_file%3dphp://input(.*)$ [NC,OR]\n";
		$crawlprotect.="RewriteCond %{HTTP_USER_AGENT} PHP\ CGI\ Argument\ Injection\ Exploiter[NC]\n";
		$crawlprotect.="RewriteRule (.*) ".$redirecturl."&crawlprotecttype=codeinjection&crawlprotecturl=PHP_CGI_Argument_Injection_Remote_Exploit   [L,QSA]\n";		
	//fordidden url			
	if(count($forbiddenurllist) > 0)
		{
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="#Block forbidden url\n";
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="RewriteCond %{REQUEST_METHOD} (GET|POST) [NC]\n";
		if($yourip==1)
			{
			foreach($trustiplist as $value)
				{
				$crawlprotect.="RewriteCond %{REMOTE_ADDR} !^".trim($value)."\n";
				}
			}
		$crawlprotect.="RewriteCond %{REQUEST_URI} ^(.*)";
		$i=1;
		foreach($forbiddenurllisttreat as $value)
			{
			if($i==count($forbiddenurllisttreat))
				{
				$crawlprotect.= trim($value)."(.*)$ [NC]\n";
				}
			else
				{
				$crawlprotect.= trim($value)."|";
				}
			$i++;
			}
			$crawlprotect.="RewriteRule (.*) ".$redirecturl."&crawlprotecttype=forbiddenurl&crawlprotecturl=%{REQUEST_URI}&crawlprotect2content   [L,QSA]\n";
		}
	//fordidden parameter	
	if(count($forbiddenparameterlist) > 0)
		{
		$crawlprotect.="#######################################################################################\n";	
		$crawlprotect.="#Block forbidden parameters\n";
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="RewriteCond %{REQUEST_METHOD} (GET|POST) [NC]\n";
	
		if($yourip==1)
			{
			foreach($trustiplist as $value)
				{
				$crawlprotect.="RewriteCond %{REMOTE_ADDR} !^".$value."\n";
				}
			}			
		$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)";
		$i=1;
		foreach($forbiddenparameterlisttreat as $value)
			{
			if($i==count($forbiddenparameterlisttreat))
				{
				$crawlprotect.= $value."(.*)$ [NC]\n";
				}
			else
				{
				$crawlprotect.= $value."|";
				}
			$i++;
			}
			$crawlprotect.="RewriteRule (.*) ".$redirecturl."&crawlprotecttype=forbiddenparameter&crawlprotecturl=%{REQUEST_URI}&crawlprotect2content   [L,QSA]\n";
		}
	//xss	
	$crawlprotect.="#######################################################################################\n";
	$crawlprotect.="#xss blocage\n";
	$crawlprotect.="#######################################################################################\n";
	$crawlprotect.="RewriteCond %{REQUEST_METHOD} (GET|POST) [NC]\n";
	if($yourip==1)
		{
		foreach($trustiplist as $value)
			{
			$crawlprotect.="RewriteCond %{REMOTE_ADDR} !^".$value."\n";
			}
		}
	foreach($trustvariablelist as $value)
		{
		$crawlprotect.="RewriteCond %{QUERY_STRING} !^(.*)".trim($value)."=(.*)$ [NC]\n";
		}

	foreach($trustsiteslist as $value)
		{
		$crawlprotect.="RewriteCond %{QUERY_STRING} !^(.*)=http://".trim($value)."(.*)$ [NC]\n";
		$crawlprotect.="RewriteCond %{QUERY_STRING} !^(.*)=http%3A%2F%2F".trim($value)."(.*)$ [NC]\n";
		$crawlprotect.="RewriteCond %{QUERY_STRING} !^(.*)=http%253A%252F%252F".trim($value)."(.*)$ [NC]\n";
		$crawlprotect.="RewriteCond %{QUERY_STRING} !^(.*)=https://".trim($value)."(.*)$ [NC]\n";
		$crawlprotect.="RewriteCond %{QUERY_STRING} !^(.*)=https%3A%2F%2F".trim($value)."(.*)$ [NC]\n";
		$crawlprotect.="RewriteCond %{QUERY_STRING} !^(.*)=https%253A%252F%252F".trim($value)."(.*)$ [NC]\n";
		$crawlprotect.="RewriteCond %{QUERY_STRING} !^(.*)=ftp://".trim($value)."(.*)$ [NC]\n";
		$crawlprotect.="RewriteCond %{QUERY_STRING} !^(.*)=ftp%3A%2F%2F".trim($value)."(.*)$ [NC]\n";
		$crawlprotect.="RewriteCond %{QUERY_STRING} !^(.*)=ftp%253A%252F%252F".trim($value)."(.*)$ [NC]\n";		
		}
	if(count($xssname)>0)
		{
		$crawlprotect.="###########################Avoid issue with scripts used###############################\n";	
		foreach($xssname as $key => $value)
			{
			if($xsstype[$key]=='query')
				{
				$crawlprotect.="#".$xssname[$key]."\n";
				$crawlprotect.="RewriteCond %{QUERY_STRING} !^(.*)".$xsscontent[$key]."(.*)$ [NC]\n";	
				}	
			else
				{
				$crawlprotect.="#".$xssname[$key]."\n";
				$crawlprotect.="RewriteCond %{REQUEST_URI} !^(.*)".$xsscontent[$key]."(.*)$ [NC]\n";	
				}				
			}
		$crawlprotect.="#######################################################################################\n";		
		}					
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(%3D|=|%3A|%09)(.*)(h|%68|%48)(t|%74|%54)(t|%74|%54)(p|%70|%50)(s|%73|%53)(%3A|:)(/|%2F){2}(.*)$ [NC,OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(%3D|=|%3A|%09)(.*)(h|%68|%48)(t|%74|%54)(t|%74|%54)(p|%70|%50)(s|%73|%53)%3a(%3A|:)(/|%2F){2}(.*)$ [NC,OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(%3D|=|%3A|%09)(.*)(h|%68|%48)(t|%74|%54)(t|%74|%54)(p|%70|%50)(%3A|:)(/|%2F){2}(.*)$ [NC,OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(%3D|=|%3A|%09)(.*)(h|%68|%48)(t|%74|%54)(t|%74|%54)(p|%70|%50)%3a(%3A|:)(/|%2F){2}(.*)$ [NC,OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(%3D|=|%3A|%09)(.*)(f|%66|%46)(t|%74|%54)(p|%70|%50)(%3A|:)(/|%2F){2}(.*)$ [NC,OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(%3D|=|%3A|%09)(.*)(h|%68|%48)(t|%74|%54)%20(t|%74|%54)(p|%70|%50)(%3A|:)(/|%2F){2}(.*)$ [NC,OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(%3D|=|%3A|%09)(.*)(h|%68|%48)(t|%74|%54)(t|%74|%54)%20(p|%70|%50)(%3A|:)(/|%2F){2}(.*)$ [NC,OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(%3D|=|%3A|%09)(.*)(h|%68|%48)(t|%74|%54)(t|%74|%54)(p|%70|%50)%20(%3A|:)(/|%2F){2}(.*)$ [NC,OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(%3D|=|%3A|%09)(.*)(h|%68|%48)%20(t|%74|%54)(t|%74|%54)(p|%70|%50)(%3A|:)(/|%2F){2}(.*)$ [NC]\n";


		$crawlprotect.="RewriteRule (.*) ".$redirecturl."&crawlprotecttype=xss&crawlprotecturl=%{REQUEST_URI}&crawlprotect2content   [L,QSA]\n";

	//sql injection
	$totsql= $sq1+$sq2+$sq3+$sq4+$sq5+$sq6+$sq7;
	if($totsql >= 1)
		{
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="#Sql injection blocage\n";
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="RewriteCond %{REQUEST_METHOD} (GET|POST) [NC]\n";
		
		if($yourip==1)
			{
			foreach($trustiplist as $value)
				{
				$crawlprotect.="RewriteCond %{REMOTE_ADDR} !^".$value."\n";
				}
			}			
		if(count($sqlinjectname)>0)
			{
			$crawlprotect.="###########################Avoid issue with scripts used###############################\n";	
			foreach($sqlinjectname as $key => $value)
				{
				if($sqlinjecttype[$key]=='query')
					{
					$crawlprotect.="#".$sqlinjectname[$key]."\n";
					$crawlprotect.="RewriteCond %{QUERY_STRING} !^(.*)".$sqlinjectcontent[$key]."(.*)$ [NC]\n";	
					}	
				else
					{
					$crawlprotect.="#".$sqlinjectname[$key]."\n";
					$crawlprotect.="RewriteCond %{REQUEST_URI} !^(.*)".$sqlinjectcontent[$key]."(.*)$ [NC]\n";	
					}				
				}
			$crawlprotect.="#######################################################################################\n";		
			}			
		$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(";
		$isql=1;
		if($sq1==1)
			{
			$crawlprotect.="%20(S|%73|%53)(E|%65|%45)(L|%6C|%4C)(E|%65|%45)(C|%63|%43)(T|%74|%54)%20|\+(S|%73|%53)(E|%65|%45)(L|%6C|%4C)(E|%65|%45)(C|%63|%43)(T|%74|%54)\+|/\*(.*)(S|%73|%53)(E|%65|%45)(L|%6C|%4C)(E|%65|%45)(C|%63|%43)(T|%74|%54)\*/";
			if($totsql> $isql)
				{
				$crawlprotect.="|";	
				$isql++;
				}
			}
		if($sq2==1)
			{
			$crawlprotect.="%20(I|%69|%49)(N|%6E|%4E)(S|%73|%53)(E|%65|%45)(R|%72|%52)(T|%74|%54)%20|\+(I|%69|%49)(N|%6E|%4E)(S|%73|%53)(E|%65|%45)(R|%72|%52)(T|%74|%54)\+|/\*(.*)(I|%69|%49)(N|%6E|%4E)(S|%73|%53)(E|%65|%45)(R|%72|%52)(T|%74|%54)\*/";
			if($totsql> $isql)
				{
				$crawlprotect.="|";	
				$isql++;
				}
			}	
		if($sq3==1)
			{
			$crawlprotect.="%20(U|%75|%55)(P|%70|%50)(D|%64|%44)(A|%61|%41)(T|%74|%54)(E|%65|%45)%20|\+(U|%75|%55)(P|%70|%50)(D|%64|%44)(A|%61|%41)(T|%74|%54)(E|%65|%45)\+|/\*(.*)(U|%75|%55)(P|%70|%50)(D|%64|%44)(A|%61|%41)(T|%74|%54)(E|%65|%45)\*/";
			if($totsql> $isql)
				{
				$crawlprotect.="|";	
				$isql++;
				}	
			}
		if($sq4==1)
			{
			$crawlprotect.="%20(R|%72|%52)(E|%65|%45)(P|%70|%50)(L|%6C|%4C)(A|%61|%41)(C|%63|%43)(E|%65|%45)%20|\+(R|%72|%52)(E|%65|%45)(P|%70|%50)(L|%6C|%4C)(A|%61|%41)(C|%63|%43)(E|%65|%45)\+|/\*(.*)(R|%72|%52)(E|%65|%45)(P|%70|%50)(L|%6C|%4C)(A|%61|%41)(C|%63|%43)(E|%65|%45)\*/";
			if($totsql> $isql)
				{
				$crawlprotect.="|";	
				$isql++;
				}
			}			
		if($sq5==1)
			{
			$crawlprotect.="%20(W|%77|%57)(H|%68|%48)(E|%65|%45)(R|%72|%52)(E|%65|%45)%20|\+(W|%77|%57)(H|%68|%48)(E|%65|%45)(R|%72|%52)(E|%65|%45)\+|/\*(.*)(W|%77|%57)(H|%68|%48)(E|%65|%45)(R|%72|%52)(E|%65|%45)\*/";
			if($totsql> $isql)
				{
				$crawlprotect.="|";	
				$isql++;
				}
			}				
		if($sq6==1)
			{
			$crawlprotect.="%20(L|%6C|%4C)(I|%69|%49)(K|%6B|%4B)(E|%65|%45)%20|\+(L|%6C|%4C)(I|%69|%49)(K|%6B|%4B)(E|%65|%45)\+|/\*(.*)(L|%6C|%4C)(I|%69|%49)(K|%6B|%4B)(E|%65|%45)\*/";
			if($totsql> $isql)
				{
				$crawlprotect.="|";	
				$isql++;
				}	
			}				
		if($sq7==1)
			{
			$crawlprotect.="%20(O|%6F|%4F)(R|%72|%52)%20|\+(O|%6F|%4F)(R|%72|%52)\+|/\*(.*)(O|%6F|%4F)(R|%72|%52)\*/|%7C%7C";
			}				
						
	$crawlprotect.=")(.*)$ [NC]\n";


		$crawlprotect.="RewriteRule (.*) ".$redirecturl."&crawlprotecttype=sqlinjection&crawlprotecturl=%{REQUEST_URI}&crawlprotect2content   [L,QSA]\n";


	}
	//code injection
	$crawlprotect.="#######################################################################################\n";
	$crawlprotect.="#Code injection blocage\n";
	$crawlprotect.="#######################################################################################\n";
	$crawlprotect.="RewriteCond %{REQUEST_METHOD} (GET|POST) [NC]\n";
	if($yourip==1)
		{
		foreach($trustiplist as $value)
			{
			$crawlprotect.="RewriteCond %{REMOTE_ADDR} !^".$value."\n";
			}
		}
	if(count($codeinjectname)>0)
		{
		$crawlprotect.="###########################Avoid issue with scripts used###############################\n";	
		foreach($codeinjectname as $key => $value)
			{
			if($codeinjecttype[$key]=='query')
				{
				$crawlprotect.="#".$codeinjectname[$key]."\n";
				$crawlprotect.="RewriteCond %{QUERY_STRING} !^(.*)".$codeinjectcontent[$key]."(.*)$ [NC]\n";	
				}	
			else
				{
				$crawlprotect.="#".$codeinjectname[$key]."\n";
				$crawlprotect.="RewriteCond %{REQUEST_URI} !^(.*)".$codeinjectcontent[$key]."(.*)$ [NC]\n";	
				}				
			}
		$crawlprotect.="#######################################################################################\n";		
		}		
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(%3C|<)/?(s|%73|%53)(c|%63|%43)(r|%72|%52)(i|%69|%49)(p|%70|%50)(t|%74|%54)(.*)$ [NC,OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(%3D|=)?(j|%6A|%4A)(a|%61|%41)(v|%76|%56)(a|%61|%31)(s|%73|%53)(c|%63|%43)(r|%72|%52)(i|%69|%49)(p|%70|%50)(t|%74|%54)(%3A|:)(.*)$ [NC,OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(d|%64|%44)(o|%6F|%4F)(c|%63|%43)(u|%75|%55)(m|%6D|%4D)(e|%65|%45)(n|%6E|%4E)(t|%74|%54)\.(l|%6C|%4C)(o|%6F|%4F)(c|%63|%43)(a|%61|%41)(t|%74|%54)(i|%69|%49)(o|%6F|%4F)(n|%6E|%4E)\.(h|%68|%48)(r|%72|%52)(e|%65|%45)(f|%66|%46)(.*)$ [OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(b|%62|%42)(a|%61|%41)(s|%73|%53)(e|%65|%45)(6|%36)(4|%34)(_|%5F)(e|%65|%45)(n|%6E|%4E)(c|%63|%43)(o|%6F|%4F)(d|%64|%44)(e|%65|%45)(.*)$ [OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(G|%67|%47)(L|%6C|%4C)(O|%6F|%4F)(B|%62|%42)(A|%61|%41)(L|%6C|%4C)(S|%73|%53)(=|[|%[0-9A-Z]{0,2})(.*)$ [OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(_|%5F)(R|%72|%52)(E|%65|%45)(Q|%71|%51)(U|%75|%55)(E|%65|%45)(S|%73|%53)(T|%74|%54)(=|[|%[0-9A-Z]{0,2})(.*)$ [OR]\n";
	$crawlprotect.="RewriteCond %{REQUEST_URI} ^(.*)(_|%5F)(v|%76|%56)(t|%74|%54)(i|%69|%49)(.*)$ [OR]\n";
	$crawlprotect.="RewriteCond %{REQUEST_URI} ^(.*)(M|%4D)(S|%53)(O|%4F)(f|%66)(f|%66)(i|%69)(c|%63)(e|%65)(.*)$ [OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(/|%2F)(e|%65)(t|%74)(c|%63)(/|%2F)(p|%70)(a|%61)(s|%73)(s|%73)(w|%77)(d|%64)(.*)$ [OR]\n";
	$crawlprotect.="RewriteCond %{REQUEST_URI} ^(.*)(S|%53)(h|%68)(e|%65)(l|%6C)(l|%6C)(A|%41)(d|%64)(r|%72)(e|%65)(s|%73)(i|%69).(T|%54)(X|%58)(T|%54)(.*)$ [OR]\n";
	$crawlprotect.="RewriteCond %{REQUEST_URI} ^(.*)\[(e|%65)(v|%76)(i|%69)(l|%6C)(_|%5F)(r|%72)(o|%6F)(o|%6F)(t|%74)\]?(.*)$ [OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)\.\./\.\./\.\./(.*)$ [OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)(/|%2F)(p|%70)(r|%72)(o|%6F)(c|%63)(/|%2F)(s|%73)(e|%65)(l|%C)(f|%66)(/|%2F)(e|%65)(n|%6E)(v|%76)(i|%69)(r|%72)(o|%6F)(n|%6E)(.*)$\n";


	$crawlprotect.="RewriteRule (.*) ".$redirecturl."&crawlprotecttype=codeinjection&crawlprotecturl=%{REQUEST_URI}&crawlprotect2content   [L,QSA]\n";

	//bad bots
	if($countUA >= 1 && $blockUA==1)
		{
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="#Bad bot and site copier blocage\n";
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="RewriteCond %{HTTP_USER_AGENT} ";
		
		$iUA=1;
		foreach($baduseragent as $key => $value)
			{
			$nameUA = "UA-".$key;
			if($valid[$nameUA] == 1)
				{
				$crawlprotect.= $value;
				if($countUA> $iUA)
					{
					$crawlprotect.="|";	
					$iUA++;
					}
				}
			}
		$crawlprotect.=" [OR]\n";
		
		$crawlprotect.="RewriteCond %{HTTP_REFERER} ^XXX\n";
		if($yourip==1)
			{
			foreach($trustiplist as $value)
				{
				$crawlprotect.="RewriteCond %{REMOTE_ADDR} !^".$value."\n";
				}
			}


			$crawlprotect.="RewriteRule (.*) ".$redirecturl."&crawlprotecttype=badbot&crawlprotecturl=%{REQUEST_URI}&crawlprotect2content   [L,QSA]\n";

		}
	//shell
	$crawlprotect.="#######################################################################################\n";
	$crawlprotect.="# Filter against shell attacks\n";
	$crawlprotect.="#######################################################################################\n";
	if(count($shellname)>0)
		{
		$crawlprotect.="###########################Avoid issue with scripts used###############################\n";		
		foreach($shellname as $key => $value)
			{
			if($shelltype[$key]=='query')
				{
				$crawlprotect.="#".$shellname[$key]."\n";
				$crawlprotect.="RewriteCond %{QUERY_STRING} !^(.*)".$shellcontent[$key]."(.*)$ [NC]\n";	
				}	
			else
				{
				$crawlprotect.="#".$shellname[$key]."\n";
				$crawlprotect.="RewriteCond %{REQUEST_URI} !^(.*)".$shellcontent[$key]."(.*)$ [NC]\n";	
				}				
			}
		$crawlprotect.="#######################################################################################\n";	
		}		
	$crawlprotect.="RewriteCond %{REQUEST_URI} .*((php|my)?shell|remview.*|phpremoteview.*|sshphp.*|pcom|nstview.*|c99|r57|webadmin.*|phpget.*|phpwriter.*|fileditor.*|locus7.*|storm7.*)\.(p?s?x?htm?l?|txt|aspx?|cfml?|cgi|pl|php[3-9]{0,1}|jsp?|sql|xml) [NC,OR]\n";
	$crawlprotect.="RewriteCond %{REQUEST_METHOD} (GET|POST) [NC]\n";
	
	
$totshell = $s1+$s2+$s3+$s4+$s5+$s6+$s7+$s8+$s9+$s10+$s11+$s12+$s13+$s14+$s15+$s16+$s17+$s18+$s19+$s20+$s21+$s22+$s23+$s24+$s25+$s26+$s27+$s28;

	if($totshell >= 1)
		{	
		$crawlprotect.="RewriteCond %{QUERY_STRING} ^(.*)([-_a-z]{1,15})=(";
		$ishell=1;	
		if($s1==1)
			{
			$crawlprotect.="chmod";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}	
		if($s2==1)
			{
			$crawlprotect.="chdir";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}	
		if($s3==1)
			{
			$crawlprotect.="mkdir";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}	
		if($s4==1)
			{
			$crawlprotect.="rmdir";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}	
		if($s5==1)
			{
			$crawlprotect.="clear";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}	
		if($s6==1)
			{
			$crawlprotect.="whoami";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}
		if($s7==1)
			{
			$crawlprotect.="uname";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}			
		if($s8==1)
			{
			$crawlprotect.="unzip";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}			
		if($s9==1)
			{
			$crawlprotect.="gzip";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}			
		if($s10==1)
			{
			$crawlprotect.="gunzip";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}			
		if($s11==1)
			{
			$crawlprotect.="grep";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}			
		if($s12==1)
			{
			$crawlprotect.="umask";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}			
		if($s13==1)
			{
			$crawlprotect.="telnet";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}			
		if($s14==1)
			{
			$crawlprotect.="ssh";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}			
		if($s15==1)
			{
			$crawlprotect.="ftp";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}			
		if($s16==1)
			{
			$crawlprotect.="mkmode";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}				
		if($s17==1)
			{
			$crawlprotect.="logname";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}	
		if($s18==1)
			{
			$crawlprotect.="edit_file";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}	
		if($s19==1)
			{
			$crawlprotect.="search_text";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}	
		if($s20==1)
			{
			$crawlprotect.="find_text";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}	
		if($s21==1)
			{
			$crawlprotect.="php_eval";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}
		if($s22==1)
			{
			$crawlprotect.="download_file";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}			
		if($s23==1)
			{
			$crawlprotect.="ftp_file_down";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}
		if($s24==1)
			{
			$crawlprotect.="ftp_file_up";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}			
		if($s25==1)
			{
			$crawlprotect.="ftp_brute";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}			
		if($s26==1)
			{
			$crawlprotect.="mail_file";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}
		if($s27==1)
			{
			$crawlprotect.="mysql_dump";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}			
		if($s28==1)
			{
			$crawlprotect.="db_query";
			if($totshell> $ishell)
				{
				$crawlprotect.="|";	
				$ishell++;
				}
			}			

		$crawlprotect.=")([^a-zA-Z0-9].+)*$ [OR]\n";										
		}	
	
	
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^work_dir=.*$ [OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^command=.*&output.*$ [OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^nts_[a-z0-9_]{0,10}=.*$ [OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^c=(t|setup|codes)$ [OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^act=((about|cmd|selfremove|chbd|trojan|backc|massbrowsersploit|exploits|grablogins|upload.*)|((chmod|f)&f=.*))$ [OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^act=(ls|search|fsbuff|encoder|tools|processes|ftpquickbrute|security|sql|eval|update|feedback|cmd|gofile|mkfile)&d=.*$ [OR]\n";
	$crawlprotect.="RewriteCond %{QUERY_STRING} ^&?c=(l?v?i?&d=|v&fnot=|setup&ref=|l&r=|d&d=|tree&d|t&d=|e&d=|i&d=|codes|md5crack).*$\n";

	if($yourip==1)
		{
		foreach($trustiplist as $value)
			{
			$crawlprotect.="RewriteCond %{REMOTE_ADDR} !^".$value."\n";
			}
		}


		$crawlprotect.="RewriteRule (.*) ".$redirecturl."&crawlprotecttype=shell&crawlprotecturl=%{REQUEST_URI}&crawlprotect2content   [L,QSA]\n";

//start inclusion for display in order to get $hostsite value for hotlinking blocage
//include menu
include ("include/menusite.php");
include ("include/menumain.php");
//--------------------------------------------------------------------------------
	if (preg_match('#^http://#i', $hostsite))
		{
		$hostsite=substr($hostsite,7);
		} 
	if (preg_match('#^www.#i', $hostsite))
		{
		$hostsite=substr($hostsite,4);
		}
	$hostsite=str_replace(".","\.",$hostsite);	
		
		
	//fordidden referer	
	if(count($forbiddenrefererlisttreat) > 0)
		{
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="#Block referer spammer\n";
		$crawlprotect.="#######################################################################################\n";	
		if($yourip==1)
			{
			foreach($trustiplist as $value)
				{
				$crawlprotect.="RewriteCond %{REMOTE_ADDR} !^".$value."\n";
				}
			}
		$crawlprotect.="RewriteCond %{HTTP_REFERER} !^http://(www\.)?".$hostsite."(/)?.*$ [NC]\n";			
			
		$crawlprotect.="RewriteCond %{HTTP_REFERER} (.*)(";
		$i=1;
		foreach($forbiddenrefererlisttreat as $value)
			{
			if($i==count($forbiddenrefererlisttreat))
				{
				$crawlprotect.= $value.")(.*)\n";
				}
			else
				{
				if(($i % 20)== 0)
					{
					$crawlprotect.= $value.")(.*) [OR]\n";	
					$crawlprotect.="RewriteCond %{HTTP_REFERER}  (.*)(";	
					}
				else
					{					
					$crawlprotect.= $value."|";
					}
				}
			$i++;
			}				
	
		$crawlprotect.="RewriteRule (.*) ".$redirecturl."&crawlprotecttype=spamreferer&crawlprotecturl=%{HTTP_REFERER}  [L]\n";
		}
	//hotlink
	if($hotlink==1)
		{		 		
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="#No HotLinking\n";
		$crawlprotect.="#######################################################################################\n";	
		$crawlprotect.="RewriteCond %{HTTP_REFERER} !^http://(www\.)?".$hostsite."(/)?.*$ [NC]\n";
		if(count($hotlinkoklist) > 0)
			{					
			foreach($hotlinkoklist as $value)
				{
				$value= trim($value);
				if (preg_match('#^http://#i', $value))
					{
					$value=substr($value,7);
					} 
				if (preg_match('#^www.#i', $value))
					{
					$value=substr($value,4);
					}
				$value=str_replace(".","\.",$value);	
					
				$crawlprotect.="RewriteCond %{HTTP_REFERER} !^http://(www\.)?".$value."(/)?.*$ [NC]\n";
				}
			}
		
		$crawlprotect.="RewriteCond %{HTTP_REFERER} !^$ \n";		
		$crawlprotect.="RewriteCond %{HTTP_USER_AGENT} !^Googlebot [NC]\n";	
		$crawlprotect.="RewriteCond %{HTTP_USER_AGENT} !^Googlebot-Image [NC]\n";
		$crawlprotect.="RewriteCond %{HTTP_USER_AGENT} !^Googlebot-Mobile [NC]\n";
		$crawlprotect.="RewriteCond %{HTTP_USER_AGENT} !^Mediapartners-Google [NC]\n";
		$crawlprotect.="RewriteCond %{HTTP_USER_AGENT} !^Msnbot [NC]\n";
		$crawlprotect.="RewriteCond %{HTTP_USER_AGENT} !^bingbot [NC]\n";
		$crawlprotect.="RewriteCond %{HTTP_USER_AGENT} !^slurp [NC]\n";
		$crawlprotect.="RewriteCond %{HTTP_REFERER} !^(.*)google(.*)$\n";
		$crawlprotect.="RewriteCond %{HTTP_REFERER} !^(.*)bing(.*)$\n";
		$crawlprotect.="RewriteCond %{HTTP_REFERER} !^(.*)yahoo(.*)$\n";
		$crawlprotect.="RewriteCond %{HTTP_REFERER} !^(.*)voila(.*)$\n";
		$crawlprotect.="RewriteCond %{HTTP_REFERER} !^(.*)baidu(.*)$\n";
		$crawlprotect.="RewriteCond %{HTTP_REFERER} !^(.*)yandex(.*)$\n";
		if($url_image!='')
			{
			if (preg_match('#^http://#i', $url_image))
				{
				$url_image=substr($url_image,7);						
				}
				
			$parseimageurl= parse_url('http://'.$url_image);
			$imagelink = $parseimageurl['path'];
			$imagelink = str_replace(".","\.",$imagelink);
			$crawlprotect.="RewriteCond %{REQUEST_URI} !^(.*)$imagelink(.*)$\n";	
				 
			}
		$crawlprotect.="RewriteRule \.(gif|jpe?g|png|bmp)$ ".$redirecturl."&crawlprotecttype=hotlinking&crawlprotecturl=%{HTTP_REFERER}&crawlprotect2content   [L,NC,QSA]\n";		
		}
		

		
		
		
		
		$crawlprotect.="</ifModule>\n";	
	//actual htaccess
	if($actual !='')
		{
		$crawlprotect.="#######################################################################################\n";
		$crawlprotect.="#Existing File\n";	
		$crawlprotect.="#######################################################################################\n";	
		$htaccess2=$crawlprotect.stripslashes($actual);
		}
	else
		{
		$htaccess2=$crawlprotect;
		}

	
	
//----------------------------------------------------
//Display
	
		
echo "<div id='centre'>";
echo "<h1>".$language['yourownhtaccess']."</h1><br>";
if($okcreate==0)
	{
	echo"<div class='htaccess'><pre>\n";
	echo htmlspecialchars($htaccess2);
	echo"</pre></div><br><br>\n";
	echo"<form action=\"http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."\" method=\"POST\">\n";
	echo"<input type=\"hidden\" name ='navig' value='4'>\n";
	echo"<input type=\"hidden\" name ='okcreate' value='1'>\n";	
	echo"<input type=\"hidden\" name ='yourip' value='".$yourip."'>\n";	
	echo"<input type=\"hidden\" name ='trustip' value='".$trustip."'>\n";		
	foreach ($listscript as $key => $value)
		{
		$namevalue =str_replace(' ','--',$value);
		if( in_array($value , $listscriptused))
			{
			echo"<input type=\"hidden\" name='".$namevalue."' value=\"1\">\n";
			} 
		else
			{
			echo"<input type=\"hidden\" name='".$namevalue."' value=\"0\">\n";			
			}
		}	
	echo"<input type=\"hidden\" name ='trustsites' value='".$trustsites."'>\n";	
	echo"<input type=\"hidden\" name ='trustvariable' value='".$trustvariable."'>\n";
	for($i=1; $i<=7; $i++)
		{
		$sql="sq".$i;
		if(isset($_POST[$sql]))
			{
			echo"<input type=\"hidden\" name ='sq".$i."' value='1'>\n";		
			}
		else
			{
			echo"<input type=\"hidden\" name ='sq".$i."' value='0'>\n";
			}			
		}
	for($i=1; $i<=28; $i++)
		{
		$shell="s".$i;	
		if(isset($_POST[$shell]))
			{
			echo"<input type=\"hidden\" name ='s".$i."' value='1'>\n";
			}
		else
			{
			echo"<input type=\"hidden\" name ='s".$i."' value='0'>\n";
			}			
		}
	foreach($baduseragent as $key => $value)
		{
		$nameUA = "UA-".$key;		
		if (!isset($_POST[$nameUA])  || $_POST[$nameUA]==0)
			{
			echo"<input type=\"hidden\" name='".$nameUA."' value=\"0\">\n";
			}
		else
			{
			echo"<input type=\"hidden\" name='".$nameUA."' value=\"1\">\n";
			}
		}
	echo "<input type=\"hidden\" name ='forbiddenip' value='".$forbiddenip."'>\n";	
	echo "<input type=\"hidden\" name ='forbiddenurl' value='".$forbiddenurl."'>\n";
	echo "<input type=\"hidden\" name ='forbiddenparameter' value='".$forbiddenparameter."'>\n";
	echo "<input type=\"hidden\" name ='forbiddenword' value='".$forbiddenword."'>\n";
	echo "<input type=\"hidden\" name ='forbiddenreferer' value='".$forbiddenreferer."'>\n";
	echo "<input type=\"hidden\" name ='blockUA' value='".$blockUA."'>\n";	
	echo "<input type=\"hidden\" name ='hotlink' value='".$hotlink."'>\n";	
	echo "<input type=\"hidden\" name ='autoprepend' value='".$autoprepend."'>\n";		
	echo "<input type=\"hidden\" name ='validkey' value='".$secret_key."'>\n";
	echo "<input type=\"hidden\" name ='hotlinkok' value='".$hotlinkok."'>\n";
	echo "<input type=\"hidden\" name ='listindex' value='".$listindex."'>\n";
	echo "<input type=\"hidden\" name ='badfile' value='".$badfile."'>\n";		
	echo "<input type=\"hidden\" name ='url_image' value='".$url_image."'>\n";				
	echo "<div align='center'><br><input name='ok' type='submit'  value='".$language['installhtaccess']."' size='20' ></div><br><br>";
	}

if($okcreate==1)
	{
	//modification propose by Fabrice Jossa	
	$actual=str_replace("\r\n", "\n", $actual);   //Pour supprimer les retours à la ligne supplémentaires
    if (get_magic_quotes_gpc()) {  // Utilise stripslashes si magic_quotes est actif
    $htaccess=$crawlprotect.stripslashes($actual);
    } else {
    $htaccess=$crawlprotect.$actual;}


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
		fwrite($file, $htaccess);
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
		@chmod($filename, 0444);
		}
	if($filereplace==1)
		{
		echo "<div style=\"padding-left:20px;padding-right:20px;\"><p>".$language['newhtaccessok']."</p></div>\n";
		//case spam filter using php tag
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
			echo "<br><br><h1>".$language['tagforspam']."</h1>";
			echo "<div class=\"zonehtaccess2\">";	
			echo"<table><tr><td align='left' style=\"padding-left:20px;padding-right:20px;\">";	
			echo "<p>".$language['paramhtaccesstext24']."</p>";	
			echo "<b>require_once(\"".$path."/include/cppf.php\");</b>";
			echo"</td></tr></table><br></div>";
			}			
		}
	else
		{
		echo "<div class='alert3'>".$language['newhtaccessnook']."</div><br><br>\n";
		echo"<div class='htaccess'><pre>\n";
		echo htmlspecialchars($htaccess2);
		
		echo"</pre></div><br><br>\n";
		//case spam filter using php tag
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
			echo "<br><br><h1>".$language['tagforspam']."</h1>";
			echo "<div class=\"zonehtaccess2\">";	
			echo"<table><tr><td align='left' style=\"padding-left:20px;padding-right:20px;\">";		
			echo "<p>".$language['paramhtaccesstext24']."</p>";	
			echo "<b>require_once(\"".$path."/include/cppf.php\");</b>";
			echo"</td></tr></table><br></div>";
			}
		else
			{
			echo "<br><br><h1>".$language['tagforspam']."</h1>";
			echo "<div class=\"zonehtaccess2\">";	
			echo"<table><tr><td align='left' style=\"padding-left:20px;padding-right:20px;\">";	
			echo "<p>".$language['paramhtaccesstext21']."</p>";	
			echo"</td></tr></table><br></div>";			
			}	
			
			
			
			
			
			
			
								
		}		
	}	
}	
 ?>

        



