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

namespace Orangehrm\Rest\Api\User;

use BasicUserRoleManager;
use HolidayService;
use LeavePeriodService;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;
use sfContext;
use UserRoleManagerFactory;
use WorkScheduleService;
use WorkWeek;
use WorkWeekService;

class LeaveConfigAPI extends EndPoint
{
    const PARAMETER_FROM_DATE = 'fromDate';
    const PARAMETER_TO_DATE = 'toDate';
    const PARAMETER_EMP_NUMBER = 'empNumber';

    protected $workWeekService = null;
    protected $leavePeriodService = null;
    protected $holidayService = null;
    protected $workScheduleService = null;

    /**
     * @return WorkWeekService
     */
    protected function getWorkWeekService(): WorkWeekService
    {
        if (is_null($this->workWeekService)) {
            $this->workWeekService = new WorkWeekService();
        }
        return $this->workWeekService;
    }

    /**
     * @param WorkWeekService $service
     */
    protected function setWorkWeekService(WorkWeekService $service)
    {
        $this->workWeekService = $service;
    }

    /**
     * @return LeavePeriodService
     */
    public function getLeavePeriodService(): LeavePeriodService
    {
        if (is_null($this->leavePeriodService)) {
            $this->leavePeriodService = new LeavePeriodService();
        }
        return $this->leavePeriodService;
    }

    /**
     * @param LeavePeriodService $leavePeriodService
     */
    public function setLeavePeriodService(LeavePeriodService $leavePeriodService)
    {
        $this->leavePeriodService = $leavePeriodService;
    }

    /**
     * @return HolidayService
     */
    public function getHolidayService(): HolidayService
    {
        if (is_null($this->holidayService)) {
            $this->holidayService = new HolidayService();
        }
        return $this->holidayService;
    }

    /**
     * @param HolidayService $holidayService
     */
    public function setHolidayService(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    /**
     * @return WorkScheduleService
     */
    public function getWorkScheduleService(): WorkScheduleService
    {
        if (is_null($this->workScheduleService)) {
            $this->workScheduleService = new WorkScheduleService();
        }
        return $this->workScheduleService;
    }

    /**
     *
     * @param WorkScheduleService $service
     */
    public function setWorkScheduleService(WorkScheduleService $service)
    {
        $this->workScheduleService = $service;
    }

    protected function getUserAttribute(string $name): string
    {
        return sfContext::getInstance()->getUser()->getAttribute($name);
    }

    public function getEmployeeWorkShift(): Response
    {
        $params = $this->filterEmployeeWorkShift();
        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($params[self::PARAMETER_EMP_NUMBER]);
        $workShiftLength = $workSchedule->getWorkShiftLength();
        $startEndTime = $workSchedule->getWorkShiftStartEndTime();
        $result = [
            'workShift' => is_string($workShiftLength) ? $workShiftLength : number_format(
                (float)$workShiftLength,
                2,
                '.',
                ''
            ),
            'startTime' => date('H:i', strtotime($startEndTime['start_time'])),
            'endTime' => date('H:i', strtotime($startEndTime['end_time']))
        ];
        return new Response($result);
    }

    public function getEmployeeWorkWeek(): Response
    {
        $workWeek = $this->getWorkWeekService()->getWorkWeekOfOperationalCountry(null);
        if ($workWeek instanceof WorkWeek) {
            return new Response($workWeek->toArray());
        }
        throw new RecordNotFoundException('No Records Found');
    }

    public function getHolidays(): Response
    {
        $params = $this->filterHolidaysParameters();
        $holidayList = $this->getHolidayService()->searchHolidays(
            $params[self::PARAMETER_FROM_DATE],
            $params[self::PARAMETER_TO_DATE]
        );
        $holidays = array_map(
            function ($holiday) {
                return $holiday->toArray();
            },
            $holidayList
        );
        return new Response($holidays);
    }

    public function getLeavePeriods(): Response
    {
        $leavePeriodList = $this->getLeavePeriodService()->getGeneratedLeavePeriodList();
        $leavePeriods = [];
        foreach ($leavePeriodList as $leavePeriod) {
            $leavePeriods[] = [
                'startDate' => $leavePeriod[0],
                'endDate' => $leavePeriod[1],
            ];
        }
        return new Response($leavePeriods);
    }

    public function filterHolidaysParameters(): array
    {
        $params = [];
        $fromDate = $this->getRequestParams()->getQueryParam(self::PARAMETER_FROM_DATE);
        $toDate = $this->getRequestParams()->getQueryParam(self::PARAMETER_TO_DATE);

        if (empty($fromDate) || empty($toDate)) {
            $currentLeavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d'));
            if (empty($fromDate)) {
                $fromDate = $currentLeavePeriod[0];
            }
            if (empty($toDate)) {
                $toDate = $currentLeavePeriod[1];
            }
        }
        if (strtotime($toDate) - strtotime($fromDate) < 0) {
            throw new InvalidParamException('From date should be before to date');
        }
        $params[self::PARAMETER_FROM_DATE] = $fromDate;
        $params[self::PARAMETER_TO_DATE] = $toDate;
        return $params;
    }

    public function filterEmployeeWorkShift(): array
    {
        $params = [];
        $empNumber = $this->getRequestParams()->getQueryParam(self::PARAMETER_EMP_NUMBER);
        if (empty($empNumber)) {
            $empNumber = $this->getUserAttribute("auth.empNumber");
        } elseif (!in_array($empNumber, $this->getAccessibleEmpNumbers('assign_leave'))) {
            throw new BadRequestException('Access Denied');
        }

        $params[self::PARAMETER_EMP_NUMBER] = $empNumber;
        return $params;
    }

    public function getHolidaysValidationRules(): array
    {
        return [
            self::PARAMETER_TO_DATE => ['Date' => ['Y-m-d']],
            self::PARAMETER_FROM_DATE => ['Date' => ['Y-m-d']],
        ];
    }

    protected function getAccessibleEmpNumbers(string $action): array
    {
        $properties = ["empNumber"];
        $requiredPermissions = [BasicUserRoleManager::PERMISSION_TYPE_ACTION => [$action]];
        $employeeList = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityProperties(
            'Employee',
            $properties,
            null,
            null,
            [],
            [],
            $requiredPermissions
        );

        return array_keys($employeeList);
    }
}
