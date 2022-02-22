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

namespace OrangeHRM\Maintenance\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Maintenance\Api\Model\PurgeEmployeeModel;
use OrangeHRM\Maintenance\Service\PurgeEmployeeService;
use OrangeHRM\ORM\Exception\TransactionException;

class PurgeEmployeeAPI extends Endpoint implements CollectionEndpoint
{
    private ?PurgeEmployeeService $purgeEmployeeService = null;

    public function getPurgeEmployeeService(): PurgeEmployeeService
    {
        if (is_null($this->purgeEmployeeService)) {
            $this->purgeEmployeeService = new PurgeEmployeeService();
        }
        return $this->purgeEmployeeService;
    }

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
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     * @throws BadRequestException|TransactionException
     */
    public function delete(): EndpointResult
    {
        $empNumbers = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $purgeableEmployees = $this->getPurgeEmployeeService()->getPurgeEmployeeDao()->getEmployeePurgingList();
        foreach ($purgeableEmployees as $purgeableEmployee) {
            if ($purgeableEmployee->getEmpNumber() === $empNumbers) {
                $this->getPurgeEmployeeService()->purgeEmployeeData($empNumbers);
                return new EndpointResourceResult(PurgeEmployeeModel::class, $purgeableEmployee);
            }
        }
        throw $this->getBadRequestException("Employee is not terminated / does not exist");
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_EMP_NUMBER, new Rule(Rules::POSITIVE))
        );
    }
}
