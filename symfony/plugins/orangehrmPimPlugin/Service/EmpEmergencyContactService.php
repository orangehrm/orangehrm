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

use Exception;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Entity\EmpEmergencyContact;
use OrangeHRM\Pim\Dao\EmpEmergencyContactDao;
use OrangeHRM\Pim\Dao\EmployeeDao;
use OrangeHRM\Pim\Dto\EmpEmergencyContactSearchFilterParams;
use OrangeHRM\Pim\Event\EmployeeAddedEvent;
use OrangeHRM\Pim\Event\EmployeeEvents;

class EmpEmergencyContactService
{

    /**
     * @var EmpEmergencyContactDao|null
     */
    protected ?EmpEmergencyContactDao $EmpEmergencyContactDao = null;


    /**
     * Get Emergency contacts for given employee
     *
     * @version 2.6.11
     * @param int $empNumber Employee Number
     * EmpEmergencyContact objects as array. Array will be empty
     *               if no emergency contacts defined fo
     * r employee.
     * @throws DaoException
     *
     * @todo Rename method as getEmployeeEmergencyContacts [DONE]
     */
    public function getEmployeeEmergencyContacts(int $empNumber): ?EmpEmergencyContact
    {
        return $this->getEmpEmergencyContactDao()->getEmployeeEmergencyContacts($empNumber);
    }

    /**
     * @return EmpEmergencyContactDao|null
     */
    public function getEmpEmergencyContactDao(): ?EmpEmergencyContactDao
    {
        return $this->EmpEmergencyContactDao;
    }

    /**
     * @param EmpEmergencyContactDao|null $EmpEmergencyContactDao
     */
    public function setEmpEmergencyContactDao(?EmpEmergencyContactDao $EmpEmergencyContactDao): void
    {
        $this->EmpEmergencyContactDao = $EmpEmergencyContactDao;
    }

    public function saveEmpEmergencyContacts(EmpEmergencyContact $empEmergencyContact): EmpEmergencyContact
    {
        return $this->getEmpEmergencyContactDao()->saveEmployeeEmergencyContacts($empEmergencyContact);
    }

    /**
     * Delete the given emergency contacts from the given employee
     *
     * If $entriesToDelete is not provided (null), all entries of given employee
     * will be deleted.
     *
     * @version 2.6.11
     * @param int $empNumber Employee Number
     * @param array $sequenceNumbers Array of emergency contact sequence numbers. Optional.
     * @return int Number of records deleted
     * @throws DaoException
     *
     * @todo return number of contacts deleted (currently returns true always) [DONE]
     * @todo Exceptions should preserve previous exception [DONE]
     * @todo rename method as deleteEmployeeEmergencyContacts [DONE]
     */
    public function deleteEmployeeEmergencyContacts($empNumber, $sequenceNumbers = null): int
    {
        return $this->getEmpEmergencyContactDao()->deleteEmployeeEmergencyContacts($empNumber, $sequenceNumbers);
    }

    public function searchEmployeeEmergencyContacts(EmpEmergencyContactSearchFilterParams $emergencyContactSearchFilterParams): array
    {
        try {
            return $this->getEmpEmergencyContactDao()->searchEmployeeEmergencyContacts($emergencyContactSearchFilterParams);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getSearchEmployeeEmergencyContactsCount(EmpEmergencyContactSearchFilterParams $emergencyContactSearchFilterParams):int
    {
        try {
            return $this->getEmpEmergencyContactDao()->getSearchEmployeeEmergencyContactsCount($emergencyContactSearchFilterParams);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
