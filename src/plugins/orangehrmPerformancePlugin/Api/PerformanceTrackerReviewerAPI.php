<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
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
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Authorization\UserRole\ReviewerUserRole;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Api\EmployeeAPI;
use OrangeHRM\Pim\Api\Model\EmployeeModel;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class PerformanceTrackerReviewerAPI extends Endpoint implements CollectionEndpoint
{
    use EmployeeServiceTrait;
    use UserRoleManagerTrait;

    /**
     * @OA\Get(
     *     path="/api/v2/performance/trackers/reviewers",
     *     tags={"Performance/Tracker Configuration"},
     *     summary="List All Available Tracker Reviewers",
     *     operationId="list-all-available-employees",
     *     @OA\Parameter(
     *         name="empNumber",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             description="The Id of the employee selected - this employee will be excluded from available reviewers"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="nameOrId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="includeEmployees",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmployeeSearchFilterParams::INCLUDE_EMPLOYEES_MAP)
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmployeeSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Pim-EmployeeModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeSearchFilterParams);

        $excludeEmpNumber = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            CommonParams::PARAMETER_EMP_NUMBER
        );

        $accessibleEmpNumbers = $this->getUserRoleManager()
            ->getAccessibleEntityIds(
                Employee::class,
                null,
                null,
                ['Supervisor'],
                ['Admin', 'Reviewer'],
                [BasicUserRoleManager::PERMISSION_TYPE_USER_ROLE_SPECIFIC => [ReviewerUserRole::REVIEWER_INCLUDE_EMPLOYEE => true]]
            );

        if (!is_null($excludeEmpNumber) && in_array($excludeEmpNumber, $accessibleEmpNumbers)) {
            $accessibleEmpNumbers = array_diff($accessibleEmpNumbers, [$excludeEmpNumber]);
        }

        $employeeSearchFilterParams->setEmployeeNumbers($accessibleEmpNumbers);

        $employeeSearchFilterParams->setIncludeEmployees(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                EmployeeAPI::FILTER_INCLUDE_EMPLOYEES
            )
        );

        $employeeSearchFilterParams->setNameOrId(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                EmployeeAPI::FILTER_NAME_OR_ID
            )
        );

        $employees = $this->getEmployeeService()->getEmployeeList($employeeSearchFilterParams);
        $count = $this->getEmployeeService()->getEmployeeCount($employeeSearchFilterParams);

        return new EndpointCollectionResult(
            EmployeeModel::class,
            $employees,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    EmployeeAPI::FILTER_INCLUDE_EMPLOYEES,
                    new Rule(
                        Rules::IN,
                        [
                            array_merge(
                                array_keys(EmployeeSearchFilterParams::INCLUDE_EMPLOYEES_MAP),
                                array_values(EmployeeSearchFilterParams::INCLUDE_EMPLOYEES_MAP)
                            )
                        ]
                    )
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    EmployeeAPI::FILTER_NAME_OR_ID,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, EmployeeAPI::PARAM_RULE_FILTER_NAME_OR_ID_MAX_LENGTH]),
                )
            ),
            ...$this->getSortingAndPaginationParamsRules([])
        );
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
}
