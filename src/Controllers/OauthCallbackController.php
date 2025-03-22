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
    
        $connection_status = [];
    
        if (!$linkedin_response['source'] || (Session::pull('linkedin_oauth_state') != $linkedin_response['state'] || !$linkedin_response['code'])) {
            $connection_status = [
                'status' => false,
                'error' => [
                    'code' => 20,
                    'message' => "Something went wrong with LinkedIn Connection, please try again!",
                    'extra' => [
                        'isConfigured' => false,
                    ],
                ],
            ];
        } else {
            $LinkedInClient = new \LinkedIn\Client(env('LINKEDIN_CLIENT_ID'), env('LINKEDIN_CLIENT_SECRET'));
            $LinkedInClient->setRedirectUrl(getAbsoluteUrl('oauth_linkedin_callback', null, [
                'source' => 'connection',
            ]));
    
            try {
                $access_token = $LinkedInClient->getAccessToken($linkedin_response['code']);
                $profile_information = $LinkedInClient->api('/v2/me');
    
                if ($profile_information['id'] && $access_token->getToken()) {
    
                    // Fetch LinkedIn organization pages
                    $organizationPages = $LinkedInClient->api('/v2/organizations?q=owners&owners=urn:li:person:' . $profile_information['id']);
    
                    if (isset($organizationPages['elements']) && !empty($organizationPages['elements'])) {
                        // Loop through organization pages and save them
                        foreach ($organizationPages['elements'] as $page) {
                            $connection_info = [
                                'connection_name' => $page['localizedName'], // Use page name
                                'connection_media_type' => 'ORGANIC',
                                'linkedin' => urlencode(json_encode([
                                    'user_id' => $page['id'], // Use page ID
                                    'username' => $page['localizedName'], // Use page name
                                    'access_token' => $access_token->getToken(),
                                    'is_page' => true, // Flag as a LinkedIn page connection
                                ])),
                            ];
    
                            $AdminController = new AdminController();
                            $connection_status = $AdminController->saveConnectionConfiguration('linkedin', $connection_info, true);
                        }
                    } else {
                        // Save user profile if no pages are found.
                        $connection_info = [
                            'connection_name' => 'LinkedIn',
                            'connection_media_type' => 'ORGANIC',
                            'linkedin' => urlencode(json_encode([
                                'user_id' => $profile_information['id'],
                                'username' => $profile_information['localizedFirstName'],
                                'access_token' => $access_token->getToken(),
                                'is_page' => false,
                            ])),
                        ];
    
                        $AdminController = new AdminController();
                        $connection_status = $AdminController->saveConnectionConfiguration('linkedin', $connection_info, true);
                    }
                } else {
                    $connection_status = [
                        'status' => false,
                        'error' => [
                            'code' => 20,
                            'message' => "Something went wrong with LinkedIn Connection, please try again!",
                            'extra' => [
                                'isConfigured' => false,
                            ],
                        ],
                    ];
                }
            } catch (\LinkedIn\Exception $e) {
                $connection_status = [
                    'status' => false,
                    'error' => [
                        'code' => 20,
                        'message' => "Something went wrong with LinkedIn Connection, please try again!",
                        'extra' => [
                            'isConfigured' => false,
                        ],
                    ],
                ];
            }
        }
    
        $connection_status['provider_connection_name'] = 'LinkedIn';
    
        Session::set('CONNECTION_OAUTH_STATUS', $connection_status);
    
        redirect($redirect_url);
    }

}