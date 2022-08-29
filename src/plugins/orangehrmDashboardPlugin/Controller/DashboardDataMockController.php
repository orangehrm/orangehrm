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
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Admin\Service\CompanyStructureService;

class DashboardDataMockController extends AbstractController
{
    protected ?CompanyStructureService $companyStructureService = null;

    /**
     * @return CompanyStructureService
     */
    protected function getCompanyStructureService(): CompanyStructureService
    {
        if (!$this->companyStructureService instanceof CompanyStructureService) {
            $this->companyStructureService = new CompanyStructureService();
        }
        return $this->companyStructureService;
    }

    /**
     * @return Response
     */
    public function getEmployeeSubunitDistribution(): Response
    {
        $response = new Response();

        // Get 1st level subunits
        $subunits = array_filter($this->getCompanyStructureService()->getSubunitArray(), function ($value) {
            return $value['_indent'] === 1;
        });

        // Add fake data and sort 
        $subunits = array_map(function ($value) {
            return [
                ...$value,
                "employeeCount" => rand(0, 1000),
            ];
        }, $subunits);
        usort($subunits, function ($item1, $item2) {
            return $item2['employeeCount'] <=> $item1['employeeCount'];
        });

        // Limit Subunits to 8
        if (count($subunits) > 8) {
            $otherSubUnits = array_splice($subunits, 8);
            array_push($subunits, [
                "id" => 9999,
                "_indent" => 1,
                "label" => "Other",
                "employeeCount" => array_sum(array_column($otherSubUnits, 'employeeCount')),
            ]);
        }

        array_push($subunits,  [
            "id" => 9999,
            "_indent" => 1,
            "label" => "Unassigned",
            "employeeCount" => rand(0, 1000),
        ]);


        $response->setContent(
            json_encode([
                "data" => $subunits,
                "meta" => []
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }
}
