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
use OrangeHRM\Leave\Dto\LeaveListSearchFilterParams;
use OrangeHRM\Leave\Service\Model\LeaveListModal;
use OrangeHRM\Leave\Traits\Service\LeaveListServiceTrait;

class LeaveListAPI extends Endpoint implements CollectionEndpoint
{
    use LeaveListServiceTrait;

    public const FROM_DATE = 'fromDate';

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $leaveListSearchFilterParams = new LeaveListSearchFilterParams();

        $this->setSortingAndPaginationParams($leaveListSearchFilterParams);
        $startDate = $this->getRequestParams()->getDateTime(
            RequestParams::PARAM_TYPE_QUERY,
            self::FROM_DATE,
        );

        $leaveListSearchFilterParams->setDate($startDate);

        $empLeaveList = $this->getLeaveListService()->getLeaveListDao()
            ->getEmployeeOnLeaveList($leaveListSearchFilterParams);
        $employeeCount = $this->getLeaveListService()->getLeaveListDao()
            ->getEmployeeOnLeaveCount($leaveListSearchFilterParams);

        return new EndpointCollectionResult(
            LeaveListModal::class,
            $empLeaveList,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $employeeCount])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::FROM_DATE,
                new Rule(Rules::DATE)
            ),
            ... $this->getSortingAndPaginationParamsRules(LeaveListSearchFilterParams::ALLOWED_SORT_FIELDS),
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
