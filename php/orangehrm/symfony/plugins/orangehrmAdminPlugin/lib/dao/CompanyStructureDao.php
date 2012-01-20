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
class CompanyStructureDao extends BaseDao {

    public function getSubunitById($id) {
        try {
            return Doctrine::getTable('Subunit')->find($id);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function saveSubunit(Subunit $subunit) {
        try {
            if ($subunit->getId() == '') {
                $subunit->setId(0);
            } else {
                $tempObj = Doctrine::getTable('Subunit')->find($subunit->getId());

                $tempObj->setName($subunit->getName());
                $tempObj->setDescription($subunit->getDescription());
                $tempObj->setUnitId($subunit->getUnitId());
                $subunit = $tempObj;
            }

            $subunit->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function addSubunit(Subunit $parentSubunit, Subunit $subunit) {
        try {
            $subunit->setId(0);
            $subunit->getNode()->insertAsLastChildOf($parentSubunit);

            $parentSubunit->setRgt($parentSubunit->getRgt() + 2);
            $parentSubunit->save();

            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function deleteSubunit(Subunit $subunit) {
        try {
            $q = Doctrine_Query::create()
                            ->delete('Subunit')
                            ->where('lft >= ?', $subunit->getLft())
                            ->andWhere('rgt <= ?', $subunit->getRgt());
            $q->execute();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function setOrganizationName($name) {
        try {
            $q = Doctrine_Query:: create()->update('Subunit')
                            ->set('name', '?', $name)
                            ->where('id = 1');
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getSubunitTreeObject() {
        try {
            return Doctrine::getTable('Subunit')->getTree();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

}
