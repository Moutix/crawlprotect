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
// file: cppf.php
//----------------------------------------------------------------------
//  Last update: 19/10/2013
//----------------------------------------------------------------------
error_reporting(0);
if (!defined('IN_CRAWLT'))
	{
	//cookie to validate tag presence
	setcookie("crawlprotecttag", "present", time() + (86400), "/");
	//connection to database
	require_once("FILE_PATH/include/connection.php");
	$connexion = mysql_connect($crawlthost, $crawltuser, $crawltpassword);
	$selection = mysql_select_db($crawltdb);
	//collect htaccess existing parameter
	$sql = "SELECT forbiddenword, trustip FROM crawlp_site_setting";
	$requete = mysql_query($sql, $connexion);
	$nbrresult=mysql_num_rows($requete);
	$forbiddenword='';
	$trustip='';
	if ($nbrresult >= 1) {
		while ($ligne = mysql_fetch_assoc($requete)) {
		$forbiddenword = $forbiddenword.",".$ligne['forbiddenword'];
		$trustip = $trustip.",".$ligne['trustip'];					
		}
	}
	else {
	$forbiddenword = '';
	$trustip='';
	}
	$forbiddenword= rtrim($forbiddenword,',');
	$tabforbiddenword=explode(',',$forbiddenword);
	$trustip= rtrim($trustip,',');
	$tabtrustip=explode(',',$trustip);	
	$listsql = array();
	$listsql = array(
	"0" => "select",
	"1" => "insert",
	"2" => "update",
	"3" => "replace",
	"4" => "delete",
	"5" => "drop",
	"6" => "union",
	"7" => "group by",
	);
		
		
	if((isset($_POST['validkey']) && $_POST['validkey']==$secret_key)|| in_array($_SERVER['REMOTE_ADDR'],$tabtrustip) )
		{
		}
	else
		{
		
		foreach(range('a','z') as $i) {
		$crawlptest[]="or".$i;
		$crawlptest[]=$i."or";
		$crawlptest[]="select".$i;
		$crawlptest[]="insert".$i;
		$crawlptest[]="update".$i;
		$crawlptest[]="replace".$i;
		$crawlptest[]="delete".$i;
		$crawlptest[]="drop".$i;
		$crawlptest[]="union".$i;
		$crawlptest[]="d\'".$i;
		$crawlptest[]="l\'".$i;
		$crawlptest[]="m\'".$i;		
		$crawlptest[]="t\'".$i;		
		$crawlptest[]="s\'".$i;		
		$crawlptest[]="i\'".$i;	
		$crawlptest[]="u\'".$i;			
		$crawlptest[]="e\'".$i;		
		$crawlptest[]="y\'".$i;
		$crawlptest[]="c\'".$i;
		$crawlptest[]="j\'".$i;
		$crawlptest[]="i select the";
		$crawlptest[]="you select the";
		$crawlptest[]="we select the";
		$crawlptest[]="he select the";
		$crawlptest[]="i select a";
		$crawlptest[]="you select a";
		$crawlptest[]="we select a";
		$crawlptest[]="he select a";
		$crawlptest[]="i select one";
		$crawlptest[]="you select one";
		$crawlptest[]="we select one";
		$crawlptest[]="he select one";		
		}
		

		foreach($_POST as $key=> $value)
			{
			$value=strtolower($value);				
			//spammer protection	
			foreach($tabforbiddenword as $word)
				{
				$word=strtolower($word);
	
					
				if(preg_match("/".$word."/i", $value) && $word!='')
					{
					header("Location: URL_REDIRECT?crawlprotecttype=spammer&crawlprotectcontent=".$word."&crawlprotecturl=".$_SERVER['REQUEST_URI']."");
					exit;			
					}	
				}
			//sqlinjection protection				
			$value = str_replace('||','or',$value);
			//avoid false detection	
			$value = str_replace($crawlptest,'toto',$value);
					
			foreach($listsql as $sql)
				{				
				if( (preg_match("/".$sql."/i", $value) && (preg_match("/(\%27)|(\')/i", $value)|| preg_match("/((\%3D)|(=))[^\n]*((\%27)|(\')|(\-\-\s)|(\%3B)|(;))/i", $value))) || preg_match("/\w*((\%27)|(\'))((\%6F)|o|(\%4F))((\%72)|r|(\%52))/i", $value)|| preg_match("/(\-\-\s)|(\'\/\*)/i", $value) )
					{
					header("Location: URL_REDIRECT?crawlprotecttype=sqlinjection&crawlprotecturl=".$_SERVER['REQUEST_URI']."&crawlprotect2contentvariable_POST=".urlencode($value)."");
					exit;			
					}	
				}			
				
				
			}
		}
	}
else
	{
	$cppf='3';	
	}	
	
	
	
?>
