<?php

namespace Promulgate\Models;

/**
 * Class AnalyticsModel
 *
 * @package Promulgate\Models
 */
class AnalyticsModel extends BaseModel
{


	public function __construct()
	{
		parent::__construct();

	}

	public function getSubscriptionDetails($organization_id, $options = [])
	{

		if(!$organization_id) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/getSubscriptionAnalytics', [
				'json' => [
					"orgId" => $organization_id,
				],
			]
		);
	}

	public function getAnalyticsDetails($organization_id, $options = [])
	{

		if(!$organization_id) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/getYoutubeAnalytics', [
				'json' => [
					"orgId" => $organization_id,
				],
			]
		);
	}

	

	public function getChannelsList()
	{

		return [
			'facebook' => [
				'name'      => 'Facebook',
				'analytics' => [
					[
						'count' => '2345',
						'title' => 'Interactions',
					],
					[
						'count' => '5426',
						'title' => 'Page Impressions',
					],
					[
						'count' => '67536',
						'title' => 'Talking About',
					],
					[
						'count' => '2131',
						'title' => 'Retweets',
					],
					[
						'count' => '438',
						'title' => 'Mentions',
					],
					[
						'count' => '4535',
						'title' => 'Favourites',
					],
				],
			],
			'twitter'  => [
				'name'      => 'Twitter',
				'analytics' => [
					[
						'count' => '2545',
						'title' => 'Followers',
					],
					[
						'count' => '980',
						'title' => 'New Followers',
					],
					[
						'count' => '67536',
						'title' => 'New Tweets',
					],
					[
						'count' => '2431',
						'title' => 'Retweets',
					],
					[
						'count' => '488',
						'title' => 'Mentions',
					],
					[
						'count' => '4935',
						'title' => 'Favourites',
					],
				],
			],
			'linkedin' => [
				'name'      => 'LinkedIn',
				'analytics' => [
					[
						'count' => '2545',
						'title' => 'Followers',
					],
					[
						'count' => '980',
						'title' => 'New Followers',
					],
					[
						'count' => '190',
						'title' => 'Impressions',
					],
					[
						'count' => '5431',
						'title' => 'Clicks',
					],
					[
						'count' => '448',
						'title' => 'Likes',
					],
					[
						'count' => '4535',
						'title' => 'Avg. Engagement',
					],
				],
			],
			'youtube'  => [
				'name'      => 'Youtube',
				'analytics' => [
					[
						'count' => '2505',
						'title' => 'Subscribers',
					],
					[
						'count' => '380',
						'title' => 'New Subscribers',
					],
					[
						'count' => '190',
						'title' => 'Lifetime Views',
					],
					[
						'count' => '5431',
						'title' => 'Views This Month',
					],
					[
						'count' => '448',
						'title' => 'Likes',
					],
					[
						'count' => '4535',
						'title' => 'Comments',
					],
				],
			],
		];
	}


	public function getUsersCountForGraph()
	{
		$graph_data = [];

		$end_date = date('Y-m-d');
		$date     = date("Y-m-d", strtotime("-15 day", strtotime($end_date)));

		$dates_range = date_range($date, $end_date, "+1 day", 'Y-m-d');

		foreach($dates_range as $date) {
			$graph_data[] = [
				date("jS M 'y", strtotime($date)),
				rand(10, 1000),
				'stroke-color: #1998bf; stroke-width: 4; fill-color: #1998bf',
			];
		}

		return $graph_data;
	}


	public function getUsersSessionCountForGraph()
	{
		$graph_data = [];

		$end_date = date('Y-m-d');
		$date     = date("Y-m-d", strtotime("-14 day", strtotime($end_date)));

		$dates_range = date_range($date, $end_date, "+1 day", 'Y-m-d');

		foreach($dates_range as $date) {
			$graph_data[] = [
				date("jS M 'y", strtotime($date)),
				rand(10, 1000),
				'stroke-color: #1998bf; stroke-width: 4; fill-color: #1998bf',
			];
		}

		return $graph_data;
	}


	public function getChannelViewersCounts()
	{
		return [
			'facebook' => [
				'name'     => 'Facebook',
				'visitors' => number_format('5169', 0, ",", ","),
				'goals'    => '90',
				'gc'       => number_format('17.89', 2, ".", ","),
			],
			'twitter'  => [
				'name'     => 'Twitter',
				'visitors' => number_format('3169', 0, ",", ","),
				'goals'    => '78',
				'gc'       => number_format('13.48', 2, ".", ","), '13.34',
			],
			'linkedin' => [
				'name'     => 'LinkedIn',
				'visitors' => number_format('2565', 0, ",", ","),
				'goals'    => '67',
				'gc'       => number_format('67.9', 2, ".", ","),
			],
			'youtube'  => [
				'name'     => 'Youtube',
				'visitors' => number_format('8678', 0, ",", ","),
				'goals'    => '95',
				'gc'       => number_format('32.13', 2, ".", ","),
			],
		];
	}

	public function getSocialMediaConnections($organization_id)
	{

		if(!$organization_id) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/getSocialPresence', [
				'json' => [
					"orgId" => $organization_id,
				],
			]
		);
	}

	public function getWhatsappLeadDetails($lead_id, $org_id)
	{

		if(!$lead_id) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/getWhatsAppAnalyticsDetails', [
				'json' => [
					"leadId" => $lead_id,
					"orgId" => $org_id
				],
			]
		);
	}

	public function savePaidAnalyticsAmount($campaign_id, $orgId, $amt, $amtFor)
	{

		if(!$orgId || !$campaign_id) {
			return [
				'body' => [],
			];
		}


		return $this->makeRequest('POST', '/api/v1/savePaidAnalyticsAmount', [
				'json' => [
					"orgId"       => $orgId,
					"campaign_id" => $campaign_id,
					"amt" => $amt,
					"amtFor" => $amtFor
				],
			]
		);
	}

	public function runCronManually()
	{
		return $this->makeRequest('POST', '/api/v1/run-cron-manually', [
				'json' => [
				],
			]
		);
	}
}