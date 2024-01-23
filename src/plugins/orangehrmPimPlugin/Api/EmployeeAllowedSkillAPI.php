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

namespace OrangeHRM\Pim\Api;

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
use OrangeHRM\Pim\Api\Model\EmployeeAllowedSkillModel;
use OrangeHRM\Pim\Dto\EmployeeAllowedSkillSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeSkillService;

class EmployeeAllowedSkillAPI extends Endpoint implements CollectionEndpoint
{
    /**
     * @var EmployeeSkillService|null
     */
    protected ?EmployeeSkillService $employeeSkillService = null;

    /**
     * @return EmployeeSkillService
     */
    public function getEmployeeSkillService(): EmployeeSkillService
    {
        if (!$this->employeeSkillService instanceof EmployeeSkillService) {
            $this->employeeSkillService = new EmployeeSkillService();
        }
        return $this->employeeSkillService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/skills/allowed",
     *     tags={"PIM/Employee Skill"},
     *     summary="List Allowed Skills for an Employee",
     *     operationId="list-allowed-skills-for-an-employee",
     *     description="This endpoint allows you to list the skills that are available for an employee. This can be used when adding skills to an employee to determine which skills are available for adding.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         description="Sort the skills by name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmployeeAllowedSkillSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Pim-EmployeeAllowedSkillModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", description="The total number of available skills", type="integer")
     *             )
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $employeeAllowedSkillSearchFilterParams = new EmployeeAllowedSkillSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeAllowedSkillSearchFilterParams);
        $employeeAllowedSkillSearchFilterParams->setEmpNumber($empNumber);

        $employeeSkills = $this->getEmployeeSkillService()
            ->getEmployeeSkillDao()
            ->getEmployeeAllowedSkills($employeeAllowedSkillSearchFilterParams);
        $count = $this->getEmployeeSkillService()
            ->getEmployeeSkillDao()
            ->getEmployeeAllowedSkillsCount($employeeAllowedSkillSearchFilterParams);

        return new EndpointCollectionResult(
            EmployeeAllowedSkillModel::class,
            $employeeSkills,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => $count,
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            ...$this->getSortingAndPaginationParamsRules(EmployeeAllowedSkillSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @return ParamRule
     */
    private function getEmpNumberRule(): ParamRule
    {
        return new ParamRule(
            CommonParams::PARAMETER_EMP_NUMBER,
            new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
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
