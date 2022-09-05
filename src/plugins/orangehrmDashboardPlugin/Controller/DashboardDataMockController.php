<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Dashboard\Controller;

use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Admin\Service\LocationService;
use OrangeHRM\Core\Controller\AbstractController;

class DashboardDataMockController extends AbstractController
{
    protected ?LocationService $locationService = null;

    /**
     * @return LocationService
     */
    protected function getLocationService(): LocationService
    {
        if (!$this->locationService instanceof LocationService) {
            $this->locationService = new LocationService();
        }
        return $this->locationService;
    }

    /**
     * @return Response
     */
    public function getEmployeeLocationDistribution(): Response
    {
        $response = new Response();

        // Add fake data and sort
        $locations = array_map(function ($value) {
            return [
                "location" => $value,
                "count" => rand(0, 1000),
            ];
        }, $this->getLocationService()->getAccessibleLocationsArray());

        usort($locations, function ($item1, $item2) {
            return $item2['count'] <=> $item1['count'];
        });

        $otherEmpCount = 0;

        // Limit Subunits to 8
        if (count($locations) > 8) {
            $otherLocations = array_splice($locations, 8);
            $otherEmpCount =  array_sum(array_column($otherLocations, 'count'));
        }

        $response->setContent(
            json_encode([
                "data" => $locations,
                "meta" => [
                    "otherEmployeeCount" => $otherEmpCount,
                    "unassignedEmployeeCount" => rand(0, 1000)
                ]
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }
}
