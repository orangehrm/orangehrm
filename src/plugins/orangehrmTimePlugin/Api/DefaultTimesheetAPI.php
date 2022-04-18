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

use DateTime;
use Exception;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Time\Api\Model\DefaultTimesheetModel;
use OrangeHRM\Time\Dto\DefaultTimesheetSearchFilterParams;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class DefaultTimesheetAPI extends Endpoint implements ResourceEndpoint
{
    use TimesheetServiceTrait;
    use AuthUserTrait;
    use DateTimeHelperTrait;

    public const FILTER_DATE = 'date';

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getOne(): EndpointResult
    {
        $defaultTimesheetSearchFilterParams = new DefaultTimesheetSearchFilterParams();
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_QUERY,
            CommonParams::PARAMETER_EMP_NUMBER,
            $this->getAuthUser()->getEmpNumber()
        );
        $defaultTimesheetSearchFilterParams->setEmpNumber($empNumber);
        //if date param is available extract from date and to date
        if (!is_null($this->getDate())) {
            list($fromDate, $toDate) = $this->getTimesheetService()
                ->extractStartDateAndEndDateFromDate($this->getDate());
            $defaultTimesheetSearchFilterParams->setFromDate(new DateTime($fromDate));
            $defaultTimesheetSearchFilterParams->setToDate(new DateTime($toDate));
        }
        //if date param is given, returns timesheet from extracted boundaries
        //if no date parameter found, then returns the latest record for specific emp number DESC by date
        //if no timesheet found for given params, returns null
        $timesheet = $this->getTimesheetService()->getTimesheetDao()->getDefaultTimesheet(
            $defaultTimesheetSearchFilterParams
        );
        //check whether timesheet is available
        if (is_null($timesheet)) {
            //if timesheet not available, create new timesheet object and assign values
            $timesheet = new Timesheet();
            $timesheet->setId(0);

            if (!is_null($this->getDate())) {
                //if date param is given, extract the relevant timesheet period that belongs it
                list($fromDate, $toDate) = $this->getTimesheetService()
                    ->extractStartDateAndEndDateFromDate($this->getDate());
            } else {
                //if date param is not given, extract the timesheet period from current date
                list($fromDate, $toDate) = $this->getTimesheetService()
                    ->extractStartDateAndEndDateFromDate($this->getDateTimeHelper()->getNow());
            }
            $timesheet->setStartDate(new DateTime($fromDate));
            $timesheet->setEndDate(new DateTime($toDate));
        }
        return new EndpointResourceResult(DefaultTimesheetModel::class, $timesheet);
    }

    /**
     * @return DateTime|null
     */
    protected function getDate(): ?DateTime
    {
        return $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_DATE
        );
    }

    /**
     * @return ParamRule
     */
    private function getEmpNumberParamRule(): ParamRule
    {
        return $this->getValidationDecorator()->notRequiredParamRule(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_DATE,
                    new Rule(Rules::API_DATE)
                )
            ),
            $this->getEmpNumberParamRule(),
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
