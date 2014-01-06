<?php

/**
 * Define the name of the cookie stored locally at the user's machine.
 * @see login.php
 * @since 0.1
 */
define('PROJECT_COOKIE_NAME', 'unique7_' . PROJECT_NAME . '_cook');

/**
 * Define the delimiter used to separate the username and hash in user's local copy.
 * @see login.php
 * @since 0.1
 */
define('PROJECT_COOKIE_DELIMITER', '_0_');


/**
 * Controller Class
 * 
 * @since 0.1
 */

/**
 * Controller Abstract Object
 */
class Controller {
	function add_contact($n, $p, $e, $q, $m) {
		global $dbo;
		$result = $dbo->insert(
			TABLE_NAME,
			array(
				'name' => $n,
				'phone' => $p,
				'email' => $e,
				'qq' => $q,
				'major' => $m
				)
			);
		return $result;
	}

	function request_contacts() {
		global $dbo;
		$result = $dbo->select(
			FALSE,
			TABLE_NAME,
			array(
				'id',
				'name',
				'phone',
				'email',
				'qq',
				'major'
				)
			);
		return $result;
	}

	function next_result ($result) {
		global $dbo;
		return $dbo->next_result($result);
	}
}