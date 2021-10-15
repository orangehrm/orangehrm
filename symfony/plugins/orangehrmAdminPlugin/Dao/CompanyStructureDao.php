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

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Subunit;

class CompanyStructureDao extends BaseDao
{
    /**
     * @param int $id
     * @return Subunit|null
     */
    public function getSubunitById(int $id): ?Subunit
    {
        return $this->getRepository(Subunit::class)->find($id);
    }

    /**
     * @param Subunit $subunit
     * @return Subunit
     */
    public function saveSubunit(Subunit $subunit): Subunit
    {
        $this->persist($subunit);
        return $subunit;
    }

    /**
     * @param Subunit $parentSubunit
     * @param Subunit $subunit
     */
    public function addSubunit(Subunit $parentSubunit, Subunit $subunit): void
    {
        $parentSubunit->getNode()->addChild($subunit);
        $this->getEntityManager()->clear(Subunit::class);
    }

    /**
     * @param Subunit $subunit
     */
    public function deleteSubunit(Subunit $subunit): void
    {
        $subunit->getNode()->delete();
    }

    /**
     * @param string $name
     * @return int
     */
    public function setOrganizationName(string $name): int
    {
        $q = $this->createQueryBuilder(Subunit::class, 'su');
        $q->update()
            ->set('su.name', ':name')
            ->setParameter('name', $name)
            ->where('su.level = :level')
            ->setParameter('level', 0);
        return $q->getQuery()->execute();
    }

    /**
     * @param int|null $depth
     * @return array|Subunit[]
     */
    public function getSubunitTree(?int $depth = null): array
    {
        return Subunit::fetchTree($depth);
    }

    /**
     * @return int
     */
    public function getMaxLevel(): int
    {
        $q = $this->createQueryBuilder(Subunit::class, 's');
        $q->select($q->expr()->max('s.level'));
        return $q->getQuery()->getSingleScalarResult();
    }
}
