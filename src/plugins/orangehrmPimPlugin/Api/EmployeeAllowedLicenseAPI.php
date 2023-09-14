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
use OrangeHRM\Pim\Api\Model\EmployeeAllowedLicenseModel;
use OrangeHRM\Pim\Dto\EmployeeAllowedLicenseSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeLicenseService;

class EmployeeAllowedLicenseAPI extends Endpoint implements CollectionEndpoint
{
    /**
     * @var EmployeeLicenseService|null
     */
    protected ?EmployeeLicenseService $employeeLicenseService = null;

    /**
     * @return EmployeeLicenseService
     */
    public function getEmployeeLicenseService(): EmployeeLicenseService
    {
        if (!$this->employeeLicenseService instanceof EmployeeLicenseService) {
            $this->employeeLicenseService = new EmployeeLicenseService();
        }
        return $this->employeeLicenseService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/licenses/allowed",
     *     tags={"PIM/Employee Licenses"},
     *     summary="List Allowed Licenses for an Employee",
     *     operationId="list-allowed-licenses-for-an-employee",
     *     description="This endpoint allows you to get allowed licenses for an employee. It can be used before adding licenses to an employee in order to get a list of the records available for adding.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         description="Sort the licenses by their name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmployeeAllowedLicenseSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Admin-LicenseModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", description="The employee number given in the request", type="integer"),
     *                 @OA\Property(property="total", description="The total number of allowed license records", type="integer")
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
        $employeeAllowedLicenseSearchFilterParams = new EmployeeAllowedLicenseSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeAllowedLicenseSearchFilterParams);
        $employeeAllowedLicenseSearchFilterParams->setEmpNumber($empNumber);

        $employeeSkills = $this->getEmployeeLicenseService()
            ->getEmployeeLicenseDao()
            ->getEmployeeAllowedLicenses($employeeAllowedLicenseSearchFilterParams);
        $count = $this->getEmployeeLicenseService()
            ->getEmployeeLicenseDao()
            ->getEmployeeAllowedLicensesCount($employeeAllowedLicenseSearchFilterParams);

        return new EndpointCollectionResult(
            EmployeeAllowedLicenseModel::class,
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
            ...$this->getSortingAndPaginationParamsRules(EmployeeAllowedLicenseSearchFilterParams::ALLOWED_SORT_FIELDS)
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
