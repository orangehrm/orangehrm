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

namespace OrangeHRM\Time\Api;

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
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Time\Api\Traits\TimesheetPermissionTrait;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class TimesheetProjectActivityUniqueValidationAPI extends Endpoint implements ResourceEndpoint
{
    use TimesheetServiceTrait;
    use TimesheetPermissionTrait;

    public const PARAMETER_TIMESHEET_ID = 'timesheetId';
    public const PARAMETER_ACTIVITY_ID = 'activityId';
    public const PARAMETER_PROJECT_ID = 'projectId';
    public const PARAMETER_DUPLICATE = 'duplicate';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $timesheetId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_TIMESHEET_ID
        );
        $activityId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_ACTIVITY_ID
        );
        $projectId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_PROJECT_ID
        );

        $timesheet = $this->getTimesheetService()->getTimesheetDao()->getTimesheetById($timesheetId);
        $this->throwRecordNotFoundExceptionIfNotExist($timesheet, Timesheet::class);
        $this->checkTimesheetAccessible($timesheet);

        $isDuplicateItem = $this->getTimesheetService()
            ->getTimesheetDao()
            ->isDuplicateTimesheetItem($timesheetId, $activityId, $projectId);

        return new EndpointResourceResult(
            ArrayModel::class,
            [
                self::PARAMETER_DUPLICATE => $isDuplicateItem
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_TIMESHEET_ID,
                new Rule(Rules::POSITIVE),
            ),
            new ParamRule(
                self::PARAMETER_PROJECT_ID,
                new Rule(Rules::POSITIVE),
            ),
            new ParamRule(
                self::PARAMETER_ACTIVITY_ID,
                new Rule(Rules::POSITIVE),
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
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
