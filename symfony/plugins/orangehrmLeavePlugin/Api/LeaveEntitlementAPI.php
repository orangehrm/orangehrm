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

namespace OrangeHRM\Leave\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Leave\Api\Model\LeaveEntitlementModel;
use OrangeHRM\Leave\Api\ValidationRules\LeaveTypeIdRule;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;

class LeaveEntitlementAPI extends Endpoint implements CrudEndpoint
{
    use UserRoleManagerTrait;
    use LeaveEntitlementServiceTrait;

    public const PARAMETER_BULK_ASSIGN = 'bulkAssign';
    public const PARAMETER_LEAVE_TYPE_ID = 'leaveTypeId';
    public const PARAMETER_FROM_DATE = 'fromDate';
    public const PARAMETER_TO_DATE = 'toDate';
    public const PARAMETER_ENTITLEMENT = 'entitlement';
    public const PARAMETER_LOCATION_ID = 'locationId';
    public const PARAMETER_SUBUNIT_ID = 'subunitId';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $leaveEntitlement = $this->getLeaveEntitlementService()
            ->getLeaveEntitlementDao()
            ->getLeaveEntitlement($this->getIdUrlAttribute());
        $this->throwRecordNotFoundExceptionIfNotExist($leaveEntitlement, LeaveEntitlement::class);
        $this->checkLeaveEntitlementAccessible($leaveEntitlement);
        return new EndpointResourceResult(LeaveEntitlementModel::class, $leaveEntitlement);
    }

    /**
     * @return int
     */
    private function getIdUrlAttribute(): int
    {
        return $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
    }

    /**
     * @param LeaveEntitlement $leaveEntitlement
     */
    private function checkLeaveEntitlementAccessible(LeaveEntitlement $leaveEntitlement): void
    {
        $empNumber = $leaveEntitlement->getEmployee()->getEmpNumber();
        if (!($this->getUserRoleManager()->isEntityAccessible(Employee::class, $empNumber) ||
            $this->getUserRoleManagerHelper()->isSelfByEmpNumber($empNumber))) {
            throw $this->getForbiddenException();
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getIdParamRule()
        );
    }

    /**
     * @return ParamRule
     */
    private function getIdParamRule(): ParamRule
    {
        return new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE));
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
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
    public function create(): EndpointResult
    {
        $bulkAssign = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_BULK_ASSIGN
        );
        $leaveTypeId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_LEAVE_TYPE_ID
        );
        $fromDate = $this->getRequestParams()->getDateTime(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_FROM_DATE
        );
        $toDate = $this->getRequestParams()->getDateTime(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_TO_DATE);
        $entitlement = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ENTITLEMENT
        );

        if ($bulkAssign) {
            // TODO
            throw $this->getNotImplementedException();
        }

        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $leaveEntitlement = $this->getLeaveEntitlementService()->addEntitlementForEmployee(
            $empNumber,
            $leaveTypeId,
            $fromDate,
            $toDate,
            $entitlement
        );
        return new EndpointResourceResult(LeaveEntitlementModel::class, $leaveEntitlement);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection(
               $this->getValidationDecorator()->notRequiredParamRule(
                   new ParamRule(self::PARAMETER_BULK_ASSIGN, new Rule(Rules::BOOL_TYPE))
               ),
               new ParamRule(self::PARAMETER_LEAVE_TYPE_ID, new Rule(LeaveTypeIdRule::class)),
               $this->getEntitlementParamRule(),
            ...$this->getFromToDatesRules(),
        );
        $bulkAssign = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_BULK_ASSIGN
        );

        if ($bulkAssign) {
            // TODO:: define rule
            $paramRules->addParamValidation(new ParamRule(self::PARAMETER_LOCATION_ID));
            $paramRules->addParamValidation(new ParamRule(self::PARAMETER_SUBUNIT_ID));
            throw $this->getNotImplementedException();
        } else {
            $paramRules->addParamValidation(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                )
            );
        }
        return $paramRules;
    }

    /**
     * @return ParamRule[]
     */
    private function getFromToDatesRules(): array
    {
        return [
            new ParamRule(
                self::PARAMETER_FROM_DATE,
                new Rule(Rules::API_DATE),
                new Rule(Rules::LESS_THAN_OR_EQUAL, [
                    function () {
                        return $this->getRequestParams()->getDateTime(
                            RequestParams::PARAM_TYPE_BODY,
                            self::PARAMETER_TO_DATE
                        );
                    }
                ])
            ),
            new ParamRule(self::PARAMETER_TO_DATE, new Rule(Rules::API_DATE)),
        ];
    }

    /**
     * @return ParamRule
     */
    private function getEntitlementParamRule(): ParamRule
    {
        return new ParamRule(self::PARAMETER_ENTITLEMENT, new Rule(Rules::POSITIVE));
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $leaveEntitlement = $this->getLeaveEntitlementService()
            ->getLeaveEntitlementDao()
            ->getLeaveEntitlement($this->getIdUrlAttribute());
        $this->throwRecordNotFoundExceptionIfNotExist($leaveEntitlement, LeaveEntitlement::class);
        $this->checkLeaveEntitlementAccessible($leaveEntitlement);

        $fromDate = $this->getRequestParams()->getDateTime(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_FROM_DATE
        );
        $toDate = $this->getRequestParams()->getDateTime(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_TO_DATE);
        $entitlement = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ENTITLEMENT
        );
        $leaveEntitlement->setFromDate($fromDate);
        $leaveEntitlement->setToDate($toDate);
        $leaveEntitlement->setNoOfDays($entitlement);
        $this->getLeaveEntitlementService()->getLeaveEntitlementDao()->saveLeaveEntitlement($leaveEntitlement);
        return new EndpointResourceResult(LeaveEntitlementModel::class, $leaveEntitlement);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
               $this->getIdParamRule(),
               $this->getEntitlementParamRule(),
            ...$this->getFromToDatesRules()
        );
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
