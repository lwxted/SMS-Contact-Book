<?php
	require_once('../initialize.php');
	header('Content-Type: application/json; charset=utf-8');
	header("Expires: on, 01 Jan 1970 00:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	global $access;
	// Fallback to $_POST if $_GET returns nothing.
	$query = $_POST;
	if (empty($query)) {
		$query = $_GET;
	}
	if (empty($query)) {
		echo E::error(0000);
		exit;
	}
	// sleep(5);
	switch ($query['query']) {
		case 'add_contact':
		{
			// Check whether all the required fields are filled.
			if (empty($query['name'])) {
				echo E::error(0001);
				exit;
			}
			if (empty($query['phone'])) {
				echo E::error(0002);
				exit;
			}
			if (empty($query['email'])) {
				echo E::error(0003);
				exit;
			}
			$result = $access->add_contact(
				$query['name'],
				$query['phone'],
				$query['email'],
				$query['qq'],
				$query['major']
				);
			if ($result === FALSE) {
				echo E::error(0004);
				exit;
			}
			echo E::success();
			exit;
		}
			break;

		case 'request_contacts':
		{
			$result = $access->request_contacts();
			if ($result === FALSE) {
				echo E::error(0000);
				exit;
			}
			$d = array();
			$tc = mysqli_num_rows($result);
			for ($p = 0; $p < $tc; $p++) { 
				$d[] = $access->next_result($result);
			}
			unset($result);
			echo E::data($d);
			exit;
		}
			break;
	}