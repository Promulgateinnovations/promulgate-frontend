<?php

namespace Promulgate\Controllers;

use Promulgate\Models\ReportsModel;
use Promulgate\Models\AdminModel;
use Josantonius\Session\Session;



/**
 * Class ReportsController
 *
 * @package Promulgate\Controllers
 */
class ReportsController extends BaseController
{
    private $reportsModel;
    private $adminModel;
    private $organizationId;




    /**
     * ReportsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        // Initialize ReportsModel
        $this->reportsModel = new ReportsModel(); // Initialize the ReportsModel to fetch reports
        $this->adminModel = new AdminModel(); // Initialize the ReportsModel to fetch reports
        $this->organizationId = Session::get('organization', 'id');



    }

    /**
     * Set up the web context for Reports.
     */
    protected function setWebContext()
    {
        // Ensure Breadcrumbs is initialized as an object, not an array
        if (!isset($this->Breadcrumbs) || !is_object($this->Breadcrumbs)) {
            $this->Breadcrumbs = new BreadcrumbManager(); // Use the correct class (BreadcrumbManager)
        }

        // Example of setting a breadcrumb
        $breadcrumb = [
            'title' => 'Reports',
            'url'   => url('reports_overview'),
        ];

        // Add the breadcrumb object method
        if (is_object($this->Breadcrumbs)) {
            $this->Breadcrumbs->add($breadcrumb); // Assuming the `add()` method exists
        }

        // Set the view directory path
        $this->view_sub_directory_path = "/reports/";
    }

    /**
     * Display reports overview.
     *
     * @param string $orgId
     * @param string $month
     * @param string $year
     */
    
     public function showReportsOverview($orgId = null, $month = null, $year = null)
     {
         $all_input = input()->all();
     
         // Use session stored organization ID if not provided
         $orgId = $orgId ?? $this->organizationId ?? 'default-org-id';
         $month = $month ?? $all_input['month'] ?? date('m');
         $year = $year ?? $all_input['year'] ?? date('Y');
     
         // Fetch reports based on parameters
         $reportData = $this->reportsModel->getAnalyticsReport($orgId, $month, $year);
     
         // Handle report data
         $reports = !empty($reportData['body']) ? $reportData['body'] : [['title' => 'No Reports Available', 'description' => 'No data available for the selected period.']];
     
         // Pass data to the view
         $this->setViewData('Report.html', [
             'form_action' => url('reports_ajax'),
             'reports' => $reports,
             'page_title' => 'Reports Overview',
             'organization_id' => $this->organizationId,
         ]);
     
     }
     
      
    public function processAjax()
    {
        $all_input = input()->all();

        // Set a default value for 'form_source' if not provided
        $all_input['form_source'] = $all_input['form_source'] ?? "";

        switch ($all_input['form_source']) {
            case 'report':
                // Fetch orgId, month, year for the report from the input or use defaults
                $orgId = $all_input['organization_id'] ?? $this->organizationId; // Use the session stored organization ID if not provided
                $month = $all_input['month'] ?? null;  // Make sure month is passed explicitly
                $year = $all_input['year'] ?? null;    // Make sure year is passed explicitly

                // Call the showReportsOverview function with the provided parameters
                $this->showReportsOverview($orgId, $month, $year);
                break;

            default:
                // Default case for unsupported form_source
                response()->json([
                    'status' => false,
                    'message' => 'Invalid form_source provided.',
                ]);
                break;
        }
    }
}

