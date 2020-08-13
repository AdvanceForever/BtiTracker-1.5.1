<?php
/*
* BtiTracker v1.5.1 is a php tracker system for BitTorrent, easy to setup and configure.
* This tracker is a frontend for DeHackEd's tracker, aka phpBTTracker (now heavely modified). 
* Updated and Maintained by Yupy.
* Copyright (C) 2004-2015 Btiteam.org
*/

const REQUIRED_PHP = 50300, REQUIRED_PHP_VERSION = '5.3.0';

if (PHP_VERSION_ID < REQUIRED_PHP)
	die('PHP '.REQUIRED_PHP_VERSION.' or higher is required.');

if (ini_get('register_long_arrays') || ini_get('register_globals') || ini_get('safe_mode'))
	die('PHP is configured incorrectly. Turn off safe_mode, register_globals and register_long_arrays.');

if (ini_get('mbstring.func_overload') || ini_get('mbstring.encoding_translation'))
    die('PHP is configured incorrectly. Turn off mbstring.func_overload and mbstring.encoding_translation, mult-byte function overloading, BtiTracker v1.5.1 is fully multi-byte aware.');

if (!extension_loaded('zlib'))
    die('zlib Extension has not been loaded or not installed !');

if (!extension_loaded('memcached'))
    die('Memcached Extension has not been loaded or not installed !');

if (!extension_loaded('igbinary'))
    die('igbinary Extension has not been loaded or not installed !');

header('X-Frame-Options: DENY');

if (preg_match('/(?:\< *(?:java|script)|script\:|\+document\.)/i', serialize($_SERVER)))
    die('Forbidden');
if (preg_match('/(?:\< *(?:java|script)|script\:|\+document\.)/i', serialize($_GET)))
    die('Forbidden');
if (preg_match('/(?:\< *(?:java|script)|script\:|\+document\.)/i', serialize($_POST)))
    die('Forbidden');
if (preg_match('/(?:\< *(?:java|script)|script\:|\+document\.)/i', serialize($_COOKIE)))
    die('Forbidden');

#Define Directories...
define('INCL_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('ROOT_PATH', realpath(INCL_PATH . '..' . DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
define('CLASS_PATH', INCL_PATH . 'classes' . DIRECTORY_SEPARATOR);
define('CACHE_PATH', ROOT_PATH . 'cache' . DIRECTORY_SEPARATOR);
define('ADMIN_PATH', ROOT_PATH . 'admin' . DIRECTORY_SEPARATOR);
define('BLOCKS_PATH', ROOT_PATH . 'blocks' . DIRECTORY_SEPARATOR);
define('LANG_PATH', ROOT_PATH . 'language' . DIRECTORY_SEPARATOR);
define('STYLE_PATH', ROOT_PATH . 'style' . DIRECTORY_SEPARATOR);

#Do Not Edit...
$tracker_rev = '.18';
    
?>
