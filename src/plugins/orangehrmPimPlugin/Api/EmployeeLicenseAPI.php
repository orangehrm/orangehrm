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

use Exception;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\EmployeeLicense;
use OrangeHRM\Pim\Api\Model\EmployeeLicenseModel;
use OrangeHRM\Pim\Dto\EmployeeLicenseSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeLicenseService;

class EmployeeLicenseAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_LICENSE_ID = 'licenseId';
    public const PARAMETER_LICENSE_NO = 'licenseNo';
    public const PARAMETER_LICENSE_ISSUED_DATE = 'issuedDate';
    public const PARAMETER_LICENSE_EXPIRED_DATE = 'expiryDate';

    public const PARAM_RULE_LICENSE_NO_MAX_LENGTH = 50;

    /**
     * @var null|EmployeeLicenseService
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
     *     path="/api/v2/pim/employees/{empNumber}/licenses/{id}",
     *     tags={"PIM/Employee Licenses"},
     *     summary="Get an Employee's License",
     *     operationId="get-an-employees-license",
     *     description="This endpoint allows you to get one of an employee's licenses.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         description="Specify the numerical ID of the desired license",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeLicenseModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", description="The employee number given in the request", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     * @return EndpointResourceResult
     */
    public function getOne(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $licenseId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $employeeLicense = $this->getEmployeeLicenseService()->getEmployeeLicenseDao()->getEmployeeLicense(
            $empNumber,
            $licenseId
        );
        $this->throwRecordNotFoundExceptionIfNotExist($employeeLicense, EmployeeLicense::class);

        return new EndpointResourceResult(
            EmployeeLicenseModel::class,
            $employeeLicense,
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
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getEmpNumberRule(),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/licenses",
     *     tags={"PIM/Employee Licenses"},
     *     summary="List an Employee's Licenses",
     *     operationId="list-an-employees-licenses",
     *     description="This endpoint allows you to list an employee's licenses.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="The employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         description="Sort the licenses by the issued date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmployeeLicenseSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Pim-EmployeeLicenseModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", description="The total number of employee license records", type="integer"),
     *                 @OA\Property(property="empNumber", description="The employee number given in the request", type="integer")
     *             )
     *         )
     *     ),
     * )
     *
     * @return EndpointCollectionResult
     * @throws Exception
     */
    public function getAll(): EndpointCollectionResult
    {
        $employeeLicenseSearchParams = new EmployeeLicenseSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeLicenseSearchParams);

        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $employeeLicenseSearchParams->setEmpNumber(
            $empNumber
        );

        $employeeLicenses = $this->getEmployeeLicenseService()->getEmployeeLicenseDao()->searchEmployeeLicense($employeeLicenseSearchParams);

        return new EndpointCollectionResult(
            EmployeeLicenseModel::class,
            $employeeLicenses,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => $this->getEmployeeLicenseService()->getEmployeeLicenseDao()->getSearchEmployeeLicensesCount(
                        $employeeLicenseSearchParams
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
            $this->getEmpNumberRule(),
            ...$this->getSortingAndPaginationParamsRules(EmployeeLicenseSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/pim/employees/{empNumber}/licenses",
     *     tags={"PIM/Employee Licenses"},
     *     summary="Add a License to an Employee",
     *     operationId="add-a-license-to-an-employee",
     *     description="This endpoint allows you to create an employee license.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="licenseId", description="Specify the numerical ID of the license", type="integer"),
     *             @OA\Property(
     *                 property="licenseNo",
     *                 description="Specify the license number",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmployeeLicenseAPI::PARAM_RULE_LICENSE_NO_MAX_LENGTH
     *             ),
     *             @OA\Property(property="issuedDate", description="Specify the issued date of the license", type="string", format="date"),
     *             @OA\Property(property="expiryDate",  description="Specify the expiry date of the license", type="string", format="date"),
     *             required={"licenseNo"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeLicenseModel"
     *             ),
     *             @OA\Property(property="empNumber", description="The employee number given in the request", type="integer")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointResourceResult
    {
        $employeeLicense = $this->saveEmployeeLicense();
        return new EndpointResourceResult(
            EmployeeLicenseModel::class,
            $employeeLicense,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $employeeLicense->getEmployee()->getEmpNumber(),
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_LICENSE_ID, new Rule(Rules::REQUIRED), new Rule(Rules::POSITIVE)),
            $this->getEmpNumberRule(),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(): array
    {
        return [
            new ParamRule(
                self::PARAMETER_LICENSE_NO,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_LICENSE_NO_MAX_LENGTH]),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_LICENSE_ISSUED_DATE,
                    new Rule(Rules::API_DATE)
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_LICENSE_EXPIRED_DATE,
                    new Rule(Rules::API_DATE),
                    new Rule(
                        Rules::GREATER_THAN,
                        [$this->getRequestParams()->getDateTimeOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_LICENSE_ISSUED_DATE)]
                    )
                ),
            ),
        ];
    }

    /**
     * @OA\Put(
     *     path="/api/v2/pim/employees/{empNumber}/licenses/{id}",
     *     tags={"PIM/Employee Licenses"},
     *     summary="Update an Employee's License",
     *     operationId="update-an-employees-license",
     *     description="This endpoint allows you to update an employee's license.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         description="Specify the numerical ID of the employee license",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="licenseId", description="Specify the numerical ID of the license", type="integer"),
     *             @OA\Property(
     *                 property="licenseNo",
     *                 description="Specify the license number",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmployeeLicenseAPI::PARAM_RULE_LICENSE_NO_MAX_LENGTH
     *             ),
     *             @OA\Property(property="issuedDate", description="Specify the issue date of the license date", type="string", format="date"),
     *             @OA\Property(property="expiryDate", description="Specify the expiry date of the license", type="string", format="date"),
     *             required={"licenseNo"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeLicenseModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", description="The employee number given in the request", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResourceResult
    {
        $employeeLicense = $this->saveEmployeeLicense();

        return new EndpointResourceResult(
            EmployeeLicenseModel::class,
            $employeeLicense,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $employeeLicense->getEmployee()->getEmpNumber(),
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
            $this->getEmpNumberRule(),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/pim/employees/{empNumber}/licenses",
     *     tags={"PIM/Employee Licenses"},
     *     summary="Delete an Employee's Licenses",
     *     operationId="delete-an-employees-licenses",
     *     description="This enpoint allows you to delete an employee's licenses.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse")
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function delete(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getEmployeeLicenseService()->getEmployeeLicenseDao()->deleteEmployeeLicenses($empNumber, $ids);
        return new EndpointResourceResult(
            ArrayModel::class,
            $ids,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }

    /**
     * @return EmployeeLicense
     */
    public function saveEmployeeLicense(): EmployeeLicense
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $licenseId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_LICENSE_ID);
        $licenseNo = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_LICENSE_NO
        );
        $issuedDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_LICENSE_ISSUED_DATE
        );
        $expiryDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_LICENSE_EXPIRED_DATE
        );
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        if (!empty($licenseId)) {
            $id = $licenseId;
        }
        $employeeLicense = $this->getEmployeeLicenseService()->getEmployeeLicenseDao()->getEmployeeLicense(
            $empNumber,
            $id
        );
        if ($employeeLicense == null) {
            $employeeLicense = new EmployeeLicense();
            $employeeLicense->getDecorator()->setEmployeeByEmpNumber($empNumber);
            $employeeLicense->getDecorator()->setLicenseByLicenseId($id);
        }

        $employeeLicense->setLicenseNo($licenseNo);
        $employeeLicense->setLicenseIssuedDate($issuedDate);
        $employeeLicense->setLicenseExpiryDate($expiryDate);

        return $this->getEmployeeLicenseService()->getEmployeeLicenseDao()->saveEmployeeLicense($employeeLicense);
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
}
