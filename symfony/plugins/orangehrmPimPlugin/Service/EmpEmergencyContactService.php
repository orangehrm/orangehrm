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

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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
     * @return EmpEmergencyContactDao
     */
    public function getEmpEmergencyContactDao(): EmpEmergencyContactDao
    {
        if (!($this->EmpEmergencyContactDao instanceof EmpEmergencyContactDao)) {
            $this->EmpEmergencyContactDao = new EmpEmergencyContactDao();
        }

        return $this->EmpEmergencyContactDao;
    }

    /**
     * @param EmpEmergencyContactDao|null $EmpEmergencyContactDao
     */
    public function setEmpEmergencyContactDao(?EmpEmergencyContactDao $EmpEmergencyContactDao): void
    {
        $this->EmpEmergencyContactDao = $EmpEmergencyContactDao;
    }

    /**
     * Get Emergency contacts for given employee
     * @param int $seqNo Employee Number
     * @param int $empNumber
     * @return EmpEmergencyContact|null
     * @throws DaoException
     */
    public function getEmployeeEmergencyContact( int $empNumber, int $seqNo): ?EmpEmergencyContact
    {
        return $this->getEmpEmergencyContactDao()->getEmployeeEmergencyContact( $empNumber,  $seqNo);
    }

    /**
     * @param int $empNumber
     * @return array
     * @throws DaoException
     */
    public function getEmployeeEmergencyContactList(int $empNumber): array
    {
        return $this->getEmpEmergencyContactDao()->getEmployeeEmergencyContactList($empNumber);
    }

    /**
     * @param EmpEmergencyContact $empEmergencyContact
     * @return EmpEmergencyContact
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveEmpEmergencyContact(EmpEmergencyContact $empEmergencyContact): EmpEmergencyContact
    {
        return $this->getEmpEmergencyContactDao()->saveEmployeeEmergencyContact($empEmergencyContact );
    }

    /**
     * Delete the given emergency contacts from the given employee
     * If $entriesToDelete is not provided (null), all entries of given employee
     * will be deleted.
     * @param int $empNumber Employee Number
     * @param array|null $sequenceNumbers Array of emergency contact sequence numbers. Optional.
     * @return int Number of records deleted
     * @throws DaoException
     */
    public function deleteEmployeeEmergencyContacts(int $empNumber, array $sequenceNumbers): int
    {
        return $this->getEmpEmergencyContactDao()->deleteEmployeeEmergencyContacts($empNumber, $sequenceNumbers);
    }

}
