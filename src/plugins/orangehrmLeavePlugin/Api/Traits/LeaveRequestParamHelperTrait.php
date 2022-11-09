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

namespace OrangeHRM\Leave\Api\Traits;

use DateTime;
use LogicException;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Leave\Api\LeaveCommonParams;
use OrangeHRM\Leave\Api\ValidationRules\LeaveTypeIdRule;
use OrangeHRM\Leave\Dto\LeaveDuration;
use OrangeHRM\Leave\Dto\LeaveParameterObject;

trait LeaveRequestParamHelperTrait
{
    /**
     * @return int
     */
    protected function getLeaveTypeIdParam(): int
    {
        return $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID
        );
    }

    /**
     * @return DateTime|null
     */
    protected function getFromDateParam(): ?DateTime
    {
        return $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            LeaveCommonParams::PARAMETER_FROM_DATE
        );
    }

    /**
     * @return DateTime|null
     */
    protected function getToDateParam(): ?DateTime
    {
        return $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            LeaveCommonParams::PARAMETER_TO_DATE
        );
    }

    /**
     * @return string|null
     */
    protected function getCommentParam(): ?string
    {
        return $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            LeaveCommonParams::PARAMETER_COMMENT
        );
    }

    /**
     * @param string $key
     * @param array|null $default
     * @return array|null
     */
    protected function getDurationParam(string $key, ?array $default = null): ?array
    {
        return $this->getRequestParams()->getArrayOrNull(RequestParams::PARAM_TYPE_BODY, $key, $default);
    }

    /**
     * @return string|null
     */
    protected function getPartialOptionParam(): ?string
    {
        return $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            LeaveCommonParams::PARAMETER_PARTIAL_OPTION
        );
    }

    /**
     * @param int $empNumber
     * @param string $paramObjectClassName
     * @return LeaveParameterObject
     */
    protected function getLeaveRequestParams(
        int $empNumber,
        string $paramObjectClassName = LeaveParameterObject::class
    ): LeaveParameterObject {
        if (!$this instanceof Endpoint) {
            throw $this->getEndpointLogicException();
        }
        $leaveTypeId = $this->getLeaveTypeIdParam();
        $fromDate = $this->getFromDateParam();
        $toDate = $this->getToDateParam();

        $leaveRequestParams = new $paramObjectClassName($empNumber, $leaveTypeId, $fromDate, $toDate);
        $leaveRequestParams->setComment($this->getCommentParam());

        if ($leaveRequestParams->isMultiDayLeave()) {
            $this->setMultiDayPartialOptions($leaveRequestParams);
        } else {
            $this->setSingleDayDuration($leaveRequestParams);
        }
        return $leaveRequestParams;
    }

    /**
     * @param LeaveParameterObject $leaveRequestParams
     */
    protected function setMultiDayPartialOptions(LeaveParameterObject $leaveRequestParams): void
    {
        if (!$this instanceof Endpoint) {
            throw $this->getEndpointLogicException();
        }
        $leaveRequestParams->setMultiDayPartialOption(
            $this->getPartialOptionParam() ?? LeaveParameterObject::PARTIAL_OPTION_NONE
        );
        if ($leaveRequestParams->getMultiDayPartialOption() === LeaveParameterObject::PARTIAL_OPTION_END) {
            $leaveRequestParams->setEndMultiDayDuration(
                $this->getGeneratedDuration(LeaveCommonParams::PARAMETER_DURATION, false)
            );
        } elseif ($leaveRequestParams->getMultiDayPartialOption() !== LeaveParameterObject::PARTIAL_OPTION_NONE) {
            $leaveRequestParams->setStartMultiDayDuration(
                $this->getGeneratedDuration(LeaveCommonParams::PARAMETER_DURATION, false)
            );
        }
        if ($leaveRequestParams->getMultiDayPartialOption() === LeaveParameterObject::PARTIAL_OPTION_START_END) {
            $leaveRequestParams->setEndMultiDayDuration(
                $this->getGeneratedDuration(LeaveCommonParams::PARAMETER_END_DURATION, false)
            );
        }
    }

    /**
     * @param LeaveParameterObject $leaveRequestParams
     */
    protected function setSingleDayDuration(LeaveParameterObject $leaveRequestParams): void
    {
        $leaveRequestParams->setSingleDayDuration($this->getGeneratedDuration(LeaveCommonParams::PARAMETER_DURATION));
    }

    /**
     * @param string $key LeaveCommonParams::PARAMETER_DURATION, LeaveCommonParams::PARAMETER_END_DURATION
     * @return LeaveDuration|null
     */
    protected function getGeneratedDuration(string $key, bool $singleDay = true): ?LeaveDuration
    {
        if (!$this instanceof Endpoint) {
            throw $this->getEndpointLogicException();
        }
        $duration = $this->getDurationParam($key);
        if ($singleDay && is_null($duration)) {
            $duration[LeaveCommonParams::PARAMETER_DURATION_TYPE] = LeaveDuration::FULL_DAY;
        } elseif (is_null($duration)) {
            return null;
        }
        return new LeaveDuration(
            $duration[LeaveCommonParams::PARAMETER_DURATION_TYPE],
            isset($duration[LeaveCommonParams::PARAMETER_DURATION_FROM_TIME]) ?
                new DateTime($duration[LeaveCommonParams::PARAMETER_DURATION_FROM_TIME]) : null,
            isset($duration[LeaveCommonParams::PARAMETER_DURATION_TO_TIME]) ?
                new DateTime($duration[LeaveCommonParams::PARAMETER_DURATION_TO_TIME]) : null
        );
    }

    /**
     * @return ParamRuleCollection
     */
    protected function getCommonParamRuleCollection(): ParamRuleCollection
    {
        if (!$this instanceof Endpoint) {
            throw $this->getEndpointLogicException();
        }
        $durationRule = new ParamRule(
            LeaveCommonParams::PARAMETER_DURATION,
            new Rule(Rules::CALLBACK, [
                function ($duration) {
                    $fromDate = $this->getFromDateParam();
                    $toDate = $this->getToDateParam();

                    // not required parameter for single day leave request
                    $singleDayNotRequired = $fromDate == $toDate && is_null($duration);

                    // not required duration for multi day leave request with none (null) partial option
                    $multiDayNonePartialDayNotRequired = $fromDate < $toDate &&
                        (is_null($this->getPartialOptionParam()) ||
                            $this->getPartialOptionParam() == LeaveParameterObject::PARTIAL_OPTION_NONE);

                    if ($singleDayNotRequired || $multiDayNonePartialDayNotRequired) {
                        return true;
                    }

                    // for all other scenarios' duration -> type required
                    if (!is_array($duration) || !isset($duration[LeaveCommonParams::PARAMETER_DURATION_TYPE])) {
                        return false;
                    }

                    // validating duration type
                    $durationType = $duration[LeaveCommonParams::PARAMETER_DURATION_TYPE];
                    if (!in_array($durationType, array_values(LeaveDuration::DURATION_MAP))) {
                        return false;
                    }

                    // `full_day` duration type not allowed with partial options (multi day leaves)
                    if (!is_null($this->getPartialOptionParam()) && $durationType === LeaveDuration::FULL_DAY) {
                        return false;
                    }

                    // if duration type `specify_time`, `fromTime` & `toTime` should define
                    if ($durationType === LeaveDuration::SPECIFY_TIME) {
                        if ((!isset($duration[LeaveCommonParams::PARAMETER_DURATION_FROM_TIME]) ||
                            !isset($duration[LeaveCommonParams::PARAMETER_DURATION_TO_TIME]))) {
                            return false;
                        }
                        if (new DateTime($duration[LeaveCommonParams::PARAMETER_DURATION_FROM_TIME]) >=
                            new DateTime($duration[LeaveCommonParams::PARAMETER_DURATION_TO_TIME])) {
                            return false;
                        }
                    }

                    return true;
                }
            ]),
        );
        $endDurationRule = new ParamRule(
            LeaveCommonParams::PARAMETER_END_DURATION,
            new Rule(Rules::CALLBACK, [
                function ($endDuration) {
                    $fromDate = $this->getFromDateParam();
                    $toDate = $this->getToDateParam();

                    if (!($fromDate < $toDate &&
                        $this->getPartialOptionParam() == LeaveParameterObject::PARTIAL_OPTION_START_END)) {
                        return is_null($endDuration);
                    }

                    // endDuration -> type required
                    if (!is_array($endDuration) ||
                        !isset($endDuration[LeaveCommonParams::PARAMETER_DURATION_TYPE])) {
                        return false;
                    }

                    // validating duration type
                    $durationType = $endDuration[LeaveCommonParams::PARAMETER_DURATION_TYPE];
                    if (!in_array(
                        $durationType,
                        [
                            LeaveDuration::HALF_DAY_MORNING,
                            LeaveDuration::HALF_DAY_AFTERNOON,
                            LeaveDuration::SPECIFY_TIME
                        ]
                    )) {
                        return false;
                    }

                    // if duration type `specify_time`, `fromTime` & `toTime` should define
                    if ($durationType === LeaveDuration::SPECIFY_TIME) {
                        if ((!isset($endDuration[LeaveCommonParams::PARAMETER_DURATION_FROM_TIME]) ||
                            !isset($endDuration[LeaveCommonParams::PARAMETER_DURATION_TO_TIME]))) {
                            return false;
                        }
                        if (new DateTime($endDuration[LeaveCommonParams::PARAMETER_DURATION_FROM_TIME]) >=
                            new DateTime($endDuration[LeaveCommonParams::PARAMETER_DURATION_TO_TIME])) {
                            return false;
                        }
                    }
                    return true;
                }
            ]),
        );
        $partialOptionParamRule = new ParamRule(
            LeaveCommonParams::PARAMETER_PARTIAL_OPTION,
            new Rule(Rules::CALLBACK, [
                function ($partialOption) {
                    $fromDate = $this->getFromDateParam();
                    $toDate = $this->getToDateParam();

                    // partial day options should not define for single day
                    if ($fromDate == $toDate && !is_null($partialOption)) {
                        return false;
                    }

                    // validating partial day options
                    if (!in_array(
                        $partialOption,
                        [
                            null,
                            LeaveParameterObject::PARTIAL_OPTION_NONE,
                            LeaveParameterObject::PARTIAL_OPTION_ALL,
                            LeaveParameterObject::PARTIAL_OPTION_START,
                            LeaveParameterObject::PARTIAL_OPTION_END,
                            LeaveParameterObject::PARTIAL_OPTION_START_END,
                        ]
                    )) {
                        return false;
                    }

                    return true;
                }
            ]),
        );
        return new ParamRuleCollection(
            new ParamRule(LeaveCommonParams::PARAMETER_LEAVE_TYPE_ID, new Rule(LeaveTypeIdRule::class)),
            new ParamRule(
                LeaveCommonParams::PARAMETER_FROM_DATE,
                new Rule(Rules::API_DATE),
                new Rule(Rules::LESS_THAN_OR_EQUAL, [fn () => $this->getToDateParam()])
            ),
            new ParamRule(LeaveCommonParams::PARAMETER_TO_DATE, new Rule(Rules::API_DATE)),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    LeaveCommonParams::PARAMETER_COMMENT,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, 255])
                )
            ),
            $durationRule,
            $endDurationRule,
            $partialOptionParamRule,
        );
    }

    /**
     * @return LogicException
     */
    private function getEndpointLogicException(): LogicException
    {
        return new LogicException(LeaveRequestParamHelperTrait::class . ' should use in instanceof' . Endpoint::class);
    }
}
