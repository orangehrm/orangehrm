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

namespace OrangeHRM\Admin\Service;

use OrangeHRM\Admin\Dao\OrganizationDao;
use OrangeHRM\Entity\Organization;
use OrangeHRM\ORM\Exception\TransactionException;

class OrganizationService
{
    /**
     * @var OrganizationDao|null
     */
    private ?OrganizationDao $organizationDao = null;

    /**
     * @return OrganizationDao
     */
    public function getOrganizationDao(): OrganizationDao
    {
        if (is_null($this->organizationDao)) {
            $this->organizationDao = new OrganizationDao();
        }
        return $this->organizationDao;
    }

    /**
     * @param OrganizationDao $organizationDao
     */
    public function setOrganizationDao(OrganizationDao $organizationDao): void
    {
        $this->organizationDao = $organizationDao;
    }

    /**
     * Get organization general information
     *
     * @return Organization|null
     */
    public function getOrganizationGeneralInformation(): ?Organization
    {
        return $this->getOrganizationDao()->getOrganizationGeneralInformation();
    }

    /**
     * @param Organization $organization
     * @return Organization
     * @throws TransactionException
     */
    public function saveOrganizationGeneralInformation(Organization $organization): Organization
    {
        return $this->getOrganizationDao()->saveOrganizationGeneralInformation($organization);
    }
}
