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
// file: post.php
//----------------------------------------------------------------------
//  Last update: 19/02/2012
//----------------------------------------------------------------------
if (!isset($crawltlang)) {
	if (isset($_POST['lang'])) {
		$crawltlang = htmlspecialchars($_POST['lang']);
	} else {
		$crawltlang = 'english';
	}
}
if (isset($_POST['newlang'])) {
	$crawltnewlang = htmlspecialchars($_POST['newlang']);
} else {
	$crawltnewlang = 'english';
}
if (isset($_POST['idmysql'])) {
	$idmysql = $_POST['idmysql'];
} else {
	$idmysql = '';
}
if (isset($_POST['passwordmysql'])) {
	$passwordmysql = $_POST['passwordmysql'];
} else {
	$passwordmysql = '';
}
if (isset($_POST['hostmysql'])) {
	$hostmysql = $_POST['hostmysql'];
} else {
	$hostmysql = 'localhost';
}
if (isset($_POST['basemysql'])) {
	$basemysql = $_POST['basemysql'];
} else {
	$basemysql = '';
}





if (isset($_POST['sessionid'])) {
	$crawltsessionid = (int)$_POST['sessionid'];
} else {
	if (isset($_GET['sessionid'])) {
		$crawltsessionid = (int)$_GET['sessionid'];
	}
}
if (isset($_POST['sessionid1'])) {
	$crawltsessionid1 = (int)$_POST['sessionid1'];
} else {
	if (isset($_GET['sessionid1'])) {
		$crawltsessionid1 = (int)$_GET['sessionid1'];
	} else {
		$crawltsessionid1 = 0;
	}
}
if (isset($_POST['sessionid2'])) {
	$crawltsessionid2 = (int)$_POST['sessionid2'];
} else {
	if (isset($_GET['sessionid2'])) {
		$crawltsessionid2 = (int)$_GET['sessionid2'];
	} else {
		$crawltsessionid2 = 0;
	}
}
if (isset($_POST['sessionid3'])) {
	$crawltsessionid3 = (int)$_POST['sessionid3'];
} else {
	if (isset($_GET['sessionid3'])) {
		$crawltsessionid3 = (int)$_GET['sessionid3'];
	} else {
		$crawltsessionid3 = 0;
	}
}
if (isset($_POST['sessionid4'])) {
	$crawltsessionid4 = (int)$_POST['sessionid4'];
} else {
	if (isset($_GET['sessionid4'])) {
		$crawltsessionid4 = (int)$_GET['sessionid4'];
	} else {
		$crawltsessionid4 = 0;
	}
}
if (isset($_POST['sessionid5'])) {
	$crawltsessionid5 = (int)$_POST['sessionid5'];
} else {
	if (isset($_GET['sessionid5'])) {
		$crawltsessionid5 = (int)$_GET['sessionid5'];
	} else {
		$crawltsessionid5 = 0;
	}
}
if (isset($_POST['sessionid6'])) {
	$crawltsessionid6 = (int)$_POST['sessionid6'];
} else {
	if (isset($_GET['sessionid6'])) {
		$crawltsessionid6 = (int)$_GET['sessionid6'];
	} else {
		$crawltsessionid6 = 0;
	}
}
if (isset($_POST['sessionid7'])) {
	$crawltsessionid7 = (int)$_POST['sessionid7'];
} else {
	if (isset($_GET['sessionid7'])) {
		$crawltsessionid7 = (int)$_GET['sessionid7'];
	} else {
		$crawltsessionid7 = 0;
	}
}
if (isset($_POST['sessionid8'])) {
	$crawltsessionid8 = (int)$_POST['sessionid8'];
} else {
	if (isset($_GET['sessionid8'])) {
		$crawltsessionid8 = (int)$_GET['sessionid8'];
	} else {
		$crawltsessionid8 = 0;
	}
}
if (isset($_POST['includeparameter'])) {
	$crawltincludeparameter = (int)$_POST['includeparameter'];
} else {
	if (isset($_GET['includeparameter'])) {
		$crawltincludeparameter = (int)$_GET['includeparameter'];
	}
}
if (isset($_POST['login'])) {
	$login = $_POST['login'];
} else {
	$login = '';
}
if (isset($_POST['password1'])) {
	$password1 = $_POST['password1'];
} else {
	$password1 = '';
}
if (isset($_POST['password2'])) {
	$password2 = $_POST['password2'];
} else {
	$password2 = '';
}
if (isset($_POST['password3'])) {
	$password3 = $_POST['password3'];
} else {
	$password3 = '';
}
if (isset($_POST['logintype'])) {
	$logintype = (int)$_POST['logintype'];
} else {
	$logintype = 0;
}
if (isset($_POST['crawlername2'])) {
	$crawlername2 = htmlspecialchars($_POST['crawlername2']);
} else {
	$crawlername2 = '';
}
if (isset($_POST['crawlerua2'])) {
	$crawlerua2 = htmlspecialchars($_POST['crawlerua2']);
} else {
	$crawlerua2 = '';
}
if (isset($_POST['crawleruser2'])) {
	$crawleruser2 = htmlspecialchars($_POST['crawleruser2']);
} else {
	$crawleruser2 = '';
}
if (isset($_POST['crawlerurl2'])) {
	$crawlerurl2 = htmlspecialchars($_POST['crawlerurl2']);
} else {
	$crawlerurl2 = '';
}
if (isset($_POST['crawlerip2'])) {
	$crawlerip2 = htmlspecialchars($_POST['crawlerip2']);
} else {
	$crawlerip2 = '';
}
if (isset($_POST['logochoice'])) {
	$logochoice = (int)$_POST['logochoice'];
} else {
	$logochoice = 0;
}
//case  can use also hypertext link
if (isset($_POST['validsite'])) {
	$validsite = (int)$_POST['validsite'];
} else {
	if (isset($_GET['validsite'])) {
		$validsite = (int)$_GET['validsite'];
	} else {
		$validsite = 0;
	}
}
if (isset($_POST['sitename'])) {
	$sitename = htmlspecialchars($_POST['sitename']);
} else {
	if (isset($_GET['sitename'])) {
		$sitename = htmlspecialchars($_GET['sitename']);
	} else {
		$namecp = $_SERVER['HTTP_HOST']; 
	if (preg_match('#^www.#i', $namecp))
		{
		$namecp=substr($namecp,4);
		}
	$namecptab=explode('.',$namecp);
	$sitename=ucfirst($namecptab[0]);	
	}
}
if (isset($_POST['siteurl'])) {
	$siteurl = htmlspecialchars($_POST['siteurl']);
} else {
	if (isset($_GET['siteurl'])) {
		$siteurl = htmlspecialchars($_GET['siteurl']);
	} else {
		$siteurl = $_SERVER['HTTP_HOST'];
	}
}
if (isset($_POST['urlreferer'])) {
	$urlreferer = htmlspecialchars($_POST['urlreferer']);
} else {
	if (isset($_GET['urlreferer'])) {
		$urlreferer = htmlspecialchars($_GET['urlreferer']);
	} else {
		$urlreferer = '';
	}
}
if (isset($_POST['validform'])) {
	$validform = (int)$_POST['validform'];
} else {
	if (isset($_GET['validform'])) {
		$validform = (int)$_GET['validform'];
	} else {
		$validform = 0;
	}
}
if (isset($_POST['validlogin'])) {
	$validlogin = (int)$_POST['validlogin'];
} else {
	if (isset($_GET['validlogin'])) {
		$validlogin = (int)$_GET['validlogin'];
	} else {
		$validlogin = 0;
	}
}
if (isset($_POST['period'])) {
	$period = (int)$_POST['period'];
} else {
	if (isset($_GET['period'])) {
		$period = (int)$_GET['period'];
	} else {
		$period = 0;
	}
}
if (isset($_POST['navig'])) {
	$navig = (int)$_POST['navig'];
} else {
	if (isset($_GET['navig'])) {
		$navig = (int)$_GET['navig'];
	} else {
		$navig = 0;
	}
}

//modified according Stef proposal
if (isset($_POST['site'])) {
	$site = (int)$_POST['site'];
} elseif (isset($_GET['site'])) {
	$site = (int)$_GET['site'];
} else {
		if (file_exists('include/connection.php') ) {	
		$sqlsite = "SELECT id_site FROM crawlp_site_setting WHERE INSTR('".$_SERVER['HTTP_HOST']."',url) > 0";
		$requetesite = mysql_query($sqlsite, $connexion);
		$nbrresult = @mysql_num_rows($requetesite);
		if ($nbrresult >= 1)
			{
			while ($ligne = mysql_fetch_object($requetesite))
				{
				$site = $ligne->id_site;
				}
			}	
		else
			{	
			$site = 1;
			}
		} else {
		$site = 1;
		}		
}

//case summary
if ($site == 0) {
	$navig = 23;
	$site = 1;
}
if (isset($_POST['crawler'])) {
	$crawler = htmlspecialchars($_POST['crawler']);
	$crawler2 = htmlspecialchars($_POST['crawler']);
} else {
	if (isset($_GET['crawler'])) {
		$crawler = htmlspecialchars(stripslashes($_GET['crawler']));
		$crawler2 = htmlspecialchars($_GET['crawler']);
	} else {
		$crawler = 0;
		$crawler2 = 0;
	}
}



if (isset($_POST['search'])) {
	$search = htmlspecialchars($_POST['search']);
} else {
	if (isset($_GET['search'])) {
		$search = htmlspecialchars($_GET['search']);
	} else {
		$search = 0;
	}
}
if (isset($_GET['displayall']) && ($_GET['displayall'] == 'no' || $_GET['displayall'] == 'yes')) {
	$displayall = $_GET['displayall'];
} else {
	$displayall = 'no';
}
if (!isset($firstdayweek)) {
	if (isset($_GET['firstdayweek']) && ($_GET['firstdayweek'] == 'Monday' || $_GET['firstdayweek'] == 'Sunday')) {
		$firstdayweek = $_GET['firstdayweek'];
	} else {
		$firstdayweek = 'Monday';
	}
}
if (isset($_POST['graphpos'])) {
	$graphpos = (int)$_POST['graphpos'];
} else {
	if (isset($_GET['graphpos'])) {
		$graphpos = (int)$_GET['graphpos'];
	} else {
		$graphpos = 0;
	}
}
if (isset($_POST['logitself'])) {
	$logitself = (int)$_POST['logitself'];
} else {
	if (isset($_GET['logitself'])) {
		$logitself = (int)$_GET['logitself'];
	} else {
		$logitself = 0;
	}
}
if (isset($_GET['checklink'])) {
	$checklink = (int)$_GET['checklink'];
} else {
	$checklink = 0;
}
if (isset($_POST['novisitor'])) {
	$novisitor = (int)$_POST['novisitor'];
} else {
	$novisitor = 0;
}
if (isset($_GET['nocookie'])) {
	$nocookie = (int)$_GET['nocookie'];
} else {
	$nocookie = 0;
}
if(isset($_POST['changefile']))
	{
	$changefile = $_POST['changefile'];
	}
else
	{
	$changefile = '';
	}
if(isset($_POST['resetdata']))
	{
	$resetdata = $_POST['resetdata'];
	}
else
	{
	$resetdata = '';
	}
if(isset($_POST['resetdata2']))
	{
	$resetdata2 = $_POST['resetdata2'];
	}
else
	{
	$resetdata2 = '';
	}
if(isset($_POST['changelogin']))
	{
	$changelogin = $_POST['changelogin'];
	}
else
	{
	$changelogin = '';
	}
if(isset($_POST['changehtaccess']))
	{
	$changehtaccess = $_POST['changehtaccess'];
	}
else
	{
	$changehtaccess = '';
	}
if(isset($_POST['changehtaccess2']))
	{
	$changehtaccess2 = $_POST['changehtaccess2'];
	}
else
	{
	$changehtaccess2 = '';
	}					
?>
