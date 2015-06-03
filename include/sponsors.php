<?php
//----------------------------------------------------------------------
//  CrawlProtect 3.2.3
//----------------------------------------------------------------------
// Protect your website from hackers
//----------------------------------------------------------------------
// Author: Jean-Denis Brun
//----------------------------------------------------------------------
// Website: www.crawlprotect.com
//----------------------------------------------------------------------
// That script is distributed under GNU GPL license
//----------------------------------------------------------------------
// file: sponsors.php
//----------------------------------------------------------------------
//  Last update: 07/07/2013
//----------------------------------------------------------------------

echo"<div class=\"sponsortop\">&nbsp;</div>\n";
echo"<div class=\"sponsor\">\n";
echo"<div align=\"center\">";
	echo "<br><div style=\" padding:10px; width:300px;\"><p style=\"font-size:12px; \">".$language['help_crawlprotect']."</p>";
	if($crawltlang=='french')
		{
		echo"<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">\n";
		echo"<input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\">\n";
		echo"<input type=\"hidden\" name=\"hosted_button_id\" value=\"10304523\">\n";
		echo"<input type=\"image\" src=\"https://www.paypal.com/fr_FR/FR/i/btn/btn_donate_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !\">\n";
		echo"<img alt=\"\" border=\"0\" src=\"https://www.paypal.com/fr_FR/i/scr/pixel.gif\" width=\"1\" height=\"1\">\n";
		echo"</form>\n";
		}
	else
		{
		echo"<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">\n";
		echo"<input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\">\n";
		echo"<input type=\"hidden\" name=\"hosted_button_id\" value=\"10304614\">\n";
		echo"<input type=\"image\" src=\"https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online.\">\n";
		echo"<img alt=\"\" border=\"0\" src=\"https://www.paypal.com/fr_FR/i/scr/pixel.gif\" width=\"1\" height=\"1\">\n";
		echo"</form>\n";
		}
	echo"<div align=\"right\"><p style=\"font-size:12px; font-style:italic;\">\n";
	echo $language['thanks'];
	echo"</p></div></div>\n";
echo"</div><br>";
echo"</div>\n";
?>
