<?php

namespace Promulgate\Models;

/**
 * Class ReportsModel
 *
 * Handles fetching analytics reports and related details.
 *
 * @package Promulgate\Models
 */
class SuperModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showAddAgencyDetails(array $agency_details): array 
    {
        if (!$agency_details) {
            return [
                'body' =>[],
            ];
        }

        $agency_data = [
            'userId' => $agency_details['user_id'],
            "name"  => $agency_details['agency_name'],
			"email"   => $agency_details['agency_email'],
			"description"      => $agency_details['agency_description'],
        ];

        return $this->makeRequest('POST', '/api/v1/agency/saveAgencyDetails', [
            'json' => $agency_data,
        ]);
    }
    

    public function getAgencyList(): array
    {
        return $this->makeRequest('GET', '/api/v1/agency/getAgencyList', [
            'json' => null,
        ]);
    }
    
    
    public function getEmployeeList(string $agencyId = Null): array
    {
        if(!$agencyId) {
			return [
				'body' => [],
			];
		} 
        
        $payload = [
            'agencyId' => $agencyId,
        ];
    
        return $this->makeRequest('POST', '/api/v1/agency/getTeamDetails', [
            'json' => $payload,
        ]);
    }

    public function showAddEmployeeDetails(array $user_Employee): array
    {
        if (!$user_Employee){
            return [
                'body' => [],
            ];
        }

        $user_data = [
            'firstName' => $user_Employee['first_name'],
            'lastName' => $user_Employee['last_name'],
            'email' => $user_Employee['email'],
            'userName' => $user_Employee['username'],
            'password' => $user_Employee['password'],
            'userStatus' => $user_Employee['status'],
            'agencyId' => $user_Employee['agencyId'],
        ];

        return $this->makeRequest('POST', '/api/v1/agency/saveAgencyTeamDetails', [
            'json' => $user_data,
        ]);
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

    public function getListOfCampaigns($organization_id, $options = [])
    {
    if (!$organization_id) {
        return [
            'body' => [],
        ];
    }

    $options['pageSize'] = $options['page_size'] ?? 100;

    return $this->makeRequest('POST', '/api/v1/getCampaignListing?pageSize=' . $options['pageSize'], [
        'json' => [
            "orgId" => $organization_id,
        ],
    ]);
    }

    public function deleteEmployeeDetails(string $userId = NULL, string $agencyId = NULL): array
    {
        if (!$userId || !$agencyId) {
            return [
                'body' => [],
            ];
        }

        return $this->makeRequest('DELETE', 'api/v1/deleteAgencyEmployeeDetails', [
            'json' => [
                "userId"   => $userId,
                "agencyId" => $agencyId,
            ],
        ]);
    }

    public function updateSuperAgencyDetails(array $updated_agency): array
    {
		if(!$updated_agency) {
			return [
				'body' => [],
			];
		}

		$updated_data = [
			"agencyId"       => $updated_agency['agencyId'],
			"name"        => $updated_agency['name'],
			"email"   => $updated_agency['email'],
			"description"      => $updated_agency['description']
		];

		return $this->makeRequest('POST', '/api/v1/agency/updateAgencyDetails', [
				'json' => $updated_data,
			]
		);
	}

    public function updatesuperEmployeeDetails(array $update_Details): array
	{
		if(!$update_Details) {
			return [
				'body' => [],
			];
		}

		$user_data = [
            "userId"        => $update_Details['userId'],
			"firstName"  => $update_Details['firstName'],
			"lastName"   => $update_Details['lastName'],
			"email"      => $update_Details['email'],
			"userName"   => $update_Details['userName'],
            "userStatus"      => $update_Details['userStatus'],
			"agencyId"      => $update_Details['agencyId'],
		];

		return $this->makeRequest('POST', '/api/v1/agency/updateAgencyEmpDetails', [
				'json' => $user_data,
			]
		);
	}

}

