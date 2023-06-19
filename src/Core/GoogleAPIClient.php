<?php


namespace Promulgate\Core;

class GoogleAPIClient
{
	public  $client_id;
	private $GoogleClient;


	public function __construct()
	{
		$this->GoogleClient = new \Google_Client([
			'credentials' => $this->getOauthCredentials(),
		]);
		$this->client_id    = $this->GoogleClient->getConfig('client_id');

	}


	private function getOauthCredentials()
	{
		return BASE_DIR_PRIVATE.'google_client_secret_promulgate_frontend.json';
	}


	public function verifyUserCredentialsValidToken($token)
	{
		return $this->GoogleClient->verifyIdToken($token);
	}


	public function getTokensByAuthCode($code)
	{
		// This is important as we are fetching the Code using Javascript
		$this->GoogleClient->setRedirectUri('postmessage');
		return $this->GoogleClient->fetchAccessTokenWithAuthCode($code);
	}

	public function getTokenByRefreshToken($refresh_token)
	{
		return $this->GoogleClient->fetchAccessTokenWithRefreshToken($refresh_token);
	}
}