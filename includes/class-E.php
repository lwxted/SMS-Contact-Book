<?php

/**
 * E
 * Error code Class
 * Class created for posting and formatting errors.
 * @since 0.1
 */
class E {

	/**
	 * Returns the error list which includes error code and description.
	 * @return array Error list
	 * @access private
	 * @since 0.1
	 */
	private static function error_list() {
		return
		array(
			0000 => __('This is an error.'),
			0001 => __('请输入你的名字。'),
			0002 => __('请输入你的电话。'),
			0003 => __('请输入你的邮箱。'),
			0004 => __('出现了错误，请再试一次。')
		);
	}

	/**
	 * Return a JSON-format error.
	 * @param  int    $error_code Designated error code
	 * @return string             JSON-format error
	 * @access public
	 * @since 0.1
	 */
	static function error ($error_code) {
		$error_list = self::error_list();
		if (array_key_exists($error_code, $error_list)) {
			$payload = __($error_list[$error_code]);
		}
		$error_array[IDT] = IDT_ERROR;
		$error_array['error_code'] = $error_code;
		if (!isset($payload)) {
			$error_array['payload'] = NULL;
		} else {
			$error_array['payload'] = $payload;
		}
		$json = json_encode($error_array);
		if ($json) {
			return $json;
		} else {
			return;
		}
	}

	/**
	 * Return a JSON-format success.
	 * @return string             JSON-format success
	 * @access public
	 * @since 0.1
	 */
	static function success () {
		$success_array[IDT] = IDT_SUCCESS;
		$json = json_encode($success_array);
		if ($json) {
			return $json;
		} else {
			return;
		} 
	}

	/**
	 * Return a JSON-format data array.
	 * @param  mixed  $value Data
	 * @return string        JSON-format data
	 * @access public
	 * @since 0.1
	 */
	static function data ($value) {
		$data_array[IDT] = IDT_DATA;
		$data_array['data'] = $value;
		$json = json_encode($data_array);
		if ($json) {
			return $json;
		} else {
			return;
		}
	}

}