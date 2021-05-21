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

namespace OrangeHRM\Pim\Dao;

use Doctrine\ORM\QueryBuilder;
use OrangeHRM\ORM\Doctrine;
use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmpEmergencyContact;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\EmpEmergencyContactSearchFilterParams;

class EmpEmergencyContactDao extends BaseDao
{

    public function saveEmployeeEmergencyContacts(int $empNumber ,EmpEmergencyContact $empEmergencyContact): EmpEmergencyContact
    {
        try {
            $this->persist($empEmergencyContact);  //there should be a filter to check if empnumber matches with the right value
            return $empEmergencyContact;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get Emergency contacts for given employee
     * @param int $empNumber Employee Number
     * @return array EmpEmergencyContact objects as array
     */
    public function getEmployeeEmergencyContacts(int $empNumber): ?EmpEmergencyContact
    {

        try {
            $q = Doctrine_Query:: create()->from('EmpEmergencyContact ec')
                ->where('ec.emp_number = ?', $empNumber)
                ->orderBy('ec.name ASC');
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Delete Emergency contacts
     * @param int $empNumber
     * @param array $entriesToDelete
     * @returns integer
     * @throws DaoException
     */
    public function deleteEmployeeEmergencyContacts(int $empNumber, array $entriesToDelete = null): int
    {

        try {

            $q = Doctrine_Query::create()->delete('EmpEmergencyContact')
                ->where('emp_number = ?', $empNumber);

            if (is_array($entriesToDelete) && count($entriesToDelete) > 0) {
                $q->whereIn('seqno', $entriesToDelete);
            }

            return $q->execute();

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd

    }

    public function searchEmployeeEmergencyContacts(EmpEmergencyContactSearchFilterParams $emergencyContactSearchFilterParams)
    {
        try {
            $paginator = $this->getSearchEmployeeEmergencyContactsPaginator($emergencyContactSearchFilterParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function getSearchEmployeeEmergencyContactsPaginator(EmpEmergencyContactSearchFilterParams $emergencyContactSearchFilterParams): Paginator
    {
        $q = $this->createQueryBuilder(EmpEmergencyContact::class, 'ec');
        $this->setSortingAndPaginationParams($q, $emergencyContactSearchFilterParams);

        if (!empty($emergencyContactSearchFilterParams->getName())) {
            $q->andWhere('ec.name = :name');
            $q->setParameter('name', $emergencyContactSearchFilterParams->getName());
        }
        if (!empty($emergencyContactSearchFilterParams->getRelationship())) {
            $q->andWhere('ec.relationship = :relationship');
            $q->setParameter('relationship', $emergencyContactSearchFilterParams->getRelationship());
        }
        if (!empty($emergencyContactSearchFilterParams->getHomePhone())) {
            $q->andWhere('ec.home_phone = :home_phone');
            $q->setParameter('home_phone', $emergencyContactSearchFilterParams->getHomePhone());
        }
        if (!empty($emergencyContactSearchFilterParams->getOfficePhone())) {
            $q->andWhere('ec.office_phone = :office_phone');
            $q->setParameter('office_phone', $emergencyContactSearchFilterParams->getOfficePhone());
        }
        if (!empty($emergencyContactSearchFilterParams->getMobilePhone())) {
            $q->andWhere('ec.mobile_phone = :mobile_phone');
            $q->setParameter('mobile_phone', $emergencyContactSearchFilterParams->getMobilePhone());
        }
        return $this->getPaginator($q);
    }

    public function getSearchEmployeeEmergencyContactsCount(EmpEmergencyContactSearchFilterParams $emergencyContactSearchFilterParams):int
    {
        try {
            $paginator = $this->getSearchEmployeeEmergencyContactsPaginator($emergencyContactSearchFilterParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
