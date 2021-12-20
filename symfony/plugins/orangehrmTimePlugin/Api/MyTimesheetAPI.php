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
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Time\Api\Model\TimesheetModel;
use OrangeHRM\Time\Api\ValidationRules\MyTimesheetDateRule;
use OrangeHRM\Time\Dto\MyTimesheetSearchFilterParams;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class MyTimesheetAPI extends Endpoint implements CollectionEndpoint
{
    use AuthUserTrait;
    use TimesheetServiceTrait;
    use DateTimeHelperTrait;
    use UserRoleManagerTrait;

    public const PARAMETER_DATE = 'date';

    public const FILTER_FROM_DATE = 'fromDate';
    public const FILTER_TO_DATE = 'toDate';
    public const FILTER_DATE = 'date';

    /**
     * @inheritDoc
     * @throws BadRequestException
     * @throws Exception
     */
    public function getAll(): EndpointCollectionResult
    {
        $this->validateDateInputs();

        $myTimesheetParamHolder = new MyTimesheetSearchFilterParams();
        $this->setSortingAndPaginationParams($myTimesheetParamHolder);

        $myTimesheetParamHolder->setAuthEmpNumber(
            $this->getAuthUser()->getEmpNumber()
        );

        if (!is_null($this->getDate())) {
            list($fromDate, $toDate) = $this->getTimesheetService()->extractStartDateAndEndDateFromDate(
                $this->getDate()
            );
            $myTimesheetParamHolder->setFromDate(new DateTime($fromDate));
            $myTimesheetParamHolder->setToDate(new DateTime($toDate));
        } elseif (is_null($this->getFromDateParam()) && is_null($this->getDate())) {
            list($fromDate, $toDate) = $this->getTimesheetService()->extractStartDateAndEndDateFromDate(
                $this->getDateTimeHelper()->getNow()
            );
            $myTimesheetParamHolder->setFromDate(new DateTime($fromDate));
            $myTimesheetParamHolder->setToDate(new DateTime($toDate));
        } else {
            $myTimesheetParamHolder->setFromDate($this->getFromDateParam());
            $myTimesheetParamHolder->setToDate($this->getToDateParam());
        }

        $myTimesheets = $this->getTimesheetService()->getTimesheetDao()->getTimesheetByStartAndEndDate(
            $myTimesheetParamHolder
        );
        $count = $this->getTimesheetService()->getTimesheetDao()->getTimesheetCount(
            $myTimesheetParamHolder
        );
        return new EndpointCollectionResult(
            TimesheetModel::class,
            $myTimesheets,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @throws BadRequestException
     */
    private function validateDateInputs(): void
    {
        $date = $this->getDate();
        $fromDate = $this->getFromDateParam();
        $toDate = $this->getToDateParam();

        //if fromDate available and toDate is not available
        if ($fromDate && is_null($toDate)) {
            throw $this->getBadRequestException("To Date is required");
        }

        //if toDate available and fromDate is not available
        if ($toDate && is_null($fromDate)) {
            throw $this->getBadRequestException("From Date is required");
        }

        //if single Date is available with from date and to date
        if ($fromDate && $date) {
            throw $this->getBadRequestException("You can't pass date param together with fromDate and toDate params");
        }

        //if to date earlier than from date
        if ($fromDate && $fromDate >= $toDate) {
            throw $this->getBadRequestException("From date should be earlier than to date");
        }
    }

    /**
     * @return DateTime|null
     */
    protected function getFromDateParam(): ?DateTime
    {
        return $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_FROM_DATE,
            null,
        );
    }

    /**
     * @return DateTime|null
     */
    protected function getToDateParam(): ?DateTime
    {
        return $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_TO_DATE,
            null,
        );
    }

    /**
     * @return DateTime|null
     */
    protected function getDate(): ?DateTime
    {
        return $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_DATE,
            null,
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_DATE,
                    new Rule(Rules::API_DATE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_FROM_DATE,
                    new Rule(Rules::API_DATE)
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_TO_DATE,
                    new Rule(Rules::API_DATE)
                )
            ),
            ...$this->getSortingAndPaginationParamsRules(MyTimesheetSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $timesheet = new Timesheet();
        $empNumber = $this->getAuthUser()->getEmpNumber();
        $startDate = $this->getRequestParams()->getDateTime(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DATE);
        $timesheet->getDecorator()->setEmployeeByEmployeeNumber($empNumber);
        $this->getTimesheetService()->createTimesheetByDate($timesheet, $startDate);
        return new EndpointResourceResult(TimesheetModel::class, $timesheet);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_DATE,
                    new Rule(Rules::API_DATE),
                    new Rule(MyTimesheetDateRule::class),
                ),
            ),
        );
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
