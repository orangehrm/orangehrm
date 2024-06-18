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

use InvalidArgumentException;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\EmployeeImmigrationRecord;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\EmployeeImmigrationRecordSearchFilterParams;

class EmployeeImmigrationRecordDao extends BaseDao
{
    /**
     * @param EmployeeImmigrationRecord $employeeImmigrationRecord
     * @return EmployeeImmigrationRecord
     */
    public function saveEmployeeImmigrationRecord(
        EmployeeImmigrationRecord $employeeImmigrationRecord
    ): EmployeeImmigrationRecord {
        if ($employeeImmigrationRecord->getRecordId() === '0') {
            $q = $this->createQueryBuilder(EmployeeImmigrationRecord::class, 'eir');
            $empNumber = $employeeImmigrationRecord->getEmployee()->getEmpNumber();
            $q->andWhere('eir.employee = :empNumber')
                ->setParameter('empNumber', $empNumber);
            $q->select($q->expr()->max('eir.recordId'));
            $maxRecordId = $q->getQuery()->getSingleScalarResult();
            $recordId = 1;
            if (!is_null($maxRecordId)) {
                $recordId += intval($maxRecordId);
            }
            $employeeImmigrationRecord->setRecordId($recordId);
        }
        $recordId = intval($employeeImmigrationRecord->getRecordId());
        if (!($recordId < 100 && $recordId > 0)) {
            throw new InvalidArgumentException('Invalid `recordId`');
        }

        $this->persist($employeeImmigrationRecord);
        return $employeeImmigrationRecord;
    }

    /**
     * @param int $empNumber
     * @param int $recordId
     * @return EmployeeImmigrationRecord|null
     */
    public function getEmployeeImmigrationRecord(int $empNumber, int $recordId): ?EmployeeImmigrationRecord
    {
        $employeeImmigrationRecord = $this->getRepository(EmployeeImmigrationRecord::class)->findOneBy([
            'employee' => $empNumber,
            'recordId' => $recordId,
        ]);
        if ($employeeImmigrationRecord instanceof EmployeeImmigrationRecord) {
            return $employeeImmigrationRecord;
        }
        return null;
    }

    public function getExistingEmployeeImmigrationIdsForEmpNumber(array $ids, int $empNumber): array
    {
        $qb = $this->createQueryBuilder(EmployeeImmigrationRecord::class, 'employeeImmigrationRecord');

        $qb->select('employeeImmigrationRecord.recordId')
            ->andWhere($qb->expr()->in('employeeImmigrationRecord.recordId', ':ids'))
            ->andWhere($qb->expr()->eq('employeeImmigrationRecord.employee', ':empNumber'))
            ->setParameter('ids', $ids)
            ->setParameter('empNumber', $empNumber);

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int $empNumber
     * @param array $entriesToDelete
     * @return int
     */
    public function deleteEmployeeImmigrationRecords(int $empNumber, array $entriesToDelete): int
    {
        $q = $this->createQueryBuilder(EmployeeImmigrationRecord::class, 'eir');
        $q->delete()
            ->where('eir.employee = :empNumber')
            ->setParameter('empNumber', $empNumber);
        $q->andWhere($q->expr()->in('eir.recordId', ':ids'))
            ->setParameter('ids', $entriesToDelete);
        return $q->getQuery()->execute();
    }

    /**
     * @param int $empNumber
     * @return array
     */
    public function getEmployeeImmigrationRecordList(int $empNumber): array
    {
        $q = $this->createQueryBuilder(EmployeeImmigrationRecord::class, 'eir');
        $q->andWhere('eir.employee = :empNumber')
            ->setParameter('empNumber', $empNumber);
        $q->addOrderBy('eir.number', ListSorter::ASCENDING);

        return $q->getQuery()->execute();
    }

    /**
     * @param EmployeeImmigrationRecordSearchFilterParams $immigrationRecordSearchFilterParams
     * @return array
     */
    public function searchEmployeeImmigrationRecords(
        EmployeeImmigrationRecordSearchFilterParams $immigrationRecordSearchFilterParams
    ): array {
        $paginator = $this->getSearchEmployeeImmigrationRecordsPaginator($immigrationRecordSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param EmployeeImmigrationRecordSearchFilterParams $immigrationRecordSearchFilterParams
     * @return Paginator
     */
    private function getSearchEmployeeImmigrationRecordsPaginator(
        EmployeeImmigrationRecordSearchFilterParams $immigrationRecordSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(EmployeeImmigrationRecord::class, 'eir');
        $this->setSortingAndPaginationParams($q, $immigrationRecordSearchFilterParams);

        if (!empty($immigrationRecordSearchFilterParams->getEmpNumber())) {
            $q->andWhere('eir.employee = :empNumber');
            $q->setParameter('empNumber', $immigrationRecordSearchFilterParams->getEmpNumber());
        }
        if (!empty($immigrationRecordSearchFilterParams->getNumber())) {
            $q->andWhere('eir.number = :number');
            $q->setParameter('number', $immigrationRecordSearchFilterParams->getNumber());
        }

        return $this->getPaginator($q);
    }

    /**
     * @param EmployeeImmigrationRecordSearchFilterParams $immigrationRecordSearchFilterParams
     * @return int
     */
    public function getSearchEmployeeImmigrationRecordsCount(
        EmployeeImmigrationRecordSearchFilterParams $immigrationRecordSearchFilterParams
    ): int {
        $paginator = $this->getSearchEmployeeImmigrationRecordsPaginator($immigrationRecordSearchFilterParams);
        return $paginator->count();
    }
}
