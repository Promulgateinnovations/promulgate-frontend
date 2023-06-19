<?php


namespace Promulgate\Models;


class IndexModel extends BaseModel
{
	public function __construct()
	{
		parent::__construct();

	}


	/**
	 * @param string  $email
	 * @param array   $other_details
	 *
	 * @return array
	 */
	public function login(string $email, array $other_details = []): array
	{
		if(!$email) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/agency/login', [
				'json' => [
					'email' => $email,
				],
			]
		);

	}

}