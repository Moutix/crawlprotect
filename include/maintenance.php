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
// file: maintenance.php
//----------------------------------------------------------------------
//  Last update: 20/10/2013
//----------------------------------------------------------------------
// This file will manage all maintenance actions, database level (initial creation and updates)

// Tables information array, stores all tables informations and needed queries
// Special case : the insert queries can be replaced by a filename, which will be used instead.
// A fucntion can also be specified with the key 'execute_after', it'll be executed when an update is needed

// Error messages array, should be empty at the end
$tables_actions_error_messages = array();
$fields_actions_error_messages = array();
$index_actions_error_messages = array();

// Special cases for some tables
$existing_crawlt_config_table = true;
$existing_crawlt_update_attack_table = true;
$existing_crawlt_update_table = true;

// Spacial cases for fields
$existing_crawlt_site_url_field = true;

if(!isset($tables_to_check) || empty($tables_to_check))
{
	// The array wasn't defined earlier, so let's create a default one
	$tables_to_check = array(
		array(
			'table_name' => 'crawlp_scripts',
			'action' => 'create',
			'create_delete_query' => "CREATE TABLE crawlp_scripts (
				  id tinyint(4) NOT NULL auto_increment,
				  name varchar(45) NOT NULL,
				  type enum('query','request') NOT NULL,
				  content varchar(245) NOT NULL,
				  xss tinyint(4) NOT NULL default '0',
				  sqlinject tinyint(4) NOT NULL default '0',
				  codeinject tinyint(4) NOT NULL default '0',
				  shell tinyint(4) NOT NULL default '0',
				  PRIMARY KEY  (`id`)
				)",
			'insert_query' => "scripts.sql"
		),


		array(
			'table_name' => 'crawlp_login',
			'action' => 'create',
			'create_delete_query' => "CREATE TABLE crawlp_login (
				  crawlp_user varchar(20) default NULL,
				  crawlp_password varchar(45) default NULL
				)",
			'insert_query' => ''
		),
		array(
			'table_name' => 'crawlp_site_setting',
			'action' => 'create',
			'create_delete_query' => "CREATE TABLE crawlp_site_setting (
		  `id_site` tinyint(3) unsigned NOT NULL auto_increment,
		  `name` varchar(245) NOT NULL,		  
		  `url` varchar(245) NOT NULL,
		  `url_crawlprotect` varchar(245) NOT NULL,	
		  `whichfile` varchar(5) NOT NULL,		  
		  `nologs` varchar(5) NOT NULL,
		  `nocache` varchar(5) NOT NULL,	  
		  `nostats` varchar(5) NOT NULL,
		  `folderlevel` varchar(5) NOT NULL,
		  `justbad` varchar(5) NOT NULL,
		  `listfiledontchangeserialize` longtext,
		  `listfolderdontchangeserialize` longtext,		  
		  `scriptsused` varchar(245) NOT NULL,
		  `yourip` tinyint(3) NOT NULL,	
		  `blockUA` tinyint(3) NOT NULL,		  	  
		  `hotlink` tinyint(3) NOT NULL,		  		  
		  `shellsetting` varchar(245) NOT NULL,
		  `sqlsetting` varchar(245) NOT NULL,
		  `trustsites` varchar(245) NOT NULL,
		  `trustip` varchar(245) NOT NULL,
		  `trustuseragent` varchar(650) NOT NULL,
		  `trustvariable` varchar(245) NOT NULL,
		  `forbiddenip` text NOT NULL,		  		  
		  `forbiddenurl` text NOT NULL,
		  `forbiddenparameter` text NOT NULL,
		  `forbiddenreferer` text NOT NULL,
		  `forbiddenword` text NOT NULL,
		  `autoprepend` tinyint(3) NOT NULL,		  
		  `actualhtaccess` text NOT NULL,
		  `hotlinkok` text  NOT NULL,
		  `listindex` tinyint(3) NOT NULL,
		  `badfile` varchar(245) NOT NULL,
		  `url_image` varchar(245) NOT NULL,
		  `prelude_onoff` tinyint(3) NOT NULL DEFAULT 0,
		  `prelude_analyzer_name` varchar(245) NOT NULL DEFAULT 'Crawlprotect',
		  PRIMARY KEY  (`id_site`)
		)",
			'insert_query' => ''
		),
		array(
			'table_name' => 'crawlp_general_setting',
			'action' => 'create',
			'create_delete_query' => "CREATE TABLE crawlp_general_setting (
		  language varchar(45) NOT NULL,	
		  version smallint(4) NOT NULL
		)",
			'insert_query' => "INSERT INTO crawlp_general_setting (language, version) 
			VALUES ('".sql_quote($crawltlang)."','325')"
		),
		array(
			'table_name' => 'crawlp_stats',
			'action' => 'create',
			'create_delete_query' => "CREATE TABLE crawlp_stats (
			   date  varchar(25) default NULL,
			   id_site  tinyint(4) NOT NULL,
			   attack  varchar(45) default NULL,
			   url  text,
			   ip  varchar(15) default NULL
			)",
			'insert_query' => ''
		),
		array(
			'table_name' => 'crawlp_uablock',
			'action' => 'create',
			'create_delete_query' => "CREATE TABLE crawlp_uablock (
			  id_UA int(6) NOT NULL auto_increment,
			  UA varchar(45) NOT NULL,
			  PRIMARY KEY  (`id_UA`)
			)",
			'insert_query' => "uablock.sql"
		),

	);
}



// Get the tables list
$tables_list_sql = "SHOW TABLES ";
$tables = mysql_query($tables_list_sql, $connexion) or exit("MySQL query error"); 
$tables_names = array();
while (list($tablename)=mysql_fetch_array($tables)) 
	{
	$tables_names[] = strtolower($tablename);
	}
//case install on a V2 base (issue with crawlp_stats table already existing)
if(in_array('crawlp_stats', $tables_names))
	{
	$table_info_res = mysql_query("SHOW COLUMNS FROM crawlp_stats");
	$table_field_names = array();
	while ($table_info = mysql_fetch_assoc($table_info_res)) {
	$table_field_names[] = $table_info['Field'];
	}
	if(!in_array('id_site', $table_field_names))	//case V2
		{
		mysql_query("ALTER TABLE crawlp_stats RENAME AS crawlp_stats_v2");
		// Get the updated tables list
		$tables_list_sql = "SHOW TABLES ";
		$tables = mysql_query($tables_list_sql, $connexion) or exit("MySQL query error"); 
		$tables_names = array();
		while (list($tablename)=mysql_fetch_array($tables)) 
			{
			$tables_names[] = strtolower($tablename);
			}	
		}			
	}
// Okay, now as we have the reference data, we can start working
if($maintenance_mode == 'install') {
	// This is a new install, just create the tables and their content
	foreach($tables_to_check as $table_to_check) {
		if($table_to_check['action'] == 'create') {
			// Action is to create the table
			if(!in_array($table_to_check['table_name'], $tables_names)) {
				// The table isn't in the existing tables list, create it
				$result_create = mysql_query($table_to_check['create_delete_query'], $connexion);
				if (!$result_create) {
					// Query failed, add the error message
					$tables_actions_error_messages[] = mysql_error();
				}
				// Add data in the table if needed
				if (!empty($table_to_check['insert_query'])) {
					// Check if the insert query is a filename or a standard query
					if (strpos($table_to_check['insert_query'], 'INSERT') !== false) {
						$result_insert = mysql_query($table_to_check['insert_query'], $connexion);
					} else {
						// use the SQL file in data directory
						$result_insert = mysql_query(file_get_contents(dirname(__FILE__) . '/data/' . $table_to_check['insert_query']), $connexion);
					}
					if (!$result_insert) {
						// Query failed, add the error message
						$tables_actions_error_messages[] = mysql_error();
					}
				}
			}
		}
		if (isset($table_to_check['execute_after']) && !empty($table_to_check['execute_after'])) {
			call_user_func($table_to_check['execute_after']);
		}
	}
} else {
// This is an update	
if ($version < 301) {
	//CHANGE SIZE OF TRUSTUSERAGENT FIELD
	$sqlupdateversion ='ALTER TABLE crawlp_site_setting CHANGE trustuseragent trustuseragent VARCHAR(650)';
	$requeteupdateversion = mysql_query($sqlupdateversion,$connexion);
}
if ($version < 310) {
	//ADD HOTLINKOK FIELD
			$table_info_res = mysql_query("SHOW COLUMNS FROM crawlp_site_setting");
			$table_field_names = array();
			while ($table_info = mysql_fetch_assoc($table_info_res)) {
				$table_field_names[] = $table_info['Field'];
			}
		if (!in_array('hotlinkok', $table_field_names)) {	
	$sqlupdateversion ='ALTER TABLE crawlp_site_setting ADD hotlinkok text NOT NULL';
	$requeteupdateversion = mysql_query($sqlupdateversion,$connexion);
		}
}
if ($version < 323) {
	//ADD Listindex FIELD
			$table_info_res = mysql_query("SHOW COLUMNS FROM crawlp_site_setting");
			$table_field_names = array();
			while ($table_info = mysql_fetch_assoc($table_info_res)) {
				$table_field_names[] = $table_info['Field'];
			}
		if (!in_array('listindex', $table_field_names)) {	
	$sqlupdateversion ='ALTER TABLE crawlp_site_setting ADD listindex tinyint(3) NOT NULL';
	$requeteupdateversion = mysql_query($sqlupdateversion,$connexion);
		}
		if (!in_array('badfile', $table_field_names)) {	
	$sqlupdateversion ="ALTER TABLE crawlp_site_setting ADD  badfile varchar(245) NOT NULL default '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0'";
	$requeteupdateversion = mysql_query($sqlupdateversion,$connexion);	
		}
}		
if ($version < 324) {
	//ADD url_image FIELD
			$table_info_res = mysql_query("SHOW COLUMNS FROM crawlp_site_setting");
			$table_field_names = array();
			while ($table_info = mysql_fetch_assoc($table_info_res)) {
				$table_field_names[] = $table_info['Field'];
			}

		if (!in_array('url_image', $table_field_names)) {	
	$sqlupdateversion ='ALTER TABLE crawlp_site_setting ADD  url_image varchar(245) NOT NULL';
	$requeteupdateversion = mysql_query($sqlupdateversion,$connexion);	
		}	
}
}
?>
