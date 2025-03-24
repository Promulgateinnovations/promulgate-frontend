<?php

namespace Promulgate\Models;

/**
 * Class DealershipModel
 *
 * Handles fetching dealership analytics and related details.
 *
 * @package Promulgate\Models
 */
class DealershipModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get Dealership Analytics Data
     *
     * Fetches the dealership analytics data based on the provided parameters (orgId, month, year).
     *
     * @param string $orgId
     * @param string $month
     * @param string $year
     * @return array
     */
    public function getAnalyticsData(string $orgId, string $month, string $year): array
    {
        if (!$orgId || !$month || !$year) {
            return [
                'body' => [],
            ];
        }

        $payload = [
            'orgId' => $orgId,
            'month' => $month,
            'year' => $year,
        ];

        return $this->makeRequest('POST', '/api/v1/delear-analy-report', [
            'json' => $payload,
        ]);
    }
}
