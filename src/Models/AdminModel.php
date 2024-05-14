<?php

namespace Promulgate\Models;

/**
 * Class AdminModel
 *
 * @package Promulgate\Models
 */
class AdminModel extends BaseModel
{


	public function __construct()
	{
		parent::__construct();

	}


	public function getOrganizationDetails(string $organization_id = NULL): array
	{

		if(!$organization_id) {
			return [
				'body' => [],
			];
		}


		return $this->makeRequest('POST', '/api/v1/getOrgDetails', [
				'json' => [
					"orgId" => $organization_id,
				],
			]
		);

	}


	public function getTeamsList(string $organization_id = NULL): array
	{
		if(!$organization_id) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/getTeamDetails', [
			'json' => [
				"orgId" => $organization_id,
			],
		]);

	}


	public function getRoles(string $organization_id = NULL): array
	{
		if(!$organization_id) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/getRoles', [
			'json' => [
				"orgId" => $organization_id,
			],
		]);

	}


	public function getBusinessDetails(string $organization_id = NULL): array
	{

		if(!$organization_id) {
			return [
				'body' => [],
			];
		}


		return $this->makeRequest('POST', '/api/v1/getBusinessDetails', [
				'json' => [
					"orgId" => $organization_id,
				],
			]
		);

	}


	public function getConnectionsList(string $organization_id = NULL): array
	{
		if(!$organization_id) {
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
						[
							'id'   => rand(1, 1000),
							'name' => 'Instagram',
							'type' => 'ORGANIC',
						],
						[
							'id'   => rand(1, 1000),
							'name' => 'Reddit',
							'type' => 'ORGANIC',
						],
						[
							'id'   => rand(1, 1000),
							'name' => 'E-Mail',
							'type' => 'PAID',
						],
						[
							'id'   => rand(1, 1000),
							'name' => 'SMS',
							'type' => 'PAID',
						],
						[
							'id'   => rand(1, 1000),
							'name' => 'WhatsApp',
							'type' => 'PAID',
						],
						[
							'id'   => rand(1, 1000),
							'name' => 'Facebook Ads',
							'type' => 'PAID',
						],
						[
							'id'   => rand(1, 1000),
							'name' => 'Twitter promotions',
							'type' => 'PAID',
						],
						//						[
						//							'id'   => rand(1, 1000),
						//							'name' => 'Whatsapp',
						//							'type' => 'ORGANIC',
						//						], [
						//							'id'   => rand(1, 1000),
						//							'name' => 'Reddit',
						//							'type' => 'DIRECT',
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
		//				"orgId" => $organization_id,
		//			],
		//		]);

	}


	public function saveOrganizationDetails(array $organization_details): array
	{
		if(!$organization_details) {
			return [
				'body' => [],
			];
		}

		$organization_data = [
			"name"        => $organization_details['company_name'],
			"aliasName"   => $organization_details['company_alias'],
			"orgUrl"      => $organization_details['company_url'],
			"orgStatus"   => $organization_details['org_status'],
			"orgSmPolicy" => $organization_details['social_media_policy'],
			"userId"      => $organization_details['user_id'],
			"agencyId"    => $organization_details['agencyId']
		];

		return $this->makeRequest('POST', '/api/v1/saveOrgDetails', [
				'json' => $organization_data,
			]
		);

	}


	public function updateOrganizationDetails($organization_id, array $organization_details): array
	{
		if(!$organization_details || !$organization_id) {
			return [
				'body' => [],
			];
		}

		$organization_data = [
			"orgId"       => $organization_id,
			"name"        => $organization_details['company_name'],
			"aliasName"   => $organization_details['company_alias'],
			"orgUrl"      => $organization_details['company_url'],
			"orgSmPolicy" => $organization_details['social_media_policy'],
		];

		return $this->makeRequest('POST', '/api/v1/updateOrgDetails', [
				'json' => $organization_data,
			]
		);

	}

	public function saveWhatsappConnectionDetails(array $whatsapp_details): array
	{
		if(!$whatsapp_details) {
			return [
				'body' => [],
			];
		}

		$whatsapp_data = [
			"phoneNumberId"      => $whatsapp_details['phone_number_id'],
			"whatsappBusinesAccountId"   => $whatsapp_details['Whatsapp_busines_account_id'],
			"userId"      => $whatsapp_details['user_id'],
			"agencyId"    => $whatsapp_details['agencyId'],
			"orgId"    => $whatsapp_details['org_id'],
			"status"    => $whatsapp_details['status']
		];

		return $this->makeRequest('POST', '/api/v1/saveWhatsappDetails', [
				'json' => $whatsapp_data,
			]
		);

	}

	public function getWhatsAppConnectionDetails(string $organization_id = NULL): array
	{

		if(!$organization_id) {
			return [
				'body' => [],
			];
		}


		return $this->makeRequest('POST', '/api/v1/getWhatsAppDetails', [
				'json' => [
					"orgId" => $organization_id,
				],
			]
		);

	}

	public function updateWhatsappConnectionDetails($whatsapp_connection_id, array $whatsapp_details): array
	{
		if(!$whatsapp_details || !$whatsapp_connection_id) {
			return [
				'body' => [],
			];
		}

		$whatsapp_data = [
			"whatsapp_connection_id"       => $whatsapp_connection_id,
			"phone_number_id"        => $whatsapp_details['phone_number_id'],
			"Whatsapp_busines_account_id"   => $whatsapp_details['Whatsapp_busines_account_id']
		];

		return $this->makeRequest('POST', '/api/v1/updateWhatsappDetails', [
				'json' => $whatsapp_data,
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
			"hubType"         => str_replace("_", "-", strtoupper($business_details['hub_type'])),
			"hubUrl"          => $business_details['hub_url'],
			"credentials"     => $business_details['hub_credentials'],
		];

		if(isset($business_details['assetName'])) {

			$business_data['assetName']        = $business_details['assetName'];
			$business_data['assetExpiry']      = $business_details['assetExpiry'];
			$business_data['assetCredentials'] = $business_details['assetCredentials'];

		}

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
			"hubType"         => str_replace("_", "-", strtoupper($business_details['hub_type'])),
			"hubUrl"          => $business_details['hub_url'],
			"credentials"     => $business_details['hub_credentials'],
		];

		if(isset($business_details['assetName'])) {

			$business_data['assetName']        = $business_details['assetName'];
			$business_data['assetExpiry']      = $business_details['assetExpiry'];
			$business_data['assetCredentials'] = $business_details['assetCredentials'];

		}
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
			"agencyId"   => $user_Details['agency_id'],
			"roleId"     => (int)$user_Details['user_role'],
			"userId"     => $user_Details['user_name'],
			"orgId"      => $user_Details['org_id'],
		];

		return $this->makeRequest('POST', '/api/v1/saveTeamDetails', [
				'json' => $user_data,
			]
		);

	}

}