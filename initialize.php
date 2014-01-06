<?php

mb_internal_encoding('UTF-8');

mb_http_output('UTF-8');

/**
 * All initial settings. Also imports all definitions and database configurations.
 */

global $file_name;
$file_name = substr(basename($_SERVER['PHP_SELF']), 0, -4);

global $ver;
$ver = 6;

// Define the absolute path of the root directory.
if ( !defined('ABS_PATH') ) {
	define('ABS_PATH', dirname(__FILE__) );
}

// Define the absolute path of the 'includes' folder that contains all files to be included.
if ( !defined('INCLUDES_DIR') ) {
	define('INCLUDES_DIR', 'includes');
	define('INCLUDES_PATH', ABS_PATH . '/' . INCLUDES_DIR);
}

// Define the absolute path of the 'models' folder that contains all files to be included.
if ( !defined('MODELS_DIR') ) {
	define('MODELS_DIR', 'ajax');
	define('MODELS_PATH', ABS_PATH . '/' . MODELS_DIR);
}

// Define the absolute path of the contents folder that contains all dynamically generated files.
if ( !defined('CONTENTS_DIR') ) {
	define('CONTENTS_DIR', 'contents');
	define('CONTENTS_PATH', ABS_PATH . '/' . CONTENTS_DIR);
}

// Define the absolute path of the assets folder that contains all static files.
if ( !defined('ASSETS_DIR') ) {
	define('ASSETS_DIR', 'assets');
	define('ASSETS_PATH', ABS_PATH . '/' . ASSETS_DIR);
}

if ( !defined('CONFIGS_DIR') ) {
	define('CONFIGS_DIR', 'configs');
	define('CONFIGS_PATH', ABS_PATH . '/' . CONFIGS_DIR);
}

// Define the absolute path of the pages folder that contains all pages to be shown
if ( !defined('PAGES_DIR') ) {
	define('PAGES_DIR', 'pages');
	define('PAGES_PATH', ABS_PATH . '/' . PAGES_DIR);
}

// Define the absolute path of the main folder that contains main block elements.
if ( !defined('COMMON_DIR') ) {
	define('COMMON_DIR', 'main');
	define('COMMON_PATH', CONTENTS_PATH . '/' .  COMMON_DIR);
}

// Figure out the HTML root path.
if (!defined('HTML_ROOT_PATH')) {
	$abs_p = ABS_PATH;
	$server_dr = $_SERVER['DOCUMENT_ROOT'];	
	$root_path = str_replace($server_dr, '', $abs_p);

	define('HTML_ROOT_PATH', $root_path);
}

// Figure out the assets path.
if (!defined('ASSETS_INC_PATH')) {
	define('ASSSETS_INC_PATH', HTML_ROOT_PATH . '/' . ASSETS_DIR);
}

// Figure out the pages path
if (!defined('PAGES_INC_PATH')) {
	define('PAGES_INC_PATH', HTML_ROOT_PATH . '/' . PAGES_DIR);
}

if ( !defined('LOGIN_PAGE_REDR_DIR') ) {
	define('LOGIN_PAGE_REDR_DIR', 'account');
	define('LOGIN_PAGE_REDR_PATH', HTML_ROOT_PATH . '/' . PAGES_DIR . '/login.php');
}

// Load main configuration file.
require_once (CONFIGS_PATH . '/config.php');

require_once (INCLUDES_PATH . '/load-functions.php');	

require_once (INCLUDES_PATH . '/load.php');

require_once (INCLUDES_PATH . '/controller.php');


// Disable magic quotes at runtime. 
@ini_set( 'magic_quotes_runtime', 0 );
@ini_set( 'magic_quotes_sybase',  0 );

// Automatically calculates offsets from UTC.
date_default_timezone_set( 'Asia/Shanghai' );

// Check whether the installation is running in debug mode. Parameters can be defined in config.php.
check_debug_mode();

// Initialize the database access object, which can be accessed using global $dbo;.
init_db_object();

// Initialize the global Controller object.
init_access_object();
?>