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

use DateTime;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Leave\Dto\LeaveDuration;
use OrangeHRM\Leave\Dto\LeaveParameterObject;

class EmployeeLeaveRequestAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_LEAVE_TYPE_ID = 'leaveTypeId';
    public const PARAMETER_FROM_DATE = 'fromDate';
    public const PARAMETER_TO_DATE = 'toDate';
    public const PARAMETER_COMMENT = 'comment';
    public const PARAMETER_DURATION = 'duration';
    public const PARAMETER_END_DURATION = 'endDuration';
    public const PARAMETER_PARTIAL_OPTION = 'partialOption';

    public const PARAMETER_DURATION_TYPE = 'type';
    public const PARAMETER_DURATION_FROM_TIME = 'fromTime';
    public const PARAMETER_DURATION_TO_TIME = 'toTime';


    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
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
        throw $this->getNotImplementedException();
    }

    /**
     * @param int $empNumber
     * @return LeaveParameterObject
     */
    protected function getLeaveRequestParams(int $empNumber): LeaveParameterObject
    {
        $leaveTypeId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_LEAVE_TYPE_ID);
        list($fromDate, $toDate) = $this->getFromToDates();

        $leaveRequestParams = new LeaveParameterObject($empNumber, $leaveTypeId, $fromDate, $toDate);
        $leaveRequestParams->setComment(
            $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_COMMENT)
        );

        if ($leaveRequestParams->isMultiDayLeave()) {
            $this->setMultiDayPartialOptions($leaveRequestParams);
        } else {
            $this->setSingleDayDuration($leaveRequestParams);
        }
        return $leaveRequestParams;
    }

    /**
     * @return DateTime[]
     */
    private function getFromToDates(): array
    {
        $fromDate = $this->getRequestParams()->getDateTime(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_FROM_DATE);
        $toDate = $this->getRequestParams()->getDateTime(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_TO_DATE);
        return [$fromDate, $toDate];
    }

    /**
     * @param LeaveParameterObject $leaveRequestParams
     */
    private function setMultiDayPartialOptions(LeaveParameterObject $leaveRequestParams): void
    {
        $leaveRequestParams->setMultiDayPartialOption(
            $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PARTIAL_OPTION)
        );
        if ($leaveRequestParams->getMultiDayPartialOption() === LeaveParameterObject::PARTIAL_OPTION_END) {
            $leaveRequestParams->setEndMultiDayDuration($this->getGeneratedDuration(self::PARAMETER_DURATION, false));
        } elseif ($leaveRequestParams->getMultiDayPartialOption() !== LeaveParameterObject::PARTIAL_OPTION_NONE) {
            $leaveRequestParams->setStartMultiDayDuration($this->getGeneratedDuration(self::PARAMETER_DURATION, false));
        }
        if ($leaveRequestParams->getMultiDayPartialOption() === LeaveParameterObject::PARTIAL_OPTION_START_END) {
            $leaveRequestParams->setEndMultiDayDuration(
                $this->getGeneratedDuration(self::PARAMETER_END_DURATION, false)
            );
        }
    }

    /**
     * @param LeaveParameterObject $leaveRequestParams
     */
    private function setSingleDayDuration(LeaveParameterObject $leaveRequestParams): void
    {
        $leaveRequestParams->setSingleDayDuration($this->getGeneratedDuration(self::PARAMETER_DURATION));
    }

    /**
     * @param string $key EmployeeLeaveRequestAPI::PARAMETER_DURATION, EmployeeLeaveRequestAPI::PARAMETER_END_DURATION
     * @return LeaveDuration|null
     */
    private function getGeneratedDuration(string $key, bool $singleDay = true): ?LeaveDuration
    {
        $duration = $this->getRequestParams()->getArrayOrNull(RequestParams::PARAM_TYPE_BODY, $key);
        if ($singleDay && is_null($duration)) {
            $duration[self::PARAMETER_DURATION_TYPE] = LeaveDuration::FULL_DAY;
        } elseif (is_null($duration)) {
            return null;
        }
        return new LeaveDuration(
            $duration[self::PARAMETER_DURATION_TYPE],
            isset($duration[self::PARAMETER_DURATION_FROM_TIME]) ?
                new DateTime($duration[self::PARAMETER_DURATION_FROM_TIME]) : null,
            isset($duration[self::PARAMETER_DURATION_TO_TIME]) ?
                new DateTime($duration[self::PARAMETER_DURATION_TO_TIME]) : null
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @return ParamRuleCollection
     */
    protected function getCommonBodyParamRuleCollection(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_LEAVE_TYPE_ID, new Rule(Rules::POSITIVE)),
            new ParamRule(self::PARAMETER_FROM_DATE, new Rule(Rules::API_DATE)),
            new ParamRule(
                self::PARAMETER_TO_DATE,
                new Rule(Rules::API_DATE),
                new Rule(Rules::LESS_THAN_OR_EQUAL, [
                    function () {
                        list($fromDate) = $this->getFromToDates();
                        return $fromDate;
                    }
                ])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_COMMENT,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, 255])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DURATION,
                    new Rule(Rules::ARRAY_TYPE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_END_DURATION,
                    new Rule(Rules::ARRAY_TYPE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PARTIAL_OPTION,
                    new Rule(Rules::IN, [
                        [
                            LeaveParameterObject::PARTIAL_OPTION_NONE,
                            LeaveParameterObject::PARTIAL_OPTION_ALL,
                            LeaveParameterObject::PARTIAL_OPTION_START,
                            LeaveParameterObject::PARTIAL_OPTION_END,
                            LeaveParameterObject::PARTIAL_OPTION_START_END,
                        ]
                    ])
                )
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
