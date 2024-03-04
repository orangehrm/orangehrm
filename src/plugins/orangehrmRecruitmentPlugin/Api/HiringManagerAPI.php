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

namespace OrangeHRM\Recruitment\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Pim\Api\Model\EmployeeModel;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;
use OrangeHRM\Recruitment\Dto\VacancySearchFilterParams;
use OrangeHRM\Recruitment\Traits\Service\VacancyServiceTrait;

class HiringManagerAPI extends Endpoint implements CollectionEndpoint
{
    use VacancyServiceTrait;
    use EmployeeServiceTrait;
    use UserRoleManagerTrait;

    /**
     * @OA\Get(
     *     path="/api/v2/recruitment/hiring-managers",
     *     tags={"Recruitment/Hiring Managers"},
     *     summary="List Available Employees for Hiring Manager",
     *     operationId="list-available-employees-for-hiring-manager",
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
     *                 ref="#/components/schemas/Pim-EmployeeModel"
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $accessibleVacancyIds = $this->getUserRoleManager()->getAccessibleEntityIds(Vacancy::class);
        $vacancySearchFilterParams = new VacancySearchFilterParams();
        $vacancySearchFilterParams->setVacancyIds($accessibleVacancyIds);
        $hiringManagerEmpNumbers = $this->getVacancyService()
            ->getVacancyDao()
            ->getHiringManagerEmpNumberList($vacancySearchFilterParams);
        $employeeSearchFilterParams = new EmployeeSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeSearchFilterParams);
        $employeeSearchFilterParams->setEmployeeNumbers($hiringManagerEmpNumbers);
        $employeeSearchFilterParams->setIncludeEmployees(
            EmployeeSearchFilterParams::INCLUDE_EMPLOYEES_CURRENT_AND_PAST
        );
        $hiringManagers = $this->getEmployeeService()->getEmployeeList($employeeSearchFilterParams);
        $count = $this->getEmployeeService()->getEmployeeCount($employeeSearchFilterParams);
        return new EndpointCollectionResult(
            EmployeeModel::class,
            $hiringManagers,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules(EmployeeSearchFilterParams::ALLOWED_SORT_FIELDS)
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
