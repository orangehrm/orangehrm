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

namespace Orangehrm\Rest\Api\Pim;


use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Pim\Entity\EmployeeEvent;
use Orangehrm\Rest\Http\Response;

class EmployeeEventAPI extends EndPoint
{

    /**
     * Employee event constants
     */
    const PARAMETER_EMPLOYEE_ID = "employeeId";
    const PARAMETER_TYPE = "type";
    const PARAMETER_EVENT = "event";
    const PARAMETER_FROM_DATE = "fromDate";
    const PARAMETER_TO_DATE = "toDate";


    private $employeeEventService;

    /**
     * Get Employee event Service
     *
     * @return \EmployeeEventService|mixed
     */
    private function getEmployeeEventService()
    {

        if (is_null($this->employeeEventService)) {
            $this->employeeEventService = new \EmployeeEventService();
        }

        return $this->employeeEventService;
    }

    /**
     * @param $employeeEventService
     */
    public function setEmployeeEventService($employeeEventService)
    {
        $this->employeeEventService = $employeeEventService;
    }

    /**
     * Get employee event
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getEmployeeEvent()
    {
        $parameters = $this->getParameters();
        $eventList = $this->getEmployeeEventService()->getEmployeeEvent($parameters);
        $response = null;

        foreach ($eventList as $event) {
            $employeeEvent = new EmployeeEvent($event->getEmployeeId(), $event->getType(), $event->getEvent());
            $employeeEvent->build($event);
            $response[] = $employeeEvent->toArray();

        }
        if (count($response) > 0) {
            return new Response($response);

        } else {
            throw new RecordNotFoundException('No Event Records Found');
        }


    }

    /**
     * Create event search parameter object
     *
     * @return \ParameterObject
     */
    protected function getParameters()
    {
        $fromDate = $this->getRequestParams()->getUrlParam(self::PARAMETER_FROM_DATE);
        $toDate = $this->getRequestParams()->getUrlParam(self::PARAMETER_TO_DATE);
        $dateRange = new \DateRange($fromDate, $toDate);
        $searchParams = new \ParameterObject(array(
            'employeeId' => $this->getRequestParams()->getUrlParam(self::PARAMETER_EMPLOYEE_ID),
            'dateRange' => $dateRange,
            'event' => $this->getRequestParams()->getUrlParam(self::PARAMETER_EVENT),
            'type' => $this->getRequestParams()->getUrlParam(self::PARAMETER_TYPE)
        ));

        return $searchParams;

    }

    /**
     * Parameter validation rules
     *
     * @return array
     */
    public function getValidationRules()
    {
        return array(
            self::PARAMETER_FROM_DATE => array('Date' => array('Y-m-d')),
            self::PARAMETER_TO_DATE => array('Date' => array('Y-m-d')),

        );
    }

}
