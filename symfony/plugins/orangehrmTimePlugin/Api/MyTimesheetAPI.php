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

use Exception;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Entity\TimesheetActionLog;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Time\Api\Model\TimesheetModel;
use OrangeHRM\Time\Api\Traits\TimesheetPermissionTrait;
use OrangeHRM\Time\Api\ValidationRules\MyTimesheetActionRule;
use OrangeHRM\Time\Api\ValidationRules\MyTimesheetDateRule;
use OrangeHRM\Time\Service\TimesheetService;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class MyTimesheetAPI extends Endpoint implements CrudEndpoint
{
    use AuthUserTrait;
    use TimesheetServiceTrait;
    use DateTimeHelperTrait;
    use TimesheetPermissionTrait;
    use DateTimeHelperTrait;
    use EntityManagerHelperTrait;
    use ServiceContainerTrait;

    public const PARAMETER_DATE = 'date';
    public const PARAMETER_ACTION = 'action';
    public const PARAMETER_COMMENT = 'comment';

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
     * @throws TransactionException
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

            $timesheet = $this->getTimesheetService()->getTimesheetDao()->getTimesheetById($timesheetId);
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
            $this->setTimesheetActionLog($state, $comment, $timesheet);
            $this->commitTransaction();

            return new EndpointResourceResult(TimesheetModel::class, $timesheet);
        } catch (RecordNotFoundException|ForbiddenException $e) {
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
            new ParamRule(CommonParams::PARAMETER_ID),

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
                )
            ),
        );
    }

    /**
     * @param string $state
     * @param string|null $comment
     * @param Timesheet $timesheet
     * @return void
     */
    private function setTimesheetActionLog(string $state, ?string $comment, Timesheet $timesheet): void
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
