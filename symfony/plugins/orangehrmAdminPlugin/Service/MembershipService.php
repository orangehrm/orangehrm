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

namespace OrangeHRM\Admin\Service;

use OrangeHRM\Admin\Dao\MembershipDao;
use OrangeHRM\Admin\Dto\MembershipSearchFilterParams;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Membership;

class MembershipService
{
    /**
     * @var Membership|null
     */
    private ?MembershipDao $membershipDao = null;

    /**
     * @return Membership
     */
    public function getMembershipDao(): MembershipDao
    {
        if (!($this->membershipDao instanceof MembershipDao)) {
            $this->membershipDao = new MembershipDao();
        }
        return $this->membershipDao;
    }

    /**
     * @param MembershipDao $MembershipDao
     * @return void
     */
    public function setMembershipDao(MembershipDao $MembershipDao): void
    {
        $this->membershipDao = $MembershipDao;
    }

    public function saveMembership(Membership $membership): Membership
    {
        return $this->getMembershipDao()->saveMembership($membership);
    }

    /**
     * @param int $id
     * @return Membership|null
     */
    public function getMembershipById(int $id): ?Membership
    {
        return $this->getMembershipDao()->getMembershipById($id);
    }

    /**
     * @param string $name
     * @return Membership
     */
    public function getMembershipByName(string $name): ?Membership
    {
        return $this->getMembershipDao()->getMembershipByName($name);
    }

    /**
     * @param MembershipSearchFilterParams $membershipSearchParamsHolder
     * @return array
     */
    public function getMembershipList(MembershipSearchFilterParams $membershipSearchParamsHolder): array
    {
        return $this->getMembershipDao()->getMembershipList($membershipSearchParamsHolder);
    }

    /**
     * @param MembershipSearchFilterParams $membershipSearchParamsHolder
     * @return int
     */
    public function getMembershipCount(MembershipSearchFilterParams $membershipSearchParamsHolder): int
    {
        return $this->getMembershipDao()->getMembershipCount($membershipSearchParamsHolder);
    }


    /**
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteMemberships(array $toDeleteIds): int
    {
        return $this->getMembershipDao()->deleteMemberships($toDeleteIds);
    }

    /**
     * @param string $membershipName
     * @return bool
     */
    public function isExistingLicenseName(string $membershipName): bool
    {
        return $this->getMembershipDao()->isExistingMembershipName($membershipName);
    }



}

