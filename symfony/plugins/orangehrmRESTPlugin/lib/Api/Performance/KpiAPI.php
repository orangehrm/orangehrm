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

namespace Orangehrm\Rest\Api\Performance;

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Performance\Entity\Kpi;
use Orangehrm\Rest\Http\Response;

class KpiAPI extends EndPoint
{

    protected $kpiService;

    /**
     *
     * @return \KpiService
     */
    public function getKpiService() {

        if ($this->kpiService == null) {
            return new \KpiService();
        } else {
            return $this->kpiService;
        }
    }

    /**
     * Get Kpi details
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function getKpiDetails()
    {
        $responseArray = null;
        $kpis = $this->getKpiService()->searchKpi();

        foreach ($kpis as $kpi){
            $tempKpi = new Kpi();
            $tempKpi->build($kpi);
            $jobTitle = $kpi->getJobTitle();
            if($jobTitle instanceof \JobTitle){
                $tempKpi->setJobTitle($jobTitle->toArray());
            }
            $responseArray[]=$tempKpi->toArray();
        }
        if(count($responseArray) > 0){
            return new Response($responseArray, array());
        } else {
            throw new RecordNotFoundException("No Records Found");
        }

    }
}
