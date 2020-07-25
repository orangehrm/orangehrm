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
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Http\Response;
use UserRoleManagerFactory;

class EmployeesAPI extends EndPoint
{
    const PARAMETER_ACTION = 'actionName';
    const PARAMETER_PROPERTIES = 'properties';
    const PARAMETER_PAST_EMPLOYEE = 'pastEmployee';

    public function getEmployees():Response
    {
        $params = $this->filterParameters();
        $employeeList = $this->getAccessibleEmployees(
            $params[self::PARAMETER_ACTION],
            $params[self::PARAMETER_PROPERTIES],
            $params[self::PARAMETER_PAST_EMPLOYEE]
        );
        return new Response(array_values($employeeList));
    }

    public function getAccessibleEmployees(
        string $action = null,
        array $properties = [],
        bool $withPastEmployee = false
    ): array {
        $isTerminationIdRequested = in_array('termination_id', $properties);
        if (!$isTerminationIdRequested) {
            array_push($properties, 'termination_id');
        }
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

        $employees = [];
        foreach ($employeeList as $employee) {
            if (!$withPastEmployee && !is_null($employee['termination_id'])) {
                continue;
            }
            if (!$isTerminationIdRequested) {
                unset($employee['termination_id']);
            }
            unset($employee['ReportTo']);
            $employees[] = $employee;
        }
        return $employees;
    }

    protected function filterParameters(): array
    {
        $params = [];
        $params[self::PARAMETER_ACTION] = $this->getRequestParams()->getQueryParam(self::PARAMETER_ACTION);
        $properties = $this->getRequestParams()->getQueryParam(self::PARAMETER_PROPERTIES);
        if (empty($properties)) {
            $properties = [];
        } elseif (!is_array($properties)) {
            throw new InvalidParamException(sprintf("Invalid `%s` Value", self::PARAMETER_PROPERTIES));
        }

        $pastEmployee = $this->getRequestParams()->getQueryParam(self::PARAMETER_PAST_EMPLOYEE, 'false');
        if (!($pastEmployee == 'true' || $pastEmployee == 'false')) {
            throw new InvalidParamException(sprintf("Invalid `%s` Value", self::PARAMETER_PAST_EMPLOYEE));
        }
        $pastEmployee = $pastEmployee == 'true' ? true : false;

        $params[self::PARAMETER_PROPERTIES] = $properties;
        $params[self::PARAMETER_PAST_EMPLOYEE] = $pastEmployee;
        return $params;
    }
}
