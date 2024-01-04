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

use OrangeHRM\Entity\EmployeeImmigrationRecord;
use OrangeHRM\Pim\Dao\EmployeeImmigrationRecordDao;
use OrangeHRM\Pim\Dto\EmployeeImmigrationRecordSearchFilterParams;

class EmployeeImmigrationRecordService
{
    /**
     * @var EmployeeImmigrationRecordDao|null
     */
    protected ?EmployeeImmigrationRecordDao $employeeImmigrationRecordDao = null;

    /**
     * @return EmployeeImmigrationRecordDao
     */
    public function getEmployeeImmigrationRecordDao(): EmployeeImmigrationRecordDao
    {
        if (!($this->employeeImmigrationRecordDao instanceof EmployeeImmigrationRecordDao)) {
            $this->employeeImmigrationRecordDao = new EmployeeImmigrationRecordDao();
        }
        return $this->employeeImmigrationRecordDao;
    }

    /**
     * @param EmployeeImmigrationRecordDao|null $employeeImmigrationRecordDao
     */
    public function setEmployeeImmigrationRecordDao(?EmployeeImmigrationRecordDao $employeeImmigrationRecordDao): void
    {
        $this->employeeImmigrationRecordDao = $employeeImmigrationRecordDao;
    }

    /**
     * @param int $empNumber
     * @param int $recordNo
     * @return EmployeeImmigrationRecord|null
     */
    public function getEmployeeImmigrationRecord(int $empNumber, int $recordNo): ?EmployeeImmigrationRecord
    {
        return $this->getEmployeeImmigrationRecordDao()->getEmployeeImmigrationRecord($empNumber, $recordNo);
    }

    /**
     * @param int $empNumber
     * @return array
     */
    public function getEmployeeImmigrationRecordList(int $empNumber): array
    {
        return $this->getEmployeeImmigrationRecordDao()->getEmployeeImmigrationRecordList($empNumber);
    }

    /**
     * @param EmployeeImmigrationRecord $employeeImmigrationRecord
     * @return EmployeeImmigrationRecord
     */
    public function saveEmployeeImmigrationRecord(
        EmployeeImmigrationRecord $employeeImmigrationRecord
    ): EmployeeImmigrationRecord {
        return $this->getEmployeeImmigrationRecordDao()->saveEmployeeImmigrationRecord($employeeImmigrationRecord);
    }

    /**
     * @param int $empNumber
     * @param array $sequenceNumbers
     * @return int
     */
    public function deleteEmployeeImmigrationRecords(int $empNumber, array $sequenceNumbers): int
    {
        return $this->getEmployeeImmigrationRecordDao()->deleteEmployeeImmigrationRecords($empNumber, $sequenceNumbers);
    }

    /**
     * @param EmployeeImmigrationRecordSearchFilterParams $employeeImmigrationRecordSearchFilterParams
     * @return array
     */
    public function searchEmployeeImmigrationRecords(
        EmployeeImmigrationRecordSearchFilterParams $employeeImmigrationRecordSearchFilterParams
    ): array {
        return $this->getEmployeeImmigrationRecordDao()->searchEmployeeImmigrationRecords($employeeImmigrationRecordSearchFilterParams);
    }

    /**
     * @param EmployeeImmigrationRecordSearchFilterParams $employeeImmigrationRecordSearchFilterParams
     * @return int
     */
    public function getSearchEmployeeImmigrationRecordsCount(
        EmployeeImmigrationRecordSearchFilterParams $employeeImmigrationRecordSearchFilterParams
    ): int {
        return $this->getEmployeeImmigrationRecordDao()->getSearchEmployeeImmigrationRecordsCount($employeeImmigrationRecordSearchFilterParams);
    }
}
