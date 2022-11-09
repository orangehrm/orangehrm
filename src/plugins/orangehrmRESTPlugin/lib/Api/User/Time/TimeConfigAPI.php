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

namespace Orangehrm\Rest\Api\User\Time;

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Http\Response;
use TimesheetPeriodService;

class TimeConfigAPI extends EndPoint
{
    /**
     * @var null|TimesheetPeriodService
     */
    protected $timesheetPeriodService = null;

    public function getTimesheetPeriodService(): TimesheetPeriodService
    {
        if (is_null($this->timesheetPeriodService)) {
            $this->timesheetPeriodService = new TimesheetPeriodService();
        }
        return $this->timesheetPeriodService;
    }

    /**
     * @param TimesheetPeriodService $service
     */
    public function setTimesheetPeriodService(TimesheetPeriodService $service)
    {
        $this->timesheetPeriodService = $service;
    }

    public function getTimeConfigs(): Response
    {
        $startDate = $this->getTimesheetPeriodService()->getTimesheetStartDate();
        $startDate = $startDate ? intval($startDate) : TimesheetPeriodService::DEFAULT_TIMESHEET_START_DATE;
        return new Response(
            [
                'startDate' => $startDate
            ]
        );
    }
}
