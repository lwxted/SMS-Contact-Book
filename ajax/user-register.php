<?php
	require_once('../initialize.php');
	header('Content-Type: application/json; charset=utf-8');
	header("Expires: on, 01 Jan 1970 00:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	global $user;
	// Fallback to $_POST if $_GET returns nothing.
	$query = $_POST;
	if (empty($query)) {
		echo E::error(1000);
		exit;
	}
	// Check whether all the required fields are filled.
	if ( empty($query['username']) || empty($query['password']) || empty($query['email']) || empty($query['email_again']) || empty($query['role']) ) {
		echo E::error(1001);
		exit;
	}
	// Check whether the length of the username matches.
	if (strlen($query['username']) < 5) {
		echo E::error(1003);
		exit;
	}
	// Check whether the username contains invalid characters. 
	// Characters such as $ is reserved for login authentification token.
	if (strpos($query['username'], '$') !== FALSE) {
		echo E::error(1007);
		exit;
	}
	// Check whether the length of the password matches.
	if (strlen($query['password']) < 5) {
		echo E::error(1006);
		exit;
	}
	// Check if the user already existed.
	if ($user->user_exists($query['username'])) {
		echo E::error(1005);
		exit;
	}
	// Check whether the email applied is valid.
	if (!filter_var($query['email'], FILTER_VALIDATE_EMAIL)) {
		echo E::error(1002);
		exit;
	}
	// Check whether the confirmed email matches the first input.
	if ($query['email'] != $query['email_again']) {
		echo E::error(1004);
		exit;
	}
	// Insert user if everything else is all right.
	$array = $query;
	unset($array['email_again']);
	$array['password'] = $user->hash_password($array['password']);
	
	if ($user->new_user($array)) {
		echo E::success();
		exit;
	} else {
		echo E::error(1000);
		exit;
	}
