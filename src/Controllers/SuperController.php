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




    

    public function showAgencyToUpdate()
	{
        $edit_agencyId = request()->getInputHandler()->value('agencyId');

		$this->Breadcrumbs->add([
			'title' => 'Update Agency',
			'url'   => url('super_upadate_agency', ['agencyId' => $edit_agencyId]),
		]);

		$agency_details = $this->superModel->getAgencyDetails($edit_agencyId)['body'];

		if(getValue('status', $agency_details) == 'success') {

			$agency_details = $agency_details['data'];

		} else {
			$agency_details = [];
		}

		$this->setViewData('update_agency.html',
			[
				'form_action'          => url('super_ajax'),
				'agency_details' => $agency_details,
				'page_title'           => "Update Agency",
				'hide_side_menu' => true,
			]
		);
	}

    private function updateAgency($agency_id, $agency_details)
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

		$updated_created_agency = $this->superModel->updateAgencyModel($agency_id, $agency_details)['body'];

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
            
            case 'update_agency' :
                $agency_id = $all_input['agency_id'];
                $this->updateAgency($agency_id, $all_input);
                break;

            default:
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid form source.',
                ]);
        }
    }



}

