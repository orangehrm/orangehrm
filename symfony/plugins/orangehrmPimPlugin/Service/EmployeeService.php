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

namespace OrangeHRM\Pim\Service;

use DateTime;
use OrangeHRM\Core\Exception\CoreServiceException;
use OrangeHRM\Core\Registration\Event\RegistrationEvent;
use OrangeHRM\Core\Service\IDGeneratorService;
use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Dao\EmployeeDao;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Pim\Event\EmployeeAddedEvent;
use OrangeHRM\Pim\Event\EmployeeEvents;
use OrangeHRM\Pim\Event\EmployeeJoinedDateChangedEvent;
use OrangeHRM\Pim\Service\Model\EmployeeModel;

class EmployeeService
{
    use EventDispatcherTrait;
    use ConfigServiceTrait;
    use NormalizerServiceTrait;

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
     * @param EmployeeEventService $employeeEventService
     */
    public function setEmployeeEventService(EmployeeEventService $employeeEventService): void
    {
        $this->employeeEventService = $employeeEventService;
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

        // TODO:: Improve
        $eventName = $employee->getEmployeeTerminationRecord() == null ? RegistrationEvent::EMPLOYEE_ADD_EVENT_NAME : RegistrationEvent::EMPLOYEE_TERMINATE_EVENT_NAME;
        $this->getEventDispatcher()->dispatch(new RegistrationEvent(), $eventName);
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
        return $this->getEmployeeDao()->deleteEmployees($empNumbers);
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
     * @throws CoreServiceException
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
}
