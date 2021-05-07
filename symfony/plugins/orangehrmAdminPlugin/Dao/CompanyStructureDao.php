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

namespace OrangeHRM\Admin\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Subunit;

class CompanyStructureDao extends BaseDao
{
    /**
     * @param int $id
     * @return Subunit|null
     * @throws DaoException
     */
    public function getSubunitById(int $id): ?Subunit
    {
        try {
            $subUnit = $this->getRepository(Subunit::class)->find($id);
            if ($subUnit instanceof Subunit) {
                return $subUnit;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param Subunit $subunit
     * @return Subunit
     * @throws DaoException
     */
    public function saveSubunit(Subunit $subunit): Subunit
    {
        try {
            $this->persist($subunit);
            return $subunit;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param Subunit $parentSubunit
     * @param Subunit $subunit
     * @return Subunit
     * @throws DaoException
     */
    public function addSubunit(Subunit $parentSubunit, Subunit $subunit): Subunit
    {
        try {
            $parentSubunit->getNode()->addChild($subunit);
            return $subunit;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param Subunit $subunit
     * @throws DaoException
     */
    public function deleteSubunit(Subunit $subunit): void
    {
        try {
            $subunit->getNode()->delete();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $name
     * @return int
     * @throws DaoException
     */
    public function setOrganizationName(string $name): int
    {
        try {
            $q = $this->createQueryBuilder(Subunit::class, 'su');
            $q->update()
                ->set('su.name', ':name')
                ->setParameter('name', $name)
                ->where('su.level = :level')
                ->setParameter('level', 0);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int|null $depth
     * @return array|Subunit[]
     * @throws DaoException
     */
    public function getSubunitTree(?int $depth = null): array
    {
        try {
            return Subunit::fetchTree($depth);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
