<?php
	require_once('../initialize.php');
	header('Content-Type: application/json; charset=utf-8');
	header("Expires: on, 01 Jan 1970 00:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	/**
	 * QUERY STRING FORMAT:
	 * 
	 * ?username=<username>&
	 * password=<password>&
	 * remember-me={0, 1}
	 */
	global $user;
	// Fallback to $_POST if $_GET returns nothing.
	$query = $_POST;
	// Check whether the query itself is empty.
	if (empty($query)) {
		echo E::error(1100);
		exit;
	}
	// Check whether all fields are filled in.
	if (empty($query['username']) || empty($query['password'])) {
		echo E::error(1101);
		exit;
	}
	// Check whether the user actually exists.
	if (!$user->user_exists($query['username'])) {
		echo E::error(1102);
		exit;
	}
	// Check whether the credential matches.
	if (!$user->credential_matches($query['username'], $query['password'])) {
		echo E::error(1103);
		exit;
	}
	// Attempt to write cookie and server side authentification.
	$remember = FALSE;
	if (isset($query['remember-me']) && $query['remember-me'] == 1) {
		$remember = TRUE;
	}
	if ($user->write_auth($query['username'], $remember)) {
		echo E::success();
		exit;
	} else {
		echo E::error(1100);
		exit;
	}
