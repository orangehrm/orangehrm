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

namespace OrangeHRM\Buzz\Api;

use DateInterval;
use OrangeHRM\Buzz\Api\Model\EmployeeAnniversariesModel;
use OrangeHRM\Buzz\Traits\Service\BuzzAnniversaryServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Buzz\Dto\EmployeeAnniversarySearchFilterParams;

class EmployeeAnniversaryAPI extends Endpoint implements CollectionEndpoint
{
    use BuzzAnniversaryServiceTrait;
    use DateTimeHelperTrait;

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $employeeAnniversarySearchFilterParams = new EmployeeAnniversarySearchFilterParams();
        $this->setSortingAndPaginationParams($employeeAnniversarySearchFilterParams);

        $thisYear = $this->getDateTimeHelper()->getNow()->format('Y');
        $nextDate =  $this->getDateTimeHelper()->getNow();
        $nextDate->add(new DateInterval('P30D'));

        $employeeAnniversarySearchFilterParams->setThisYear($thisYear);
        $employeeAnniversarySearchFilterParams->setNextDate($nextDate);

        $upcomingAnniversaries = $this->getBuzzAnniversaryService()->getUpcomingAnniversariesDao()
            ->getUpcomingAnniversariesList($employeeAnniversarySearchFilterParams);

        $count = $this->getBuzzAnniversaryService()
            ->getUpcomingAnniversariesDao()
            ->getUpcomingAnniversariesCount($employeeAnniversarySearchFilterParams);

        return new EndpointCollectionResult(
            EmployeeAnniversariesModel::class,
            $upcomingAnniversaries,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules()
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
