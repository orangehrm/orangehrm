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

namespace OrangeHRM\Pim\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeWorkShift;
use OrangeHRM\Entity\ReportingMethod;
use OrangeHRM\Entity\ReportTo;
use OrangeHRM\ORM\QueryBuilderWrapper;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;

class EmployeeDao extends BaseDao
{
    use TextHelperTrait;

    /**
     * @param EmployeeSearchFilterParams $employeeSearchParamHolder
     * @return Employee[]
     */
    public function getEmployeeList(EmployeeSearchFilterParams $employeeSearchParamHolder): array
    {
        $qb = $this->getEmployeeListQueryBuilderWrapper($employeeSearchParamHolder)->getQueryBuilder();
        return array_column($qb->getQuery()->execute(), 0);
    }

    /**
     * @param EmployeeSearchFilterParams $employeeSearchParamHolder
     * @return int[]
     */
    public function getEmpNumbersByFilterParams(EmployeeSearchFilterParams $employeeSearchParamHolder): array
    {
        $employeeSearchParamHolder->setSortField('employee.empNumber');
        $q = $this->getEmployeeListQueryBuilderWrapper($employeeSearchParamHolder)->getQueryBuilder();
        $q->select('employee.empNumber');

        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'empNumber');
    }

    /**
     * @param EmployeeSearchFilterParams $employeeSearchParamHolder
     * @return int
     */
    public function getEmployeeCount(EmployeeSearchFilterParams $employeeSearchParamHolder): int
    {
        $qb = $this->getEmployeeListQueryBuilderWrapper($employeeSearchParamHolder)->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param EmployeeSearchFilterParams $employeeSearchParamHolder
     * @return QueryBuilderWrapper
     */
    protected function getEmployeeListQueryBuilderWrapper(
        EmployeeSearchFilterParams $employeeSearchParamHolder
    ): QueryBuilderWrapper {
        $q = $this->createQueryBuilder(Employee::class, 'employee');
        $q->addSelect('employee.empNumber');
        $q->distinct();
        $q->leftJoin('employee.jobTitle', 'jobTitle');
        $q->leftJoin('employee.subDivision', 'subunit');
        $q->leftJoin('employee.empStatus', 'empStatus');
        $q->leftJoin('employee.locations', 'location');

        $joinedSupervisors = false;
        if ($this->getTextHelper()->strStartsWith($employeeSearchParamHolder->getSortField(), 'supervisor')) {
            $q->leftJoin('employee.supervisors', 'supervisor');
            $joinedSupervisors = true;
        }

        $this->setSortingAndPaginationParams($q, $employeeSearchParamHolder);
        if ($employeeSearchParamHolder->getSortField() !== null && $employeeSearchParamHolder->getSortField() !== "") {
            $q->addSelect($employeeSearchParamHolder->getSortField());
        }

        if (is_null($employeeSearchParamHolder->getIncludeEmployees()) ||
            $employeeSearchParamHolder->getIncludeEmployees() ===
            EmployeeSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT
        ) {
            $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
        } elseif (
            $employeeSearchParamHolder->getIncludeEmployees() ===
            EmployeeSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_PAST
        ) {
            $q->andWhere($q->expr()->isNotNull('employee.employeeTerminationRecord'));
        }

        if (!is_null($employeeSearchParamHolder->getName())) {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->like('employee.firstName', ':name'),
                    $q->expr()->like('employee.lastName', ':name'),
                    $q->expr()->like('employee.middleName', ':name'),
                    $q->expr()->like(
                        $q->expr()->concat(
                            'employee.firstName',
                            $q->expr()->literal(' '),
                            'employee.lastName',
                        ),
                        ':name'
                    ),
                    $q->expr()->like(
                        $q->expr()->concat(
                            'employee.firstName',
                            $q->expr()->literal(' '),
                            'employee.middleName',
                            $q->expr()->literal(' '),
                            'employee.lastName',
                        ),
                        ':name'
                    ),
                )
            );
            $q->setParameter('name', '%' . $employeeSearchParamHolder->getName() . '%');
        }

        if (!is_null($employeeSearchParamHolder->getNameOrId())) {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->like('employee.firstName', ':nameOrId'),
                    $q->expr()->like('employee.lastName', ':nameOrId'),
                    $q->expr()->like('employee.middleName', ':nameOrId'),
                    $q->expr()->like('employee.employeeId', ':nameOrId'),
                    $q->expr()->like(
                        $q->expr()->concat(
                            'employee.firstName',
                            $q->expr()->literal(' '),
                            'employee.lastName',
                        ),
                        ':nameOrId'
                    ),
                    $q->expr()->like(
                        $q->expr()->concat(
                            'employee.firstName',
                            $q->expr()->literal(' '),
                            'employee.middleName',
                            $q->expr()->literal(' '),
                            'employee.lastName',
                        ),
                        ':nameOrId'
                    ),
                )
            );
            $q->setParameter('nameOrId', '%' . $employeeSearchParamHolder->getNameOrId() . '%');
        }

        if (!is_null($employeeSearchParamHolder->getEmployeeId())) {
            $q->andWhere('employee.employeeId = :employeeId')
                ->setParameter('employeeId', $employeeSearchParamHolder->getEmployeeId());
        }

        if (!is_null($employeeSearchParamHolder->getSubunitId())) {
            $q->andWhere($q->expr()->in('subunit.id', ':subunitIds'))
                ->setParameter('subunitIds', $employeeSearchParamHolder->getSubunitIdChain());
        }

        if (!is_null($employeeSearchParamHolder->getLocationId())) {
            $q->andWhere('location.id = :locationId')
                ->setParameter('locationId', $employeeSearchParamHolder->getLocationId());
        }

        if (!is_null($employeeSearchParamHolder->getEmpStatusId())) {
            $q->andWhere('empStatus.id = :empStatusId')
                ->setParameter('empStatusId', $employeeSearchParamHolder->getEmpStatusId());
        }

        if (!is_null($employeeSearchParamHolder->getJobTitleId())) {
            $q->andWhere('jobTitle.id = :jobTitleId')
                ->setParameter('jobTitleId', $employeeSearchParamHolder->getJobTitleId());
        }

        if (!is_null($employeeSearchParamHolder->getEmployeeNumbers())) {
            $q->andWhere($q->expr()->in('employee.empNumber', ':empNumbers'))
                ->setParameter('empNumbers', $employeeSearchParamHolder->getEmployeeNumbers());
        }

        if (!is_null($employeeSearchParamHolder->getSupervisorEmpNumbers())) {
            if (!$joinedSupervisors) {
                $q->leftJoin('employee.supervisors', 'supervisor');
            }
            $q->andWhere($q->expr()->in('supervisor.empNumber', ':supervisorEmpNumbers'))
                ->setParameter('supervisorEmpNumbers', $employeeSearchParamHolder->getSupervisorEmpNumbers());
        }

        $q->andWhere($q->expr()->isNull('employee.purgedAt'));

        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param Employee $employee
     * @return Employee
     */
    public function saveEmployee(Employee $employee): Employee
    {
        $this->persist($employee);
        return $employee;
    }

    /**
     * @param int $empNumber
     * @return Employee|null
     */
    public function getEmployeeByEmpNumber(int $empNumber): ?Employee
    {
        return $this->getRepository(Employee::class)->find($empNumber);
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingEmpNumbers(array $ids): array
    {
        $qb = $this->createQueryBuilder(Employee::class, 'employee');

        $qb->select('employee.empNumber')
            ->andWhere($qb->expr()->in('employee.empNumber', ':ids'))
            ->andWhere($qb->expr()->isNull('employee.purgedAt'))
            ->setParameter(':ids', $ids);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int $supervisorId
     * @param bool $includeChain
     * @param array $supervisorIdStack
     * @param int|null $maxDepth
     * @param int $depth
     * @return int[]
     */
    public function getSubordinateIdListBySupervisorId(
        int $supervisorId,
        bool $includeChain = false,
        array $supervisorIdStack = [],
        ?int $maxDepth = null,
        int $depth = 1
    ): array {
        $employeeIdList = [];
        $q = $this->createQueryBuilder(ReportTo::class, 'r');
        $q->andWhere('r.supervisor = :supervisorId')
            ->setParameter('supervisorId', $supervisorId);

        /** @var ReportTo[] $reportToArray */
        $reportToArray = $q->getQuery()->execute();

        foreach ($reportToArray as $reportTo) {
            $subordinateEmpNumber = $reportTo->getSubordinate()->getEmpNumber();
            array_push($employeeIdList, $subordinateEmpNumber);

            if ($includeChain || (!is_null($maxDepth) && ($depth < $maxDepth))) {
                if (!in_array($subordinateEmpNumber, $supervisorIdStack)) {
                    $supervisorIdStack[] = $subordinateEmpNumber;
                    $subordinateIdList = $this->getSubordinateIdListBySupervisorId(
                        $subordinateEmpNumber,
                        $includeChain,
                        $supervisorIdStack,
                        $maxDepth,
                        $depth + 1
                    );
                    if (count($subordinateIdList) > 0) {
                        foreach ($subordinateIdList as $id) {
                            array_push($employeeIdList, $id);
                        }
                    }
                }
            }
        }
        return $employeeIdList;
    }

    /**
     * @param bool $includeTerminated
     * @return int
     */
    public function getNumberOfEmployees(bool $includeTerminated = false): int
    {
        $q = $this->createQueryBuilder(Employee::class, 'e');

        if ($includeTerminated == false) {
            $q->andWhere($q->expr()->isNull('e.employeeTerminationRecord'));
        }

        return $this->count($q);
    }

    /**
     * Return List of Subordinates for given Supervisor
     *
     * @param int $supervisorId Supervisor Id
     * @param bool $includeTerminated Terminated status
     * @param bool $includeChain
     * @param array $supervisorIdStack
     * @return Employee[] of Subordinates
     */
    public function getSubordinateList(
        int $supervisorId,
        bool $includeTerminated = false,
        bool $includeChain = false,
        array $supervisorIdStack = []
    ): array {
        $employeeList = [];
        $q = $this->createQueryBuilder(ReportTo::class, 'rt');
        $q->leftJoin('rt.subordinate', 'e');
        $q->andWhere('rt.supervisor = :supervisorId')
            ->setParameter('supervisorId', $supervisorId);

        if ($includeTerminated == false) {
            $q->andWhere($q->expr()->isNull('e.employeeTerminationRecord'));
        }

        /** @var ReportTo[] $reportToArray */
        $reportToArray = $q->getQuery()->execute();

        foreach ($reportToArray as $reportTo) {
            $employeeList[] = $reportTo->getSubordinate();

            if ($includeChain) {
                $subordinateEmpNumber = $reportTo->getSubordinate()->getEmpNumber();
                if (!in_array($subordinateEmpNumber, $supervisorIdStack)) {
                    $supervisorIdStack[] = $subordinateEmpNumber;
                    $subordinateList = $this->getSubordinateList(
                        $subordinateEmpNumber,
                        $includeTerminated,
                        $includeChain,
                        $supervisorIdStack
                    );
                    if (count($subordinateList) > 0) {
                        foreach ($subordinateList as $sub) {
                            $employeeList[] = $sub;
                        }
                    }
                }
            }
        }

        return $employeeList;
    }

    /**
     * Check if employee with given empNumber is a supervisor
     * @param int $empNumber
     * @return bool - True if given employee is a supervisor, false if not
     */
    public function isSupervisor(int $empNumber): bool
    {
        $q = $this->createQueryBuilder(ReportTo::class, 'r');
        $q->andWhere('r.supervisor = :supervisorId')
            ->setParameter('supervisorId', $empNumber);

        return ($this->count($q) > 0);
    }

    /**
     * @param int $subordinateId
     * @param int $supervisorId
     * @return ReportingMethod|null
     */
    public function getReportingMethod(int $subordinateId, int $supervisorId): ?ReportingMethod
    {
        $q = $this->createQueryBuilder(ReportingMethod::class, 'rm');
        $q->leftJoin('rm.reportTos', 'reportTo')
            ->andWhere('reportTo.supervisor = :supervisorId')
            ->setParameter('supervisorId', $supervisorId)
            ->andWhere('reportTo.subordinate = :subordinateId')
            ->setParameter('subordinateId', $subordinateId);

        return $this->fetchOne($q);
    }

    /**
     * @param bool $excludeTerminatedEmployees
     * @return int[]
     */
    public function getEmpNumberList(bool $excludeTerminatedEmployees = false): array
    {
        $q = $this->createQueryBuilder(Employee::class, 'e');
        $q->select('e.empNumber');
        $q->addOrderBy('e.empNumber');
        $q->andWhere($q->expr()->isNull('e.purgedAt'));

        if ($excludeTerminatedEmployees) {
            $q->andWhere($q->expr()->isNull('e.employeeTerminationRecord'));
        }

        $result = $q->getQuery()->getArrayResult();
        return array_column($result, 'empNumber');
    }

    /**
     * @param int[] $empNumbers
     * @returns int
     */
    public function deleteEmployees(array $empNumbers): int
    {
        $q = $this->createQueryBuilder(Employee::class, 'e');
        $q->delete()
            ->where($q->expr()->in('e.empNumber', ':empNumbers'))
            ->setParameter('empNumbers', $empNumbers);
        return $q->getQuery()->execute();
    }


    /**
     * @param int $subordinateId
     * @param bool $includeChain
     * @param array $subordinateIdStack
     * @param int|null $maxDepth
     * @param int $depth
     * @return int[]
     */
    public function getSupervisorIdListBySubordinateId(
        int $subordinateId,
        bool $includeChain = false,
        array $subordinateIdStack = [],
        ?int $maxDepth = null,
        int $depth = 1
    ): array {
        $employeeIdList = [];
        $q = $this->createQueryBuilder(ReportTo::class, 'r');
        $q->andWhere('r.subordinate = :subordinateId')
            ->setParameter('subordinateId', $subordinateId);

        /** @var ReportTo[] $reportToArray */
        $reportToArray = $q->getQuery()->execute();

        foreach ($reportToArray as $reportTo) {
            $supervisorEmpNumber = $reportTo->getSupervisor()->getEmpNumber();
            array_push($employeeIdList, $supervisorEmpNumber);

            if ($includeChain || (!is_null($maxDepth) && ($depth < $maxDepth))) {
                if (!in_array($supervisorEmpNumber, $subordinateIdStack)) {
                    $subordinateIdStack[] = $supervisorEmpNumber;
                    $supervisorIdList = $this->getSupervisorIdListBySubordinateId(
                        $supervisorEmpNumber,
                        $includeChain,
                        $subordinateIdStack,
                        $maxDepth,
                        $depth + 1
                    );
                    if (count($supervisorIdList) > 0) {
                        foreach ($supervisorIdList as $id) {
                            array_push($employeeIdList, $id);
                        }
                    }
                }
            }
        }
        return $employeeIdList;
    }

    /**
     * @param int $empNumber
     * @return EmployeeWorkShift|null
     */
    public function getEmployeeWorkShift(int $empNumber): ?EmployeeWorkShift
    {
        $q = $this->createQueryBuilder(EmployeeWorkShift::class, 'ews');
        $q->where('ews.employee = :empNumber')
            ->setParameter('empNumber', $empNumber);

        return $this->fetchOne($q);
    }

    /**
     * @return Employee[]
     */
    public function getAvailableEmployeeListForWorkShift(EmployeeSearchFilterParams $employeeSearchParamHolder): array
    {
        $q = $this->getEmployeeListQueryBuilderWrapper($employeeSearchParamHolder)->getQueryBuilder();
        $q->leftJoin('employee.employeeWorkShift', 'ew');
        $q->andWhere($q->expr()->isNull('ew.employee'));
        return array_column($q->getQuery()->execute(), 0);
    }

    /**
     * @return array|null
     */
    public function getEmailList(): ?array
    {
        $q = $this->createQueryBuilder(Employee::class, 'e');
        $q->select('e.workEmail, e.otherEmail');
        return  $q->getQuery()->getArrayResult();
    }

    /**
     * @param string $email
     * @return bool
     */
    public function isEmailAvailable(string $email): bool
    {
        $q = $this->createQueryBuilder(Employee::class, 'employee');
        $q->andWhere(
            $q->expr()->orX(
                'employee.workEmail = :email',
                'employee.otherEmail = :email'
            )
        );
        $q->setParameter('email', $email);
        return $this->getPaginator($q)->count() > 0;
    }

    public function isUniqueEmployeeId(string $employeeId): bool
    {
        $q = $this->createQueryBuilder(Employee::class, 'employee')
            ->andWhere('employee.employeeId = :employeeId')
            ->setParameter('employeeId', $employeeId);
        return $this->count($q) === 0;
    }
}
