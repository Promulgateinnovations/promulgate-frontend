<?php

namespace Promulgate\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\ResponseInterface;

class Api
{
	private $GuzzleClientApi;


	/**
	 * Api constructor.
	 *
	 * @param array  $params
	 */
	public function __construct(array $params = [])
	{
		$source = $params['api_source'] ?? false;

		if($source == 'promulgate') {

			$this->GuzzleClientApi = new Client([
				'base_uri'    => USE_TEST_API ? env('API_BASE_URL_TEST') : env('API_BASE_URL'),
				'headers'     => [
					'Content-Type'  => 'application/json',
					'Accept'        => "application/json",
					'Authorization' => "Basic ".base64_encode(env('API_USERNAME').":".env('API_PASSWORD')),
				],
				'http_errors' => false,
				'verify'      => false,
			]);

		} else {

			$this->GuzzleClientApi = new Client();

		}
	}


	public function makeRequest($method, $endpoint, $params = [])
	{

		$response = [];

		try {

			$current_request_details = [];
			$params['on_stats']      = function(TransferStats $stats) use (&$current_request_details)
			{
				//$current_request_url = $stats->getEffectiveUri();
				$current_request_details['url']  = $stats->getRequest()->getUri();
				$current_request_details['body'] = json_encode(json_decode($stats->getRequest()->getBody(), true), JSON_PRETTY_PRINT);
			};

			$response = $this->GuzzleClientApi->request($method, $endpoint, $params);
		}
		catch (GuzzleException $guzzleException) {

			api_errors([
				'error_type'    => 'api',
				'error_message' => $guzzleException->getMessage(),
				'request_data'  => $current_request_details,
				'status_code'   => '-',
			]);
		}
		$response_code = 0;

		if($response instanceof ResponseInterface) {

			$response_code = $response->getStatusCode();
			$response      = json_decode($response->getBody()->getContents(), true) ?: [];

			switch ($response_code) {

				case in_array($response_code, Config::RESPONSE_CODE_GROUPS['DEVELOPER']) :
					api_errors([
						'error_type'    => 'developer',
						'error_message' => 'Looks like there is some problem with API Server',
						'request_data'  => $current_request_details,
						'status_code'   => $response_code,
					]);
					break;

				case in_array($response_code, Config::RESPONSE_CODE_GROUPS['SERVER']) :
					api_errors([
						'error_type'    => 'api',
						'error_message' => 'Looks like there is some problem with API Server',
						'request_data'  => $current_request_details,
						'status_code'   => $response_code,
					]);
					break;
			}

		}

		api_log(false, $method, $current_request_details['url'], (int)$response_code, $params['json'], $response);

		return [
			'body' => $response,
		];
	}
}