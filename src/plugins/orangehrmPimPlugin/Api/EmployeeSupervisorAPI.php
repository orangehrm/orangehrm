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
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\Pim\Api\Model\EmployeeSupervisorModel;
use OrangeHRM\Pim\Dto\EmployeeSupervisorSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeReportingMethodService;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Pim\Service\ReportingMethodConfigurationService;

class EmployeeSupervisorAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_REPORTING_METHOD = 'reportingMethodId';

    /**
     * @var EmployeeReportingMethodService|null
     */
    protected ?EmployeeReportingMethodService $employeeReportingMethodService = null;

    /**
     * @var ReportingMethodConfigurationService|null
     */
    protected ?ReportingMethodConfigurationService $reportingMethodService = null;


    /**
     * @return EmployeeReportingMethodService
     */
    public function getEmployeeReportingMethodService(): EmployeeReportingMethodService
    {
        if (!$this->employeeReportingMethodService instanceof EmployeeReportingMethodService) {
            $this->employeeReportingMethodService = new EmployeeReportingMethodService();
        }
        return $this->employeeReportingMethodService;
    }

    /**
     * @return ReportingMethodConfigurationService
     */
    public function getReportingMethodConfigurationService(): ReportingMethodConfigurationService
    {
        if (!$this->reportingMethodService instanceof ReportingMethodConfigurationService) {
            $this->reportingMethodService = new ReportingMethodConfigurationService();
        }
        return $this->reportingMethodService;
    }


    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/supervisors/{id}",
     *     tags={"PIM/Employee Supervisor"},
     *     summary="Get an Employee's Supervisor",
     *     operationId="get-an-employees-supervisor",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeSupervisorModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        list($empNumber, $supervisorId) = $this->getUrlAttributes();

        $empSupervisor = $this->getEmployeeReportingMethodService()->getEmployeeReportingMethodDao()->getEmployeeReportToByEmpNumbers(
            $empNumber,
            $supervisorId
        );
        $this->throwRecordNotFoundExceptionIfNotExist($empSupervisor, ReportTo::class);

        return new EndpointResourceResult(
            EmployeeSupervisorModel::class,
            $empSupervisor,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
        );
    }


    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/supervisors",
     *     tags={"PIM/Employee Supervisor"},
     *     summary="List an Employee's Supervisors",
     *     operationId="list-an-employees-supervisors-pim",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmployeeSupervisorSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 ref="#/components/schemas/Pim-EmployeeSupervisorModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="empNumber", type="integer")
     *             )
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     * @throws ServiceException
     */
    public function getAll(): EndpointCollectionResult
    {
        $employeeSupervisorSearchFilterParams = new EmployeeSupervisorSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeSupervisorSearchFilterParams);

        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );

        $employeeSupervisorSearchFilterParams->setEmpNumber(
            $empNumber
        );

        $empSupervisors = $this->getEmployeeReportingMethodService()->getImmediateSupervisorListForEmployee($employeeSupervisorSearchFilterParams);
        return new EndpointCollectionResult(
            EmployeeSupervisorModel::class,
            $empSupervisors,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => $this->getEmployeeReportingMethodService()->getImmediateSupervisorListCountForEmployee(
                        $employeeSupervisorSearchFilterParams
                    )
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
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            ...$this->getSortingAndPaginationParamsRules(EmployeeSupervisorSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/pim/employees/{empNumber}/supervisors",
     *     tags={"PIM/Employee Supervisor"},
     *     summary="Add a Supervisor to an Employee",
     *     operationId="add-a-supervisor-to-an-employee",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="empNumber", type="integer"),
     *             @OA\Property(property="reportingMethodId", type="integer"),
     *             required={"empNumber", "reportingMethodId"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeSupervisorModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        $supervisor = new ReportTo();
        $this->setSupervisorParams($supervisor);

        $supervisor = $this->getEmployeeReportingMethodService()->getEmployeeReportingMethodDao()->saveEmployeeReportTo($supervisor);
        return new EndpointResourceResult(EmployeeSupervisorModel::class, $supervisor);
    }

    /**
     * @param ReportTo $supervisor
     * @return void
     */
    public function setSupervisorParams(ReportTo $supervisor): void
    {
        $reportingMethodId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_REPORTING_METHOD
        );
        $supervisorEmpNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $supervisor->getDecorator()->setReportingMethodByReportingMethodId($reportingMethodId);
        $supervisor->getDecorator()->setSubordinateEmployeeByEmpNumber($empNumber);
        $supervisor->getDecorator()->setSupervisorEmployeeByEmpNumber($supervisorEmpNumber);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            $this->getReportingMethodIdRule()
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/pim/employees/{empNumber}/supervisors",
     *     tags={"PIM/Employee Supervisor"},
     *     summary="Delete an Employee's Supervisors",
     *     operationId="delete-an-employees-supervisors",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $ids = $this->getEmployeeReportingMethodService()->getEmployeeReportingMethodDao()->getExistingSupervisorIdsForEmpNumber(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS),
            $empNumber
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getEmployeeReportingMethodService()->getEmployeeReportingMethodDao()->deleteEmployeeSupervisors(
            $empNumber,
            $ids
        );
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            $this->getSupervisorIdsRule()
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/pim/employees/{empNumber}/supervisors/{id}",
     *     tags={"PIM/Employee Subordinates"},
     *     summary="Update an Employee's Supervisor",
     *     operationId="update-an-employees-supervisor",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="reportingMethodId", type="integer"),
     *             required={"reportingMethodId"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeSupervisorModel"
     *             ),
     *             @OA\Property(property="empNumber", type="integer")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        list($empNumber, $supervisorId) = $this->getUrlAttributes();
        $reportingMethodId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_REPORTING_METHOD
        );

        $employeeSupervisor = $this->getEmployeeReportingMethodService()
            ->getEmployeeReportingMethodDao()
            ->getEmployeeReportToByEmpNumbers($empNumber, $supervisorId);
        $this->throwRecordNotFoundExceptionIfNotExist($employeeSupervisor, ReportTo::class);
        $reportingMethod = $this->getReportingMethodConfigurationService()->getReportingMethodById($reportingMethodId);
        $employeeSupervisor->setReportingMethod($reportingMethod);
        $this->getEmployeeReportingMethodService()->getEmployeeReportingMethodDao()->saveEmployeeReportTo($employeeSupervisor);

        return new EndpointResourceResult(
            EmployeeSupervisorModel::class,
            $employeeSupervisor,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @return array
     */
    private function getUrlAttributes(): array
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        return [$empNumber, $id];
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::REQUIRED), new Rule(Rules::POSITIVE)),
            $this->getEmpNumberRule(),
            $this->getReportingMethodIdRule()
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
     * @return ParamRule
     */
    private function getReportingMethodIdRule(): ParamRule
    {
        return new ParamRule(self::PARAMETER_REPORTING_METHOD, new Rule(Rules::POSITIVE));
    }

    private function getSupervisorIdsRule(): ParamRule
    {
        return new ParamRule(CommonParams::PARAMETER_IDS, new Rule(Rules::ARRAY_TYPE));
    }
}
