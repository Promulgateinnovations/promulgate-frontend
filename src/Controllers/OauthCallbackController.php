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
                    // Fetch LinkedIn organization pages using organizationalEntityAcls endpoint
                    $aclResponse = $LinkedInClient->api('/v2/organizationalEntityAcls?q=roleAssignee');
    
                    if (isset($aclResponse['elements']) && !empty($aclResponse['elements'])) {
                        $orgUrns = array_map(function ($org) {
                            return $org['organizationalTarget'] ?? null;
                        }, $aclResponse['elements']);
    
                        $orgUrns = array_filter($orgUrns);
    
                        foreach ($orgUrns as $urn) {
                            try {
                                $orgId = str_replace('urn:li:organization:', '', $urn);
                                $orgDetails = $LinkedInClient->api('/v2/organizations/' . rawurlencode($orgId));
                                $pageDescription = $orgDetails['localizedDescription'] ?? '';
                                $pageIndustry = $orgDetails['industries'][0] ?? '';
    
                                $connection_info = [
                                    'connection_name' => $orgDetails['localizedName'],
                                    'connection_media_type' => 'ORGANIC',
                                    'linkedin' => urlencode(json_encode([
                                        'user_id' => $urn,
                                        'username' => $orgDetails['localizedName'],
                                        'access_token' => $access_token->getToken(),
                                        'is_page' => true,
                                        'page_description' => $pageDescription,
                                        'page_industry' => $pageIndustry,
                                        'pageName' => $orgDetails['localizedName'],
                                    ])),
                                ];
    
                                $AdminController = new AdminController();
                                $connection_status = $AdminController->saveConnectionConfiguration('linkedin', $connection_info, true);
                                if($connection_status['status'] === false){
                                    break;
                                }
                            } catch (\LinkedIn\Exception $orgDetailException) {
                                $connection_status = [
                                    'status' => false,
                                    'error' => [
                                        'code' => 20,
                                        'message' => "Error fetching LinkedIn organization details: " . $orgDetailException->getMessage(),
                                        'extra' => ['isConfigured' => false],
                                    ],
                                ];
                                break;
                            }
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
                                'pageName' => $profile_information['localizedFirstName'],
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