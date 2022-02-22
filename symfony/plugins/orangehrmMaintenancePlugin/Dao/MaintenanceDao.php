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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\Maintenance\Dao;
use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
//use Doctrine_Collection;
//use Doctrine_Query;
use OrangeHRM\Entity\Employee;

/**
 * Class MaintenanceDao
 */
class MaintenanceDao extends BaseDao
{

    /**
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function getEmployeePurgingList():array
    {
        try {
            $q = Doctrine_Query::create()
                ->select('empNumber', 'firstName', 'middleName', 'lastName')
                ->from('Employee')
                ->where('termination_id IS NOT NULL')
                ->andwhere('purged_at IS NULL');
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param $empNumber
     * @param $table
     * @return mixed
     * @throws DaoException
     */
    public function extractDataFromEmpNumber($matchByValues, $table):array
    {
        $tableName='OrangeHRM\Entity'."\\".$table;

        $employeeId = reset($matchByValues);
        $field = key($matchByValues);

        $q=$this->createQueryBuilder($tableName,'e');
        if(in_array('join',$matchByValues)){
            $table2 = $matchByValues['join'];
            $q->select('e.*')
                ->innerJoin('l.' . $table2, 't')
                ->andWhere('e. '.$field ."=:empId")
                ->setParameter('empId',$employeeId);
        }else{
            $q->andWhere('e.'.$field ."=:empId")->setParameter('empId',$employeeId);
        }
        return $q->getQuery()->execute();

    }

    /**
     * @param $enitity
     * @return bool
     * @throws DaoException
     */
    public function saveEntity($entity):bool
    {
        try {
            $entity->save();
            return $entity;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function getVacancyListToPurge()
    {
        try {
            $q = Doctrine_Query::create()
                ->select('id', 'name')
                ->from('JobVacancy');
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @throws DaoException
     */
    public function getDeniedCandidatesToKeepDataByVacnacyId($vacancyId)
    {
        try {
            $q = Doctrine_Query::create()
                ->select('*')
                ->from('JobCandidate l')
                ->innerJoin('l.CandidateHistory t')
                ->where("l.consentToKeepData = ?", false)
                ->andWhere("t.vacancyId = ?", $vacancyId);
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd

    }
}
