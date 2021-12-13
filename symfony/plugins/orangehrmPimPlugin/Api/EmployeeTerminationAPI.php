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
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Pim\Api\Model\EmployeeTerminationModel;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class EmployeeTerminationAPI extends Endpoint implements CrudEndpoint
{
    use EmployeeServiceTrait;

    public const PARAMETER_TERMINATION_REASON_ID = 'terminationReasonId';
    public const PARAMETER_DATE = 'date';
    public const PARAMETER_NOTE = 'note';

    public const PARAM_RULE_NOTE_MAX_LENGTH = 255;

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        list($empNumber, $id) = $this->getUrlAttributes();
        $employeeTerminationRecord = $this->getEmployeeService()
            ->getEmployeeTerminationService()
            ->getEmployeeTerminationDao()->getEmployeeTermination($id);
        $this->throwRecordNotFoundExceptionIfNotExist($employeeTerminationRecord, EmployeeTerminationRecord::class);

        return new EndpointResourceResult(
            EmployeeTerminationModel::class,
            $employeeTerminationRecord,
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
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        list($empNumber) = $this->getUrlAttributes();
        $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
        $this->throwRecordNotFoundExceptionIfNotExist($employee, Employee::class);

        $employeeTerminationRecord = new EmployeeTerminationRecord();
        $employeeTerminationRecord->setEmployee($employee);
        $this->setEmployeeTerminationRecord($employeeTerminationRecord);

        $employee->setEmployeeTerminationRecord($employeeTerminationRecord);
        $this->getEmployeeService()->saveEmployee($employee);

        return new EndpointResourceResult(
            EmployeeTerminationModel::class,
            $employeeTerminationRecord,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @param EmployeeTerminationRecord $employeeTerminationRecord
     */
    private function setEmployeeTerminationRecord(EmployeeTerminationRecord $employeeTerminationRecord): void
    {
        $employeeTerminationRecord->getDecorator()->setTerminationReasonById(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_TERMINATION_REASON_ID
            )
        );
        $employeeTerminationRecord->setDate(
            $this->getRequestParams()->getDateTime(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DATE
            )
        );
        $employeeTerminationRecord->setNote(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NOTE
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return $this->getCommonBodyValidationRules();
    }

    /**
     * @return ParamRuleCollection
     */
    private function getCommonBodyValidationRules(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                self::PARAMETER_TERMINATION_REASON_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NOTE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NOTE_MAX_LENGTH])
                )
            ),
            new ParamRule(
                self::PARAMETER_DATE,
                new Rule(Rules::API_DATE)
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        list($empNumber, $id) = $this->getUrlAttributes();
        $employeeTerminationRecord = $this->getEmployeeService()
            ->getEmployeeTerminationService()
            ->getEmployeeTerminationDao()
            ->getEmployeeTermination($id);
        $this->throwRecordNotFoundExceptionIfNotExist($employeeTerminationRecord, EmployeeTerminationRecord::class);

        $this->setEmployeeTerminationRecord($employeeTerminationRecord);
        $this->getEmployeeService()
            ->getEmployeeTerminationService()
            ->getEmployeeTerminationDao()
            ->saveEmployeeTermination($employeeTerminationRecord);
        return new EndpointResourceResult(
            EmployeeTerminationModel::class,
            $employeeTerminationRecord,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $paramRules = $this->getCommonBodyValidationRules();
        $paramRules->addParamValidation(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            )
        );
        return $paramRules;
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        list($empNumber) = $this->getUrlAttributes();
        $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
        $this->throwRecordNotFoundExceptionIfNotExist($employee, Employee::class);

        $employeeTerminationRecord = $employee->getEmployeeTerminationRecord();
        $this->throwRecordNotFoundExceptionIfNotExist($employeeTerminationRecord, EmployeeTerminationRecord::class);

        $employee->setEmployeeTerminationRecord(null);
        $this->getEmployeeService()->saveEmployee($employee);

        return new EndpointResourceResult(
            EmployeeTerminationModel::class,
            $employeeTerminationRecord,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            )
        );
    }
}
