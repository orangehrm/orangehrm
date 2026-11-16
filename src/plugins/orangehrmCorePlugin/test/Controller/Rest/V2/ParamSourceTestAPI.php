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

namespace OrangeHRM\Tests\Core\Controller\Rest\V2;

use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;

/**
 * Test endpoint: `empNumber` is accepted only for employees 1 and 3, the way a row level rule accepts only
 * the employees a user may access. GET one reads the route value, POST reads the body value (and the
 * route value when the route has one), GET all reads the query value.
 */
class ParamSourceTestAPI extends Endpoint implements ResourceEndpoint, CollectionEndpoint
{
    public const ACCESSIBLE_EMP_NUMBERS = [1, 3];

    /**
     * @var array|null what the last handler call read, null when no handler ran
     */
    public static ?array $handled = null;

    public function getOne(): EndpointResult
    {
        return $this->handled([
            'empNumber' => $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, 'empNumber'),
        ]);
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection($this->getEmpNumberRule());
    }

    public function getAll(): EndpointResult
    {
        return $this->handled([
            'empNumber' => $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_QUERY, 'empNumber'),
        ]);
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection($this->getEmpNumberRule());
    }

    public function create(): EndpointResult
    {
        return $this->handled([
            'empNumber' => $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_BODY, 'empNumber'),
            'routeEmpNumber' => $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                'empNumber'
            ),
        ]);
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection($this->getEmpNumberRule());
    }

    public function update(): EndpointResult
    {
        return $this->getOne();
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection($this->getEmpNumberRule());
    }

    public function delete(): EndpointResult
    {
        return $this->getOne();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection($this->getEmpNumberRule());
    }

    private function getEmpNumberRule(): ParamRule
    {
        return new ParamRule(
            'empNumber',
            new Rule(Rules::CALLBACK, [
                static fn ($empNumber) => is_numeric($empNumber)
                    && in_array((int)$empNumber, self::ACCESSIBLE_EMP_NUMBERS, true),
            ])
        );
    }

    private function handled(array $read): EndpointResult
    {
        self::$handled = $read;
        return new EndpointResourceResult(ArrayModel::class, $read);
    }
}
