<?php

namespace Promulgate\Controllers;

use Josantonius\Session\Session;
use Promulgate\Models\AnalyticsModel;
use Promulgate\Models\CampaignModel;
use Promulgate\Models\LeadsModel;
use Promulgate\Core\GoogleAPIClient;

/**
 * Class LeadsController
 *
 * @package Promulgate\Controllers
 */
class LeadsController extends BaseController
{
	private $analyticsModel;
	private $campaignModel;
	private $leadsModel;
	private $organizationId;


	/**
	 * AdminController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->analyticsModel = new AnalyticsModel();
		$this->campaignModel  = new CampaignModel();
		$this->leadsModel = new LeadsModel();
	}


	protected function setWebContext()
	{

		$this->view_sub_directory_path = "/leads/";
		$this->Breadcrumbs->add([
			'title' => 'Leads',
			'url'   => url('leads'),
		]);

		$this->setOrganizationId(Session::get('organization', 'id'));
	}


	private function setOrganizationId($organizationId)
	{
		if($organizationId && !is_array($organizationId)) {
			$organizationId       = trim($organizationId);
			$this->organizationId = $organizationId;
		}
	}


	public function showLeads()
	{

		$this->Breadcrumbs->add([
			'title' => 'Leads',
			'url'   => url('leads'),
		]);

		
		$org_id = Session::get('organization', 'id');


		$leads_details = $this->leadsModel->getLeadsDetails($org_id);
		$this->setViewData('leads.html',
			[
				'form_action'           => url('analytics_ajax'),
				'page_title'            => "leads",
				'lead_details' 			=> $leads_details['body'] ? $leads_details['body']['data'] : [],
				'hide_side_menu' => false,  // Show side menu for Organization page
                'hide_side_bar' => true, 
			]
		);

	}

	public function showBroadcastedLeads()
	{

		$this->Breadcrumbs->add([
			'title' => 'Broadcasted',
			'url'   => url('leads'),
		]);

		
		$org_id = Session::get('organization', 'id');
		$leads_details = $this->leadsModel->getBroadcastedLeads($org_id);
		$this->setViewData('broadcasted_leads.html',
			[
				'form_action'           => url('analytics_ajax'),
				'page_title'            => "leads",
				'lead_details' 			=> $leads_details['body'] ? $leads_details['body']['data'] : [],
				'hide_side_menu' => false,  // Show side menu for Organization page
                'hide_side_bar' => true, 
			]
		);

	}

	public function createLeadDetails()
	{
		$all_input = input()->all();
	}

	public function showLeadsDetails($lead_id)
	{

		$this->Breadcrumbs->add([
			'title' => 'Leads',
			'url'   => url('leads'),
		]);

		$lead_contacts = $this->leadsModel->getLeadContacts($lead_id);
		// print_r($lead_contacts);
		// exit();

		// print_r($led_id);
		// exit();

		// $all_campaigns     = $this->campaignModel->getListOfCampaigns($this->organizationId)['body'];


		// $subscriptionDetails = []

		// if(getValue('status', $organization_channels) == 'success') {
		// 	$subscriptionDetails = $organization_channels['data']
		// }



		$users_count_per_day   = $this->analyticsModel->getUsersCountForGraph();
		$this->setViewData('lead_details.html',
			[
				'form_action'           => url('leads_ajax'),
				//'graph_users_count'     => json_encode($users_count_per_day, true),
				//'last_days_users_count' => array_sum(array_column($users_count_per_day, 1)) ?? 0,
				'page_title'            => "leads",
				'show_only_content'         => true,
				'lead_contacts' 		=> $lead_contacts['body'] ? $lead_contacts['body']['data'] : [],
				'hide_side_menu' => false,  // Show side menu for Organization page
                'hide_side_bar' => true, 
			]
		);

	}

	public function showBroadcastedLeadsDetails($broadcast_id)
	{

		$this->Breadcrumbs->add([
			'title' => 'Leads',
			'url'   => url('leads'),
		]);

		$lead_logs = $this->leadsModel->getLeadLogs($broadcast_id);



		$this->setViewData('broadcasted_lead_details.html',
			[
				'form_action'           => url('leads_ajax'),
				'page_title'            => "Broadcasted Lead Details",
				'show_only_content'         => true,
				'lead_contacts' 		=> $lead_logs['body'] ? $lead_logs['body']['data'] : []
			]
		);

	}

	public function showWhatsappAnalytics()
	{
		//overall analytics

		$this->Breadcrumbs->add([
			'title' => 'Leads',
			'url'   => url('leads'),
		]);
		$org_id = Session::get('organization', 'id');
		$whatsAppAnalytics = $this->leadsModel->getWhatsAppAnalytics($org_id);



		$this->setViewData('whatsapp_analytics.html',
			[
				'form_action'           => url('leads_ajax'),
				'page_title'            => "WhatsApp Analytics",
				'show_only_content'         => true,
				'whatsAppAnalytics' 		=> $whatsAppAnalytics['body'] ? $whatsAppAnalytics['body']['data'] : []
			]
		);

	}

	public function showUpload()
	{

		$this->Breadcrumbs->add([
			'title' => 'Upload',
			'url'   => url('upload'),
		]);
		$org_id = Session::get('organization', 'id');
		$this->setViewData('upload.html',
			[
				'form_action'           => url('leads_ajax'),
				'API_BASE_URL'    => env('API_BASE_URL'),
				'API_TOKEN' 		=> 'cHJvbXVsZ2F0ZTpwcm9tdWxnYXRl',
				'page_title'            => "leads",
				'org_id' => $org_id
			]
		);
	}

	public function showLeadsBroadcast()
	{
		$all_input = input()->all();
		$all_leads = isset($all_input['leads']) ? json_decode($all_input['leads']) : [];

		if(count($all_leads) <= 0) {
			Session::set('REDIRECT_MESSAGES', [
				[
					'type'          => 'error',
					'message'       => 'Select atlast one lead to broadcast.',
					'positionClass' => 'toast-top-full-width',
				],
			]);

			session_write_close();
			redirect(url('leads'));
		} else {
			$org_id = Session::get('organization', 'id');
			$content_to_post = [
				'org_id' => $org_id
			];
			$checkTokenAvailable     = $this->leadsModel->checkFbToken($content_to_post);
			if(!isset($checkTokenAvailable['body']['status']) && $checkTokenAvailable['body']['status'] == "success") {
				Session::set('REDIRECT_MESSAGES', [
					[
						'type'          => 'error',
						'message'       => 'Please connect whatsapp first...',
						'positionClass' => 'toast-top-full-width',
					],
				]);

				session_write_close();
				redirect(url('admin_connections'));
			}
			
			$this->Breadcrumbs->add([
				'title' => 'Broadcast',
				'url'   => url('leads'),
			]);
			$org_id = Session::get('organization', 'id');
			$content_to_post = [
				'org_id' => $org_id
			];
			$wp_templates = $this->leadsModel->getTemplates($content_to_post)['body'];
			$all_campaigns = $this->campaignModel->getListOfCampaigns($org_id)['body'];
			$list_of_campaigns = [];

			if(getValue('status', $all_campaigns) == 'success') {
				$list_of_campaigns = $all_campaigns['data']['campaignList']['rows'];
			};

			//print_r($list_of_campaigns);exit();

			$AdminController  = new AdminController();
			$CampaignController     = new CampaignController([
				'context' => 'data',
			]);
			$business_details  = $AdminController->getBusinessDetails(Session::get('organization', 'id'));
			$google_drive_credentials = $business_details['assetCredentials'];

			$google_drive_access_token = "";

			if(isset($google_drive_credentials['access_token'])) {

				// Check for access token validation
				$access_token_expiry = $google_drive_credentials['created'] + ($google_drive_credentials['expires_in'] - 300);

				// P(date("Y-m-d H:i:s", time()) < date("Y-m-d H:i:s", $access_token_expiry), true);
				if(!(time() < $access_token_expiry)) {

					// Get credentials By token
					$GoogleApiClient = new GoogleAPIClient();
					$user_tokens     = $GoogleApiClient->getTokenByRefreshToken($google_drive_credentials['refresh_token']);

					if(!isset($user_tokens['error'])) {

						$google_drive_credentials = $user_tokens;
					}
				}
				$google_drive_access_token = $google_drive_credentials['access_token'];

			}
		
			$this->setViewData('broadcast.html',
				[
					'form_action'           => url('leads_ajax'),
					'wp_templates' 			=> isset($wp_templates['data']) ? $wp_templates['data'] : [],
					'page_title'            => "templates",
					'selected_leads' => $all_leads,
					'plugins_google_drive_picker'                  => true,
					'GOOGLE_OAUTH_CLIENT_ID'                       => env('GOOGLE_OAUTH_CLIENT_ID'),
					'GOOGLE_DRIVE_API_KEY'                         => env('GOOGLE_DRIVE_API_KEY'),
					'GOOGLE_APP_ID'                                => env('GOOGLE_APP_ID'),
					'GOOGLE_DRIVE_ACCESS_TOKEN'                    => $google_drive_access_token,
					'BUSINESS_URL'                                 => url('admin_business'),
					'list_of_campaigns' 			=> $list_of_campaigns,
				]
			);
		}
	}
	
	public function addNewTemplates()
	{

		$this->Breadcrumbs->add([
			'title' => 'Add Template',
			'url'   => url('broadcast'),
		]);


		$AdminController  = new AdminController();
		$CampaignController     = new CampaignController([
			'context' => 'data',
		]);
		$business_details  = $AdminController->getBusinessDetails(Session::get('organization', 'id'));
		$google_drive_credentials = $business_details['assetCredentials'];

		$google_drive_access_token = "";

		if(isset($google_drive_credentials['access_token'])) {

			// Check for access token validation
			$access_token_expiry = $google_drive_credentials['created'] + ($google_drive_credentials['expires_in'] - 300);

			// P(date("Y-m-d H:i:s", time()) < date("Y-m-d H:i:s", $access_token_expiry), true);
			if(!(time() < $access_token_expiry)) {

				// Get credentials By token
				$GoogleApiClient = new GoogleAPIClient();
				$user_tokens     = $GoogleApiClient->getTokenByRefreshToken($google_drive_credentials['refresh_token']);

				if(!isset($user_tokens['error'])) {

					$google_drive_credentials = $user_tokens;
				}
			}
			$google_drive_access_token = $google_drive_credentials['access_token'];

		}

		
		$this->setViewData('add_template.html',
			[
				'form_action'           => url('leads_ajax'),
				'current_campaign_strategy_definition_details' => $business_details,
				'page_title'            => "add templates",
				'plugins_google_drive_picker'                  => true,
				'GOOGLE_OAUTH_CLIENT_ID'                       => env('GOOGLE_OAUTH_CLIENT_ID'),
				'GOOGLE_DRIVE_API_KEY'                         => env('GOOGLE_DRIVE_API_KEY'),
				'GOOGLE_APP_ID'                                => env('GOOGLE_APP_ID'),
				// NOT SECURE BUT FOR NOW
				'GOOGLE_DRIVE_ACCESS_TOKEN'                    => $google_drive_access_token,
				'BUSINESS_URL'                                 => url('admin_business')
			]
		);

	}

	public function processAjax()
	{
		//$this->showLeads();
		// redirect('/leads/leads');
		// return;
		$all_input = input()->all();
		$all_input['form_source'] = $all_input['form_source'] ?? "";

		switch ($all_input['form_source']) {

			case 'lead_upload' :
				$this->leadsModel->saveLeadsDetails($all_input);
				break;
			
			case 'whatsapp_content_curation' :
				$selected_leads = json_decode($all_input['selected_leads']);
				$this->saveContentCuration($selected_leads, $all_input);
				break;

			case 'delete_lead' :
				$lead_id = $all_input['lead_id'];
				$this->deleteLead($lead_id);
				break;

			case 'read_inbox_msg' :
				$inbox_id = $all_input['inbox_id'];
				$inbox_read_status = $all_input['inbox_read_status'];
				$this->readSocialInbox($inbox_id, $inbox_read_status);
				break;

			case 'add_template' :
				$this->saveNewWaTemplate($all_input);
				break;

			case 'delete_user' :
				$userId = Session::get('user', 'id');
				$this->deleteUser($userId);
				break;

			default:
				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 100,
						'message' => 'Invalid data',
					],
				]);
				break;
		}


		// $all_input['form_source'] = $all_input['form_source'] ?? "";

		// switch ($all_input['form_source']) {

		// 	default:
		// 		response()->json([
		// 			'status' => false,
		// 			'error'  => [
		// 				'code'    => 100,
		// 				'message' => 'Invalid data',
		// 			],
		// 		]);
		// 		break;
		// }
	}

	public function saveNewWaTemplate($content)
	{

		if(Session::get('user', 'id')) {
			$agency_id = Session::get('agency', 'id');
			$org_id = Session::get('organization', 'id');

			$content_to_post = [
				'url'               => $content['file_url'],
				'description'       => $content['template_body'],
				'cta'              => $content['cta'],
				'templateName'		=> $content['template_name'],
				'agency_id' => $agency_id,
				'org_id' => $org_id
			];


			$saved_wa_template = $this->leadsModel->addTemplate($content_to_post)['body'];

			if(getValue('status', $saved_wa_template) != 'success') {

				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 20,
						'message' => $saved_wa_template['message'] ?? "Template could not be created",
					],
				]);

			} else {

				response()->json([
					'status' => true,
					'data'   =>
						[
							'message' => "Whatsapp Template Created",
							'extra'   => [
								'next_tab'    => false,
								'next_screen' => url('broadcasted_leads'),
							],
						],
				]);

			}
		} else {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => "Template Information or User details are missing",
				],
			]);
		}
	}

	public function saveContentCuration($selected_leads, $content)
	{

		if(count($selected_leads) > 0
			&& Session::get('user', 'id')) {

			$org_id = Session::get('organization', 'id');
			$agency_id = Session::get('agency', 'id');
			$final_posts_at = [];
			foreach((array)$content['when_to_post[]'] as $post_at) {
				if(trim($post_at)) {
					$final_posts_at[] = getCustomUtcDate(strtotime($post_at));
				}
			}

			$content_to_post = [
				'curation_channel'               => $content['curation_channel'],
				'selected_leads'       => $content['selected_leads'],
				'wa_template'              => $content['wa_template'],
				'wa_campaign'              => $content['wa_campaign'],
				'wa_template_lang'               => $content['wa_template_lang'],
				'file_url'               => $content['wa_file_url'],
				'postAt'            => $final_posts_at,
				'agency_id' => $agency_id,
				'org_id' => $org_id
			];

			
			$saved_campaign_selected_channels = $this->leadsModel->saveContent($content_to_post)['body'];

			if(getValue('status', $saved_campaign_selected_channels) != 'success') {

				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 20,
						'message' => "Broadcast could not be saved"
					],
				]);

			} else {

				response()->json([
					'status' => true,
					'data'   =>
						[
							'message' => "Whatsapp Broadcast Saved",
							'extra'   => [
								'next_tab'    => true,
								'next_screen' => url('broadcasted_leads'),
							],
						],
				]);

			}
		} else {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => "Leads Information or User details are missing",
				],
			]);
		}
	}

	public function deleteLead($lead_id)
	{

		if($lead_id) {

			$content_to_post = [
				'lead_id'               => $lead_id
			];

			
			$deleteLead = $this->leadsModel->deleteLead($content_to_post)['body'];

			if(getValue('status', $deleteLead) != 'success') {

				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 20,
						'message' => "Lead could not be deleted."
					],
				]);

			} else {

				response()->json([
					'status' => true,
					'data'   =>
						[
							'message' => "Lead deleted.",
							'extra'   => [
								'next_tab'    => true,
								'next_screen' => url('leads'),
							],
						],
				]);

			}
		} else {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => "Leads Information or User details are missing",
				],
			]);
		}
	}

	public function readSocialInbox($inbox_id, $inbox_read_status)
	{

		if($inbox_id && $inbox_read_status) {

			$content_to_post = [
				'inbox_id'               => $inbox_id,
				'inbox_read_status' => $inbox_read_status
			];

			
			$readSocialInbox = $this->leadsModel->readSocialInbox($content_to_post)['body'];

			if(getValue('status', $readSocialInbox) != 'success') {

				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 20,
						'message' => "Inbox could not be updated."
					],
				]);

			} else {

				response()->json([
					'status' => true,
					'data'   =>
						[
							'message' => "Inbox updated.",
							'extra'   => [
								'next_tab'    => true,
								'next_screen' => url('social_inbox'),
							],
						],
				]);

			}
		} else {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => "Leads Information or User details are missing",
				],
			]);
		}
	}
	
	public function deleteUser($user_id)
	{

		if($user_id) {

			$content_to_post = [
				'user_id'   => $user_id
			];

			
			$deleteUser = $this->leadsModel->deleteUser($content_to_post)['body'];
			if(getValue('status', $deleteUser) != 'success') {

				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 20,
						'message' => "User could not be deleted."
					],
				]);

			} else {
				Session::destroy('user');
				Session::destroy('organization');
				Session::destroy('agency');

				session_write_close();
				response()->json([
					'status' => true,
					'data'   =>
						[
							'message' => "User deleted, now you are logged out.",
							'extra'   => [
								'next_tab'    => true,
								'next_screen' => url('user_login'),
							],
						],
				]);
			}
		} else {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => "User details missing",
				],
			]);
		}
	}
}