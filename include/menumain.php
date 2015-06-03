<?php
//----------------------------------------------------------------------
//  CrawlProtect 3.0.1
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
// file: menumain.php
//----------------------------------------------------------------------
// menu based on a www.alsacreations.com css menu
//----------------------------------------------------------------------
//  Last update: 08/09/2012
//----------------------------------------------------------------------
if (!defined('IN_CRAWLT')) {
	exit('<h1>Hacking attempt !!!!</h1>');
}
$crawlencode = urlencode($crawler);
?>
<div class="menumain">

  <div id="menum7">
  <dl>
  		<dt><a href="index.php?navig=0&amp;site=<?php echo $site ?>"><img src="./images/house.png" width="16" height="16" border="0" title="<?php echo $language['home'] ?>" alt="<?php echo $language['home'] ?>"></a></dt>
  	</dl>
  </div>
<?php

	echo "<div id=\"menum6\">\n";
	echo "	<dl>\n";
	echo "		<dt ><a href=\"".$crawlprotecturl."?navig=2&amp;site=$site\">" . $language['fileandfolders'] . "</a></dt>\n";
	echo "	</dl>\n";
	echo "</div>\n";
	
	echo "<div id=\"menum5\">\n";
	echo "	<dl>\n";
	echo "		<dt ><a href=\"".$crawlprotecturl."?navig=3&amp;site=$site\">" . $language['htaccess'] . "</a></dt>\n";

	echo "	</dl>\n";
	echo "</div>\n";




echo "<div id=\"menud2\">\n";

echo "	<dl>\n";
echo "		<dt ><a href=\"./php/refresh.php?navig=$navig&amp;site=$site\"><img src=\"./images/refresh.png\" width=\"16\" height=\"16\" border=\"0\" title=\"" . $language['refresh'] . "\" alt=\"" . $language['refresh'] . "\"></a></dt>\n";
echo "	</dl>\n";
echo "	<dl>\n";
echo "		<dt ><a href=\"".$crawlprotecturl."?navig=1&amp;site=$site\"><img src=\"./images/wrench.png\" width=\"16\" height=\"16\" border=\"0\" title=\"" . $language['wrench'] . "\" alt=\"" . $language['wrench'] . "\"></a></dt>\n";
echo "	</dl>\n";
echo "	<dl>\n";
echo "		<dt onmouseover=\"javascript:montre();\" onclick=\"window.print()\"><a href=\"#\"><img src=\"./images/printer.png\" width=\"16\" height=\"16\" border=\"0\" title=\"" . $language['printer'] . "\" alt=\"" . $language['printer'] . "\"></a></dt>\n";
echo "	</dl>\n";
echo "	<dl>\n";
if ($crawltlang == 'french' ) {
	echo "		<dt onmouseover=\"javascript:montre();\"><a href=\"http://www.crawltrack.fr/crawlprotect/documentation.php\"><img src=\"./images/information.png\" width=\"16\" height=\"16\" border=\"0\" title=\"" . $language['information'] . "\" alt=\"" . $language['information'] . "\"></a></dt>\n";
} else {
	echo "		<dt onmouseover=\"javascript:montre();\"><a href=\"http://www.crawltrack.net/crawlprotect/documentation.php\"><img src=\"./images/information.png\" width=\"16\" height=\"16\" border=\"0\" title=\"" . $language['information'] . "\" alt=\"" . $language['information'] . "\"></a></dt>\n";
}
echo "	</dl>\n";
if ($crawltlang == 'french' ) {
	echo "	<dl>\n";
	echo "		<dt onmouseover=\"javascript:montre();\" onclick=\"return window.open('./html/infofr.htm','CrawlProtect','top=300px,height=600px,width=800px')\"><a href=\"#\"><img src=\"./images/help.png\" width=\"16\" height=\"16\" border=\"0\" title=\"" . $language['help'] . "\" alt=\"" . $language['help'] . "\"></a></dt>\n";
	echo "	</dl>\n";
} else {
	echo "	<dl>\n";
	echo "		<dt onmouseover=\"javascript:montre();\" onclick=\"return window.open('./html/infoen.htm','CrawlProtect','top=300px,height=600px,width=800px')\"><a href=\"#\"><img src=\"./images/help.png\" width=\"16\" height=\"16\" border=\"0\" title=\"" . $language['help'] . "\" alt=\"" . $language['help'] . "\"></a></dt>\n";
	echo "	</dl>\n";
}
echo "	<dl>\n";
echo "		<dt onmouseover=\"javascript:montre();\"><a href=\"index.php?navig=7\"><img src=\"./images/cross.png\" width=\"16\" height=\"16\" border=\"0\" title=\"" . $language['cross'] . "\" alt=\"" . $language['cross'] . "\"></a></dt>\n";
echo "	</dl>\n";
echo "</div>\n";
echo "<br><br><br>\n";
echo "</div>\n";

//printing

if ($navig != 6) {
	echo "<br>\n";
}
?>
