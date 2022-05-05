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

namespace OrangeHRM\Performance\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Performance\Api\Model\EmployeeTrackerModel;
use OrangeHRM\Performance\Dto\EmployeeTrackerSearchFilterParams;
use OrangeHRM\Performance\Service\EmployeeTrackerService;

class EmployeeTrackerAPI extends Endpoint implements CollectionEndpoint
{
    public const FILTER_INCLUDE_EMPLOYEES = 'includeEmployees';

    private ?EmployeeTrackerService $employeeTrackerService = null;

    /**
     * @return EmployeeTrackerService
     */
    public function getEmployeeTrackerService(): EmployeeTrackerService
    {
        if (!$this->employeeTrackerService instanceof EmployeeTrackerService) {
            $this->employeeTrackerService = new EmployeeTrackerService();
        }
        return $this->employeeTrackerService;
    }

    public function getAll(): EndpointResult
    {
        $employeeTrackerSearchFilterParams = $this->getEmployeeTrackerSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeTrackerSearchFilterParams);

        $employeeTrackerList = $this->getEmployeeTrackerService()
            ->getEmployeeTrackerList($employeeTrackerSearchFilterParams);
        $employeeTrackerCount = $this->getEmployeeTrackerService()
            ->getEmployeeTrackerCount($employeeTrackerSearchFilterParams);

        return new EndpointCollectionResult(
            EmployeeTrackerModel::class,
            $employeeTrackerList,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $employeeTrackerCount])
        );
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_INCLUDE_EMPLOYEES,
                    new Rule(Rules::IN, [EmployeeTrackerSearchFilterParams::INCLUDE_EMPLOYEES])
                )
            ),
            ...$this->getSortingAndPaginationParamsRules(
                EmployeeTrackerSearchFilterParams::ALLOWED_SORT_FIELDS
            )
        );
    }

    /**
     * @return EmployeeTrackerSearchFilterParams
     */
    protected function getEmployeeTrackerSearchFilterParams(): EmployeeTrackerSearchFilterParams
    {
        $employeeTrackerSearchFilterParams = new EmployeeTrackerSearchFilterParams();
        $employeeTrackerSearchFilterParams->setEmpNumber(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                CommonParams::PARAMETER_EMP_NUMBER
            )
        );
        $employeeTrackerSearchFilterParams->setIncludeEmployees(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_INCLUDE_EMPLOYEES,
                EmployeeTrackerSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT
            )
        );
        return $employeeTrackerSearchFilterParams;
    }

    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
