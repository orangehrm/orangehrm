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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Api\Model\EmployeeModel;
use OrangeHRM\Time\Dto\DetailedTimesheet;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class EmployeeTimesheetItemAPI extends Endpoint implements CrudEndpoint
{
    use AuthUserTrait;
    use TimesheetServiceTrait;
    use UserRoleManagerTrait;
    use DateTimeHelperTrait;
    use NormalizerServiceTrait;

    public const PARAMETER_TIMESHEET_ID = 'timesheetId';

    public const META_PARAMETER_DATES = 'dates';
    public const META_PARAMETER_SUM = 'sum';
    public const META_PARAMETER_COLUMNS = 'columns';
    public const META_PARAMETER_TIMESHEET = 'timesheet';
    public const META_PARAMETER_EMPLOYEE = 'employee';
    public const META_PARAMETER_ALLOWED_ACTIONS = 'actions';

    public const MY_TIMESHEET_ACTION_MAP = [
        '0' => "View",
        '1' => "Submit",
        '2' => "Approve",
        '3' => "Reject",
        '4' => "Reset",
        '5' => "Modify",
        '6' => "Create",
    ];

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @param DetailedTimesheet $detailedTimesheet
     * @return ParameterBag
     */
    protected function getResultMetaForGetAll(DetailedTimesheet $detailedTimesheet): ParameterBag
    {
        $dates = [];
        $columns = [];
        $sum = 0;
        foreach ($detailedTimesheet->getColumns() as $column) {
            $sum += $column->getTotal();
            $date = $this->getDateTimeHelper()->formatDateTimeToYmd($column->getDate());
            $dates[] = $date;
            $columns[$date] = [
                'total' => $this->getDateTimeHelper()->convertSecondsToTimeString($column->getTotal()),
            ];
        }

        $allowedActions = [];
        foreach (
            $this->getTimesheetService()->getAllowedWorkflowsForMyTimesheet(
                $this->getAuthUser()->getEmpNumber(),
                $detailedTimesheet
            ) as $action
        ) {
            $allowedActions[] = [
                'action' => self::MY_TIMESHEET_ACTION_MAP[$action]
            ];
        }

        return new ParameterBag([
            self::META_PARAMETER_TIMESHEET => [
                CommonParams::PARAMETER_ID => $detailedTimesheet->getTimesheet()->getId(),
                'startDate' => $this->getDateTimeHelper()->formatDateTimeToYmd(
                    $detailedTimesheet->getTimesheet()->getStartDate()
                ),
                'endDate' => $this->getDateTimeHelper()->formatDateTimeToYmd(
                    $detailedTimesheet->getTimesheet()->getEndDate()
                ),
            ],
            self::META_PARAMETER_SUM => $this->getDateTimeHelper()->convertSecondsToTimeString($sum),
            self::META_PARAMETER_COLUMNS => $columns,
            self::META_PARAMETER_DATES => $dates,
            self::META_PARAMETER_EMPLOYEE => $this->getNormalizedEmployee(
                $detailedTimesheet->getTimesheet()->getEmployee()
            ),
            self::META_PARAMETER_ALLOWED_ACTIONS => $allowedActions
        ]);
    }

    /**
     * @param Employee $employee
     * @return array
     */
    protected function getNormalizedEmployee(Employee $employee): array
    {
        return $this->getNormalizerService()->normalize(
            EmployeeModel::class,
            $employee
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_TIMESHEET_ID,
                new Rule(Rules::POSITIVE),
            )
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
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
