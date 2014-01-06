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
	 * ?filter_type=PPACCESS_PROVIDERSBS
	 * platform[]='<pp1>'&
	 * platform[]='<pp2>'&
	 * industry[]='<industry1>'&
	 * industry[]='<industry2>'&
	 * application[]='<application1>'&
	 * application[]='<application2>'&
	 * limit=<limit>&
	 * page=<page>
	 * 
	 * ?filter_type=PPACCESS_PRODUCTSOP&
	 * id='<id>'
	 */
	global $ppaccess;
	// Fallback to $_POST if $_GET returns nothing.
	$query = $_POST;
	if (empty($query)) {
		echo E::error(0001);
		exit;
	}
	// Fetch types switching
	switch ($query['filter_type']) {
		case PPACCESS_PROVIDERSBS:
			{
				// sleep(5);
				$limit = 10000;
				$query_limit = isset($query['limit']) ? $query['limit'] : 20;
				$offset = 0;
				$query_offset = (isset($query['page']) ? ($query['page'] - 1) * $query_limit : 0 );
				$params_array = array();
				if (isset($query['platform'])) {
					$params_array[] = $query['platform'];
				}
				if (isset($query['industry'])) {
					$params_array[] = $query['industry'];
				}
				if (isset($query['application'])) {
					$params_array[] = $query['application'];
				}
				// var_dump($query);
				$result = $ppaccess->get_provider_ids_by_params($params_array, $limit, $offset);
				if ($result === FALSE) {
					echo E::error(0001);
					exit;
				}
				if (mysqli_num_rows($result) == 0) {
					echo E::error(2000);
					exit;
				}
				$provider_ids_array = array();
				$count_of_results = mysqli_num_rows($result);
				for ($count = 0; $count < $count_of_results; $count++) { 
					$id_array = $ppaccess->next_result($result);
					if (!empty($id_array)) {
						$provider_ids_array[] = $id_array['provider_id'];
					}
				}
				
				$providers_result = $ppaccess->get_providers_by_ids($provider_ids_array);
				if ($providers_result === FALSE) {
					echo E::error(0001);
					exit;
				}
				
				// echo get_class($providers_result);
				$count_of_providers_results = mysqli_num_rows($providers_result);
				$providers_array = array();

				for ($offset_count = 0; $offset_count < $query_offset; $offset_count++) { 
					$ppaccess->next_result($providers_result);
				}
				for ($count = 0; $count < $query_limit; $count++) { 
					$p_array = $ppaccess->next_result($providers_result);
					if (!empty($p_array)) {
						$p_array['website'] = 'http://' . parse_url($p_array['website'], PHP_URL_HOST);
						$providers_array[] = $p_array;
					}
				}

				echo E::data(array(
					'result_count' => $count_of_providers_results,
					'providers' => $providers_array
					));
				exit;
			}
			break;

		case PPACCESS_PRODUCTSOP:
			{
				if (!isset($query['id'])) {
					echo E::error(0001);
					exit;
				}
				$id = $query['id'];
				$result = $ppaccess->get_products_of_provider($id);

				if ($result === FALSE) {
					echo E::error(0001);
					exit;
				}

				$result_count = mysqli_num_rows($result);
				$data_array = array();
				for ($pointer = 0; $pointer < $query_limit; $pointer++) { 
					$d_array = $ppaccess->next_result($result);
					if (!empty($d_array)) {
						$data_array[] = $d_array;
					}
				}
				if (empty($data_array)) {
					echo E::error(2001);
					exit;
				}
				echo E::data($data_array);
				exit;
			}
			break;

		case PPACCESS_PRODUCTINFOBYID:
		{
			if (!isset($query['provider_id'])) {
				echo E::error(0001);
				exit;
			}
			$provider_id = $query['provider_id'];
			$result = $ppaccess->get_providers_with_services_by_ids(array($provider_id));
			$result_count = mysqli_num_rows($result);
			$d_array = $ppaccess->next_result($result);
			if (empty($d_array)) {
				echo E::error(0001);
				exit;
			} else {
				echo E::data($d_array);
				exit;
			}
			
		}
			break;
	}