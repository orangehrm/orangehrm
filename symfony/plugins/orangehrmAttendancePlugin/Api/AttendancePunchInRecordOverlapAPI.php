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

namespace OrangeHRM\Attendance\Api;

use DateTime;
use DateTimeZone;
use OrangeHRM\Attendance\Exception\AttendanceServiceException;
use OrangeHRM\Attendance\Traits\Service\AttendanceServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
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
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;

class AttendancePunchInRecordOverlapAPI extends Endpoint implements ResourceEndpoint
{
    use AuthUserTrait;
    use AttendanceServiceTrait;
    use DateTimeHelperTrait;

    public const PARAMETER_DATE = 'date';
    public const PARAMETER_TIME = 'time';
    public const PARAMETER_TIME_ZONE_OFFSET = 'timezoneOffset';
    public const PARAMETER_IS_PUNCH_IN_OVERLAP = 'valid';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        try {
            $employeeNumber = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_QUERY,
                CommonParams::PARAMETER_EMP_NUMBER,
                $this->getAuthUser()->getEmpNumber()
            );

            $punchInUtcTime = $this->getUTCTimeByOffsetAndDateTime();
            $isPunchInOverlap = !$this->getAttendanceService()
                ->getAttendanceDao()
                ->checkForPunchInOverLappingRecords($punchInUtcTime, $employeeNumber);

            return new EndpointResourceResult(
                ArrayModel::class,
                [
                    self::PARAMETER_IS_PUNCH_IN_OVERLAP => $isPunchInOverlap,
                ]
            );
        } catch (AttendanceServiceException $attendanceServiceException) {
            throw $this->getBadRequestException($attendanceServiceException->getMessage());
        }
    }

    /**
     * @return DateTime
     */
    protected function getUTCTimeByOffsetAndDateTime(): DateTime
    {
        $date = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_DATE,
        );
        $time = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_TIME,
        );
        $timeZoneOffset = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_TIME_ZONE_OFFSET,
        );

        $dateTime = $date . ' ' . $time;
        $dateTime = new DateTime(
            $dateTime,
            $this->getDateTimeHelper()->getTimezoneByTimezoneOffset($timeZoneOffset)
        );
        return $dateTime->setTimezone(new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC));
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_DATE,
                    new Rule(Rules::API_DATE)
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_TIME,
                    new Rule(Rules::TIME, ['H:i'])
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_TIME_ZONE_OFFSET,
                    new Rule(Rules::STRING_TYPE)
                )
            )
        );
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
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
