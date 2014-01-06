<?php
	require_once('../initialize.php');
	header('Content-Type: application/json; charset=utf-8');
	header("Expires: on, 01 Jan 1970 00:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	global $service;
	// Fallback to $_POST if $_GET returns nothing.
	$query = $_POST;
	if (empty($query)) {
		echo E::error(0001);
		exit;
	}
	// Fetch types switching
	switch ($query['manage_type']) {
		case MANAGE_ADD_SERVICE:
			{
				if (mb_strlen($query['name']) > 200) {
					echo E::error(3000);
					exit;
				}
				if (empty($query['name'])) {
					echo E::error(3002);
					exit;
				}
				if (empty($query['provider_name'])) {
					echo E::error(3003);
					exit;
				} else {
					if ($query['provider_name'] == "0") {
						echo E::error(3003);
						exit;
					}
				}
				if (empty($query['service_type'])) {
					echo E::error(3005);
					exit;
				} else {
					if ($query['service_type'] == "0") {
						echo E::error(3005);
						exit;
					}
				}
				$provider_name = $query['provider_name'];
				$provider_id = $service->provider_id_by_name($provider_name);
				if (empty($provider_id)) {
					echo E::error(3001);
					exit;
				}
				$service_name = $query['name'];
				$service_type = $query['service_type'];
				$description = $query['description'];

				$data_array = array(
						'name' => $service_name,
						'provider_id' => $provider_id,
						'service_type' => $service_type,
						'description' => $description
						);

				$result = $service->add_service_entry(
						$data_array
					);
				if (!$result) {
					echo E::error(3004);
					exit;
				} else {
					echo E::success();
					exit;
				}
			}
			break;

		case MANAGE_LIST_OF_PROVIDERS:
		{
			$result = $service->list_of_providers_with_service();
			$providers_array = array();
			$count_of_results = mysqli_num_rows($result);
			for ($count = 0; $count < $count_of_results; $count++) { 
				$id_array = $service->next_result($result);
				if (!empty($id_array)) {
					$providers_array[] = $id_array['name'];
				}
			}
			echo E::data($providers_array);
			exit;
		}
			break;
	}
