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

namespace OrangeHRM\Pim\Api;

use Exception;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\EndpointCreateResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointDeleteResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetAllResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetOneResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointUpdateResult;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
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

    public const FILTER_LICENSE_NO = 'licenseNo';

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
     * @param EmployeeLicenseService $employeeLicenseService
     */
    public function setEmployeeLicenseService(EmployeeLicenseService $employeeLicenseService): void
    {
        $this->employeeLicenseService = $employeeLicenseService;
    }

    /**
     * @return EndpointGetOneResult
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function getOne(): EndpointGetOneResult
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

        return new EndpointGetOneResult(
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
            new ParamRule(CommonParams::PARAMETER_ID),
            $this->getEmpNumberRule(),
        );
    }

    /**
     * @return EndpointGetAllResult
     * @throws Exception
     */
    public function getAll(): EndpointGetAllResult
    {
        $employeeLicenseSearchParams = new EmployeeLicenseSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeLicenseSearchParams);
//        $employeeLicenseSearchParams->setLicenseNo(
//            $this->getRequestParams()->getStringOrNull(
//                RequestParams::PARAM_TYPE_QUERY,
//                self::FILTER_LICENSE_NO
//            )
//        );
//        $employeeLicenseSearchParams->setLicenseIssuedDate(
//            $this->getRequestParams()->getDateTimeOrNull(
//                RequestParams::PARAM_TYPE_QUERY,
//                self::PARAMETER_LICENSE_ISSUED_DATE
//            )
//        );
//        $employeeLicenseSearchParams->setLicenseExpiryDate(
//            $this->getRequestParams()->getDateTimeOrNull(
//                RequestParams::PARAM_TYPE_QUERY,
//                self::PARAMETER_LICENSE_EXPIRED_DATE
//            )
//        );
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $employeeLicenseSearchParams->setEmpNumber(
            $empNumber
        );

        $employeeLicenses = $this->getEmployeeLicenseService()->getEmployeeLicenseDao()->searchEmployeeLicense($employeeLicenseSearchParams);

        return new EndpointGetAllResult(
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
//            new ParamRule(self::FILTER_LICENSE_NO),
//            new ParamRule(self::PARAMETER_LICENSE_ISSUED_DATE,
//                new Rule(Rules::API_DATE)
//            ),
//            new ParamRule(self::PARAMETER_LICENSE_EXPIRED_DATE,
//                new Rule(Rules::API_DATE)
//            ),
            ...$this->getSortingAndPaginationParamsRules(EmployeeLicenseSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointCreateResult
    {
        $employeeLicense = $this->saveEmployeeLicense();
        return new EndpointCreateResult(
            EmployeeLicenseModel::class, $employeeLicense,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $employeeLicense->getEmployee()->getEmpNumber(),
                    //self::PARAMETER_LICENSE_ID => $employeeLicense->getLicenseId()->getId()
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
            new ParamRule(self::PARAMETER_LICENSE_ID, new Rule(Rules::REQUIRED)),
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
            new ParamRule(self::PARAMETER_LICENSE_ISSUED_DATE,
                new Rule(Rules::API_DATE)
            ),
            new ParamRule(self::PARAMETER_LICENSE_EXPIRED_DATE,
                new Rule(Rules::API_DATE)
            ),
        ];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointUpdateResult
    {
        $employeeLicense = $this->saveEmployeeLicense();

        return new EndpointUpdateResult(
            EmployeeLicenseModel::class, $employeeLicense,
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
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::REQUIRED), new Rule(Rules::POSITIVE)),
            $this->getEmpNumberRule(),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @inheritDoc
     * @throws DaoException
     * @throws Exception
     */
    public function delete(): EndpointDeleteResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getEmployeeLicenseService()->getEmployeeLicenseDao()->deleteEmployeeLicenses($empNumber, $ids);
        return new EndpointDeleteResult(
            ArrayModel::class, $ids,
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
     * @throws DaoException
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
        $employeeLicense = $this->getEmployeeLicenseService()->getEmployeeLicenseDao()->getEmployeeLicense($empNumber,
            $id);
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
