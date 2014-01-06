<?php

/**
 * Definitions that adjust application behaviors.
 */

/**
 * Define whether the installation is running on a local environment.
 * @since 0.1
 */
define('LOCAL_ENV', 0);

/**
 * Define whether the debug mode is turned on.
 * @since 0.1
 */
define('DEBUG_MODE', 1);

/**
 * Define whether PHP errors are displayed.
 * @see load.php
 * @since 0.1
 */
define('DEBUG_DISPLAY', 1);

/**
 * Define whether PHP errors are logged.
 * @see load.php
 * @since 0.1
 */
define('DEBUG_LOG', 1);

/**
 * Define the name of the table that is used to store user login info.
 * @see login.php
 * @since 0.1
 */
define('TABLE_NAME', 'Student');



/**
 * Database configurations.
 * 
 * Edit this to change the database connection settings, and to switch
 * between local environment and remote environment.
 * 
 * @since 0.1
 */


if (defined('LOCAL_ENV') && LOCAL_ENV) {


	// ** MySQL settings - You can get this info from your web host ** //
	/** The name of the database */
	define('DB_NAME', 'sms');

	/** MySQL database username */
	define('DB_USER', 'root');

	/** MySQL database password */
	define('DB_PASSWORD', 'root');

	/** MySQL hostname */
	define('DB_HOST', 'localhost');

	/** MySQL connection port */
	define('DB_PORT', 8889);

	/** Database Charset to use in creating database tables. */
	define('DB_CHARSET', 'utf8');

	/** The Database Collate type. Don't change this if in doubt. */
	// define('DB_COLLATE', '');

	

} else {

	// ** MySQL settings - Local environment ** //
	/** The name of the database */
	define('DB_NAME', 'sms');

	/** MySQL database username */
	define('DB_USER', 'adminGugHMgR');

	/** MySQL database password */
	define('DB_PASSWORD', '1bM35cH9V-lc');

	/** MySQL hostname */
	define('DB_HOST', '127.8.146.2');

	/** MySQL connection port */
	define('DB_PORT', 3306);

	/** Database Charset to use in creating database tables. */
	define('DB_CHARSET', 'utf8');

	/** The Database Collate type. Don't change this if in doubt. */
	// define('DB_COLLATE', '');

}



?>