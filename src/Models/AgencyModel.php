<?php

namespace Promulgate\Models;

/**
 * Class AgencyModel
 *
 * @package Promulgate\Models
 */
class AgencyModel extends BaseModel
{


	public function __construct()
	{
		parent::__construct();

	}


	public function getAgencyDetails(string $agencyId = NULL): array
	{

		if(!$agencyId) {
			return [
				'body' => [],
			];
		}


		return $this->makeRequest('POST', '/api/v1/agency/getAgencyDetails', [
				'json' => [
					"agencyId" => $agencyId,
				],
			]
		);

	}


	public function getTeamsList(string $agencyId = NULL): array
	{
		if(!$agencyId) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/agency/getTeamDetails', [
			'json' => [
				"agencyId" => $agencyId,
			],
		]);

	}


	public function getRoles(string $agencyId = NULL): array
	{
		if(!$agencyId) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/getRoles', [
			'json' => [
				"orgId" => $agencyId,
			],
		]);

	}


	public function getBusinessDetails(string $agencyId = NULL): array
	{

		if(!$agencyId) {
			return [
				'body' => [],
			];
		}


		return $this->makeRequest('POST', '/api/v1/getBusinessDetails', [
				'json' => [
					"orgId" => $agencyId,
				],
			]
		);

	}


	public function getConnectionsList(string $agencyId = NULL): array
	{
		if(!$agencyId) {
			return [
				'body' => [],
			];
		}

		return [
			'body' => [
				"status" => "success",
				"data"   => [
					'connections' => [
						[
							'id'   => rand(1, 1000),
							'name' => 'Facebook',
							'type' => 'ORGANIC',
						],
						[
							'id'   => rand(1, 1000),
							'name' => 'Youtube',
							'type' => 'ORGANIC',
						],
						[
							'id'   => rand(1, 1000),
							'name' => 'Twitter',
							'type' => 'ORGANIC',
						],
						[
							'id'   => rand(1, 1000),
							'name' => 'LinkedIn',
							'type' => 'ORGANIC',
						],
/*						[
							'id'   => rand(1, 1000),
							'name' => 'Instagram',
							'type' => 'ORGANIC',
						],*/
//						[
//							'id'   => rand(1, 1000),
//							'name' => 'Whatsapp',
//							'type' => 'ORGANIC',
//						], [
//							'id'   => rand(1, 1000),
//							'name' => 'Reddit',
//							'type' => 'SOCIAL',
//						],
//						[
//							'id'   => rand(1, 1000),
//							'name' => 'Facebook Ads',
//							'type' => 'PAID',
//						],
//						[
//							'id'   => rand(1, 100),
//							'name' => 'SEO Website',
//							'type' => 'PAID',
//						],
//						[
//							'id'   => rand(1, 100),
//							'name' => 'Electronic Media Adverts',
//							'type' => 'PAID',
//						],
//						[
//							'id'   => rand(1, 100),
//							'name' => 'Print Media',
//							'type' => 'PAID',
//						],
//						[
//							'id'   => rand(1, 100),
//							'name' => 'Google Ads',
//							'type' => 'PAID',
//						],
//						[
//							'id'   => rand(1, 100),
//							'name' => 'Twitter Ads',
//							'type' => 'PAID',
//						],
					],
				],
			],
		];

		//		return $this->makeRequest('POST', '/api/v1/getTeamDetails', [
		//			'json' => [
		//				"orgId" => $agencyId,
		//			],
		//		]);

	}


	public function saveAgencyDetails(array $agency_details): array
	{
		if(!$agency_details) {
			return [
				'body' => [],
			];
		}

		$agency_data = [
			"name"        => $agency_details['agency_name'],
			"email"   => $agency_details['agency_email'],
			"description"      => $agency_details['agency_description'],
			"userId"      => $agency_details['user_id'],
		];

		return $this->makeRequest('POST', '/api/v1/agency/saveAgencyDetails', [
				'json' => $agency_data,
			]
		);

	}


	public function updateAgencyDetails($agencyId, array $agency_details): array
	{
		if(!$agency_details || !$agencyId) {
			return [
				'body' => [],
			];
		}

		$agency_data = [
			"agencyId"       => $agencyId,
			"name"        => $agency_details['agency_name'],
			"email"   => $agency_details['agency_email'],
			"description"      => $agency_details['agency_description']
		];

		return $this->makeRequest('POST', '/api/v1/agency/updateAgencyDetails', [
				'json' => $agency_data,
			]
		);

	}

	public function getListOfOrganizations($agencyId, $userId): array
	{
		if( !$agencyId) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/agency/getOrgLists', [
			'json' => [
				"agencyId" => $agencyId,
				"userId" => $userId
			],
		]
	);
	}

	public function saveBusinessDetails(array $business_details): array
	{
		if(!$business_details) {
			return [
				'body' => [],
			];
		}


		$business_data = [
			"orgId"           => $business_details['org_id'],
			"description"     => $business_details['about_business'],
			"descriptionTags" => $business_details['page_description_tags'],
			"tagLine"         => $business_details['business_tag_line'],
			"competitor1"    => $business_details['competitor_1'],
			"competitor2"    => $business_details['competitor_2'],
		];

		return $this->makeRequest('POST', '/api/v1/saveBusinessDetails', [
				'json' => $business_data,
			]
		);

	}


	public function updateBusinessDetails($business_id, array $business_details): array
	{
		if(!$business_details || !$business_id) {
			return [
				'body' => [],
			];
		}

		$business_data = [
			"businessId"      => $business_id,
			"description"     => $business_details['about_business'],
			"descriptionTags" => $business_details['page_description_tags'],
			"tagLine"         => $business_details['business_tag_line'],
			"competitor1"    => $business_details['competitor_1'],
			"competitor2"    => $business_details['competitor_2'],
		];

		return $this->makeRequest('POST', '/api/v1/updateBusinessDetails', [
				'json' => $business_data,
			]
		);

	}


	public function saveConnectionConfiguration(array $connection_details): array
	{
		if(!$connection_details) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/saveSocialMediaConnections', [
				'json' => $connection_details,
			]
		);

	}

	public function updateConnectionConfiguration(array $connection_details): array
	{
		if(!$connection_details) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/updateSocialMediaConnectionStatus', [
				'json' => $connection_details,
			]
		);

	}


	public function createUser(array $user_Details): array
	{
		if(!$user_Details) {
			return [
				'body' => [],
			];
		}

		$user_data = [
			"firstName"  => $user_Details['first_name'],
			"lastName"   => $user_Details['last_name'],
			"email"      => $user_Details['email'],
			"userName"   => $user_Details['username'],
			"password"   => $user_Details['password'],
			"userStatus" => $user_Details['status'],
			"agencyId"      => $user_Details['agencyId'],
		];

		return $this->makeRequest('POST', '/api/v1/agency/saveAgencyTeamDetails', [
				'json' => $user_data,
			]
		);

	}

}