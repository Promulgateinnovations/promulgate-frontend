<?php

namespace Promulgate\Models;

/**
 * Class CampaignModel
 *
 * @package Promulgate\Models
 */
class CampaignModel extends BaseModel
{


	public function __construct()
	{
		parent::__construct();

	}


	public function getListOfCampaigns($organization_id, $options = [])
	{

		if(!$organization_id) {
			return [
				'body' => [],
			];
		}

		$options['pageSize'] = $options['page_size'] ?? 100;

		return $this->makeRequest('POST', '/api/v1/getCampaignListing?pageSize='.$options['pageSize'], [
				'json' => [
					"orgId" => $organization_id,
				],
			]
		);

	}


	public function getStrategyDefinitionDetails($campaign_id)
	{

		if(!$campaign_id) {
			return [
				'body' => [],
			];
		}


		return $this->makeRequest('POST', '/api/v1/getCampaignDefinition', [
				'json' => [
					"campaignDefinitionId" => $campaign_id,
				],
			]
		);
	}


	public function getCampaignDetails($organization_id, $campaign_id)
	{

		if(!$organization_id || !$campaign_id) {
			return [
				'body' => [],
			];
		}


		return $this->makeRequest('POST', '/api/v1/getCampaignDetails', [
				'json' => [
					"orgId"                => $organization_id,
					"campaignDefinitionId" => $campaign_id,
				],
			]
		);
	}


	public function getCampaignAnalysisDetails($campaign_id)
	{

		if(!$campaign_id) {
			return [
				'body' => [],
			];
		}


		return $this->makeRequest('POST', '/api/v1/getCampaginAnalytics', [
				'json' => [
					"campaignDefinitionId" => $campaign_id,
				],
			]
		);
	}

	public function getCampaignWhatsAppDetails($org_id, $campaign_id)
	{

		if(!$org_id || !$campaign_id) {
			return [
				'body' => [],
			];
		}


		return $this->makeRequest('POST', '/api/v1/getCampaignWhatsAppDetails', [
				'json' => [
					"orgId"                => $org_id,
					"campaignDefinitionId" => $campaign_id,
				],
			]
		);
	}


	public function getTopVideoUrls($organization_id)
	{

		if(!$organization_id) {
			return [
				'body' => [],
			];
		}


		return $this->makeRequest('POST', '/api/v1/getyoutTubeVideos', [
				'json' => [
					"orgId" => $organization_id,
				],
			]
		);
	}


	public function getVideoEnrichDetails($organization_id, $video_id)
	{

		if(!$organization_id) {
			return [
				'body' => [],
			];
		}


		return $this->makeRequest('POST', '/api/v1/getYoutubeVideoDetails', [
				'json' => [
					"orgId"     => $organization_id,
					"youtubeId" => $video_id,
				],
			]
		);
	}


	public function saveEnrichVideoSettings($organization_id, $video_details)
	{

		if(!$organization_id) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/enrichYoutubeVideo', [
				'json' => [
					"orgId"   => $organization_id,
					"id"      => $video_details['video_id'],
					"snippet" => [
						"tags"        => $video_details['tags']
							? explode(',', $video_details['tags'])
							: [],
						"title"       => $video_details['title'] ?? "",
						"description" => $video_details['description'] ?? "",
						"categoryId"  => $video_details['category_id'] ?? "",
					],
				],
			]
		);
	}


	public function createStrategyDefinition($campaign_details)
	{

		if(!$campaign_details) {
			return [
				'body' => [],
			];
		}

		$campaign_data = [
			"name"           => $campaign_details['campaign_name'],
			"topic"          => $campaign_details['campaign_topic'],
			"objective"      => $campaign_details['campaign_objective'],
			"videoUrl"       => $campaign_details['campaign_video_url'],
			"influencers"    => $campaign_details['campaign_influencers'],
			"startAt"        => $campaign_details['campaign_start_date'],
			"endAt"          => $campaign_details['campaign_end_date'],
			"totalAudience"  => $campaign_details['campaign_targeted_audience'],
			"status"         => $campaign_details['status'],
			"campaignTypes"  => $campaign_details['type_of_campaign'],
			"tags"           => $campaign_details['page_description_tags'],
			"captiveMembers" => $campaign_details['share_with_captive_members'],
			"orgId"          => $campaign_details['organization_id'],
			"userId"         => $campaign_details['user_id'],
		];

		return $this->makeRequest('POST', '/api/v1/saveCampaignDefintion', [
				'json' => $campaign_data,
			]
		);

	}


	public function updateStrategyDefinition($campaign_id, array $campaign_details): array
	{
		if(!$campaign_id || !$campaign_details) {
			return [
				'body' => [],
			];
		}

		$campaign_data = [
			"campaignDefinitionId" => $campaign_id,
			"name"                 => $campaign_details['campaign_name'],
			"topic"                => $campaign_details['campaign_topic'],
			"objective"            => $campaign_details['campaign_objective'],
			"videoUrl"             => $campaign_details['campaign_video_url'],
			"influencers"          => $campaign_details['campaign_influencers'],
			"startAt"              => $campaign_details['campaign_start_date'],
			"endAt"                => $campaign_details['campaign_end_date'],
			"totalAudience"        => $campaign_details['campaign_targeted_audience'],
			"campaignTypes"        => $campaign_details['type_of_campaign'],
			"tags"                 => $campaign_details['page_description_tags'],
			"captiveMembers"       => $campaign_details['share_with_captive_members'],
			"orgId"                => $campaign_details['organization_id'],
			"userId"               => $campaign_details['user_id'],
		];

		return $this->makeRequest('POST', '/api/v1/updateCampaignDefintion', [
				'json' => $campaign_data,
			]
		);

	}


	public function approveCampaign($campaign_id): array
	{
		if(!$campaign_id) {
			return [
				'body' => [],
			];
		}

		$campaign_data = [
			"campaignDefinitionId" => $campaign_id,
			"status"               => "APPROVED",
		];

		return $this->makeRequest('POST', '/api/v1/updateCampaignDefintion', [
				'json' => $campaign_data,
			]
		);

	}


	public function saveTargetViewers($campaign_target_viewer_details)
	{

		if(!$campaign_target_viewer_details) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/saveCaimpaignViewers', [
				'json' => $campaign_target_viewer_details,
			]
		);

	}


	public function updateTargetViewers($campaign_id, $campaign_viewers_id, array $campaign_target_viewer_details): array
	{

		if(!$campaign_id || !$campaign_viewers_id || !$campaign_target_viewer_details) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/updateCampaignViewers', [
				'json' => $campaign_target_viewer_details,
			]
		);

	}


	public function getTargetViewers($campaign_id)
	{

		if(!$campaign_id) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/getCampaginViewers', [
				'json' => [
					"campaignDefinitionId" => $campaign_id,
				],
			]
		);
	}


	public function getSocialMediaConnections($organization_id)
	{

		if(!$organization_id) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/getSocailMediaConnections', [
				'json' => [
					"orgId" => $organization_id,
				],
			]
		);
	}


	public function getChannelSelectionSocialMediaConnections($organization_id)
	{

		if(!$organization_id) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/getCampaignSelectionChannels', [
				'json' => [
					"campaignDefinitionId" => $organization_id,
				],
			]
		);
	}


	public function saveSelectedChannels($campaign_selected_channels_details)
	{

		if(!$campaign_selected_channels_details) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/saveCampaignSelectionChannels', [
				'json' => $campaign_selected_channels_details,
			]
		);

	}


	public function saveContent($content_to_post)
	{

		if(!$content_to_post) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/saveCampaignContent', [
				'json' => $content_to_post,
			]
		);

	}


	public function saveComments($review_data)
	{

		if(!$review_data) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/saveCampaignComments', [
				'json' => $review_data,
			]
		);

	}


	public function getCountries()
	{

		return [
			"INDIA" => "India",
		];
	}


	public function getStates()
	{
		return [
			'ANDHRA PRADESH'         => 'Andhra Pradesh',
			'ARUNACHAL PRADESH'      => 'Arunachal Pradesh',
			'ASSAM'                  => 'Assam',
			'BIHAR'                  => 'Bihar',
			'CHHATTISGARH'           => 'Chhattisgarh',
			'GOA'                    => 'Goa',
			'GUJARAT'                => 'Gujarat',
			'HARYANA'                => 'Haryana',
			'HIMACHAL PRADESH'       => 'Himachal Pradesh',
			'JAMMU & KASHMIR'        => 'Jammu & Kashmir',
			'JHARKHAND'              => 'Jharkhand',
			'KARNATAKA'              => 'Karnataka',
			'KERALA'                 => 'Kerala',
			'MADHYA PRADESH'         => 'Madhya Pradesh',
			'MAHARASHTRA'            => 'Maharashtra',
			'MANIPUR'                => 'Manipur',
			'MEGHALAYA'              => 'Meghalaya',
			'MIZORAM'                => 'Mizoram',
			'NAGALAND'               => 'Nagaland',
			'ODISHA'                 => 'Odisha',
			'PUNJAB'                 => 'Punjab',
			'RAJASTHAN'              => 'Rajasthan',
			'SIKKIM'                 => 'Sikkim',
			'TAMIL NADU'             => 'Tamil Nadu',
			'TRIPURA'                => 'Tripura',
			'UTTARAKHAND'            => 'Uttarakhand',
			'UTTAR PRADESH'          => 'Uttar Pradesh',
			'WEST BENGAL'            => 'West Bengal',
			'ANDAMAN & NICOBAR'      => 'Andaman & Nicobar',
			'CHANDIGARH'             => 'Chandigarh',
			'DADRA AND NAGAR HAVELI' => 'Dadra and Nagar Haveli',
			'DAMAN & DIU'            => 'Daman & Diu',
			'DELHI'                  => 'Delhi',
			'LAKSHADWEEP'            => 'Lakshadweep',
			'PUDUCHERRY'             => 'Puducherry',
		];
	}


	public function getLanguages()
	{
		return [
			'ENGLISH'  => 'English',
			'HINDI'    => 'Hindi',
			'TELUGU'   => 'Telugu',
			'TAMIL'    => 'Tamil',
			'BENGALI'  => 'Bengali',
			'BIHARI'   => 'Bihari',
			'GUJARATI' => 'Gujarati',
			'MARATHI'  => 'Marathi',
			'NEPALI'   => 'Nepali',
			'ORIYA'    => 'Oriya',
			'PUNJABI'  => 'Punjabi',
			'SANSKRIT' => 'Sanskrit',
			'URDU'     => 'Urdu',
		];
	}

}