<?php

namespace Promulgate\Controllers;

use Promulgate\Models\DealershipModel;
use Promulgate\Models\AdminModel;
use Josantonius\Session\Session;

/**
 * Class DealershipController
 *
 * @package Promulgate\Controllers
 */
class DealershipController extends BaseController
{
    private $dealershipModel;
    private $adminModel;
    private $organizationId;

    /**
     * DealershipController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        // Initialize DealershipModel
        $this->dealershipModel = new DealershipModel(); // Fetch dealership data
        $this->adminModel = new AdminModel(); // Fetch admin-related data
        $this->organizationId = Session::get('organization', 'id');
    }

    /**
     * Set up the web context for Dealership.
     */
    protected function setWebContext()
    {
        // Ensure Breadcrumbs is initialized as an object
        if (!isset($this->Breadcrumbs) || !is_object($this->Breadcrumbs)) {
            $this->Breadcrumbs = new BreadcrumbManager();
        }

        // Example of setting a breadcrumb
        $breadcrumb = [
            'title' => 'Dealership',
            'url'   => url('dealership_overview'),
        ];

        // Add the breadcrumb
        if (is_object($this->Breadcrumbs)) {
            $this->Breadcrumbs->add($breadcrumb);
        }

        // Set the view directory path
        $this->view_sub_directory_path = "/dealership/";
    }

    /**
     * Display dealership overview.
     *
     * @param string $orgId
     * @param string $month
     * @param string $year
     */
    public function showDealershipOverview($orgId = null, $month = null, $year = null)
    {
        $all_input = input()->all();

        $orgId = $orgId ?? $this->organizationId ?? 'default-org-id';
        $month = $month ?? $all_input['month'] ?? date('m');
        $year = $year ?? $all_input['year'] ?? date('Y');

        // Fetch dealership analytics
        $dealershipData = $this->dealershipModel->getAnalyticsData($orgId, $month, $year);

        // Handle dealership data
        $dealerships = !empty($dealershipData['body']) ? $dealershipData['body'] : [['title' => 'No Data Available', 'description' => 'No dealership data for the selected period.']];

        // Pass data to the view
        $this->setViewData('Dealership.html', [
            'form_action' => url('dealership_ajax'),
            'dealerships' => $dealerships,
            'page_title' => 'Dealership Overview',
            'organization_id' => $this->organizationId,
            'hide_side_menu' => false,
            'hide_side_bar' => true,  
        ]);
    }

    
    public function processAjax()
    {
        $all_input = input()->all();

        $all_input['form_source'] = $all_input['form_source'] ?? "";

        switch ($all_input['form_source']) {
            case 'dealership':
                $orgId = $all_input['organization_id'] ?? $this->organizationId;
                $month = $all_input['month'] ?? null;
                $year = $all_input['year'] ?? null;

                $this->showDealershipOverview($orgId, $month, $year);
                break;

            default:
                response()->json([
                    'status' => false,
                    'message' => 'Invalid form_source provided.',
                ]);
                break;
        }
    }
}
