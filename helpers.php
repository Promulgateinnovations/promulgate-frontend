<?php

use Pecee\Http\Request;
use Pecee\Http\Response;
use Pecee\Http\Url;
use Pecee\SimpleRouter\SimpleRouter as Router;

/**
 * Gets the value of an environment variable.
 *
 * @param string  $key
 * @param mixed   $default
 *
 * @return mixed
 */
function env($key, $default = "MISSING_DATA")
{
	$value = $_ENV[$key] ?? ($default."-".$key);

	if($value === false) {
		return $default;
	}

	switch (strtolower($value)) {
		case 'true':
		case '(true)':
			return true;
		case 'false':
		case '(false)':
			return false;
		case 'empty':
		case '(empty)':
			return '';
		case 'null':
		case '(null)':
			return $default;
	}

	if(($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
		return substr($value, 1, -1);
	}

	return $value;
}


/**
 * Get url for a route by using either name/alias, class or method name.
 *
 * The name parameter supports the following values:
 * - Route name
 * - Controller/resource name (with or without method)
 * - Controller class name
 *
 * When searching for controller/resource by name, you can use this syntax "route.name@method".
 * You can also use the same syntax when searching for a specific controller-class "MyController@home".
 * If no arguments is specified, it will return the url for the current loaded route.
 *
 * @param string|null        $name
 * @param string|array|null  $parameters
 * @param array|null         $getParams
 * @param array|null         $options
 *
 * @return \Pecee\Http\Url
 * @throws \InvalidArgumentException
 */
function url(?string $name = NULL, $parameters = NULL, ?array $getParams = NULL, ?array $options = NULL): Url
{

	if(!(isset($options['NO_DEBUG']) && $options['NO_DEBUG'])) {

		if(ALLOWED_TO_DEBUG) {
			$getParams['devDebug'] = 1;
		}

		if(USE_TEST_API) {
			$getParams['devApi'] = 1;
		}
	}

	return Router::getUrl($name, $parameters, $getParams);
}


/**
 * @return \Pecee\Http\Response
 */
function response(): Response
{
	return Router::response();
}


/**
 * @return \Pecee\Http\Request
 */
function request(): Request
{
	return Router::request();
}


function getAbsoluteUrl(?string $name = NULL, $parameters = NULL, ?array $getParams = NULL, ?array $options = NULL)
{
	return url($name, $parameters, $getParams, $options)->getAbsoluteUrl();
}


/**
 * Get input class
 *
 * @param string|null  $index         Parameter index name
 * @param string|null  $defaultValue  Default return value
 * @param array        ...$methods    Default methods
 *
 * @return \Pecee\Http\Input\InputHandler|array|string|null
 */
function input($index = NULL, $defaultValue = NULL, ...$methods)
{
	if($index !== NULL) {
		return request()->getInputHandler()->value($index, $defaultValue, ...$methods);
	}

	return request()->getInputHandler();
}


/**
 * @param string    $url
 * @param int|null  $code
 */
function redirect(string $url, ?int $code = NULL): void
{
	if($code !== NULL) {
		response()->httpCode($code);
	}

	response()->redirect($url);
}


/**
 * Get current loaded route
 *
 * @return string|null
 */
function get_loaded_route_name(): string
{
	return request()->getLoadedRoute()->getName() ?: "";
}


/**
 * Get current csrf-token
 *
 * @return string|null
 */
function csrf_token(): ?string
{
	$baseVerifier = Router::router()->getCsrfVerifier();
	if($baseVerifier !== NULL) {
		return $baseVerifier->getTokenProvider()->getToken();
	}

	return NULL;
}


/**
 * This function will be called on Core/Api to save all errors from all endpoints called in current request & shows them in the screen when called with get_errors true
 *
 * @param array  $error
 * @param bool   $get_errors
 *
 * @return array
 */
function api_errors(array $error, bool $get_errors = false)
{

	static $api_response_errors = [];

	if($get_errors) {

		return $api_response_errors;

	} else {

		$api_response_errors[] = $error;
	}
}


/**
 * @param bool    $fetch
 * @param string  $method
 * @param string  $url
 * @param int     $response_code
 * @param array   $request
 * @param         $response
 *
 * @return array
 */
function api_log($fetch, string $method = NULL, string $url = '', int $response_code = 0, array $request = NULL, array $response = NULL)
{

	static $api_logs = [];

	if($fetch) {

		return $api_logs;

	} else {

		$api_logs[] = [
			'method'        => $method,
			'url'           => $url,
			'response_code' => $response_code,
			'request'       => $request,
			'response'      => $response,
		];
	}

}


function get_api_logs()
{
	static $api_logs;

	return $api_logs;
}


function get_date($timestamp = '', $format_number = 0)
{

	if($timestamp == '') {
		$timestamp = time();
	} elseif(strpos((string)$timestamp, '-') !== false) {
		$timestamp = strtotime($timestamp);
	}
	$timestamp = (int)$timestamp;
	$date      = date("Y-m-d", $timestamp);

	if(!$timestamp) {
		return $date;
	}

	//$date  = getdate((int)$timestamp);
	switch ($format_number) {
		case 0;
			$date = date("Y-m-d", $timestamp);
			break;
		case 1;
			$date = date("d.m.Y", $timestamp);
			break;
		case 2;
			$date = date("d-m-Y", $timestamp);
			break;
		case 3;
			$date = date("jS F Y", $timestamp);
			break;
		case 4;
			$date = date("jS F Y @ g:i A", $timestamp);
			break;
		case 5;
			$date = date("jS F'y - l@g:i A", $timestamp);
			break;
		case 6;
			$date = date("Y/m/d H:i", $timestamp);
			break;
	}

	return $date;
}


/**
 * @param       $data
 * @param bool  $var_dump
 */
function p($data, $var_dump = false)
{
	print '<br>';
	if($var_dump) {

		var_dump($data);

	} elseif(is_array($data)) {

		echo '<pre>';
		print_r($data);
		echo '</pre>';

	} else {

		print $data;
	}

}


function getValue($key, $source, $default = false)
{
	return $source[$key] ?? $default;
}


function in_arrayi($needle, $haystack)
{
	return in_array(strtolower($needle), array_map('strtolower', $haystack));
}


function getFirstCharactersFromString($string)
{
	$exploded_sting               = explode(' ', $string);
	$first_characters_from_string = '';

	foreach($exploded_sting as $v) {
		$first_characters_from_string .= substr($v, 0, 1);
	}

	return $first_characters_from_string;
}


function date_range($first, $last, $step = '+1 day', $output_format = 'd/m/Y')
{

	$dates   = array();
	$current = strtotime($first);
	$last    = strtotime($last);

	while($current <= $last) {

		$dates[] = date($output_format, $current);
		$current = strtotime($step, $current);
	}

	return $dates;
}


function getCustomUtcDate($date = '')
{

	return str_replace('+00:00', '.000Z', gmdate('c', $date));

}