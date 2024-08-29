<?php

namespace Promulgate\Controllers;

use Josantonius\Session\Session;
use Promulgate\Models\AnalyticsModel;
use Promulgate\Models\CampaignModel;
use Promulgate\Models\LeadsModel;

/**
 * Class AnalyticsController
 *
 * @package Promulgate\Controllers
 */
class AnalyticsController extends BaseController
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

		$this->view_sub_directory_path = "/analytics/";
		$this->Breadcrumbs->add([
			'title' => 'Analytics',
			'url'   => url('analytics_channels'),
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


	public function showChannelsAnalysis()
	{

		$this->Breadcrumbs->add([
			'title' => 'Subscription Analysis',
			'url'   => url('analytics_channels'),
		]);

		$CampaignController     = new CampaignController([
			'context' => 'data',
		]);
		
		$configured_connections = $this->analyticsModel->getSocialMediaConnections($this->organizationId)['body'];

		if($configured_connections['status'] == "success") {
			$all_channels = $configured_connections;
			foreach ($configured_connections['socialData'] as $k => $sData) {

				$fistVal = 0;
                $fistValIncStatus = 0; //1-up, 2-down
                $secondVal = 0;
                $secondValIncStatus = 0; //1-up, 2-down
                $thirdval = 0;
                $thirdvalIncStatus = 0; //1-up, 2-down
                if(isset($sData)){
	                foreach ($sData as $ks => $vs) {
	                	if($vs['totalPosts'] < $fistVal) {
	                        $fistValIncStatus = 2;
	                    }
	                    if($vs['totalPosts'] > $fistVal) {
	                        $fistValIncStatus = 1;
	                    }
	                    if($vs['totalFollowing'] < $secondVal) {
	                        $secondValIncStatus = 2;
	                    }
	                    if($vs['totalFollowing'] > $secondVal) {
	                        $secondValIncStatus = 1;
	                    }
	                    if($vs['totalFollowers'] < $thirdval) {
	                        $thirdvalIncStatus = 2;
	                    }
	                    if($vs['totalFollowers'] > $thirdval) {
	                        $thirdvalIncStatus = 1;
	                    }
	                    $fistVal = $vs['totalPosts'];
	                    $secondVal = $vs['totalFollowing'];
	                    $thirdval = $vs['totalFollowers'];

	                    foreach ($all_channels['data'] as $k => $channel) {
	                    	if($channel['socialMediaPage']['socialMediaConnectionSocalMediaConnectionId'] == $vs['socalMediaConnectionId']) {
	                    		$all_channels['data'][$k]['totalPosts'] = $fistVal;
		                        $all_channels['data'][$k]['fistValIncStatus'] = $fistValIncStatus;
		                        $all_channels['data'][$k]['totalFollowing'] = $secondVal;
		                        $all_channels['data'][$k]['secondValIncStatus'] = $secondValIncStatus;
		                        $all_channels['data'][$k]['totalFollowers'] = $thirdval;
		                        $all_channels['data'][$k]['thirdvalIncStatus'] = $thirdvalIncStatus;
	                    	}
	                    }
					}
				}
			}
		}

		//print_r($all_channels);exit();
		// $all_campaigns     = $this->campaignModel->getListOfCampaigns($this->organizationId)['body'];

		//$organization_channels = $this->analyticsModel->getSubscriptionDetails($this->organizationId)['body'];

		// $subscriptionDetails = []

		// if(getValue('status', $organization_channels) == 'success') {
		// 	$subscriptionDetails = $organization_channels['data']
		// }

		$users_count_per_day   = $this->analyticsModel->getUsersCountForGraph();
		$this->setViewData('channel_analysis.html',
			[
				'form_action'           => url('analytics_ajax'),
				//'organization_channels' => $organization_channels['data'],
				'graph_users_count'     => json_encode($users_count_per_day, true),
				'last_days_users_count' => array_sum(array_column($users_count_per_day, 1)) ?? 0,
				'page_title'            => "Subscription Analysis",
				'all_social_medias' => $all_channels
			]
		);

	}


	public function showCampaignsAnalysis()
	{

		$this->Breadcrumbs->add([
			'title' => 'Campaign Analysis',
			'url'   => url('analytics_campaigns'),
		]);

		$all_campaigns     = $this->campaignModel->getListOfCampaigns($this->organizationId)['body'];
		$list_of_campaigns = [];

		if(getValue('status', $all_campaigns) == 'success') {

			$list_of_campaigns = $all_campaigns['data']['campaignList']['rows'];


			$total_no_of_campaigns           = $all_campaigns['data']['campaignList']['count'];
			$current_page                    = $all_campaigns['data']['currentPage'];
			$no_of_campaigns_in_current_page = $all_campaigns['data']['pageSize'];

		};

		$this->setViewData('campaign_analysis.html',
			[
				'form_action'    => url('analytics_ajax'),
				'campaigns_list' => $list_of_campaigns,
				'page_title'     => "Campaign Analysis- ".env('SITE_NAME'),
			]
		);
	}


	public function showCampaignsAnalysisDetails($campaign_id)
	{

		$organization_id = Session::get('organization', 'id');
		$campaign_analysis_details = $this->campaignModel->getCampaignAnalysisDetails($campaign_id)['body'];
		if(getValue('status', $campaign_analysis_details) == 'success') {

			$campaign_analysis_details = $campaign_analysis_details['data'];

		};

		$CampaignController  = new CampaignController();
		$CampaignController->setCampaignIdAndDetails($campaign_id);
		$calendar_events = $CampaignController->getContentCurationSocialMediaConnections([
			'parse_full_data' => true,
		]);
		$campaign_whatsapp_details = $this->campaignModel->getCampaignWhatsAppDetails($organization_id, $campaign_id)['body'];
		if(getValue('status', $campaign_whatsapp_details) == 'success') {

			$campaign_whatsapp_details = $campaign_whatsapp_details['data'];

		};
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

		foreach($campaign_whatsapp_details as $wa_event) {
			$formatted_date = explode('@', get_date($wa_event['postAt'], 5));

			if(!isset($formatted_date[1])) {
				continue; // TIme is missing means we cant show anything
			}
			$final_calendar_events[$formatted_date[0]][] = [
				'date'                => $formatted_date[0],
				'time'                => $formatted_date[1],
				'channel'             => 'WhatsApp',
				'unique_name'         => 'WhatsApp',
				'campaignSelectionId' => $wa_event['whatsappContentPostID'],
				'post_id'             => $wa_event['postId'] ?? false,
				'status'              => strtolower($wa_event['postStatus']),
			];
		}

		$this->setViewData('campaign_analysis_details.html',
			[
				'form_action'               => url('analytics_ajax'),
				'campaign_analysis_details' => $campaign_analysis_details,
				'page_title'                => "Analysis of campaign- ".env('SITE_NAME'),
				'show_only_content'         => true,
				'current_calendar_events'                      => $final_calendar_events,
				'campaign_id' => $campaign_id
			]
		);
	}


	public function showViewershipAnalysis()
	{

		$this->Breadcrumbs->add([
			'title' => 'Viewership Analysis',
			'url'   => url('analytics_views'),
		]);
		$users_session_count_per_day = $this->analyticsModel->getUsersCountForGraph();
		$sum_of_sessions             = array_sum(array_column($users_session_count_per_day, 1)) ?? 0;

		$this->setViewData('viewership_analysis.html',
			[
				'form_action'                            => url('analytics_ajax'),
				'page_title'                             => "Viewership Analysis",
				'graph_session_users_count'              => json_encode($users_session_count_per_day, true),
				'last_days_users_session_count'          => $sum_of_sessions,
				'last_days_average_users_sessions_count' => (int)($sum_of_sessions / 15),
				'channel_viewers_count'                  => $this->analyticsModel->getChannelViewersCounts(),
			]
		);
	}

	public function showCompetitorAnalysis()
	{

		$AdminController  = new AdminController();
		$business_details = $AdminController->getBusinessDetails($this->organizationId);

		$competitors_list = [];
		$competitors      = "";

		if(isset($business_details['competitor1'])) {
			$competitors_list[] = $business_details['competitor1'];
		}

		if(isset($business_details['competitor2'])) {
			$competitors_list[] = $business_details['competitor2'];
		}

		if(!empty($competitors_list)) {
			$competitors = implode('/', $competitors_list);
		}

		$this->setViewData('competitor_analysis.html',
			[
				'form_action' => url('analytics_ajax'),
				'competitors' => $competitors,
				'page_title'  => "Competitor Analysis".env('SITE_NAME'),
			]
		);
	}

	public function showMentionsAnalysis()
	{

		$AdminController  = new AdminController();


		$this->setViewData('mention_analysis.html',
			[
				'form_action' => url('analytics_ajax'),
				'page_title'  => "Mentions Analysis".env('SITE_NAME'),
			]
		);
	}

	public function showYoutubeAnalysis()
	{

		$AdminController  = new AdminController();
		$business_details = $AdminController->getBusinessDetails($this->organizationId);

		$competitors_list = [];
		$competitors      = "";

		// if(isset($business_details['competitor1'])) {
		// 	$competitors_list[] = $business_details['competitor1'];
		// }

		// if(isset($business_details['competitor2'])) {
		// 	$competitors_list[] = $business_details['competitor2'];
		// }

		// if(!empty($competitors_list)) {
		// 	$competitors = implode('/', $competitors_list);
		// }

		$yoututbe_analyticsData = $this->analyticsModel->getAnalyticsDetails($this->organizationId)['body'];


		$this->setViewData('yotube_analysis.html',
			[
				'form_action' => url('analytics_ajax'),
				'yoututbe_analyticsData' => $yoututbe_analyticsData['data'],
				'page_title'  => "Competitor Analysis".env('SITE_NAME'),
			]
		);
	}
	

	public function processAjax()
	{
		$all_input                = input()->all();
		$all_input['form_source'] = $all_input['form_source'] ?? "";
		switch ($all_input['form_source']) {
			case 'whatsappLeadsData' :

				$waLeadId = $all_input['wa_lead_id'];

				if($waLeadId) {
					$org_id = Session::get('organization', 'id');
					$whatsappLeadDetails = $this->analyticsModel->getWhatsappLeadDetails($waLeadId, $org_id)['body'];
				}
				
				if($whatsappLeadDetails['status'] == 'success') {
					response()->json([
						'status' => true,
						'success'  => [
							'code'    => 200,
							'message' => 'Whatsapp Lead Fetched',
						],
						'data' => $whatsappLeadDetails
					]);
				}
				 else {
					response()->json([
						'status' => false,
						'error'  => [
							'code'    => 100,
							'message' => 'Whatsapp Lead Details Not Fetched',
							'data' => []
						],
					]);
				 }
				
				break;
			case 'saveCampaignAnalyticsPaidValues' :

				$campaign_id = $all_input['campaign_id'];
				$amt = $all_input['amt'];
				$amtFor = $all_input['amtFor'];

				if($campaign_id) {
					$org_id = Session::get('organization', 'id');
					$saveAmount = $this->analyticsModel->savePaidAnalyticsAmount($campaign_id, $org_id, $amt, $amtFor)['body'];
				}
				
				if($saveAmount['status'] == 'success') {
					response()->json([
						'status' => true,
						'success'  => [
							'code'    => 200,
							'message' => 'Whatsapp Lead Fetched',
						],
						'data' => $saveAmount
					]);
				}
					else {
					response()->json([
						'status' => false,
						'error'  => [
							'code'    => 100,
							'message' => 'Whatsapp Lead Details Not Fetched',
							'data' => []
						],
					]);
					}
				
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

	public function showWhatsappAnalytics()
	{
		//overall analytics

		// print_r($_GET['start_date']);
		// exit();

		$this->Breadcrumbs->add([
			'title' => 'Analytics',
			'url'   => url('analytics_channels'),
		]);
		$org_id = Session::get('organization', 'id');
		$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
		$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
		$duration = isset($_GET['duration']) ? $_GET['duration'] : null;
		$whatsAppAnalytics = $this->leadsModel->getWhatsAppAnalytics($org_id, $start_date, $end_date, $duration);

		$totalLeads = 0;
		$totalSent = 0;
		$allCampaigns = [];


		if (isset($whatsAppAnalytics['body'])) {
			$totalLeads = $whatsAppAnalytics['body']['totalLeads'];
			$totalSent = $whatsAppAnalytics['body']['totalWhatsappSent'];
			$allCampaigns = $whatsAppAnalytics['body']['getUniqueCampaigns'];
		}

		$this->setViewData('whatsapp_analytics.html',
			[
				'form_action'           => url('analytics_ajax'),
				'page_title'            => "WhatsApp Analytics",
				'totalLeads' 		=> $totalLeads,
				'totalSent' 		=> $totalSent,
				'allCampaigns' => $allCampaigns,
				'start_date' => isset($_GET['start_date']) ? $_GET['start_date'] : '',
				'end_date' => isset($_GET['end_date']) ? $_GET['end_date'] : '',
				'duration' => isset($_GET['duration']) ? $_GET['duration'] : ''
			]
		);

	}

	public function showSocialInbox($type, $filterBy)
	{
		$this->Breadcrumbs->add([
			'title' => 'Analytics',
			'url'   => url('analytics_channels'),
		]);
		$org_id = Session::get('organization', 'id');
		$getSocialInbox = $this->leadsModel->getSocialInbox($org_id, null, null, null, $type, str_replace('_', ' ', $filterBy));



		$this->setViewData('social_inbox.html',
			[
				'form_action'           => url('analytics_ajax'),
				'page_title'            => "Social Inbox",
				'socialInbox' 		=> isset($getSocialInbox['body']['data']) ? $getSocialInbox['body']['data'] : [],
				'type' => strtoupper($type),
				'filter_by' => $filterBy
			]
		);

	}

	public function showWhatsappGraphAnalytics()
	{
		//overall analytics

		$this->Breadcrumbs->add([
			'title' => 'Analytics',
			'url'   => url('analytics_channels'),
		]);

		$org_id = Session::get('organization', 'id');
		$whatsAppAnalytics = $this->leadsModel->getWhatsAppAnalytics($org_id);

		$waData = $whatsAppAnalytics['body'] ? $whatsAppAnalytics['body']['data'] : [];
//print_r($waData);exit();
		//total sent, delivered, read, replied
		$totalAnalysis = [0, 0, 0, 0];

		//analysis by numbers
		$uniqueNumbers = [];
		$indUniqueSentCounts = [];
		$indUniqueDeliveredCounts = [];
		$indUniqueReadCounts = [];
		$indUniqueRepliedCounts = [];
		foreach ($waData as $k => $waD) {
			if (!in_array($waD['phone_number'], $uniqueNumbers)) {
				array_push($uniqueNumbers, $waD['phone_number']);
				$indUniqueSentCounts[$waD['phone_number']] = $waD['sent'];
				$indUniqueReadCounts[$waD['phone_number']] = $waD['read'];
				$indUniqueDeliveredCounts[$waD['phone_number']] = $waD['delivered'];
				$indUniqueRepliedCounts[$waD['phone_number']] = $waD['replied'];
			} else {
				$indUniqueSentCounts[$waD['phone_number']] = $indUniqueSentCounts[$waD['phone_number']] + $waD['sent'];
				$indUniqueReadCounts[$waD['phone_number']] = $indUniqueReadCounts[$waD['phone_number']] + $waD['read'];
				$indUniqueDeliveredCounts[$waD['phone_number']] = $indUniqueDeliveredCounts[$waD['phone_number']] + $waD['delivered'];
				$indUniqueRepliedCounts[$waD['phone_number']] = $indUniqueRepliedCounts[$waD['phone_number']] + $waD['replied'];
			}

			$totalAnalysis[0] = $totalAnalysis[0]+ $waD['sent'];
			$totalAnalysis[1] = $totalAnalysis[1]+ $waD['delivered'];
			$totalAnalysis[2] = $totalAnalysis[2]+ $waD['read'];
			$totalAnalysis[3] = $totalAnalysis[3]+ $waD['replied'];
		}
		$this->setViewData('whatsapp_graph_analytics.html',
			[
				'form_action'           => url('analytics_ajax'),
				'page_title'            => "WhatsApp Analytics",
				// 'show_only_content'         => true,
				'whatsAppAnalytics' 		=> $whatsAppAnalytics['body'] ? $whatsAppAnalytics['body']['data'] : [],
				//analysis by numbers
				'uniqueNumbers' => json_encode($uniqueNumbers),
				'indUniqueSentCounts' => json_encode($indUniqueSentCounts),
				'indUniqueDeliveredCounts' => json_encode($indUniqueDeliveredCounts),
				'indUniqueReadCounts' => json_encode($indUniqueReadCounts),
				'indUniqueRepliedCounts' => json_encode($indUniqueRepliedCounts),


				//total analysis
				'totalAnalysis' => json_encode($totalAnalysis)
			]
		);

	}

}