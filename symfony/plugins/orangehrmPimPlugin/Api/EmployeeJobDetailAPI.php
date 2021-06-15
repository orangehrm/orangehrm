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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Api\Model\EmployeeJobDetailModel;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class EmployeeJobDetailAPI extends Endpoint implements ResourceEndpoint
{
    use EmployeeServiceTrait;
    use DateTimeHelperTrait;

    public const PARAMETER_JOINED_DATE = 'joinedDate';
    public const PARAMETER_JOB_TITLE_ID = 'jobTitleId';
    public const PARAMETER_EMP_STATUS_ID = 'empStatusId';
    public const PARAMETER_JOB_CATEGORY_ID = 'jobCategoryId';
    public const PARAMETER_SUBUNIT_ID = 'subunitId';
    public const PARAMETER_LOCATION_ID = 'locationId';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
        $this->throwRecordNotFoundExceptionIfNotExist($employee, Employee::class);
        return new EndpointResourceResult(EmployeeJobDetailModel::class, $employee);
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
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
        $this->throwRecordNotFoundExceptionIfNotExist($employee, Employee::class);

        $joinedDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_JOINED_DATE
        );
        $jobTitleId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_JOB_TITLE_ID
        );
        $empStatusId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_EMP_STATUS_ID
        );
        $jobCategoryId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_JOB_CATEGORY_ID
        );
        $subunitId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SUBUNIT_ID
        );
        $locationId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_LOCATION_ID
        );

        $currentJoinedDate = $employee->getJoinedDate();
        $employee->setJoinedDate($joinedDate);
        $employee->getDecorator()->setJobTitleById($jobTitleId);
        $employee->getDecorator()->setEmpStatusById($empStatusId);
        $employee->getDecorator()->setJobCategoryById($jobCategoryId);
        $employee->getDecorator()->setSubunitById($subunitId);
        $employee->getDecorator()->setLocationById($locationId);

        $this->getEmployeeService()->updateEmployeeJobDetails($employee);

        if (!$this->getDateTimeHelper()->isDatesEqual($currentJoinedDate, $employee->getJoinedDate(), true)) {
            $this->getEmployeeService()->dispatchJoinedDateChangedEvent($employee, $currentJoinedDate);
        }

        return new EndpointResourceResult(EmployeeJobDetailModel::class, $employee);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_JOINED_DATE,
                    new Rule(Rules::API_DATE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_JOB_TITLE_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_EMP_STATUS_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_JOB_CATEGORY_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SUBUNIT_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_LOCATION_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
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
