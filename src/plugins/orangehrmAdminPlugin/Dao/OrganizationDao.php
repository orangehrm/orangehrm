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

use DateTime;
use Exception;
use OrangeHRM\Authentication\Dto\OrganizationSetup;
use OrangeHRM\Authentication\Utils\RoleUtils;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Utility\PasswordHash;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Organization;
use OrangeHRM\Entity\Subunit;
use OrangeHRM\Entity\User;
use OrangeHRM\ORM\Exception\TransactionException;
use Throwable;

class OrganizationDao extends BaseDao
{
    use AuthUserTrait;

    /**
     * @return Organization|null
     * @throws DaoException
     */
    public function getOrganizationGeneralInformation(): ?Organization
    {
        try {
            $orgInfo = $this->findById($this->getAuthUser()->getOrgId());
            if ($orgInfo instanceof Organization) {
                return $orgInfo;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function findById(int $organizationId): ?Organization
    {
        return $this->getRepository(Organization::class)
            ->findOneBy(['id' => $organizationId]);
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
     * @throws TransactionException
     */
    public function setupNewOrganization(OrganizationSetup $organizationSetup)
    {
        // Organization Details
        $organization = Organization::instance();
        $organization->setName($organizationSetup->getOrganizationName());
        $organization->setCountry($organizationSetup->getCountryCode());

        // Subunit Details
        $subunit = new Subunit();
        $subunit->setLevel(0);
        $subunit->setName($organization->getName());
        $subunit->setLft(1);
        $subunit->setRgt(2);

        // Employee Details
        $employee = new Employee();
        $employee->setFirstName($organizationSetup->getFirstName());
        $employee->setLastName($organizationSetup->getLastName());

        // Role Details
        $adminRole = RoleUtils::admin();
        $essRole = RoleUtils::ess();

        $passwordHasher = new PasswordHash();
        $hashedPassword = $passwordHasher->hash($organizationSetup->getPassword());

        // User Details
        $user = new User();
        $user->setUserName($organizationSetup->getEmail());
        $user->setUserPassword($hashedPassword);
        $user->setDateEntered(new DateTime());

        $this->beginTransaction();
        try {
            $this->persist($organization);
            $subunit->setOrgId($organization->getId());
            $this->persist($subunit);
            $employee->setOrgId($organization->getId());
            $this->persist($employee);
            $adminRole->setOrgId($organization->getId());
            $essRole->setOrgId($organization->getId());

            // Roles
            $this->persist($essRole);
            $this->persist($adminRole);

            // User
            $user->setEmployee($employee);
            $user->setUserRole($adminRole);
            $user->setOrgId($organization->getId());
            $this->persist($user);

            $this->commitTransaction();
        } catch (Throwable $t) {
            $this->rollBackTransaction();
            throw new TransactionException($t);
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
