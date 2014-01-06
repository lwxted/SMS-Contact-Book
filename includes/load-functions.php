<?php

/**
 * Common functions
 */

require_once (INCLUDES_PATH . '/class-DB.php');


/**
 * Return a string after translation.
 * @param  string $string String for translation
 * @return string         String after translation
 * @todo To be implemented.
 * @access public
 * @since 0.1
 */
function __($string) {
	return htmlspecialchars($string);
}

/**
 * Echo a string after translation
 * @param  string $string String for translation
 * @return string         String after translation
 * @todo To be implemented.
 * @see __
 * @access public
 * @since 0.1
 */
function _t($string) {
	echo __($string);
}

/**
 * Sets PHP error handling and handles debug mode.
 *
 * Uses three constants: DEBUG_MODE, DEBUG_DISPLAY, and DEBUG_LOG. All three can be
 * defined in wp-config.php. Example: <code> define( 'DEBUG', true ); </code>
 *
 * DEBUG_DISPLAY and DEBUG_LOG perform no function unless DEBUG is true.
 * DEBUG defaults to false.
 *
 * When DEBUG is true, all PHP notices are reported. It will also display
 * notices, including one when a deprecated function, function argument,
 * or file is used. Deprecated code may be removed from a later version.
 *
 * It is strongly recommended that plugin and theme developers use DEBUG in their
 * development environments.
 *
 * When DEBUG_DISPLAY is true, errors will be forced to be displayed.
 * DEBUG_DISPLAY defaults to true. Defining it as null prevents it from
 * changing the global configuration setting. Defining DEBUG_DISPLAY as false
 * will force errors to be hidden.
 *
 * When DEBUG_LOG is true, errors will be logged to wp-content/debug.log.
 * DEBUG_LOG defaults to false.
 *
 * @access private
 * @since 0.1
 */
function check_debug_mode() {
	if ( DEBUG_MODE ) {
		// E_DEPRECATED is a core PHP constant in PHP 5.3. Don't define this yourself.
		// The two statements are equivalent, just one is for 5.3+ and for less than 5.3.
		if ( defined( 'E_DEPRECATED' ) )
			error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT );
		else
			error_reporting( E_ALL );

		if ( DEBUG_DISPLAY )
			ini_set( 'display_errors', 1 );
		elseif ( null !== DEBUG_DISPLAY )
			ini_set( 'display_errors', 0 );

		if ( DEBUG_LOG ) {
			ini_set( 'log_errors', 1 );
			ini_set( 'error_log', CONTENTS_PATH . '/debug.log' );
		}
	} else {
		error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
	}
}


/**
 * Retrieve the path of an asset to be included.
 * @param  string $asset_type    The type of the asset
 * @param  string $path_to_asset The relative path to the asset
 * @param  bool   $flush_cache   Whether or not to forcefully flush the cache 
 * @return string                The path to the asset that can be used in HTML.
 */
function path_to_asset ($asset_type, $path_to_asset, $flush_cache) {
	$path  = ASSSETS_INC_PATH . "/{$asset_type}" . '/' . $path_to_asset;
	if ($flush_cache) {
		global $ver;
		if (!empty($ver)) {
			$path .= "?ver={$ver}";
		}
	}
	return $path;
}


/**
 * Return the path to the page that can be used in HTML.
 * @param  string $path_to_page The relative path to the page that resides under the 'pages' folder
 * @return string               The path to the page that can be used in HTML.
 */
function _page ($path_to_page) {
	return PAGES_INC_PATH . '/' . $path_to_page;
}


/**
 * Output the path to the page that can be used in HTML.
 * @param  string $path_to_page The relative path to the page that resides under the 'pages' folder
 * @since 0.1
 * @access public
 */
function __page ($path_to_page) {
	echo _page($path_to_page);
}

/**
 * Use an external CSS in a PHP file.
 * @param  string $css_file The name of the CSS file
 * @since 0.1
 * @access public
 */
function use_css ($css_file) {
	echo '<link rel="stylesheet" href="' . path_to_asset ('css', $css_file, TRUE) . '" />';
}

/**
 * Use an external JS in a PHP file.
 * @param  string $js_file The name of the JS file
 * @since 0.1
 * @access public
 */
function use_js ($js_file) {
	echo '<script src="' . path_to_asset ('js', $js_file, TRUE) . '"></script>';
}

/**
 * Return the path to the CSS that can be used in HTML.	
 * @param  string $path_to_asset The relative path to the CSS
 * @param  bool   $flush_cache   Whether or not to forcefully flush the cache 
 * @since 0.1
 * @access public
 */
function _css ($path_to_asset, $flush_cache = FALSE) {
	return path_to_asset ('css', $path_to_asset, $flush_cache);
}

/**
 * Return the path to the image that can be used in HTML.	
 * @param  string $path_to_asset The relative path to the image
 * @param  bool   $flush_cache   Whether or not to forcefully flush the cache 
 * @since 0.1
 * @access public
 */
function _img ($path_to_asset, $flush_cache = FALSE) {
	return path_to_asset ('img', $path_to_asset, $flush_cache);
}

/**
 * Return the path to the JS that can be used in HTML.	
 * @param  string $path_to_asset The relative path to the JS
 * @param  bool   $flush_cache   Whether or not to forcefully flush the cache 
 * @since 0.1
 * @access public
 */
function _js ($path_to_asset, $flush_cache = FALSE) {
	return path_to_asset ('js', $path_to_asset, $flush_cache);
}

/**
 * Output the path to the CSS that can be used in HTML.	
 * @param  string $path_to_asset The relative path to the CSS
 * @param  bool   $flush_cache   Whether or not to forcefully flush the cache 
 * @since 0.1
 * @access public
 */
function __css ($path_to_asset, $flush_cache = FALSE) {
	echo path_to_asset ('css', $path_to_asset, $flush_cache);
}

/**
 * Output the path to the image that can be used in HTML.	
 * @param  string $path_to_asset The relative path to the image
 * @param  bool   $flush_cache   Whether or not to forcefully flush the cache 
 * @since 0.1
 * @access public
 */
function __img ($path_to_asset, $flush_cache = FALSE) {
	echo path_to_asset ('img', $path_to_asset, $flush_cache);
}

/**
 * Output the path to the JS that can be used in HTML.	
 * @param  string $path_to_asset The relative path to the JS
 * @param  bool   $flush_cache   Whether or not to forcefully flush the cache 
 * @since 0.1
 * @access public
 */
function __js ($path_to_asset, $flush_cache = FALSE) {
	echo path_to_asset ('js', $path_to_asset, $flush_cache);
}

/**
 * Get the real remote IP.
 * @return string The real remote IP
 * @since 0.1
 * @access public
 */
function get_server_ip () {
	try {
		define('HEADER_NAME','HTTP_INCAP_CLIENT_IP');
		//stop process if there is no header
		if (empty($_SERVER[HEADER_NAME])) throw new Exception('No header defined', 1);
		//validate header value
		if (function_exists('filter_var')) {
			$ip = filter_var($_SERVER[HEADER_NAME], FILTER_VALIDATE_IP);
			if (false === $ip) throw new Exception('The value is not a valid IP address', 2);
		} else {
			$ip = trim($_SERVER[HEADER_NAME]);
			if (false === preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $ip)) throw new Exception('The value is not a valid IP address', 2);
		}
		return $ip;
		//At this point the initial IP value is exist and validated
	} catch (Exception $e) {
		return $_SERVER['REMOTE_ADDR'];
	}
}

/**
 * Get the contents directory path.
 * @param  string $contents_dir_name The directory name
 * @return string                    The path to the contents path
 * @since 0.1
 * @access public
 */
function get_contents_path ($contents_dir_name) {
	return CONTENTS_PATH . '/' . $contents_dir_name;
}


/**
 * Initialize the global database access object.
 * Always use global $dbo to reference to the object.
 * @since 0.1
 * @access public
 */
function init_db_object() {

	global $dbo;

	if (isset($dbo)) {
		return;
	}

	// The data access object created for all MySQL queries.
	// Always use global $dbo to reference this object.
	$dbo = new DB (DB_USER, DB_PASSWORD, DB_NAME, DB_HOST, DB_PORT);
}



/**
 * Initialize the global user abstract access object.
 * Always use global $user to reference to the object.
 * @since 0.1
 * @access public
 */
function init_user_object() {

	global $user;

	if (isset($user)) {
		return;
	}

	// The data access object created for all MySQL queries.
	// Always use global $dbo to reference this object.
	$user = new User();
}

/**
 * Initialize the global Products / Providers Accessor abstract access object.
 * Always use global $ppaccess to reference to the object.
 * @since 0.1
 * @access public
 */
function init_access_object () {
	global $access;

	if (isset($access)) {
		return;
	}
	$access = new Controller();
}

/**
 * Initialize the global Products / Providers Accessor abstract access object.
 * Always use global $ppaccess to reference to the object.
 * @since 0.1
 * @access public
 */
function init_service_object () {
	global $service;

	if (isset($service)) {
		return;
	}
	
	$service = new Service();
}