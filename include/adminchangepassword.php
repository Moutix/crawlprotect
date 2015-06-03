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
// file: adminchangepassword.php
//----------------------------------------------------------------------
//  Last update: 04/10/2013
//----------------------------------------------------------------------
if (!defined('IN_CRAWLT_ADMIN')) {
	exit('<h1>Hacking attempt !!!!</h1>');
}
if ($validlogin == 1) {
		//database connection
		$sqllogin = "SELECT crawlp_password FROM crawlp_login WHERE crawlp_user='" . sql_quote($_SESSION['userlogin']) . "'";
		$requetelogin = mysql_query($sqllogin, $connexion) or die("MySQL query error");
		while ($ligne = mysql_fetch_object($requetelogin)) {
			$userpass = $ligne->crawlp_password;
		}

	if (md5($password1) != $userpass || empty($password2) || empty($password3) || $password2 != $password3) {

		echo "<hr></hr><div class=\"form\">\n";				
		echo "<div class='alert2'>" . $language['login_no_ok'] . "</div>";
		echo "<form action=\"index.php\" method=\"POST\" >\n";
		echo "<input type=\"hidden\" name ='validform' value='30'>\n";
		echo "<input type=\"hidden\" name ='navig' value='1'>\n";
		echo "<input type=\"hidden\" name ='changelogin' value='ok'>\n";
		echo "<input type=\"hidden\" name ='validlogin' value='0'>\n";
		echo "<input name='ok' type='submit'  value=' " . $language['back_to_form'] . " ' size='20'>\n";
		echo "</form>\n";
		echo "</div><br><hr>\n";
	} else {
		//password treatment
		$pass = md5($password2);
		
		$sqllogin = "UPDATE crawlp_login SET  crawlp_password='" . sql_quote($pass) . "'";
		$requetelogin = db_query($sqllogin, $connexion);

		echo "<hr><br><h1>" . $language['update'] . "</h1><br><hr>";
	}
} else {
	//first arrival on the page
	echo "<hr><h1>" . $language['change_password'] . "</h1>\n";
	echo "<div class=\"form5\">\n";
	echo "<form action=\"index.php\" method=\"POST\" >\n";
	echo "<input type=\"hidden\" name ='validform' value=\"30\">";
	echo "<input type=\"hidden\" name ='navig' value='1'>\n";
	echo "<input type=\"hidden\" name ='validlogin' value='1'>\n";
	echo "<input type=\"hidden\" name ='changelogin' value='ok'>\n";
	echo "<table class=\"centrer\">\n";
	echo "<tr>\n";
	echo "<td>" . $language['old_password'] . "</td>\n";
	echo "<td><input name='password1'  value='$password1' type='password' size='50'/></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>" . $language['new_password'] . "</td>\n";
	echo "<td><input name='password2' value='$password2' type='password' size='50'/></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan=\"2\">\n";
	echo "" . $language['valid_new_password'] . "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>" . $language['new_password'] . "</td>\n";
	echo "<td><input name='password3' value='$password3' type='password' size='50'/></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan=\"2\">\n";
	echo "<br>\n";
	echo "<input name='ok' type='submit'  value=' OK ' size='20'>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>\n";
	echo "</div><br><hr>\n";
}
?>
