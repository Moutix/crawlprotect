<?php
//----------------------------------------------------------------------
//  CrawlProtect 3.2.5
//----------------------------------------------------------------------
// Crawler Tracker for website
//----------------------------------------------------------------------
// Author: Jean-Denis Brun
//----------------------------------------------------------------------
// Website: www.crawltrack.net
//----------------------------------------------------------------------
// That script is distributed under GNU GPL license
//----------------------------------------------------------------------
// file: header.php
//----------------------------------------------------------------------
//  Last update: 19/10/2013
//----------------------------------------------------------------------

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>CrawlProtect</title>
	<meta name="author" content="Jean-Denis Brun">
	<meta name="description" content="CrawlProtect, anti hacking protection">

	<?php
	if( $language['go_install']=="Installer")
		{
		echo"<meta http-equiv=\"Content-Language\" content=\"fr\">\n";
		}
	elseif( $language['go_install']=="Istalla")
		{
		echo"<meta http-equiv=\"Content-Language\" content=\"it\">\n";
		}	
	else
		{
		echo"<meta http-equiv=\"Content-Language\" content=\"en\">\n";
		}
 ?>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<link rel="stylesheet" type="text/css" href="./styles/style2.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="./styles/imprim.css" media="print" />
	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="./styles/ie.css" />
	<![endif]-->
	<script type="text/javascript">
		<!--
		function montre(id) {
		var d = document.getElementById(id);
			for (var i = 1; i<=300; i++) {
				if (document.getElementById('smenu'+i)) {document.getElementById('smenu'+i).style.display='none';}
			}
		if (d) {d.style.display='block';}
		}
		//-->
	</script>
</head>
<body>

<div class="main">
<div class="header" onmouseover="javascript:montre();">
<?php
//to check if tag is present (to create cookie that will be test later)
if(!isset($_COOKIE["crawlprotecttag"]))
	{
	echo "<iframe name=\"I1\" src=\"http://".$_SERVER['HTTP_HOST']."\" marginwidth=\"0\" marginheight=\"0\" scrolling=\0\" frameborder=\"0\" width=\"0px\" height=\"0px\"></iframe>\n";
	}


if( $language['go_install']=="Installer")
	{
?>		
	<a href="http://www.crawltrack.fr/crawlprotect/">CrawlProtect</a>
<?php	
	}
else
	{
?>		
	<a href="http://www.crawltrack.net/crawlprotect/">CrawlProtect</a>
<?php				
	}
?>
 <span class="headertext"><?php echo $language['sitesecurity'] ?></span>
</div>
