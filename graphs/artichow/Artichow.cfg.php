<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */

/*
 * Path to Artichow
 */
 
$version = (int)substr(phpversion(), 0, 1);
define('ARTICHOW', dirname(__FILE__).DIRECTORY_SEPARATOR.'php'.$version);


/*
 * Path to TrueType fonts
 * DO NOT USE FONT PATH WITH SPACE CHARACTER (" ") WITH GD <= 2.0.18
 */
if(!defined('ARTICHOW_FONT')) {
	
	define('ARTICHOW_FONT', ARTICHOW.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'font');
	
}

/*
 * Patterns directory
 */
if(!defined('ARTICHOW_PATTERN')) {
	
	define('ARTICHOW_PATTERN', ARTICHOW.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'patterns');
	
}

/*
 * Images directory
 */
if(!defined('ARTICHOW_IMAGE')) {
	
	define('ARTICHOW_IMAGE', ARTICHOW.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'images');
	
}

/*
 * Enable/disable cache support
 */
define('ARTICHOW_CACHE', TRUE);

/*
 * Cache directory
 */
if(!defined('ARTICHOW_CACHE_DIRECTORY')) {
	
	define('ARTICHOW_CACHE_DIRECTORY', ARTICHOW.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'cache');
	
}

/*
 * Prefix for class names
 * No prefix by default
 */
define('ARTICHOW_PREFIX', '');

/*
 * Trigger errors when use of a deprecated feature
 */
define('ARTICHOW_DEPRECATED', TRUE);

/*
 * Fonts to use
 */
//mod to be able to use graph without ttf support using  modification propose by Joel Alexandre  http://paradigma.pt/ja/slog/
//test to see if ttf font is available
if(function_exists('gd_info'))
	{
	$fontttf= gd_info();
	if(@$fontttf['FreeType Linkage']=='with freetype')
		{
		$fonts = array(
		'Tuffy',
		'TuffyBold',
		'TuffyBoldItalic',
		'TuffyItalic',
		'simsun'
		);
		}
	else
		{
		$fonts = array(
		);
		}
	}
else
	{
	$fonts = array(
	);
	}
?>
