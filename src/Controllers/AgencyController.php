<?php

namespace Promulgate\Controllers;

use Josantonius\Session\Session;
use Promulgate\Core\Config;
use Promulgate\Core\GoogleAPIClient;
use Promulgate\Models\AgencyModel;
use Promulgate\Models\AdminModel;
/**
 * Class AgencyController
 *
 * @package Promulgate\Controllers
 */
class AgencyController extends BaseController
{
	private $agencyModel;
	private $agencyId;
	private $adminModel;

	/**
	 * AgencyController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->agencyModel = new AgencyModel();
		$this->adminModel = new AdminModel();

	}


	protected function setWebContext()
	{

		$this->view_sub_directory_path = "/agency/";
		$this->Breadcrumbs->add([
			'title' => 'Agency',
			'url'   => url('agency_details'),
		]);

		$this->setAgencyId(Session::get('agency', 'id'));
	}


	private function setAgencyId($agencyId)
	{
		if($agencyId && !is_array($agencyId)) {
			$agencyId       = trim($agencyId);
			$this->agencyId = $agencyId;
		}
	}


	public function showAgencyDetails()
	{
		$this->Breadcrumbs->add([
			'title' => 'Agency',
			'url'   => url('agency_details'),
		]);

		$agency_details = $this->agencyModel->getAgencyDetails($this->agencyId)['body'];

		if(getValue('status', $agency_details) == 'success') {

			$agency_details = $agency_details['data'];

		} else {
			$agency_details = [];
		}

		$this->setViewData('agency.html',
			[
				'form_action'          => url('agency_ajax'),
				'agency_details' => $agency_details,
				'page_title'           => "Admin Agency",
				'hide_side_menu' => true,
			]
		);
	}


	public function showAgencyTeamMembers()
	{

		$this->Breadcrumbs->add([
			'title' => 'Team',
			'url'   => url('agency_team_members'),
		]);

		$teams_list = $this->agencyModel->getTeamsList($this->agencyId)['body'];
		if(getValue('status', $teams_list) != 'success') {
			$teams_list = [];
		} else {
			$teams_list =$teams_list['data'];
		}

		$final_team_list = [];
		$directors_list  = [];

		//TODO: Propose to return director data from API

		// Separate Director list from results
		// foreach($teams_list as $team_member) {
		// 	if(strtolower($team_member['role']['roleName']) == "director") {
		// 		$directors_list[] = $team_member;
		// 	} else {
		// 		$final_team_list[] = $team_member;
		// 	}
		// }

		$this->setViewData('team.html',
			[
				'teams_list'       => $teams_list,
				'page_title'       => "Agency Team",
				'hide_side_menu' => true,
			]
		);

	}


	public function showAddAgencyMember()
	{

		$this->Breadcrumbs->add([
			'form_action'          => url('agency_ajax'),
			'title' => 'Team',
			'url'   => url('agency_team_members'),
		]);

		$this->Breadcrumbs->add([
			'title' => 'Add New',
			'url'   => url('agency_add_new_member'),
		]);

		$this->setViewData('add_new_agency_member.html',
			[
				'form_action'          => url('agency_ajax'),
				'page_title'              => "Add new Team",
				'hide_side_menu' => true,
				
			]
		);

	}


	public function showBusiness()
	{

		$this->Breadcrumbs->add([
			'title' => 'Business',
			'url'   => url('admin_business'),
		]);

		$business_details = $this->getBusinessDetails($this->agencyId);

		$this->setViewData('business.html',
			[
				'form_action'      => url('admin_ajax'),
				'page_title'       => "Admin Business",
				'business_details' => $business_details,
			]
		);
	}


	public function showConnections()
	{

		$this->Breadcrumbs->add([
			'title' => 'Connections',
			'url'   => url('admin_connections'),
		]);

		$agency_connections = $this->agencyModel->getConnectionsList($this->agencyId)['body'] ?? [];
		if(getValue('status', $agency_connections) != 'success') {
			$agency_connections = [];
		} else {
			$agency_connections = getValue('connections', $agency_connections['data'], []);
		}

		$CampaignController     = new CampaignController([
			'context' => 'data',
		]);
		$configured_connections = $CampaignController->getSocialMediaConnections(true);

		$final_agency_connections        = [];
		$final_agency_connections_titles = [];

		if($agency_connections) {

			foreach($agency_connections as $connection) {

				$unique_name   = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $connection['name']));
				$final_agency_connections[$connection['type']]
				[$unique_name] = [
					'name'        => $connection['name'],
					'unique_name' => $unique_name,
					'id'          => $connection['id'] ?? 0,
					'type'        => $connection['type'],
				];
			}

			// For looping in same order
			$final_agency_connections = [
				'ORGANIC' => $final_agency_connections['ORGANIC'] ?? [],
				#'PAID'    => $final_agency_connections['PAID'] ?? [],
				#'SOCIAL'  => $final_agency_connections['SOCIAL'] ?? [],
			];

			$final_agency_connections_titles = [
				'ORGANIC' => "Organic",
				#'SOCIAL'  => "Social",
				#'PAID'    => "Paid",
			];
		}

		$this->setViewData('connections.html',
			[
				'form_action'                     => url('admin_ajax'),
				'page_title'                      => "Admin Connections",
				'agency_connections'        => $final_agency_connections,
				'agency_connections_titles' => $final_agency_connections_titles,
				'supported_api_connections'       => Config::API_CONFIGURATION_CONNECTIONS,
				'configured_connections'          => $configured_connections,
				'plugins_google_youtube'          => true,
				'plugins_facebook'                => true,
				'facebook_app_id'                 => env('FACEBOOK_APP_ID'),
				'facebook_app_client_id'          => env('FACEBOOK_CLIENT_ID'),
				'facebook_app_client_secret'      => env('FACEBOOK_CLIENT_SECRET'),
				'facebook_graph_api_version'      => env('FACEBOOK_GRAPH_API_VERSION'),
				'GOOGLE_OAUTH_CLIENT_ID'          => env('GOOGLE_OAUTH_CLIENT_ID'),
				'GOOGLE_YOUTUBE_API_KEY'          => env('GOOGLE_YOUTUBE_API_KEY'),
			]
		);
	}


	public function processAjax()
	{
		$all_input                = input()->all();
		
		$all_input['form_source'] = $all_input['form_source'] ?? "";

		switch ($all_input['form_source']) {

			case 'agency' :

				$agency_id = $all_input['agency_id'];

				if($agency_id) {

					$this->updateAgencyDetails($agency_id, $all_input);

				} else {

					$this->createAgency($all_input);
				}
				break;

			case 'business' :

				$business_id = $all_input['business_id'];

				if($business_id) {

					$this->updateBusinessDetails($business_id, $all_input);

				} else {

					$this->saveBusiness($all_input);
				}
				break;


			case 'connection_configuration' :

				$connection_type = $all_input['connection_type'] ?? "";
				$this->saveConnectionConfiguration($connection_type, $all_input);

				break;

			case 'update_connection_configuration' :

				$this->updateConnectionConfiguration($all_input, 'isConfigurationRemoved');

				break;

			case 'update_connection_status' :

				$this->updateConnectionConfiguration($all_input, 'isConnectionStatusUpdated');

				break;

			case 'add_new_user' :
				$this->addUser($all_input);
				break;

			case 'updateAgencyEmployee':
				$this->updateAgencyEmployee($all_input);
				break;

			case 'currentOrganization' :
				$this->setCurrentOrganizationId($all_input);
				break;
				
				case 'deleteAgencyEmployee':

					$userId = $all_input['userId'];
					$agencyId = $all_input['agencyId'];
				
					if ($userId && $agencyId) {
						$this->deleteAgencyEmployee($userId, $agencyId);
					} else {
						return response()->json([
							'status' => false,
							'error'  => [
								'code'    => 10,
								'message' => 'User ID or Agency ID is missing.',
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


	
	private function createAgency($agency_details)
	{
		$agency_details['user_id'] = Session::get('user', 'id');

		$created_agency = $this->agencyModel->saveAgencyDetails($agency_details)['body'];

		if(getValue('status', $created_agency) != 'success') {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => $created_agency['message'] ?? "Some problem form API",
				],
			]);

		} else {

			Session::set('agency', [
				'id' => $created_agency['data']['agencyId'],
			]);
			Session::set('user', [
				'id'       => Session::get('user', 'id'),
				'email'    =>Session::get('user', 'email'),
				'name'     => Session::get('user', 'name'),
				'username' => Session::get('user', 'username'),
				'role'     => 'AGENCY HEAD',
			]);

			response()->json([
				'status' => true,
				'data'   =>
					[
						'message' => "agency created successfully",
						'extra'   => [
							'agency_id' => $created_agency['data']['agencyId'],
						],
					],
			]);

		}
	}


	private function updateAgencyDetails($agency_id, $agency_details)
	{

		if(!$agency_id) {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 10,
					'message' => 'No Agency to update details',
				],
			]);

		}

		$updated_created_agency = $this->agencyModel->updateAgencyDetails($agency_id, $agency_details)['body'];

		if(getValue('status', $updated_created_agency) != 'success') {

			response()->json([
				'api_log' => api_log(true),
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => $updated_created_agency['message'] ?? "Some problem form API",
				],
			]);

		} else {

			//Update company name
			Session::set('agency', [
				'id' => $agency_id,
			]);

			response()->json([
				'status' => true,
				'data'   =>
					[
						'message' => "Agency details updated successfully",
					],
			]);

		}

	}


	private function saveBusiness($business_details)
	{
		$business_details['org_id'] = Session::get('agency', 'id');

		$business_details['competitor_1'] = 'https://youtube.com/'.$business_details['competitor_1'];
		$business_details['competitor_2'] = 'https://youtube.com/'.$business_details['competitor_2'];

		$created_business_info = $this->agencyModel->saveBusinessDetails($business_details)['body'];

		if(getValue('status', $created_business_info) != 'success') {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => $created_business_info['message'] ?? "Some problem form API",
				],
			]);

		} else {

			response()->json([
				'status' => true,
				'data'   =>
					[
						'message' => "Business details saved successfully",
						'extra'   => [
							'business_id' => $created_business_info['data']['businessId'],
						],
					],
			]);

		}
	}

	private function setCurrentOrganizationId($organization)
	{
		$organization_details = $this->adminModel->getOrganizationDetails($organization['current_organization'])['body'];

		if(getValue('status', $organization_details) == 'success') {

			$organization_details = $organization_details['data'];
			Session::set('organization', [
				'id'=> $organization_details['orgId'],
				'name' => $organization_details['name']
			]);
		}
		
		response()->json([
			'status' => true,
			'data'   =>
			[
				'extra'   => [
					'next_screen' => url('admin_organization'),
				],
			],
		]);
	}

	private function updateBusinessDetails($business_id, $business_details)
	{

		if(!$business_id) {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 10,
					'message' => 'No Business details to update details',
				],
			]);
		}

		$business_details['competitor_1'] = 'https://youtube.com/'.$business_details['competitor_1'];
		$business_details['competitor_2'] = 'https://youtube.com/'.$business_details['competitor_2'];

		$updated_business_details = $this->agencyModel->updateBusinessDetails($business_id, $business_details)['body'];

		if(getValue('status', $updated_business_details) != 'success') {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => $updated_business_details['message'] ?? "Some problem form API",
				],
			]);

		} else {

			response()->json([
				'status' => true,
				'data'   =>
					[
						'message' => "Business details updated successfully",
					],
			]);

		}

	}


	/**
	 * @param $organization_id
	 *
	 * @return array|mixed
	 */
	public function getBusinessDetails($organization_id)
	{
		$business_details = $this->agencyModel->getBusinessDetails($organization_id)['body'];

		if(getValue('status', $business_details) == 'success') {

			$business_details                = $business_details['data'];
			$business_details['competitor1'] = str_replace('https://youtube.com/', '', $business_details['competitor1']);
			$business_details['competitor2'] = str_replace('https://youtube.com/', '', $business_details['competitor2']);

		} else {
			$business_details = [];
		}

		return $business_details;
	}


	private function addUser($user_details)
	{
		$agency_id = Session::get('agency', 'id');
		if(!$agency_id) {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 10,
					'message' => 'No Agency to create user',
				],
			]);
		}

		$user_details['status'] = 'ACTIVE';
		$user_details['agencyId'] = $agency_id;

		$created_user = $this->agencyModel->createUser($user_details)['body'];

		if(getValue('status', $created_user) != 'success') {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => $created_user['message'] ?? "Some problem form API",
				],
			]);

		} else {

			response()->json([
				'status' => true,
				'data'   =>
					[
						'message' => "User created successfully",
						'extra'   => [
							'next_screen' => url('agency_team_members'),
						],
					],
			]);

		}
	}

	public function deleteAgencyEmployee()
	{
		$all_input = input()->all();
	
		$userId = $all_input['userId'];
		$agencyId = $all_input['agencyId'];
	
		if (!$userId || !$agencyId) {
			return response()->json([
				'status' => false,
				'error'  => [
					'code'    => 10,
					'message' => 'User ID or Agency ID is missing.',
				],
			]);
		}
	
		$response = $this->agencyModel->deleteAgencyEmployeeDetails($userId, $agencyId);
	
		if (isset($response['body']['status']) && $response['body']['status'] === 'success') {
			return response()->json([
				'success' => true,
				'message' => 'Employee deleted successfully.',
			]);
		} else {
			return response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => $response['body']['message'] ?? 'Failed to delete employee.',
				],
			]);
		}
	}
	
	public function updateAgencyEmployee($agyEmply_Details)
	{
        $all_input = input()->all();
	
		$response_emply_data = $this->agencyModel->updateAgencyEmployeeDetails($agyEmply_Details)['body'];
	
		if(getValue('status', $response_emply_data) != 'success') {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => $response_emply_data['message'] ?? "Some problem form API",
				],
			]);
		} else {
            response()->json([
				'status' => true,
				'data'   =>
					[
                        'message' => "Employee Updated successfully",
						'extra'   => [
                            'next_screen'=> url('agency_team_members'),
						],
					],
			]);
        }
	}

}