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

namespace OrangeHRM\Claim\Api;

use OrangeHRM\Claim\Api\Model\ClaimExpenseModel;
use OrangeHRM\Claim\Traits\Service\ClaimServiceTrait;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\ClaimExpense;

class ClaimExpenseAPI extends Endpoint implements CrudEndpoint
{
    use EntityManagerHelperTrait;
    use ClaimServiceTrait;
    use DateTimeHelperTrait;
    use AuthUserTrait;

    public const PARAMETER_EXPENSE_TYPE_ID = 'expenseTypeId';
    public const PARAMETER_AMOUNT = 'amount';
    public const PARAMETER_NOTE = 'note';
    public const PARAMETER_REQUEST_ID = 'requestId';

    public function getAll(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function create(): EndpointResult
    {
        $claimExpense = new ClaimExpense();
        $this->setClaimExpense($claimExpense);
        return new EndpointResourceResult(ClaimExpenseModel::class, $claimExpense);
    }

    public function setClaimExpense(ClaimExpense $claimExpense):void{
        $claimExpense->getDecorator()->setExpenseTypeByExpenseTypeId($this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY,self::PARAMETER_EXPENSE_TYPE_ID));
        $claimExpense->setDate($this->getDateTimeHelper()->getNow());
        $claimExpense->setAmount($this->getRequestParams()->getFloat(RequestParams::PARAM_TYPE_BODY,self::PARAMETER_AMOUNT));
        $claimExpense->setNote($this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY,self::PARAMETER_NOTE));
        $claimExpense->getDecorator()->setClaimRequestByRequestId($this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY,self::PARAMETER_REQUEST_ID));
        $this->getClaimService()->getClaimDao()->saveClaimExpense($claimExpense);
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_EXPENSE_TYPE_ID,
                    new Rule(Rules::INT_TYPE)
                ),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_AMOUNT,
                    new Rule(Rules::FLOAT_TYPE)
                ),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_REQUEST_ID,
                    new Rule(Rules::INT_TYPE)
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NOTE,
                    new Rule(Rules::STRING_TYPE)
                ),
            ),
        );
    }

    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function getOne(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}