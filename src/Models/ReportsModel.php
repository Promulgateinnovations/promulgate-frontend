<?php

namespace Promulgate\Models;

/**
 * Class ReportsModel
 *
 * Handles fetching analytics reports and related details.
 *
 * @package Promulgate\Models
 */
class ReportsModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get Analytics Report
     *
     * Fetches the analytics report based on the provided parameters (orgId, month, year).
     *
     * @param string $orgId
     * @param string $month
     * @param string $year
     * @return array
     */
    public function getAnalyticsReport(string $orgId, string $month, string $year): array
{
    // Check if the necessary parameters are provided
    if (!$orgId || !$month || !$year) {
        return [
            'body' => [], // Return empty response if parameters are missing
        ];
    }

    // Prepare the payload with the required parameters
    $payload = [
        'orgId' => $orgId,
        'month' => $month,
        'year' => $year,
    ];

    // Make the POST request with the payload
    return $this->makeRequest('POST', '/api/v1/analytics-report', [
        'json' => $payload,
    ]);
}

}
