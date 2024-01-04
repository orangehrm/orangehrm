<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Attendance\Api;

use DateTime;
use OrangeHRM\Attendance\Api\Model\EmployeeAttendanceSummaryListModel;
use OrangeHRM\Attendance\Api\ValidationRules\EmployeeDataGroupReadPermissionRule;
use OrangeHRM\Attendance\Dto\EmployeeAttendanceSummarySearchFilterParams;
use OrangeHRM\Attendance\Traits\Service\AttendanceServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;

class EmployeeAttendanceSummaryAPI extends Endpoint implements CollectionEndpoint
{
    use UserRoleManagerTrait;
    use AttendanceServiceTrait;
    use DateTimeHelperTrait;

    public const PARAMETER_DATE = 'date';
    public const FILTER_FROM_DATE = 'fromDate';
    public const FILTER_TO_DATE = 'toDate';

    /**
     * @OA\Get(
     *     path="/api/v2/attendance/employees/summary",
     *     tags={"Attendance/Employee Attendance Summary"},
     *     summary="Summarize Employee Attendance",
     *     operationId="summarize-employee-attendance",
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmployeeAttendanceSummarySearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Attendance-EmployeeAttendanceSummeryListModel")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer"),
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $employeeAttendanceSummarySearchFilterParams = new EmployeeAttendanceSummarySearchFilterParams();
        $this->setSortingAndPaginationParams($employeeAttendanceSummarySearchFilterParams);
        $employeeNumber = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            CommonParams::PARAMETER_EMP_NUMBER,
        );

        $date = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_DATE,
        );

        $fromDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_FROM_DATE
        );

        $toDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_TO_DATE
        );

        if ($fromDate != null && $toDate != null && $fromDate > $toDate) {
            throw $this->getInvalidParamException(["fromDate","toDate"]);
        }

        if ($fromDate == null && $toDate == null && $date == null) {
            $date = $this->getDateTimeHelper()->getNow()->format('Y-m-d');
            $employeeAttendanceSummarySearchFilterParams->setFromDate(new DateTime($date . ' ' . '00:00:00'));
            $employeeAttendanceSummarySearchFilterParams->setToDate(new DateTime($date . ' ' . '23:59:59'));
        } elseif ($fromDate == null && $toDate == null) {
            $employeeAttendanceSummarySearchFilterParams->setFromDate(new DateTime($date . ' ' . '00:00:00'));
            $employeeAttendanceSummarySearchFilterParams->setToDate(new DateTime($date . ' ' . '23:59:59'));
        } else {
            if (!$fromDate instanceof DateTime || !$toDate instanceof DateTime) {
                throw $this->getInvalidParamException(["fromDate", "toDate"]);
            }
            $employeeAttendanceSummarySearchFilterParams->setFromDate(
                new DateTime($fromDate->format('Y-m-d') . ' ' . '00:00:00')
            );

            $employeeAttendanceSummarySearchFilterParams->setToDate(
                new DateTime($toDate->format('Y-m-d') . ' ' . '23:59:59')
            );
        }

        if (!is_null($employeeNumber)) {
            $employeeAttendanceSummarySearchFilterParams->setEmployeeNumbers([$employeeNumber]);
        } else {
            $accessibleEmpNumbers = $this->getUserRoleManager()->getAccessibleEntityIds(Employee::class);
            $employeeAttendanceSummarySearchFilterParams->setEmployeeNumbers($accessibleEmpNumbers);
        }

        $employeeAttendanceSummaryRecordList = $this->getAttendanceService()
            ->getAttendanceDao()
            ->getEmployeeAttendanceSummaryList($employeeAttendanceSummarySearchFilterParams);

        $employeeAttendanceSummaryRecordListCount = $this->getAttendanceService()
            ->getAttendanceDao()
            ->getEmployeeAttendanceSummaryListCount($employeeAttendanceSummarySearchFilterParams);

        return new EndpointCollectionResult(
            EmployeeAttendanceSummaryListModel::class,
            [$employeeAttendanceSummaryRecordList],
            new ParameterBag([
                CommonParams::PARAMETER_TOTAL => $employeeAttendanceSummaryRecordListCount,
            ])
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
                    self::PARAMETER_DATE,
                    new Rule(Rules::API_DATE)
                ),
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
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::ENTITY_ID_EXISTS, [Employee::class]),
                    new Rule(
                        EmployeeDataGroupReadPermissionRule::class,
                        ['apiv2_attendance_employee_attendance_summary']
                    ),
                    new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Employee::class])
                )
            ),
            ...
            $this->getSortingAndPaginationParamsRules(EmployeeAttendanceSummarySearchFilterParams::ALLOWED_SORT_FIELDS)
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
