<?php
//----------------------------------------------------------------------
//  CrawlProtect 3.1.1
//----------------------------------------------------------------------
// Crawler Tracker for website
//----------------------------------------------------------------------
// Author: Jean-Denis Brun
//----------------------------------------------------------------------
// Website: www.crawltrack.net
//----------------------------------------------------------------------
// That script is distributed under GNU GPL license
//----------------------------------------------------------------------
// file: install.php
//----------------------------------------------------------------------
//  Last update: 20/10/2012
//----------------------------------------------------------------------
if (!defined('IN_CRAWLT')) {
	exit('<h1>Hacking attempt !!!!</h1>');
}
// do not modify
define('IN_CRAWLT_INSTALL', TRUE);
?>
<div class="content">
<!-- test validity form -->
<?php if ($validform == 1): ?>
	<!-- echo text -->
	<h1><?php echo $language['install']; ?></h1>
	<p><?php echo $language['welcome_install']; ?></p>
	<div align="left">
		<h5><?php echo $language['menu_install_1']; ?></h5>
		<h5><?php echo $language['menu_install_2']; ?></h5>
		<h5><?php echo $language['menu_install_3']; ?></h5>
	</div>
	<div class="form">
		<form action="index.php" method="POST">
			<input type="hidden" name="validform" value="2" />
			<input type="hidden" name="lang" value="<?php echo $crawltlang; ?>" />
			<input name="ok" type="submit" value="<?php echo $language['go_install']; ?>" />
		</form>
		<br />
	</div>
<?php elseif($validform == 2): ?>
	<!-- connection data collect -->
	<h1><?php echo $language['install']; ?></h1>
	<div align="left">
		<h5><?php echo $language['menu_install_1']; ?></h5>
		<h4><?php echo $language['menu_install_2']; ?></h4>
		<h4><?php echo $language['menu_install_3']; ?></h4>
	</div>
		<p><?php echo $language['step1_install']; ?></p>
</div>
	
	<!-- data collect form -->
	<div class="form">
		<form action="index.php" method="POST">
			<input type="hidden" name="validform" value="3" />
			<input type="hidden" name="navig" value="15" />
			<input type="hidden" name="lang" value="<?php echo $crawltlang; ?>" />
			<table class="centrer">
				<tr>
				<td><?php echo $language['step1_install_login_mysql']; ?></td>
				<td><input name="idmysql" value="<?php echo $idmysql; ?>" type="text" size="50"/></td>
				</tr>
				<tr>
				<td><?php echo $language['step1_install_password_mysql']; ?></td>
				<td><input name="passwordmysql" value="<?php echo $passwordmysql; ?>" type="password" size="50" /></td>
				</tr>
				<tr>
				<td><?php echo $language['step1_install_host_mysql']; ?></td>
				<td><input name="hostmysql" value="<?php echo $hostmysql; ?>" type="text" size="50" /></td>
				</tr>
				<tr>
				<td><?php echo $language['step1_install_database_mysql']; ?></td>
				<td><input name="basemysql" value="<?php echo $basemysql; ?>" type="text" size="50" /></td>
				</tr>
				<tr>
				<td colspan="2">
				<br />
				<input name="ok" type="submit"  value=" OK " size="20" />
				</td>
				</tr>
			</table>
		</form><br />
<?php elseif($validform == 3): ?>
	<!-- file and tables creation -->
	<h1><?php echo $language['install']; ?></h1>
	<div align="left">
		<h5><?php echo $language['menu_install_1']; ?></h5>
		<h4><?php echo $language['menu_install_2']; ?></h4>
		<h4><?php echo $language['menu_install_3']; ?></h4>
	</div>
	<?php include("include/createtable.php"); ?>
<?php elseif($validform == 4): ?>
	<!-- site creation -->
	<h1><?php echo $language['install']; ?></h1>
	<div align="left">
		<h5><?php echo $language['menu_install_1']; ?></h5>
		<h5><?php echo $language['menu_install_2']; ?></h5>
		<h4><?php echo $language['menu_install_3']; ?></h4>
	</div>
	<?php include("include/createsite.php"); ?>
<?php elseif($validform == 6): ?>
	<!-- user right -->
	<h1><?php echo $language['install']; ?></h1>
	<div align="left">
		<h5><?php echo $language['menu_install_1']; ?></h5>
		<h5><?php echo $language['menu_install_2']; ?></h5>
		<h5><?php echo $language['menu_install_3']; ?></h5>
	</div>
	<?php include("include/loginsetup.php"); ?>
<?php else: ?>
	<!-- language choice -->
	<br/><h1>Welcome to the CrawlProtect installation</h1><h2>First you have to choose the language to use:</h2><br/>

	<div class="form">
		<form action="index.php" method="POST">
			<h1><table width="750px">        
				<tr><td align="left"><input type="radio" name="lang" value="english" />&nbsp;<img border="0" src="./images/flags/gb.gif" alt="english" width="18px" height="12px">&nbsp;English</td>
				<td align="left"><input type="radio" name="lang" value="italian" />&nbsp;<img border="0" src="./images/flags/it.gif" alt="italian" width="18px" height="12px">&nbsp;Italiano</td>
				<td align="left"><input type="radio" name="lang" value="french" />&nbsp;<img border="0" src="./images/flags/fr.gif" alt="french" width="18px" height="12px">&nbsp;Français</td>
				<td align="left"><input type="radio" name="lang" value="russian" />&nbsp;<img border="0" src="./images/flags/ru.gif" alt="russian" width="18px" height="12px">&nbsp;Pусский</td></tr>
				
				<tr><td align="left"><input type="radio" name="lang" value="swedish" />&nbsp;<img border="0" src="./images/flags/se.gif" alt="swedish" width="18px" height="12px">&nbsp;Svenska</td>
				<td align="left"><input type="radio" name="lang" value="spanish" />&nbsp;<img border="0" src="./images/flags/es.gif" alt="spanish" width="18px" height="12px">&nbsp;Español</td>
				<td align="left"></td>
				<td align="left"></td></tr>				
												
			</table></h1>
			<input type="hidden" name="navig" value="6" />
			<input type="hidden" name="validform" value="1" />
			<input name="ok" type="submit" value="OK" style="width:100px"/>
		</form>
		<br /><br />
	</div>
<?php endif ?>
