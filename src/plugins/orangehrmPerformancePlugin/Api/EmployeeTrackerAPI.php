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
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\InAccessibleEntityIdOption;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Entity\PerformanceTrackerReviewer;
use OrangeHRM\Performance\Api\Model\EmployeeTrackerModel;
use OrangeHRM\Performance\Api\Model\PerformanceTrackerModel;
use OrangeHRM\Performance\Dto\EmployeeTrackerSearchFilterParams;
use OrangeHRM\Performance\Service\PerformanceTrackerService;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerServiceTrait;

class EmployeeTrackerAPI extends Endpoint implements CrudEndpoint
{
    use UserRoleManagerTrait;
    use PerformanceTrackerServiceTrait;

    public const FILTER_INCLUDE_EMPLOYEES = 'includeEmployees';
    public const FILTER_NAME_OR_ID = 'nameOrId';

    public const PARAM_RULE_FILTER_NAME_OR_ID_MAX_LENGTH = 100;

    private ?PerformanceTrackerService $employeeTrackerService = null;

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $employeeTrackerSearchFilterParams = $this->getEmployeeTrackerSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeTrackerSearchFilterParams);

        $empNumber = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            CommonParams::PARAMETER_EMP_NUMBER
        );

        if (!is_null($empNumber)) {
            $employeeTrackerSearchFilterParams->setEmpNumbers([$empNumber]);
        } else {
            $accessibleEmpNumbers = $this->getUserRoleManager()->getAccessibleEntityIds(PerformanceTrackerReviewer::class);
            $employeeTrackerSearchFilterParams->setEmpNumbers($accessibleEmpNumbers);
        }

        $employeeTrackerList = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->getEmployeeTrackerList($employeeTrackerSearchFilterParams);
        $employeeTrackerCount = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->getEmployeeTrackerCount($employeeTrackerSearchFilterParams);

        return new EndpointCollectionResult(
            EmployeeTrackerModel::class,
            $employeeTrackerList,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $employeeTrackerCount])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        $inaccessibleEntityIdOption = new InAccessibleEntityIdOption();
        $inaccessibleEntityIdOption->setThrow(false)->setThrowIfOnlyEntityExist(false);

        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [PerformanceTrackerReviewer::class, $inaccessibleEntityIdOption])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_NAME_OR_ID,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_FILTER_NAME_OR_ID_MAX_LENGTH]),
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
        $employeeTrackerSearchFilterParams->setNameOrId(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME_OR_ID
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

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()
            ->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $performanceTracker = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->getPerformanceTrack($id);
        $this->throwRecordNotFoundExceptionIfNotExist($performanceTracker, PerformanceTracker::class);
        return new EndpointResourceResult(PerformanceTrackerModel::class, $performanceTracker);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(new ParamRule(
            CommonParams::PARAMETER_ID,
            new Rule(Rules::POSITIVE)
        ));
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
