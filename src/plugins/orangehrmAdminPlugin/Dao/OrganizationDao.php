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

namespace OrangeHRM\Admin\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Organization;
use OrangeHRM\Entity\Subunit;
use OrangeHRM\ORM\Exception\TransactionException;

class OrganizationDao extends BaseDao
{
    /**
     * @return Organization|null
     */
    public function getOrganizationGeneralInformation(): ?Organization
    {
        $orgInfo = $this->getRepository(Organization::class)->find(1);
        if ($orgInfo instanceof Organization) {
            return $orgInfo;
        }
        return null;
    }

    /**
     * @param Organization $organization
     * @return Organization
     * @throws TransactionException
     */
    public function saveOrganizationGeneralInformation(Organization $organization): Organization
    {
        $this->beginTransaction();
        try {
            $this->persist($organization);
            $this->updateOrganizationStructure($organization);
            $this->commitTransaction();
            return $organization;
        } catch (Exception $exception) {
            $this->rollBackTransaction();
            throw new TransactionException($exception);
        }
    }

    /**
     * @param Organization $organization
     * @return void
     */
    private function updateOrganizationStructure(Organization $organization): void
    {
        $baseUnit = $this->getRepository(Subunit::class)->findOneBy(['level' => 0]);
        /** @var Subunit $baseUnit */
        $baseUnit->setName($organization->getName());
        $this->persist($baseUnit);
    }
}
