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
// file: updatecrawlprotect.php
//----------------------------------------------------------------------
//  Last update: 19/10/2013
//----------------------------------------------------------------------
//this file is needed to update from a previous release
if (!defined('IN_CRAWLT')) {
	exit('<h1>Hacking attempt !!!!</h1>');
}

$process_ok = true;

//----------------------------------------------------------------------------------------------------
// Call the maintenance script which will do the job
$maintenance_mode = 'update';
$tables_to_touch = 'all';
include 'maintenance.php';


//----------------------------------------------------------------------------------------------------
//update cppf.php file if release < 325
if ($version < 325) {
//redirection url calculation
$redirecturl=$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$redirecturl= str_replace('index.php','noaccess/index.php',$redirecturl);
$redirecturl="http://".$redirecturl;
//determine the path to the file
if (isset($_SERVER['SCRIPT_FILENAME']) && !empty($_SERVER['SCRIPT_FILENAME'])) {
	$path = dirname($_SERVER['SCRIPT_FILENAME']);
} elseif (isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['PHP_SELF']) && !empty($_SERVER['PHP_SELF'])) {
	$path = dirname($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']);
} else {
	$path = '..';
}				
// Get the reference file and replace the needed values
$ref_file_content = file_get_contents(dirname(__FILE__) . '/data/cppf.base.php');
// Replace the values
$final_file_content2 = preg_replace('/FILE_PATH/', $path, $ref_file_content);
$final_file_content2 = preg_replace('/URL_REDIRECT/', $redirecturl, $final_file_content2);
$crawlprotect_filepath = $path . '/include/cppf.php';
$filedir = $path;

//chmod the file
@chmod($crawlprotect_filepath, 0644);
if ($file2 = @fopen($crawlprotect_filepath, "w")) {
	fwrite($file2, $final_file_content2);
	fclose($file2);
}
@chmod($crawlprotect_filepath, 0444);	
//verif file ok
//if not display for manual update
$cppf=0;
include ("include/cppf.php");	
if($cppf==0 || $cppf==1 || $cppf==2)
	{
	echo "<p><b>" . $language['step1_install_no_ok2'] . "</b></p>";
	$final_file_content=trim($final_file_content);
	$final_file_content2=trim($final_file_content2);
	echo "<p>" . $language['step1_install_no_ok4'] . "</p>";		
	echo "<div class='code'><pre>".htmlspecialchars($final_file_content2)."</pre></div><br>";		
	echo "<p><b>" . $language['step1_install_no_ok6'] . "</b></p>";				
	}	
	
	
			
}

// Just check if the main errors mesages array are empty
if (empty($tables_actions_error_messages) && empty($fields_actions_error_messages) && $process_ok) {
	$sqlupdateversion = "UPDATE crawlp_general_setting SET version='325'";
	$requeteupdateversion = mysql_query($sqlupdateversion, $connexion);
	$a = substr($versionid, 0, 1);
	$b = substr($versionid, 1, 1);
	$c = substr($versionid, 2, 1);
?>
    <div class="content">

    <h1><?php echo $language['update_crawlprotect_ok'] ?>&nbsp;<?php echo $a . $b . $c; ?></h1>
    

        <div class="form">
        <form action="index.php" method="POST" >
        <input type="hidden" name ='navig' value='0'>
        <table width="100%" align="center">
        <tr>
        <td width="100%" align="center">
        <input name='ok' type='submit'  value=' OK ' size='20'>
        </td>
        </tr>
        </table>
        </form>
        </div><br><br><br>

<?php
} else {
	// Update failed, show all error messages
	
?>
    <h1><?php echo $language['update_crawlprotect_no_ok'] ?></h1>

	<?php foreach ($tables_actions_error_messages as $table_error_message): ?>
		<?php echo $table_error_message ?><br>
	<?php
	endforeach ?>
	<?php foreach ($fields_actions_error_messages as $field_error_message): ?>
		<?php echo $field_error_message ?><br>
	<?php
	endforeach ?>
	<?php foreach ($index_actions_error_messages as $index_error_message): ?>
		<?php echo $index_error_message ?><br>
	<?php
	endforeach ?>

    <div class="form">
    <form action="index.php" method="POST" >
	<input type="hidden" name="navig" value="1">
	<input type="hidden" name="lang" value="<?php echo $crawltlang ?>">
    <table width="100%" align="center">
    <tr>
    <td width="100%" align="center">
    <input name="ok" type="submit"  value=" OK " size="20">
    </td>
    </tr>
    </table>
    </form>
    <br><br><br>
<?php
}

?>
