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
// file: createtable.php
//----------------------------------------------------------------------
//  Last update: 04/10/2013
//----------------------------------------------------------------------
if (!defined('IN_CRAWLT_INSTALL')) {
	exit('<h1>Hacking attempt !!!!</h1>');
}
//determine the path to the file
if (isset($_SERVER['SCRIPT_FILENAME']) && !empty($_SERVER['SCRIPT_FILENAME'])) {
	$path = dirname($_SERVER['SCRIPT_FILENAME']);
} elseif (isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['PHP_SELF']) && !empty($_SERVER['PHP_SELF'])) {
	$path = dirname($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']);
} else {
	$path = '..';
}
//valid form
if (empty($idmysql) || empty($passwordmysql) || empty($hostmysql) || empty($basemysql)) {
	echo "<p>" . $language['step1_install_no_ok'] . "</p>";
	echo "<div class=\"form\">\n";
	echo "<form action=\"index.php\" method=\"POST\" >\n";
	echo "<input type=\"hidden\" name ='validform' value='2'>\n";
	echo "<input type=\"hidden\" name ='navig' value='15'>\n";
	echo "<input type=\"hidden\" name ='lang' value='$crawltlang'>\n";
	echo "<input type=\"hidden\" name ='idmysql' value='$idmysql'>\n";
	echo "<input type=\"hidden\" name ='passwordmysql' value='$passwordmysql'>\n";
	echo "<input type=\"hidden\" name ='hostmysql' value='$hostmysql'>\n";
	echo "<input type=\"hidden\" name ='basemysql' value='$basemysql'>\n";
	echo "<input name='ok' type='submit'  value=' " . $language['back_to_form'] . " ' size='20'>\n";
	echo "</form>\n";
	echo "<br></div>\n";
}
//configconnect file creation
else {
	//check if file already exist
	if (file_exists('include/connection.php')) {
		$config_filepath = $path . '/include/connection.php';
	} else {
		//file didn't exist, we can create it
		
		// Get the reference file and replace the needed values
		$ref_file_content = file_get_contents(dirname(__FILE__) . '/data/connection.base.php');
		// Replace the values
		$final_file_content = preg_replace('/USER/', $idmysql, $ref_file_content);
		$final_file_content = preg_replace('/PASSWORD/', $passwordmysql, $final_file_content);
		$final_file_content = preg_replace('/DATABASE/', $basemysql, $final_file_content);
		$final_file_content = preg_replace('/HOST/', $hostmysql, $final_file_content);
		$final_file_content = preg_replace('/SECRETSENTENCE/', random(50), $final_file_content);
		$config_filepath = $path . '/include/connection.php';
		$filedir = $path . '/include';
		
		//chmod the directory
		@chmod($filedir, 0755);
		if ($file = @fopen($config_filepath, "w")) {
			fwrite($file, $final_file_content);
			fclose($file);
		}
	}
	

	//check if file already exist
	
	if (file_exists('include/cppf.php')) {
		$crawlprotect_filepath = $path . '/include/cppf.php';
	} else {
		//file didn't exist, we can create it		
		//redirection url calculation
		$redirecturl=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$redirecturl= str_replace('index.php','noaccess/index.php',$redirecturl);
		$redirecturl="http://".$redirecturl;				
		// Get the reference file and replace the needed values
		$ref_file_content = file_get_contents(dirname(__FILE__) . '/data/cppf.base.php');
		// Replace the values
		$final_file_content2 = preg_replace('/FILE_PATH/', $path, $ref_file_content);
		$final_file_content2 = preg_replace('/URL_REDIRECT/', $redirecturl, $final_file_content2);
		$crawlprotect_filepath = $path . '/include/cppf.php';
		$filedir = $path;
		
		//chmod the directory
		@chmod($filedir, 0755);
		if ($file2 = @fopen($crawlprotect_filepath, "w")) {
			fwrite($file2, $final_file_content2);
			fclose($file2);
		}
	}
	
	
	
	

	//set the correct chmod level to all folder
	@chmod($path, 0755);
	@chmod($path . '/cache', 0755);
	@chmod($path . '/geoipdatabase', 0755);
	@chmod($path . '/graphs', 0755);
	@chmod($path . '/html', 0755);
	@chmod($path . '/images', 0755);
	@chmod($path . '/include', 0755);
	@chmod($path . '/language', 0755);
	@chmod($path . '/php', 0755);
	@chmod($path . '/styles', 0755);
	//check if file correctly created
	if (file_exists('include/connection.php')) {
		//case file ok
		echo "<p>" . $language['step1_install_ok'] . "</p>\n";
		
		// tables creation
		include ("./include/connection.php");
		$connexion = mysql_connect($crawlthost, $crawltuser, $crawltpassword);
		
		// check if connection is ok
		if (!$connexion) {
			//suppress the files
			@chmod($path, 0755);
			@chmod($path . '/include', 0755);
			unlink($config_filepath);
			unlink($crawltrack_filepath);
			echo "<p>" . $language['step2_install_no_ok'] . "</p>";
			echo "<div class=\"form\">\n";
			echo "<form action=\"index.php\" method=\"POST\" >\n";
			echo "<input type=\"hidden\" name ='validform' value='2'>\n";
			echo "<input type=\"hidden\" name ='navig' value='15'>\n";
			echo "<input type=\"hidden\" name ='lang' value='$crawltlang'>\n";
			echo "<input type=\"hidden\" name ='idmysql' value='$idmysql'>\n";
			echo "<input type=\"hidden\" name ='passwordmysql' value='$passwordmysql'>\n";
			echo "<input type=\"hidden\" name ='hostmysql' value='$hostmysql'>\n";
			echo "<input type=\"hidden\" name ='basemysql' value='$basemysql'>\n";
			echo "<input name='ok' type='submit'  value=' " . $language['back_to_form'] . " ' size='20'>\n";
			echo "</form>\n";
			echo "</div>\n";
		} else {
			//check is base selection is ok
			$selection = mysql_select_db($crawltdb);
			
			if (!$selection) {
				//suppress the files
				@chmod($path, 0755);
				@chmod($path . '/include', 0755);
				unlink($config_filepath);
				unlink($crawltrack_filepath);
				echo "<p>" . $language['step3_install_no_ok'] . "</p>";
				echo "<div class=\"form\">\n";
				echo "<form action=\"index.php\" method=\"POST\" >\n";
				echo "<input type=\"hidden\" name ='validform' value='2'>\n";
				echo "<input type=\"hidden\" name ='navig' value='15'>\n";
				echo "<input type=\"hidden\" name ='lang' value='$crawltlang'>\n";
				echo "<input type=\"hidden\" name ='idmysql' value='$idmysql'>\n";
				echo "<input type=\"hidden\" name ='passwordmysql' value='$passwordmysql'>\n";
				echo "<input type=\"hidden\" name ='hostmysql' value='$hostmysql'>\n";
				echo "<input type=\"hidden\" name ='basemysql' value='$basemysql'>\n";
				echo "<input name='ok' type='submit'  value=' " . $language['back_to_form'] . " ' size='20'>\n";
				echo "</form>\n";
				echo "</div>\n";
			} else {
				// Call the maintenance script which will do the job
				$maintenance_mode = 'install';
				$tables_to_touch = 'all';
				include 'maintenance.php';
	
				if (empty($tables_actions_error_messages)) {
					//case table creation ok
					echo "<p>" . $language['step1_install_ok2'] . "</p>\n";
					echo "<div class=\"form\">\n";
					echo "<form action=\"index.php\" method=\"POST\" >\n";
					echo "<input type=\"hidden\" name ='navig' value='15'>\n";
					echo "<input type=\"hidden\" name ='validform' value='4'>\n";
					echo "<input type=\"hidden\" name ='lang' value='$crawltlang'>\n";
					echo "<input name='ok' type='submit'  value=' " . $language['step4_install'] . " ' size='60'>\n";
					echo "</form>\n";
					echo "<br></div>\n";
				} else {
					//case table creation no ok
					echo "<p>" . $language['step1_install_no_ok3'] . "</p>\n";
					echo "<div class=\"form\">\n";
					echo "<form action=\"index.php\" method=\"POST\" >\n";
					echo "<input type=\"hidden\" name ='validform' value='3'>\n";
					echo "<input type=\"hidden\" name ='navig' value='15'>\n";
					echo "<input type=\"hidden\" name ='lang' value='$crawltlang'>\n";
					echo "<input type=\"hidden\" name ='idmysql' value='$idmysql'>\n";
					echo "<input type=\"hidden\" name ='passwordmysql' value='$passwordmysql'>\n";
					echo "<input type=\"hidden\" name ='hostmysql' value='$hostmysql'>\n";
					echo "<input type=\"hidden\" name ='basemysql' value='$basemysql'>\n";
					echo "<input name='ok' type='submit'  value=' " . $language['retry'] . " ' size='60'>\n";
					echo "</form>\n";
					echo "<br></div>\n";
				}
			}
		}
	} else {
		//case file no ok
		echo "<p><b>" . $language['step1_install_no_ok2'] . "</b></p>";
		$final_file_content=trim($final_file_content);
		$final_file_content2=trim($final_file_content2);
		echo "<p>" . $language['step1_install_no_ok4'] . "</p>";		
		echo "<div class='code'><pre>".htmlspecialchars($final_file_content)."</pre></div><br><br>";
		echo "<p>" . $language['step1_install_no_ok5'] . "</p>";
		echo "<div class='code'><pre>".htmlspecialchars($final_file_content2)."</pre></div><br>";		
		echo "<p><b>" . $language['step1_install_no_ok6'] . "</b></p>";
		
		echo "<div class=\"form\">\n";
		echo "<form action=\"index.php\" method=\"POST\" >\n";
		echo "<input type=\"hidden\" name ='navig' value='15'>\n";
		echo "<input type=\"hidden\" name ='validform' value='3'>\n";
		echo "<input type=\"hidden\" name ='lang' value='$crawltlang'>\n";
		echo "<input type=\"hidden\" name ='idmysql' value='$idmysql'>\n";
		echo "<input type=\"hidden\" name ='passwordmysql' value='$passwordmysql'>\n";
		echo "<input type=\"hidden\" name ='hostmysql' value='$hostmysql'>\n";
		echo "<input type=\"hidden\" name ='basemysql' value='$basemysql'>\n";		
		echo "<input name='ok' type='submit'  value=' " . $language['Continue'] . " ' size='60'>\n";
		echo "</form>\n";
		echo "<br></div>\n";
	}
}
?>
