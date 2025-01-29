<?php

namespace Promulgate\Controllers;

use Josantonius\Session\Session;
use Promulgate\Core\Config;
use Promulgate\Core\GoogleAPIClient;
use Promulgate\Models\SuperModel;
use Promulgate\Models\AdminModel;
use Promulgate\Models\AgencyModel;



/**
 * Class ReportsController
 *
 * @package Promulgate\Controllers
 */
class SuperController extends BaseController
{
    private $superModel;
    private $agencyModel;
    private $adminModel;
    private $agencyId;



    public function __construct()
    {
        parent::__construct();        
        $this->superModel = new SuperModel(); 
        $this->adminModel = new AdminModel();
        $this->agencyModel = new AgencyModel();
    }

    protected function setWebContext()
    {
        $this->view_sub_directory_path = "/super/";
        $this->Breadcrumbs->add([
            'title' => 'super',
            'url'   => url('super_add_new_agency'),
        ]);
        $this->setSuperAgencyId(Session::get('agency', 'id'));
    }

    private function setSuperAgencyId($agencyId)
	{
		if($agencyId && !is_array($agencyId)) {
			$agencyId       = trim($agencyId);
			$this->agencyId = $agencyId;
		}
	}

    public function showEmployeeList()
    {

        $this->Breadcrumbs->add([
            'title' => 'SuperTeam',
            'url'   => url('super_employee_list'),
        ]);


        $employee_list = $this->superModel->getEmployeeList($this->agencyId)['body'];
            if(getValue('status', $employee_list) != 'success') {
                $employee_list = [];
            } else {
                $employee_list =$employee_list['data'];
            }

        $this->setViewData('employee.html', [
            'employee_list' => $employee_list,
            'page_title'   => "Super Employee",
            'hide_side_menu' => true,
        ]);
    }

    public function showAgencyList()
    {
        $this->Breadcrumbs->add([
			'title' => 'Superagency',
			'url'   => url('super_agency_list'),
		]);

        $agency_list = $this->superModel->getAgencyList()['body'];
        if(getValue('status', $agency_list)=='success'){
            $agency_list = $agency_list['data'];
            
        }else {
            $agency_list =[];
        }

        $this->setViewData('agency.html',
			[
                'form_action'          => url('super_ajax'),
				'agency_list'       => $agency_list,
				'page_title'       => "Super Agency",
				'hide_side_menu' => true,
			]
		);
    }

    public function showAddAgency() 
    {
        $this->Breadcrumbs->add([
			'form_action'   => url('super_ajax'),
			'title'         => 'Agency',
			'url'           => url('super_agency_list'),
		]);

        $this->Breadcrumbs->add([
			'title' => 'Add New',
			'url'   => url('super_add_new_agency'),
		]);

        $this->setViewData('add_agency.html', [
			'form_action'          => url('super_ajax'),
			'page_title'              => "Super Add Agency",
			'hide_side_menu' => true,
        ]);
    }

    private function addAgency($agency_details){
        $agency_details['user_id'] = Session::get('user', 'id');

        $created_agency = $this->superModel->showAddAgencyDetails($agency_details)['body'];

        if(getValue('status', $created_agency) != 'success') {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => $created_agency['message'] ?? "Some problem form API",
				],
			]);
		} else {
            response()->json([
				'status' => true,
				'data'   =>
					[
                        'message' => "Agency created successfully",
						'extra'   => [
							'id' => $created_agency['data']['agencyId'],
                            'next_screen'=> url('super_agency_list'),
						],
					],
			]);
        }
    }
    

    private function addEmployee($user_employee)
    {
        $agency_id = Session::get('agency', 'id');
        if(!$agency_id) {
            response()->json([
                'status' => false,
                'error'  => [
                    'code'    => 10,
                    'message' => 'No Agency to create Employee',
                ],
            ]);
        }

        $user_employee['status'] = 'ACTIVE';
        $user_employee['agencyId'] = $agency_id;

        $created_employee = $this->superModel->showAddEmployeeDetails($user_employee)['body'];

        if(getValue('status', $created_employee) != 'success') {

            response()->json([
                'status' => false,
                'error'  => [
                    'code'    => 20,
                    'message' => $created_employee['message'] ?? "Some problem form API",
                ],
            ]);

        } else {

            response()->json([
                'status' => true,
                'data'   =>
                    [
                        'message' => "Employee created successfully",
                        'extra'   => [
                            'next_screen' => url('super_employee_list'),
                        ],
                    ],
            ]);

        }
    }

   

    public function showAddEmployee()
    {
        $this->Breadcrumbs->add([
            'form_action'   => url('super_ajax'),
            'title'         => 'Employee',
            'url'           => url('super_employee_list'),
        ]);

        $this->Breadcrumbs->add([
            'title'     => 'Add Employee',
            'url'       => url('super_add_new_employee'),
        ]);
        

        $this->setViewData('add_employee.html', [
            'form_action'       => url('super_ajax'),
            'page_title'        => 'Super Add Employee',
            'hide_side_menu'    => true,
        ]);
    }

    public function processAjax()
    {
        $all_input = input()->all();
        $formSource = $all_input['form_source'] ?? '';

        switch ($formSource) {
            case 'add_new_employee' :
                $this->addEmployee($all_input);
                break;

            case 'add_new_agency' :
                $this->addAgency($all_input);
                break;

            case 'deleteEmployee':
                $userId = $all_input['userId'];
                $agencyId = $all_input['agencyId'];
                    
                if ($userId && $agencyId) {
                    $this->deleteEmployee($userId, $agencyId);
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
    
            case 'updateEmployeeDetails':
                $this->updateEmployeeDetails($all_input);
                break;
                
            case 'updatedAgencyDetails':
                $this->updatedAgencyDetails($all_input);
                break;

            default:
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid form source.',
                ]);
        }
    }

    public function updateEmployeeDetails($update_Details)
	{
        $all_input = input()->all();
	
		$response_data = $this->superModel->updatesuperEmployeeDetails($update_Details)['body'];
	
		if(getValue('status', $response_data) != 'success') {

			response()->json([
				'status' => false,
				'error'  => [
					'code'    => 20,
					'message' => $response_data['message'] ?? "Some problem form API",
				],
			]);
		} else {
            response()->json([
				'status' => true,
				'data'   =>
					[
                        'message' => "Employee Updated successfully",
						'extra'   => [
                            'next_screen'=> url('super_employee_list'),
						],
					],
			]);
        }
	}

    public function updatedAgencyDetails($updated_agency)
	{
        $all_input = input()->all();
        $updated_created_agency = $this->superModel->updateSuperAgencyDetails($updated_agency)['body'];

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
			response()->json([
				'status' => true,
				'data'   =>
					[
						'message' => "Agency details updated successfully",
                        'extra'   => [
							'next_screen' => url('super_agency_list'),
						],
					],
			]);

		}

	}
    
    public function deleteEmployee()
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
	
		$response = $this->superModel->deleteEmployeeDetails($userId, $agencyId);
	
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

    public function showDetails($agency_id)
    {
        $params = [
            'agencyId' => $agency_id,
        ];
    
        $response = $this->agencyModel->getListOfOrg($agency_id)['body'];
    
        $list_of_organizations = [];
    
        if ($response['status'] == 'success') {
            $list_of_organizations = $response['data'];
        }
    
        $employeeDetailsUrl = url('agency_Empdetails') . '?agencyId=' . $agency_id;
    
        // Fetch the full agency list
        $agency_list_response = $this->superModel->getAgencyList();
        if (isset($agency_list_response['body']) && is_array($agency_list_response['body'])) {
            $agency_list = $agency_list_response['body'];
        } else {
            $agency_list = [];
        }
        // Ensure $agency_list is an array before looping
        $agency_name = '';
        if (isset($agency_list['data']) && is_array($agency_list['data'])) {
            foreach ($agency_list['data'] as $agency) {
                if (isset($agency['agencyId']) && $agency['agencyId'] == $agency_id) {
                    $agency_name = $agency['name'];
                    break;
                }
            }
        }

        $this->setViewData('AgencyDetails.html', [
            'form_action' => url('super_ajax'),
            'organizations_list' => $list_of_organizations,
            'page_title' => "Details",
            'hide_side_menu' => true,
            'agency_id' => $agency_id,
            'employee_details_url' => $employeeDetailsUrl,
            'current_url_name' => 'agency_details',
            'agency_name'         => $agency_name,
        ]);
    }
    
    public function showEmpDetails()
    {
        $agencyId = $_GET['agencyId'] ?? null;
    
        $this->Breadcrumbs->add([
            'title' => 'SuperTeam',
            'url'   => url('agency_Empdetails') . '?agencyId=' . $agencyId,
        ]);
    
        $employee_list = $this->superModel->getTeamsList($agencyId)['body'];
    
        if (getValue('status', $employee_list) != 'success') {
            $employee_list = [];
        } else {
            $employee_list = $employee_list['data'];
        }
    
        $this->setViewData('EmpDetails.html', [
            'employee_list' => $employee_list,
            'page_title'    => "Super Employee",
            'hide_side_menu' => true,
            'agency_id'     => $agencyId,
            'current_agency_id' => $agencyId,
            'current_url_name' => 'agency_Empdetails',
        ]);
    }
    
    public function showCampaignDetails($organization_id)
    {
        $list_of_campaigns = [];
        $total_no_of_campaigns = 0;
        $current_page = 1;
        $no_of_campaigns_in_current_page = 0;
    
        $all_campaigns = $this->superModel->getListOfCampaigns($organization_id)['body'];
    
        if (getValue('status', $all_campaigns) == 'success') {
            $list_of_campaigns = $all_campaigns['data']['campaignList']['rows'] ?? [];
            $total_no_of_campaigns = $all_campaigns['data']['campaignList']['count'] ?? 0;
            $current_page = $all_campaigns['data']['currentPage'] ?? 1;
            $no_of_campaigns_in_current_page = $all_campaigns['data']['pageSize'] ?? 0;
        }
    
        $this->setViewData('campaigndetails.html', [
            'campaigns_list' => $list_of_campaigns,
            'hide_side_menu' => true,
            'filters' => [
                'status' => array_unique(array_column($list_of_campaigns ?? [], 'status')),
            ],
            'page_title' => "Campaign Details",
            'total_no_of_campaigns' => $total_no_of_campaigns,
            'current_page' => $current_page,
            'no_of_campaigns_in_current_page' => $no_of_campaigns_in_current_page,
        ]);
    }
    



}

