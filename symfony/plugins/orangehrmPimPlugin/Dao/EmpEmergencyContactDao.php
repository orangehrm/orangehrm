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
use OrangeHRM\Entity\Employee;
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

            $employee = $this->getReference(Employee::class,$empNumber);
            $empEmergencyContact->setEmployee($employee);
            $this->persist($empEmergencyContact) ;  //there should be a filter to check if empNumber matches with the right value
            return $empEmergencyContact;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get Emergency contacts for given employee
     * @param int $seqNo
     * @param int $empNumber Employee Number
     * @return EmpEmergencyContact|null EmpEmergencyContact objects as array
     * @throws DaoException
     */
    public function getEmployeeEmergencyContacts(int $seqNo, int $empNumber): ?EmpEmergencyContact
    {
        try {
//            $empEmergencyContact = $this->getRepository(EmpEmergencyContact::class)->find($empNumber);
//            if ($empEmergencyContact  instanceof EmpEmergencyContact) {
//                return $empEmergencyContact;
//            }
//            return null;
//            $query = $this->createQueryBuilder(EmpEmergencyContact::class, 'ec') ;
//            $query->andWhere('ec.empNumber = :empNumber');
//            //$query->andWhere('ec.seqNo = :seqNo');
//           // $query->setParameter('seqNo',1);
//            $query->setParameter('empNumber',1);
//            return $query->getQuery()->getOneOrNullResult();

            $empEmergencyContact = $this->getEntityManager()->getRepository(EmpEmergencyContact::class)->findOneBy(['seqNo'=> $seqNo, 'empNumber' => $empNumber]);
            if ($empEmergencyContact  instanceof EmpEmergencyContact) {
                return $empEmergencyContact;
            }
            return null;
//            $q = Doctrine_Query:: create()->from('EmpEmergencyContact ec')
//                ->where('ec.emp_number = ?', $empNumber)
//                ->orderBy('ec.name ASC');
//            return $q->execute();

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Delete Emergency contacts
     * @param int $empNumber
     * @param array|null $entriesToDelete
     * @return int
     * @throws DaoException
     */
    public function deleteEmployeeEmergencyContacts(int $empNumber, array $entriesToDelete = null): int
    {

        try {
            $q = $this->createQueryBuilder(EmpEmergencyContact::class, 'ec');
            $q->delete()
                ->where('ec.empNumber = :empNumber')
                ->andWhere($q->expr()->in('ec.empNumber', ':empNumbers'))
                ->setParameter('empNumbers', $entriesToDelete);
//            $q = Doctrine_Query::create()->delete('EmpEmergencyContact')
//                ->where('emp_number = ?', $empNumber);

            if (is_array($entriesToDelete) && count($entriesToDelete) > 0) {
//              $q->whereIn('seqNo', $entriesToDelete);
                $q = $this->createQueryBuilder(EmpEmergencyContact::class, 'ec');
                $q->delete()
                    ->andWhere($q->expr()->in('ec.seqNo', ':seqNos'))
                    ->setParameter('seqNos', $entriesToDelete);
            }

            return $q->getQuery()->execute();

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

        if (!empty($emergencyContactSearchFilterParams->getEmpNumber())) {
            $q->andWhere('ec.empNumber = :empNumber');
            $q->setParameter('empNumber', $emergencyContactSearchFilterParams->getEmpNumber());
        }
//        if (!empty($emergencyContactSearchFilterParams->getName())) {
//            $q->andWhere('ec.name = :name');
//            $q->setParameter('name', $emergencyContactSearchFilterParams->getName());
//        }
//        if (!empty($emergencyContactSearchFilterParams->getRelationship())) {
//            $q->andWhere('ec.relationship = :relationship');
//            $q->setParameter('relationship', $emergencyContactSearchFilterParams->getRelationship());
//        }
//        if (!empty($emergencyContactSearchFilterParams->getHomePhone())) {
//            $q->andWhere('ec.homePhone = :homePhone');
//            $q->setParameter('homePhone', $emergencyContactSearchFilterParams->getHomePhone());
//        }
//        if (!empty($emergencyContactSearchFilterParams->getOfficePhone())) {
//            $q->andWhere('ec.officePhone = :officePhone');
//            $q->setParameter('officePhone', $emergencyContactSearchFilterParams->getOfficePhone());
//        }
//        if (!empty($emergencyContactSearchFilterParams->getMobilePhone())) {
//            $q->andWhere('ec.mobilePhone = :mobilePhone');
//            $q->setParameter('officePhone', $emergencyContactSearchFilterParams->getMobilePhone());
//        }
//        return $this->Paginator($q);
        return new Paginator($q);
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
