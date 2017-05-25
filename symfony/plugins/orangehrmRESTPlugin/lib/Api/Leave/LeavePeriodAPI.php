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

namespace Orangehrm\Rest\Api\Leave;

use Orangehrm\Rest\Api\EndPoint;

use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;

class LeavePeriodAPI extends EndPoint
{
    private $leavePeriodService;

    /**
     * @return \LeavePeriodService
     */
    public function getLeavePeriodService()
    {
        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new \LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new \LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }

        return $this->leavePeriodService;
    }

    /**
     * @param mixed $leavePeriodService
     */
    public function setLeavePeriodService($leavePeriodService)
    {
        $this->leavePeriodService = $leavePeriodService;
    }

    /**
     * Get leave periods
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getLeavePeriod()
    {
        $leavePeriodList = $this->getLeavePeriodService()->getGeneratedLeavePeriodList();
        if(!empty($leavePeriodList)) {
            return new Response($leavePeriodList);
        }else {
            throw new RecordNotFoundException('No Leave Periods Found');
        }

    }

}
