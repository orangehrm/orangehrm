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

/**
 * Class PurgeDao
 */
class MaintenanceDao extends BaseDao
{
    /**
     * @param $entityClassName
     * @param $fieldValueArray
     * @param $matchByValuesArray
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function replaceEntityValues($entityClassName, $fieldValueArray, $matchByValuesArray)
    {
        try {
            $q = Doctrine_Query::create()
                ->update($entityClassName);

            foreach ($fieldValueArray as $field => $value) {
                if (is_null($value)) {
                    $q->set($field, new Doctrine_Expression('NULL'));
                } else {
                    $q->set($field, "?", $value);
                }
            }
            foreach ($matchByValuesArray as $field => $value) {
                if (is_array($value)) {
                    $q->andWhereIn($field, $value);
                } else {
                    $q->andWhere($field . " = ?", $value);
                }
            }
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd

    }

    /**
     * @param $entityClassName
     * @param $matchValuesArray
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function removeEntities($entityClassName, $matchValuesArray)
    {
        try {
            $q = Doctrine_Query::create()
                ->delete($entityClassName);
            foreach ($matchValuesArray as $field => $value) {
                if (is_array($value)) {
                    $q->whereIn($field, $value);
                } else {
                    $q->where($field . " = ?", $value);
                }
            }
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function getEmployeePurgingList()
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
}
