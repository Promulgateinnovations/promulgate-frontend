<?php

namespace Promulgate\Controllers;

use Josantonius\Session\Session;
use Promulgate\Models\AnalyticsModel;
use Promulgate\Models\CampaignModel;

/**
 * Class AnalyticsController
 *
 * @package Promulgate\Controllers
 */
class AnalyticsController extends BaseController
{
	private $analyticsModel;
	private $campaignModel;

	private $organizationId;


	/**
	 * AdminController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->analyticsModel = new AnalyticsModel();
		$this->campaignModel  = new CampaignModel();

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

		$campaign_analysis_details = $this->campaignModel->getCampaignAnalysisDetails($campaign_id)['body'];
		if(getValue('status', $campaign_analysis_details) == 'success') {

			$campaign_analysis_details = $campaign_analysis_details['data'];

		};

		$this->setViewData('campaign_analysis_details.html',
			[
				'form_action'               => url('analytics_ajax'),
				'campaign_analysis_details' => $campaign_analysis_details,
				'page_title'                => "Analysis of campaign- ".env('SITE_NAME'),
				'show_only_content'         => true,
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


}