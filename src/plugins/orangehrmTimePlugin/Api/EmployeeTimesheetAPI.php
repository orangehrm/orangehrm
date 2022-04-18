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
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Entity\TimesheetActionLog;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Time\Api\Model\TimesheetModel;
use OrangeHRM\Time\Api\Traits\TimesheetPermissionTrait;
use OrangeHRM\Time\Api\ValidationRules\MyTimesheetActionRule;
use OrangeHRM\Time\Api\ValidationRules\TimesheetDateRule;
use OrangeHRM\Time\Dto\TimesheetSearchFilterParams;
use OrangeHRM\Time\Service\TimesheetService;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class EmployeeTimesheetAPI extends Endpoint implements CrudEndpoint
{
    use AuthUserTrait;
    use TimesheetServiceTrait;
    use DateTimeHelperTrait;
    use UserRoleManagerTrait;
    use EntityManagerHelperTrait;
    use TimesheetPermissionTrait;

    public const PARAMETER_DATE = 'date';
    public const PARAMETER_ACTION = 'action';
    public const PARAMETER_COMMENT = 'comment';

    public const FILTER_FROM_DATE = 'fromDate';
    public const FILTER_TO_DATE = 'toDate';
    public const FILTER_DATE = 'date';

    public const PARAM_RULE_COMMENT_MAX_LENGTH = 2000;

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $this->validateDateInputs();

        $timesheetParamHolder = new TimesheetSearchFilterParams();
        $this->setSortingAndPaginationParams($timesheetParamHolder);

        $timesheetParamHolder->setEmpNumber($this->getEmpNumber());

        return $this->getTimeSheets($timesheetParamHolder);
    }

    /**
     * @throws BadRequestException
     */
    protected function validateDateInputs(): void
    {
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

        //if to date earlier than from date
        if ($fromDate && $fromDate >= $toDate) {
            throw $this->getBadRequestException("From date should be earlier than to date");
        }
    }

    /**
     * @return int
     */
    protected function getEmpNumber(): int
    {
        return $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
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
     * @throws NormalizeException
     * @throws Exception
     */
    protected function getTimeSheets(TimesheetSearchFilterParams $timesheetParamHolder): EndpointCollectionResult
    {
        $timesheetParamHolder->setFromDate($this->getFromDateParam());
        $timesheetParamHolder->setToDate($this->getToDateParam());

        $timesheets = $this->getTimesheetService()->getTimesheetDao()->getTimesheetByStartAndEndDate(
            $timesheetParamHolder
        );
        $count = $this->getTimesheetService()->getTimesheetDao()->getTimesheetCount(
            $timesheetParamHolder
        );
        return new EndpointCollectionResult(
            TimesheetModel::class,
            $timesheets,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
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
            $this->getEmpNumberParamRule(),
            ...$this->getSortingAndPaginationParamsRules(TimesheetSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @return ParamRule
     */
    private function getEmpNumberParamRule(): ParamRule
    {
        return new ParamRule(CommonParams::PARAMETER_EMP_NUMBER, new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS));
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $timesheet = new Timesheet();
        $startDate = $this->getRequestParams()->getDateTime(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DATE);
        $timesheet->getDecorator()->setEmployeeByEmployeeNumber($this->getEmpNumber());
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
                    new Rule(
                        TimesheetDateRule::class,
                        [$this->getRequest()->getAttributes()->get(CommonParams::PARAMETER_EMP_NUMBER)]
                    ),
                ),
            ),
            $this->getEmpNumberParamRule(),
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
    public function update(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $timesheetId = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                CommonParams::PARAMETER_ID
            );
            $comment = $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_COMMENT
            );
            $timesheet = $this->getTimesheetService()
                ->getTimesheetDao()
                ->getTimesheetById($timesheetId);
            $this->throwRecordNotFoundExceptionIfNotExist($timesheet, Timesheet::class);
            $this->checkTimesheetAccessible($timesheet);

            $action = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ACTION);
            // matching the string value to corresponding key in the associative array
            $actionKey = array_flip(TimesheetService::TIMESHEET_ACTION_MAP)[$action];

            $allowedActions = $this->getTimesheetService()
                ->getAllowedWorkflowsForTimesheet($this->getAuthUser()->getEmpNumber(), $timesheet);

            if (!isset($allowedActions[$actionKey])) {
                throw $this->getBadRequestException();
            }
            $state = $allowedActions[$actionKey]->getResultingState();
            $timesheet->setState($state);
            $this->getTimesheetService()->getTimesheetDao()->saveTimesheet($timesheet);
            $timesheetActionState = $actionKey == WorkflowStateMachine::TIMESHEET_ACTION_RESET ? Timesheet::RESET_ACTION : $state;
            $this->setTimesheetActionLog($timesheetActionState, $comment, $timesheet);
            $this->commitTransaction();

            return new EndpointResourceResult(TimesheetModel::class, $timesheet);
        } catch (RecordNotFoundException|ForbiddenException|BadRequestException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_ACTION,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(MyTimesheetActionRule::class),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_COMMENT,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_COMMENT_MAX_LENGTH]),
                )
            ),
            $this->getEmpNumberParamRule(),
        );
    }

    /**
     * @param  string  $state
     * @param  string|null  $comment
     * @param  Timesheet  $timesheet
     * @return void
     */
    protected function setTimesheetActionLog(string $state, ?string $comment, Timesheet $timesheet): void
    {
        $timesheetActionLog = new TimesheetActionLog();
        $timesheetActionLog->setAction($state);
        $timesheetActionLog->setComment($comment);
        $timesheetActionLog->setTimesheet($timesheet);
        $timesheetActionLog->setDate($this->getDateTimeHelper()->getNow());
        $timesheetActionLog->getDecorator()->setUserId($this->getAuthUser()->getUserId());
        $this->getTimesheetService()->getTimesheetDao()->saveTimesheetActionLog($timesheetActionLog);
    }
}
