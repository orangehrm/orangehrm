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

namespace OrangeHRM\Pim\Service;

use DateTime;
use OrangeHRM\Admin\Traits\Service\UserServiceTrait;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Exception\CoreServiceException;
use OrangeHRM\Core\Service\IDGeneratorService;
use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Pim\Dao\EmployeeDao;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Pim\Event\EmployeeAddedEvent;
use OrangeHRM\Pim\Event\EmployeeDeletedEvent;
use OrangeHRM\Pim\Event\EmployeeEvents;
use OrangeHRM\Pim\Event\EmployeeJoinedDateChangedEvent;
use OrangeHRM\Pim\Event\EmployeeSavedEvent;
use OrangeHRM\Pim\Service\Model\EmployeeModel;

class EmployeeService
{
    use EventDispatcherTrait;
    use ConfigServiceTrait;
    use NormalizerServiceTrait;
    use UserServiceTrait;
    use UserRoleManagerTrait;

    public const FIRST_NAME_MAX_LENGTH = 30;
    public const MIDDLE_NAME_MAX_LENGTH = 30;
    public const LAST_NAME_MAX_LENGTH = 30;
    public const EMPLOYEE_ID_MAX_LENGTH = 10;
    public const WORK_EMAIL_MAX_LENGTH = 50;

    /**
     * @var EmployeeDao|null
     */
    protected ?EmployeeDao $employeeDao = null;

    /**
     * @var EmployeeEventService|null
     */
    protected ?EmployeeEventService $employeeEventService = null;

    /**
     * @var EmployeeTerminationService|null
     */
    protected ?EmployeeTerminationService $employeeTerminationService = null;

    /**
     * @var IDGeneratorService|null
     */
    protected ?IDGeneratorService $iDGeneratorService = null;

    /**
     * @return EmployeeDao
     */
    public function getEmployeeDao(): EmployeeDao
    {
        if (is_null($this->employeeDao)) {
            $this->employeeDao = new EmployeeDao();
        }
        return $this->employeeDao;
    }

    /**
     * @param EmployeeDao $employeeDao
     */
    public function setEmployeeDao(EmployeeDao $employeeDao): void
    {
        $this->employeeDao = $employeeDao;
    }

    /**
     * @return EmployeeEventService
     */
    public function getEmployeeEventService(): EmployeeEventService
    {
        if (!$this->employeeEventService instanceof EmployeeEventService) {
            $this->employeeEventService = new EmployeeEventService();
        }
        return $this->employeeEventService;
    }

    /**
     * @return EmployeeTerminationService
     */
    public function getEmployeeTerminationService(): EmployeeTerminationService
    {
        if (!$this->employeeTerminationService instanceof EmployeeTerminationService) {
            $this->employeeTerminationService = new EmployeeTerminationService();
        }
        return $this->employeeTerminationService;
    }

    /**
     * @return IDGeneratorService
     */
    public function getIDGeneratorService(): IDGeneratorService
    {
        if (!$this->iDGeneratorService instanceof IDGeneratorService) {
            $this->iDGeneratorService = new IDGeneratorService();
        }
        return $this->iDGeneratorService;
    }

    /**
     * @param EmployeeSearchFilterParams $employeeSearchParamHolder
     * @return Employee[]
     */
    public function getEmployeeList(EmployeeSearchFilterParams $employeeSearchParamHolder): array
    {
        return $this->getEmployeeDao()->getEmployeeList($employeeSearchParamHolder);
    }

    /**
     * @param EmployeeSearchFilterParams $employeeSearchParamHolder
     * @return int
     */
    public function getEmployeeCount(EmployeeSearchFilterParams $employeeSearchParamHolder): int
    {
        return $this->getEmployeeDao()->getEmployeeCount($employeeSearchParamHolder);
    }

    /**
     * @param Employee $employee
     * @return Employee
     */
    public function saveEmployee(Employee $employee): Employee
    {
        $savedEmployee = $this->getEmployeeDao()->saveEmployee($employee);

        $this->getEventDispatcher()->dispatch(new EmployeeSavedEvent($employee), EmployeeEvents::EMPLOYEE_SAVED);
        return $savedEmployee;
    }

    /**
     * @param Employee $employee
     * @return Employee
     */
    public function saveNewEmployee(Employee $employee): Employee
    {
        $this->getIDGeneratorService()->incrementId(Employee::class);
        return $this->saveEmployee($employee);
    }

    /**
     * @param Employee $employee
     */
    public function dispatchAddEmployeeEvent(Employee $employee): void
    {
        $this->getEmployeeEventService()->saveAddEmployeeEvent($employee->getEmpNumber());
        $this->getEventDispatcher()->dispatch(new EmployeeAddedEvent($employee), EmployeeEvents::EMPLOYEE_ADDED);
    }

    /**
     * @param Employee $employee
     * @return Employee
     */
    public function updateEmployeePersonalDetails(Employee $employee): Employee
    {
        $employee = $this->saveEmployee($employee);
        $this->getEmployeeEventService()->saveUpdateEmployeePersonalDetailsEvent($employee->getEmpNumber());
        return $employee;
    }

    /**
     * @param Employee $employee
     */
    public function updateEmployeeJobDetails(Employee $employee): void
    {
        $employee = $this->saveEmployee($employee);
        $this->getEmployeeEventService()->saveUpdateJobDetailsEvent($employee->getEmpNumber());
    }

    /**
     * @param Employee $employee
     * @param DateTime|null $previousJoinedDate
     */
    public function dispatchJoinedDateChangedEvent(Employee $employee, ?DateTime $previousJoinedDate): void
    {
        $this->getEventDispatcher()->dispatch(
            new EmployeeJoinedDateChangedEvent($employee, $previousJoinedDate),
            EmployeeEvents::JOINED_DATE_CHANGED
        );
    }

    /**
     * @param int $empNumber
     * @return Employee|null
     */
    public function getEmployeeByEmpNumber(int $empNumber): ?Employee
    {
        return $this->getEmployeeDao()->getEmployeeByEmpNumber($empNumber);
    }

    /**
     * @param bool $includeTerminated
     * @return int
     */
    public function getNumberOfEmployees(bool $includeTerminated = false): int
    {
        return $this->getEmployeeDao()->getNumberOfEmployees($includeTerminated);
    }

    /**
     * Returns an array of empNumbers of subordinates for given supervisor ID
     *
     * empNumbers of whole chain under given supervisor are returned.
     *
     * @param int $supervisorId Supervisor's ID
     * @param bool|null $includeChain Include Supervisor chain or not
     * @param int|null $maxDepth
     * @return int[] An array of empNumbers
     * @throws CoreServiceException
     */
    public function getSubordinateIdListBySupervisorId(
        int $supervisorId,
        ?bool $includeChain = null,
        int $maxDepth = null
    ): array {
        if (is_null($includeChain)) {
            $includeChain = $this->getConfigService()->isSupervisorChainSupported();
        }
        return $this->getEmployeeDao()->getSubordinateIdListBySupervisorId($supervisorId, $includeChain, [], $maxDepth);
    }

    /**
     * Return List of Subordinates for given Supervisor
     *
     * @param int $supervisorId Supervisor Id
     * @param bool $includeTerminated Terminated status
     * @return Employee[] of Subordinates
     */
    public function getSubordinateList(int $supervisorId, bool $includeTerminated = false): array
    {
        $includeChain = $this->getConfigService()->isSupervisorChainSupported();
        return $this->getEmployeeDao()->getSubordinateList($supervisorId, $includeTerminated, $includeChain);
    }

    /**
     * Check if employee with given employee number is a supervisor
     *
     * @param int $empNumber Employee Number
     * @return bool True if given employee is a supervisor, false if not
     */
    public function isSupervisor(int $empNumber): bool
    {
        return $this->getEmployeeDao()->isSupervisor($empNumber);
    }

    /**
     * @param int[] $empNumbers
     * @return int
     */
    public function deleteEmployees(array $empNumbers): int
    {
        $result = $this->getEmployeeDao()->deleteEmployees($empNumbers);
        $this->getEventDispatcher()->dispatch(new EmployeeDeletedEvent($empNumbers), EmployeeEvents::EMPLOYEES_DELETED);
        return $result;
    }

    /**
     * Returns an array of empNumbers of supervisors for given subordinate ID
     *
     * empNumbers of whole chain under given subordinate are returned.
     *
     * @param int $subordinateId
     * @param bool|null $includeChain Include Supervisor chain or not
     * @param int|null $maxDepth
     * @return int[] An array of empNumbers
     */
    public function getSupervisorIdListBySubordinateId(
        int $subordinateId,
        ?bool $includeChain = null,
        int $maxDepth = null
    ): array {
        if (is_null($includeChain)) {
            $includeChain = $this->getConfigService()->isSupervisorChainSupported();
        }
        return $this->getEmployeeDao()->getSupervisorIdListBySubordinateId(
            $subordinateId,
            $includeChain,
            [],
            $maxDepth
        );
    }

    /**
     * @param int $empNumber
     * @return array|null
     */
    public function getEmployeeAsArray(int $empNumber): ?array
    {
        $employee = $this->getEmployeeByEmpNumber($empNumber);
        if (!$employee instanceof Employee) {
            return null;
        }
        return $this->getNormalizerService()->normalize(EmployeeModel::class, $employee);
    }

    /**
     * @return Employee[]
     */
    public function getAvailableEmployeesForWorkShift(EmployeeSearchFilterParams $employeeSearchParamHolder): array
    {
        return $this->getEmployeeDao()->getAvailableEmployeeListForWorkShift($employeeSearchParamHolder);
    }

    /**
     * @return int[]
     */
    public function getUndeletableEmpNumbers(): array
    {
        $undeletableIds = [$this->getUserRoleManager()->getUser()->getEmpNumber()];
        if (Config::PRODUCT_MODE === Config::MODE_DEMO &&
            ($user = $this->getUserService()->geUserDao()->getDefaultAdminUser()) instanceof User) {
            $undeletableIds[] = $user->getEmpNumber();
        }
        return $undeletableIds;
    }

    /**
     * @param string $email
     * @param string|null $currentEmail
     * @return bool
     */
    public function isUniqueEmail(string $email, ?string $currentEmail = null): bool
    {
        // we need to skip the current email on checking, otherwise count always return 1 (if current work email is not null)
        // if the current email is set and input email equals current email, return true to skip validation
        if ($currentEmail !== null && $email === $currentEmail) {
            return true;
        }

        return !$this->getEmployeeDao()->isEmailAvailable($email);
    }

    /**
     * @param string $employeeId
     * @param string|null $currentEmployeeId
     * @return bool
     */
    public function isUniqueEmployeeId(string $employeeId, ?string $currentEmployeeId = null): bool
    {
        if ($currentEmployeeId !== null && $employeeId === $currentEmployeeId) {
            return true;
        }

        return $this->getEmployeeDao()->isUniqueEmployeeId($employeeId);
    }
}
