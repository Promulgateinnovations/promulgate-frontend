<?php

namespace Promulgate\Controllers;

use Josantonius\Session\Session;
use Promulgate\Core\GoogleAPIClient;
use Promulgate\Models\CampaignModel;

/**
 * Class CampaignController
 *
 * @package Promulgate\Controllers
 */
class CampaignController extends BaseController
{
	private $campaignModel;
	private $campaign_id;
	private $campaign_strategy_definition_details;


	/**
	 * CampaignController constructor.
	 *
	 * @param array  $params
	 */
	public function __construct(array $params = [])
	{
		parent::__construct($params);
		$this->campaignModel = new CampaignModel();

	}


	/**** METHODS RELATED TO WEB VIEW START **/

	public function showInitiation()
	{
		$this->Breadcrumbs->add([
			'title' => 'Initiation',
			'url'   => url('campaign_initiation'),
		]);

		$organization_id   = Session::get('organization', 'id');
		$list_of_campaigns = [];

		$all_campaigns = $this->campaignModel->getListOfCampaigns($organization_id)['body'];

		if(getValue('status', $all_campaigns) == 'success') {

			$list_of_campaigns = $all_campaigns['data']['campaignList']['rows'];

			//
			$total_no_of_campaigns           = $all_campaigns['data']['campaignList']['count'];
			$current_page                    = $all_campaigns['data']['currentPage'];
			$no_of_campaigns_in_current_page = $all_campaigns['data']['pageSize'];

		};

		$this->setViewData('initiation.html',
			[
				'campaigns_list' => $list_of_campaigns,
				'filters'        => [
					'status' => array_unique(array_column($list_of_campaigns, 'status')),
				],
				'page_title'     => "Campaign Initiation",
			]
		);
	}


	public function showStrategyDefinition($campaign_id = NULL)
	{
		$organization_id = Session::get('organization', 'id');

		$this->setCampaignIdAndDetails($campaign_id);

		$this->Breadcrumbs->add([
			'title' => 'Strategy Definition',
			'url'   => url('campaign_strategy_definition'),
		]);

		$business_details = $this->campaign_strategy_definition_details['business_details'] ?? [];

		$organization_hub                 = [];
		$organization_top_urls            = [];
		$organization_top_urls_for_script = [];

		if(!empty($business_details)) {

			$organization_top_urls = $this->getYoutubeVideoUrls($organization_id);

			foreach($organization_top_urls as $organization_top_url) {
				$organization_top_urls_for_script[$organization_top_url['name'].' - '.$organization_top_url['url']] = $organization_top_url;
			}

			$organization_hub['type'] = $business_details['type'];
			$organization_hub['url']  = $business_details['hub_url_'.$business_details['type']];
		}


		$this->setViewData('strategy_definition.html',
			[
				'form_action'                                  => url('campaign_ajax'),
				'current_campaign_strategy_definition_details' => $this->campaign_strategy_definition_details,
				'top_video_urls_by_org'                        => $organization_top_urls,
				'top_video_urls_by_org_for_script'             => json_encode($organization_top_urls_for_script),
				'organization_hub'                             => $organization_hub,
				'page_title'                                   => "Campaign Strategy Definition",
				'page_content_heading'                         => $this->getPageContentHeading(),
			]
		);
	}


	public function showTargetViewers($campaign_id = NULL)
	{
		$this->setCampaignIdAndDetails($campaign_id);

		if($this->campaign_strategy_definition_details['selected_share_with_captive_members']) {

			Session::set('REDIRECT_MESSAGES', [
				[
					'type'          => 'warning',
					'message'       => 'Target Viewers is enabled for this campaign, Please disable it in from strategy definition',
					'positionClass' => 'toast-top-full-width',
				],
			]);

			session_write_close();
			redirect(url('campaign_strategy_definition', [
				'campaign_id' => $this->campaign_id,
			]));

		}

		$this->Breadcrumbs->add([
			'title' => 'Target Viewers',
			'url'   => url('campaign_target_viewers'),
		]);


		$this->setViewData('target_viewers.html',
			[
				'form_action'                                  => url('campaign_ajax'),
				'current_campaign_strategy_definition_details' => $this->campaign_strategy_definition_details,
				'current_campaign_target_viewers'              => $this->getTargetViewerDetails(),
				'countries'                                    => $this->campaignModel->getCountries(),
				'states'                                       => $this->campaignModel->getStates(),
				'languages'                                    => $this->campaignModel->getLanguages(),
				'page_title'                                   => "Campaign Target Viewers",
				'page_content_heading'                         => $this->getPageContentHeading(),
			]
		);
	}


	public function showChannelSelection($campaign_id = NULL)
	{
		$this->setCampaignIdAndDetails($campaign_id);
		$this->Breadcrumbs->add([
			'title' => 'Channel Selection',
			'url'   => url('campaign_channel_selection'),
		]);

		$this->setViewData('channel_selection.html',
			[
				'form_action'                                  => url('campaign_ajax'),
				'current_campaign_strategy_definition_details' => $this->campaign_strategy_definition_details,
				'supported_social_media_connections'           => $this->getSocialMediaConnections(),
				'selected_social_media_connections'            => $this->getChannelSelectionSocialMediaConnections(),
				'page_title'                                   => "Campaign Channel Selection",
				'page_content_heading'                         => $this->getPageContentHeading(),
			]
		);
	}


	public function showContentCuration($campaign_id = NULL)
	{
		$this->setCampaignIdAndDetails($campaign_id);
		$this->Breadcrumbs->add([
			'title' => 'Content Curation',
			'url'   => url('campaign_content_curation'),
		]);

		// $AdminController  = new AdminController();
		$business_details = $this->campaign_strategy_definition_details['business_details'] ?? [];
		$google_drive_credentials = $business_details['assetCredentials'];

		$google_drive_access_token = "";

		if($google_drive_credentials['access_token']) {

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

		$this->setViewData('content_curation.html',
			[
				'form_action'                                  => url('campaign_ajax'),
				'current_campaign_strategy_definition_details' => $this->campaign_strategy_definition_details,
				'selected_social_media_channels_and_content'   => $this->getContentCurationSocialMediaConnections(),
				'page_title'                                   => "Campaign Content Curation",
				'page_content_heading'                         => $this->getPageContentHeading(),
				'plugins_google_drive_picker'                  => true,
				'GOOGLE_OAUTH_CLIENT_ID'                       => env('GOOGLE_OAUTH_CLIENT_ID'),
				'GOOGLE_DRIVE_API_KEY'                         => env('GOOGLE_DRIVE_API_KEY'),
				'GOOGLE_APP_ID'                                => env('GOOGLE_APP_ID'),
				// NOT SECURE BUT FOR NOW
				'GOOGLE_DRIVE_ACCESS_TOKEN'                    => $google_drive_access_token,
				'BUSINESS_URL'                                 => url('admin_business'),
			]
		);
	}


	public function showCalendar($campaign_id = NULL)
	{
		$this->setCampaignIdAndDetails($campaign_id);
		$this->Breadcrumbs->add([
			'title' => 'Campaign Calendar',
			'url'   => url('campaign_calendar'),
		]);

		$calendar_events = $this->getContentCurationSocialMediaConnections([
			'parse_full_data' => true,
		]);

		$final_calendar_events = [];

		foreach($calendar_events as $event) {

			$posts = $event['campaignContents']['campaignContentPosts'] ?? [];

			if($posts) {

				foreach($posts as $post) {
					$formatted_date = explode('@', get_date($post['postAt'], 5));

					if(!isset($formatted_date[1])) {
						continue; // TIme is missing means we cant show anything
					}
					$final_calendar_events[$formatted_date[0]][] = [
						'date'                => $formatted_date[0],
						'time'                => $formatted_date[1],
						'channel'             => $event['name'],
						'unique_name'         => $event['unique_name'],
						'campaignSelectionId' => $event['campaignSelectionId'],
						'post_id'             => $post['postId'] ?? false,
						'status'              => strtolower($post['postStatus']),
					];
				}
			}
		}

		$this->setViewData('content_calendar.html',
			[
				'form_action'                                  => url('campaign_ajax'),
				'current_campaign_strategy_definition_details' => $this->campaign_strategy_definition_details,
				'current_calendar_events'                      => $final_calendar_events,
				'page_title'                                   => "Campaign Calendar",
				'page_content_heading'                         => $this->getPageContentHeading(),
			]
		);
	}


	/**** METHODS RELATED TO AJAX START **/

	public function processAjax()
	{
		$all_input                = input()->all();
		$all_input['form_source'] = $all_input['form_source'] ?? "";
		switch ($all_input['form_source']) {

			case 'strategy_definition' :

				$campaign_id = $all_input['campaign_id'];

				if($campaign_id) {

					$this->updateStrategyDefinition($campaign_id, $all_input);

				} else {

					$this->createStrategyDefinition($all_input);
				}
				break;

			case 'target_viewers' :

				$campaign_id         = $all_input['campaign_id'];
				$campaign_viewers_id = $all_input['campaign_viewers_id'];

				if($campaign_viewers_id) {

					$this->updateTargetViewers($campaign_id, $campaign_viewers_id, $all_input);

				} else {

					$this->saveTargetViewers($campaign_id, $all_input);
				}
				break;

			case 'channel_selection' :

				$campaign_id                = $all_input['campaign_id'];
				$campaign_selected_channels = (int)$all_input['campaign_selected_channels'];


				if($campaign_selected_channels) {

					//					// UPDATE
					//					response()->json([
					//						'status' => false,
					//						'error'  => [
					//							'code'    => 20,
					//							'message' => "Updating channels is currently not supported",
					//						],
					//					]);

					// UPDATE -- Adding new ones
					$this->saveSelectedChannels($campaign_id, $all_input);

				} else {

					$this->saveSelectedChannels($campaign_id, $all_input);
				}

				break;

			case 'content_curation' :

				$campaign_channel_selection_id = $all_input['campaign_channel_selection_id'];
				$this->saveContentCuration($campaign_channel_selection_id, $all_input);

				break;

			case 'content_calendar' :
				$campaign_id   = $all_input['campaign_id'];
				$button_source = $all_input['button_source'] ?? false;

				if($button_source == 'approve_campaign') {
					$this->approveCampaign($campaign_id);
				} else {
					$this->saveComments($campaign_id, $all_input);
				}

				break;

			case 'get_enrich_video_details' :
				$video_id = $all_input['video_id'];
				$this->getYoutubeVideoDetails($video_id);
				break;

			case 'save_enrich_video_details' :
				$this->saveYoutubeVideoEnrichSettings($all_input);
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
	}


	public function createStrategyDefinition($campaign_details)
	{
		$organization_id = Session::get('organization', 'id');
		$user_id         = Session::get('user', 'id');

		if($organization_id && $user_id) {

			$campaign_details['organization_id']            = $organization_id;
			$campaign_details['user_id']                    = $user_id;
			$campaign_details['status']                     = 'NEW';
			$campaign_details['share_with_captive_members'] = isset($campaign_details['share_with_captive_members']);

			$created_campaign = $this->campaignModel->createStrategyDefinition($campaign_details)['body'];

			if(getValue('status', $created_campaign) != 'success') {

				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 20,
						'message' => $created_campaign['message'] ?? 'Campaign could not be created',
					],
				]);

			} else {

				$campaign_id = $created_campaign['data']['campaignDefinitionId'];

				response()->json([
					'status' => true,
					'data'   =>
						[
							'message' => "Campaign definition created successfully",
							'extra'   => [
								'campaign_id' => $campaign_id,
								'next_screen' => !$campaign_details['share_with_captive_members']
									? url('campaign_target_viewers', [
										'campaign_id' => $campaign_id,
									])
									: url('campaign_channel_selection', [
											'campaign_id' => $campaign_id,
										]
									),
							],
						],
				]);

			}

		} else {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => "Organization or User details are missing",
				],
			]);
		}
	}


	public function updateStrategyDefinition($campaign_id, $campaign_details)
	{
		$organization_id = Session::get('organization', 'id');
		$user_id         = Session::get('user', 'id');

		if($campaign_id & $organization_id && $user_id) {

			$campaign_details['organization_id']            = $organization_id;
			$campaign_details['user_id']                    = $user_id;
			$campaign_details['share_with_captive_members'] = isset($campaign_details['share_with_captive_members']);

			$updated_campaign = $this->campaignModel->updateStrategyDefinition($campaign_id, $campaign_details)['body'];

			if(getValue('status', $updated_campaign) != 'success') {

				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 20,
						'message' => $updated_campaign['message'],
					],
				]);

			} else {

				response()->json([
					'status' => true,
					'data'   =>
						[
							'message' => "Campaign definition updated successfully",
							'extra'   => [
								'next_screen' => !$campaign_details['share_with_captive_members']
									? url('campaign_target_viewers', [
										'campaign_id' => $campaign_id,
									])
									: url('campaign_channel_selection', [
											'campaign_id' => $campaign_id,
										]
									),
							],
						],
				]);

			}

		} else {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => "Campaign ID or Organization or User details are missing",
				],
			]);
		}
	}


	public function saveTargetViewers($campaign_id, $campaign_target_viewer_details)
	{
		if($campaign_id
			&& Session::get('user', 'id')) {

			$target_age_range                                    = explode(",", $campaign_target_viewer_details['age_range']);
			$campaign_target_viewer_data['ageMin']               = $target_age_range[0];
			$campaign_target_viewer_data['ageMax']               = $target_age_range[1];
			$campaign_target_viewer_data['psychographic']        = $campaign_target_viewer_details['psychographic'];
			$campaign_target_viewer_data['country']              = $campaign_target_viewer_details['target_country'];
			$campaign_target_viewer_data['state']                = $campaign_target_viewer_details['target_state'];
			$campaign_target_viewer_data['gender']               = implode(',', (array)$campaign_target_viewer_details['gender[]']);
			$campaign_target_viewer_data['languages']            = implode(',', (array)$campaign_target_viewer_details['target_languages']);
			$campaign_target_viewer_data['campaignDefinitionId'] = $campaign_id;

			$saved_campaign_target_viewers = $this->campaignModel->saveTargetViewers($campaign_target_viewer_data)['body'];

			if(getValue('status', $saved_campaign_target_viewers) != 'success') {

				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 20,
						//'message' => $saved_campaign_target_viewers['message'],
						'message' => "Campaign target viewers could not be saved",
					],
				]);

			} else {

				$campaign_viewers_id = $saved_campaign_target_viewers['data']['campaignViewerId'];

				response()->json([
					'status' => true,
					'data'   =>
						[
							'message' => "Campaign target viewers saved successfully",
							'extra'   => [
								'campaign_viewers_id' => $campaign_viewers_id,
								'next_screen'         => url('campaign_channel_selection', [
									'campaign_id' => $campaign_id,
								]),
							],
						],
				]);

			}

		} else {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => "Campaign or User details are missing",
				],
			]);
		}
	}


	public function updateTargetViewers($campaign_id, $campaign_viewers_id, $campaign_target_viewer_details)
	{
		if($campaign_id
			&& $campaign_viewers_id
			&& Session::get('user', 'id')
		) {

			$target_age_range                                = explode(",", $campaign_target_viewer_details['age_range']);
			$campaign_target_viewer_data['ageMin']           = $target_age_range[0];
			$campaign_target_viewer_data['ageMax']           = $target_age_range[1];
			$campaign_target_viewer_data['psychographic']    = $campaign_target_viewer_details['psychographic'];
			$campaign_target_viewer_data['country']          = $campaign_target_viewer_details['target_country'];
			$campaign_target_viewer_data['state']            = $campaign_target_viewer_details['target_state'];
			$campaign_target_viewer_data['campaignViewerId'] = $campaign_viewers_id;
			$campaign_target_viewer_data['gender']           = implode(',', (array)$campaign_target_viewer_details['gender[]']);
			$campaign_target_viewer_data['languages']        = implode(',', (array)$campaign_target_viewer_details['target_languages']);

			$updated_campaign_target_viewers = $this->campaignModel->updateTargetViewers($campaign_id, $campaign_viewers_id, $campaign_target_viewer_data)['body'];

			if(getValue('status', $updated_campaign_target_viewers) != 'success') {

				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 20,
						'message' => "Campaign target viewers could not be updated",
						//'message' => $updated_campaign_target_viewers['message'],
					],
				]);

			} else {

				response()->json([
					'status' => true,
					'data'   =>
						[
							'message' => "Campaign target viewers updated successfully",
							'extra'   => [
								'next_screen' =>
									url('campaign_channel_selection', [
										'campaign_id' => $campaign_id,
									]),
							],
						],
				]);

			}

		} else {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => "Campaign ID or Viewers ID details are missing",
				],
			]);
		}
	}


	public function saveSelectedChannels($campaign_id, $campaign_selected_channels)
	{
		$organization_id = Session::get('organization', 'id');

		if($organization_id && $campaign_id
			&& Session::get('user', 'id')) {


			$campaign_selected_channels_data['channels'] = $campaign_selected_channels['preferred_channels[]'] ?? [];
			if($campaign_selected_channels_data['channels']) {

				$campaign_selected_channels_data['channels']             = (array)$campaign_selected_channels_data['channels'];
				$campaign_selected_channels_data['orgId']                = $organization_id;
				$campaign_selected_channels_data['campaignDefinitionId'] = $campaign_id;

				$saved_campaign_selected_channels = $this->campaignModel->saveSelectedChannels($campaign_selected_channels_data)['body'];

				if(getValue('status', $saved_campaign_selected_channels) != 'success') {

					response()->json([
						'status' => false,
						'error'  => [
							'code'    => 20,
							'message' => "Campaign selected channels could not be saved",
						],
					]);

				} else {

					response()->json([
						'status' => true,
						'data'   =>
							[
								'message' => "Campaign selected channels saved successfully",
								'extra'   => [
									'campaign_selected_channels' => 1,
									'next_screen'                => url('campaign_content_curation', [
										'campaign_id' => $campaign_id,
									]),
								],
							],
					]);

				}
			} else {
				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 20,
						'message' => "Please select at least one channel",
					],
				]);
			}

		} else {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => "Organization or Campaign or User details are missing",
				],
			]);
		}
	}


	public function saveContentCuration($campaign_channel_selection_id, $content)
	{

		if($campaign_channel_selection_id
			&& Session::get('user', 'id')) {

			$final_posts_at = [];
			foreach((array)$content['when_to_post[]'] as $post_at) {
				if(trim($post_at)) {
					$final_posts_at[] = getCustomUtcDate(strtotime($post_at));
				}
			}

			$content_to_post = [
				'url'               => $content['file_url'],
				'description'       => $content['campaign_comment'],
				'tags'              => $content['curation_tags'],
				'campaignChannelId' => $campaign_channel_selection_id,
				'postAt'            => $final_posts_at,
			];

			if(isset($content['campaign_subject'])) {
				$content_to_post['subject'] = $content['campaign_subject'];
			}

			if(isset($content['campaign_to'])) {
				$content_to_post['toEmail'] = $content['campaign_to'];
			}

			$saved_campaign_selected_channels = $this->campaignModel->saveContent($content_to_post)['body'];

			if(getValue('status', $saved_campaign_selected_channels) != 'success') {

				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 20,
						'message' => "Campaign selected channels could not be saved",
					],
				]);

			} else {

				response()->json([
					'status' => true,
					'data'   =>
						[
							'message' => ($content['curation_channel'] ?? "")." Campaign Content Saved",
							'extra'   => [
								'next_tab'    => true,
								'next_screen' => url('campaign_calendar'),
							],
						],
				]);

			}
		} else {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => "Campaign Channel Information or User details are missing",
				],
			]);
		}
	}


	public function saveComments($campaign_id, $comments_data)
	{

		if($campaign_id
			&& Session::get('user', 'id') && $comments_data['review_comments']) {

			$review_data = [
				'campaignDefinitionId' => $campaign_id,
				'comments'             => $comments_data['review_comments'],
				'userId'               => Session::get('user', 'id'),
			];

			$reviewed_data = $this->campaignModel->saveComments($review_data)['body'];

			if(getValue('status', $reviewed_data) != 'success') {

				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 20,
						'message' => "Could not send for Review",
					],
				]);

			} else {

				response()->json([
					'status' => true,
					'data'   =>
						[
							'message' => "Campaign sent for review",
						],
				]);

			}
		} else {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => "Campaign Information or User details are missing",
				],
			]);
		}
	}


	public function approveCampaign($campaign_id)
	{

		if($campaign_id
			&& Session::get('user', 'id')) {

			$approved_data = $this->campaignModel->approveCampaign($campaign_id)['body'];

			if(getValue('status', $approved_data) != 'success') {

				response()->json([
					'status' => false,
					'error'  => [
						'code'    => 20,
						'message' => "Could not approve campaign",
					],
				]);

			} else {

				response()->json([
					'status' => true,
					'data'   =>
						[
							'message'     => "Campaign has been successfully approved",
							'next_screen' => url('campaign_initiation'),
						],
				]);

			}
		} else {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => "Campaign Information or User details are missing",
				],
			]);
		}
	}


	public function saveYoutubeVideoEnrichSettings($enriched_video_details)
	{
		$organization_id = Session::get('organization', 'id');

		$saved_enriched_video_details = $this->campaignModel->saveEnrichVideoSettings($organization_id, $enriched_video_details)['body'];

		if(getValue('success', $saved_enriched_video_details) != true) {

			response()->json([
				'status'  => false,
				'error'   => [
					'code'    => 20,
					'message' => $saved_enriched_video_details['message'] ?? "Video settings could not be saved",
				],
				'api_log' => api_log(true),
			]);

		} else {

			response()->json([
				'status'  => true,
				'data'    =>
					[
						'message' => "Saved video settings successfully",
					],
				'api_log' => api_log(true),
			]);

		}
	}


	protected function setWebContext()
	{
		$this->view_sub_directory_path = "/campaign/";

		$this->Breadcrumbs->add([
			'title' => 'Campaign',
			'url'   => url('campaign_initiation'),
		]);
	}


	/**** METHODS RELATED TO WEB VIEW END **/


	/**
	 * @param mixed  $campaign_id
	 */
	private function setCampaignIdAndDetails($campaign_id): void
	{
		$this->campaign_id = $campaign_id;
		$this->setCampaignDetails();

	}


	private function setCampaignDetails()
	{

		$this->campaign_strategy_definition_details = [];
		$breadcrumb_title                           = "New Campaign";

		if($this->campaign_id) {

			$campaign_details = $this->campaignModel->getStrategyDefinitionDetails($this->campaign_id)['body'];
			if(getValue('status', $campaign_details) != 'success') {

				if($this->context == 'WEB') {

					Session::set('REDIRECT_MESSAGES', [
						[
							'type'    => 'error',
							'message' => 'Invalid campaign requested, start new campaign',
						],
					]);
					session_write_close();
					redirect(url('campaign_strategy_definition', []));

				}

			} else {

				$campaign_details                           = $campaign_details['data'];
				$this->campaign_strategy_definition_details = [
					'campaign_id'                         => $this->campaign_id ?? 0,
					'campaign_name'                       => ($campaign_details['name'] ?? ""),
					'campaign_objective'                  => ($campaign_details['objective'] ?? ""),
					'campaign_topic'                      => ($campaign_details['topic'] ?? ""),
					'campaign_video_url'                  => ($campaign_details['videoUrl'] ?? ""),
					'campaign_influencers'                => ($campaign_details['influencers'] ?? ""),
					'type_of_campaign'                    => ($campaign_details['campaignTypes'] ?? ""),
					'campaign_start_date'                 => ($campaign_details['startAt'] ?? ""),
					'campaign_end_date'                   => ($campaign_details['endAt'] ?? ""),
					'campaign_targeted_audience'          => ($campaign_details['totalAudience'] ?? ""),
					'page_description_tags'               => ($campaign_details['tags']  ?? ""),
					'selected_share_with_captive_members' => ($campaign_details['captiveMembers'] ?? false),
				];

				$breadcrumb_title = $this->campaign_strategy_definition_details['campaign_name'];
			}
		}

		$AdminController                                                     = new AdminController();
		$business_details                                                    = $AdminController->getBusinessDetails(Session::get('organization', 'id'));
		$this->campaign_strategy_definition_details['page_description_tags'] = $campaign_details['tags'] ??  $business_details['page_description_tags'];
		$this->campaign_strategy_definition_details['business_details']      = $business_details ?? [];

		// Set breadcrumb for convenience
		$this->Breadcrumbs->add([
			'title' => $breadcrumb_title,
		]);

	}


	private function getPageContentHeading()
	{
		return getValue('campaign_name', $this->campaign_strategy_definition_details);
	}


	/**
	 * Fetches all info Definition, Viewers,
	 *
	 * @return array|mixed
	 */
	private function getCampaignDetails()
	{
		$organization_id = Session::get('organization', 'id');

		$campaign_details = $this->campaignModel->getCampaignDetails($organization_id, $this->campaign_id)['body'];

		if(getValue('status', $campaign_details) == 'success') {

			$campaign_details_data = $campaign_details['data'];

			$campaign_details = [
				'campaign_id'                => $this->campaign_id,
				'campaign_viewer'            => $campaign_details_data['CampaignViewer'] ?? false,
				'campaign_selected_channels' => $this->campaign_id,
			];

		} else {

			$campaign_details = [
				'campaign_id'                => $this->campaign_id,
				'campaign_viewer'            => false,
				'campaign_selected_channels' => [],
			];
		}

		return $campaign_details;

	}


	/**
	 * @param $organisation_id
	 *
	 * @return array
	 */
	private function getYoutubeVideoUrls($organization_id)
	{

		$organisation_video_urls         = [];
		$fetched_organisation_video_urls = $this->campaignModel->getTopVideoUrls($organization_id)['body'];

		if(getValue('success', $fetched_organisation_video_urls) == true) {

			$organisation_video_urls = $fetched_organisation_video_urls['videos'] ?? [];
		}

		return $organisation_video_urls;

	}


	/**
	 * @param $video_id
	 */
	private function getYoutubeVideoDetails($video_id)
	{

		$organization_id       = Session::get('organization', 'id');
		$fetched_video_details = $this->campaignModel->getVideoEnrichDetails($organization_id, $video_id)['body'];

		if(getValue('success', $fetched_video_details) == true) {

			$org_video_details = $fetched_video_details['videos']['items'][0]['snippet'] ?? [];
			if($org_video_details) {

				response()->json([
					'status'  => true,
					'data'    =>
						[
							//'message' => "Fetched youtube details",
							'extra' => [
								'video_id'     => $fetched_video_details['videos']['items'][0]['id'] ?? '',
								'title'        => $org_video_details['title'],
								'description'  => $org_video_details['description'],
								'channel_name' => $org_video_details['channelTitle'],
								'tags'         => $org_video_details['tags']?? "",
								'category_id'  => $org_video_details['categoryId'],
							],
						],
					'api_log' => api_log(true),
				]);

			} else {

				response()->json([
					'status'  => false,
					'error'   => [
						'code'    => 20,
						'message' => "Could not get the youtube details due to missing properties",
					],
					'api_log' => api_log(true),
				]);
			}
		} else {

			response()->json([
				'status'  => false,
				'error'   => [
					'code'    => 20,
					'message' => "Could not get the youtube details",
				],
				'api_log' => api_log(true),
			]);

		}

	}


	private function getTargetViewerDetails()
	{

		$campaign_viewer_details = [
			'campaign_viewers_id' => false,
			'age_range'           => [25, 45], // Just to have the slider working
		];


		$fetched_campaign_viewer_details = $this->campaignModel->getTargetViewers($this->campaign_id)['body'];

		if(getValue('status', $fetched_campaign_viewer_details) == 'success') {

			$fetched_campaign_viewer_details = $fetched_campaign_viewer_details['data'][0]['CampaignViewer'];

			// NO Data - Create Mode
			if($fetched_campaign_viewer_details) {

				$fetched_campaign_viewer_details['gender']    = explode(',', $fetched_campaign_viewer_details['gender']);
				$fetched_campaign_viewer_details['languages'] = explode(',', $fetched_campaign_viewer_details['languages']);

				$campaign_viewer_details = [
					'campaign_viewers_id' => $fetched_campaign_viewer_details['campaignViewerId'] ?? false,
					'age_range'           => [$fetched_campaign_viewer_details['ageMin'] ?? 25, $fetched_campaign_viewer_details['ageMax'] ?? 45],
					'psychographic'       => $fetched_campaign_viewer_details['psychographic'] ?? "",
					'gender_male'         => in_arrayi("MALE", $fetched_campaign_viewer_details['gender']),
					'gender_female'       => in_arrayi("FEMALE", $fetched_campaign_viewer_details['gender']),
					'target_country'      => $fetched_campaign_viewer_details['country'] ?? "",
					'target_state'        => $fetched_campaign_viewer_details['state'] ?? "",
					'target_languages'    => $fetched_campaign_viewer_details['languages'] ?? [],
				];

			}
		}

		return $campaign_viewer_details;
	}


	public function getSocialMediaConnections($is_raw = false)
	{

		$social_media_connections = [];
		$organization_id          = Session::get('organization', 'id');

		$fetched_social_media_connections = $this->campaignModel->getSocialMediaConnections($organization_id)['body'];

		if(getValue('status', $fetched_social_media_connections) == 'success') {

			$fetched_social_media_connections = $fetched_social_media_connections['data'];

			if($fetched_social_media_connections) {

				foreach($fetched_social_media_connections as $fetched_social_media_connection) {

					$unique_name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $fetched_social_media_connection['name']));

					if($is_raw) {

						$social_media_connections
						[$unique_name] = [
							'name'                    => $fetched_social_media_connection['name'],
							'unique_name'             => $unique_name,
							'socialMediaConnectionId' => $fetched_social_media_connection['socialMediaPage']['socialMediaConnectionSocalMediaConnectionId'] ?? 0,
							'socialMediaType'         => $fetched_social_media_connection['socialMediaType'],
							'status'                  => $fetched_social_media_connection['status'],
							'isConfigured'            => $fetched_social_media_connection['isConfigured'],
							'pageName'				  =>$fetched_social_media_connection['socialMediaPage']['title'] ?? ''
						];

					} elseif($fetched_social_media_connection['isConfigured'] && $fetched_social_media_connection['status'] == 'Active') {

						$social_media_connections[$fetched_social_media_connection['socialMediaType']]
						[$unique_name] = [
							'name'                    => $fetched_social_media_connection['name'],
							'unique_name'             => $unique_name,
							'socialMediaConnectionId' => $fetched_social_media_connection['socialMediaPage']['socialMediaConnectionSocalMediaConnectionId'] ?? 0,
							'socialMediaType'         => $fetched_social_media_connection['socialMediaType'],
							'status'                  => $fetched_social_media_connection['status'],
							'isConfigured'            => $fetched_social_media_connection['isConfigured'],
							'pageName'				  =>$fetched_social_media_connection['socialMediaPage']['title'] ?? '',
						];

					}
				}

				if(!$is_raw) {
					// For looping in same order
					$social_media_connections = [
						'ORGANIC' => $social_media_connections['ORGANIC'] ?? [],
						'PAID'    => $social_media_connections['PAID'] ?? [],
						'SOCIAL'  => $social_media_connections['SOCIAL'] ?? [],
					];
				}
			}
		}

		return $social_media_connections;

	}


	private function getChannelSelectionSocialMediaConnections()
	{

		$selected_channels                 = [];
		$selected_social_media_connections = $this->campaignModel->getChannelSelectionSocialMediaConnections($this->campaign_id)['body'];

		if(getValue('status', $selected_social_media_connections) == 'success') {

			$selected_social_media_connections = $selected_social_media_connections['data'][0]['campaignSelectionChannels'];

			if($selected_social_media_connections) {

				foreach($selected_social_media_connections as $selected_social_media_connection) {

					$unique_name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $selected_social_media_connection['socialMediaConnection']['name']));

					//TODO: Ask to return Social media type as well - type might prevent issues with duplicate names
					$selected_channels[$unique_name] = [
						'name'                => $selected_social_media_connection['socialMediaConnection']['name'],
						'unique_name'         => $unique_name,
						'campaignSelectionId' => $selected_social_media_connection['campaignSelectionId'],
					];
				}
			}
		}

		return $selected_channels;

	}


	private function getContentCurationSocialMediaConnections($options = [])
	{

		$parse_full_data = isset($options['parse_full_data']) ? $options['parse_full_data'] : false;

		$selected_channels = [];
		$organization_id   = Session::get('organization', 'id');

		$selected_social_media_connections_and_content = $this->campaignModel->getCampaignDetails($organization_id, $this->campaign_id)['body'];

		if(getValue('status', $selected_social_media_connections_and_content) == 'success') {

			$selected_social_media_connections_and_content = $selected_social_media_connections_and_content['data']['campaignSelectionChannels'];

			if($selected_social_media_connections_and_content) {

				foreach($selected_social_media_connections_and_content as $selected_social_media_connection) {

					$unique_name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $selected_social_media_connection['socialMediaConnection']['name']));

					$campaign_contents = $selected_social_media_connection['campaignContents'][0] ?? [];
					if($campaign_contents) {

						if($parse_full_data) {
							$campaign_contents['campaignContentPosts'] = $campaign_contents['campaignContentPosts'] ?? [];
						} else {
							$post_at_times                             = $campaign_contents['campaignContentPosts'] ?? [];
							$campaign_contents['campaignContentPosts'] = array_column($post_at_times, 'postAt') ?? [];
						}
					}

					//TODO: Ask to return Social media type as well - type might prevent issues with duplicate names
					$selected_channels[$unique_name] = [
						'name'                => $selected_social_media_connection['socialMediaConnection']['name'],
						'unique_name'         => $unique_name,
						'campaignSelectionId' => $selected_social_media_connection['campaignSelectionId'],
						'campaignContents'    => $campaign_contents,
					];
				}
			}
		}

		return $selected_channels;

	}
	/**** METHODS RELATED TO AJAX END **/
}