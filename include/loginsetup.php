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
// file: loginsetup.php
//----------------------------------------------------------------------
//  Last update: 04/10/2013
//----------------------------------------------------------------------
if (!defined('IN_CRAWLT_INSTALL')) {
	exit('<h1>Hacking attempt !!!!</h1>');
}
//valid form
if ($validlogin == 1) {
	if (empty($login) || empty($password2) || empty($password3) || $password2 != $password3) {
		echo "<p>" . $language['login_no_ok'] . "</p>";
		echo "<div class=\"form\">\n";
		echo "<form action=\"index.php\" method=\"POST\" >\n";
		echo "<input type=\"hidden\" name ='validform' value='6'>\n";
		echo "<input type=\"hidden\" name ='navig' value='15'>\n";
		echo "<input type=\"hidden\" name ='validlogin' value='0'>\n";
		echo "<input type=\"hidden\" name ='lang' value='$crawltlang'>\n";
		echo "<input type=\"hidden\" name ='login' value='$login'>\n";
		echo "<input type=\"hidden\" name ='password2' value='$password2'>\n";
		echo "<input type=\"hidden\" name ='password3' value='$password3'>\n";
		echo "<input type=\"hidden\" name ='site' value='$site'>\n";
		echo "<input name='ok' type='submit'  value=' " . $language['back_to_form'] . " ' size='20'>\n";
		echo "</form>\n";
		echo "<br></div>\n";
	} else {
		//database connection
		include ("include/connection.php");
		$connexion = mysql_connect($crawlthost, $crawltuser, $crawltpassword) or die("MySQL connection to database problem");
		$selection = mysql_select_db($crawltdb) or die("MySQL database selection problem");
		
		//check if login already exist
		$sqlexist = "SELECT * FROM crawlp_login
			WHERE crawlp_user='" . sql_quote($login) . "'";
		$requeteexist = db_query($sqlexist, $connexion);
		$nbrresult = mysql_num_rows($requeteexist);
		
		if ($nbrresult >= 1) {
			//login already exist
			echo "<h1>" . $language['exist_login'] . "</h1>";
			echo "<div class=\"form\">\n";
			echo "<form action=\"index.php\" method=\"POST\" >\n";
			echo "<input type=\"hidden\" name ='validform' value='6'>\n";
			echo "<input type=\"hidden\" name ='navig' value='15'>\n";
			echo "<input type=\"hidden\" name ='validlogin' value='0'>\n";
			echo "<input type=\"hidden\" name ='lang' value='$crawltlang'>\n";
			echo "<input type=\"hidden\" name ='login' value='$login'>\n";
			echo "<input type=\"hidden\" name ='password2' value='$password2'>\n";
			echo "<input type=\"hidden\" name ='password3' value='$password3'>\n";
			echo "<input type=\"hidden\" name ='site' value='$site'>\n";
			echo "<input name='ok' type='submit'  value=' " . $language['back_to_form'] . " ' size='20'>\n";
			echo "</form>\n";
			echo "<br></div>\n";
		} else {
			//add the login in the database
			//password treatment
			$pass = md5($password2);
			$admin = 1;
			$website = 0;
			$sqllogin = "INSERT INTO crawlp_login (crawlp_user,crawlp_password) VALUES ('" . sql_quote($login) . "','" . sql_quote($pass) . "')";
			$requetelogin = db_query($sqllogin, $connexion);
			
			//check is requete is successfull
			if ($requetelogin == 1) {
				echo "<h2>" . $language['admin_creation'] . "</h2>\n";
				echo "<p>" . $language['login_ok'] . "</p>\n";
				echo "<h5>" . $language['login_finish'] . "</h5>\n";
				echo "<div class=\"form\">\n";
				echo "<form action=\"index.php\" method=\"POST\" >\n";
				echo "<input name='ok' type='submit'  value='OK' size='20'>\n";
				echo "</form>\n";
				echo "<br></div>\n";
			} else {
				echo "<h2>" . $language['admin_creation'] . "</h2>\n";
				echo "<p>" . $language['login_no_ok2'] . "</p>";
				echo "<div class=\"form\">\n";
				echo "<form action=\"index.php\" method=\"POST\" >\n";
				echo "<input type=\"hidden\" name ='validform' value='6'>\n";
				echo "<input type=\"hidden\" name ='navig' value='15'>\n";
				echo "<input type=\"hidden\" name ='validlogin' value='1'>\n";
				echo "<input type=\"hidden\" name ='lang' value='$crawltlang'>\n";
				echo "<input type=\"hidden\" name ='login' value='$login'>\n";
				echo "<input type=\"hidden\" name ='password2' value='$password2'>\n";
				echo "<input type=\"hidden\" name ='password3' value='$password3'>\n";
				echo "<input name='ok' type='submit'  value=' " . $language['retry'] . " ' size='20'>\n";
				echo "</form>\n";
				echo "<br></div>\n";
			}
		}
	}
}
//form
else {
	echo "<h2>" . $language['admin_creation'] . "</h2>\n";
	echo "<p>" . $language['admin_setup'] . "</p>\n";
	echo "</div>\n";
	
	//data collect form
	echo "<div class=\"form\">\n";
	echo "<form action=\"index.php\" method=\"POST\" >\n";
	echo "<input type=\"hidden\" name ='validform' value=\"6\">";
	echo "<input type=\"hidden\" name ='navig' value='15'>\n";
	echo "<input type=\"hidden\" name ='validlogin' value='1'>\n";
	echo "<input type=\"hidden\" name ='lang' value='$crawltlang'>\n";
	echo "<table class=\"centrer\">\n";
	echo "<tr>\n";
	echo "<td>" . $language['login'] . "</td>\n";
	echo "<td><input name='login'  value='$login' type='text' maxlength='20' size='50'/></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>" . $language['password'] . "</td>\n";
	echo "<td><input name='password2' value='$password2' type='password' size='50'/></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan=\"2\">\n";
	echo "" . $language['valid_password'] . "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>" . $language['password'] . "</td>\n";
	echo "<td><input name='password3' value='$password3' type='password' size='50'/></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan=\"2\">\n";
	echo "<br>\n";
	echo "<input name='ok' type='submit'  value=' OK ' size='20'>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form><br>\n";
}
?>
