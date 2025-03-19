<?php

namespace Promulgate\Controllers;

use Josantonius\Session\Session;

/**
 * Class OauthCallbackController
 *
 * @package Promulgate\Controllers
 */
class OauthCallbackController extends BaseController
{

	/**
	 * CampaignController constructor.
	 *
	 * @param array  $params
	 */
	public function __construct(array $params = [])
	{
		parent::__construct($params);

	}


	protected function setWebContext()
	{
		// TODO: Implement setWebContext() method.
	}


	public function processAjax()
	{

	}


	public function processLinkedIn()
{
    $redirect_url = url('admin_connections'); 

    $linkedin_response = input()->all();

    if (!$linkedin_response['source'] || Session::pull('linkedin_oauth_state') != $linkedin_response['state'] || !$linkedin_response['code']) {
        Session::set('CONNECTION_OAUTH_STATUS', [
            'status' => false,
            'error'  => ['message' => "Something went wrong with LinkedIn Connection, please try again!"]
        ]);
        redirect($redirect_url);
    }

    $LinkedInClient = new \LinkedIn\Client(env('LINKEDIN_CLIENT_ID'), env('LINKEDIN_CLIENT_SECRET'));
    $LinkedInClient->setRedirectUrl(getAbsoluteUrl('oauth_linkedin_callback', NULL, ['source' => 'connection']));

    try {
        $access_token = $LinkedInClient->getAccessToken($linkedin_response['code']);
        Session::set('linkedin_access_token', $access_token->getToken()); // Store access token

        redirect($redirect_url);
    } catch (\Exception $e) {
        Session::set('CONNECTION_OAUTH_STATUS', ['status' => false, 'error' => ['message' => "Failed to authenticate with LinkedIn."]]);
        redirect($redirect_url);
    }
}

}