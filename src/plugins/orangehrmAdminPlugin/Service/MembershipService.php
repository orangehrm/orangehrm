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
use OrangeHRM\Admin\Service\Model\MembershipModel;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\Membership;

class MembershipService
{
    use NormalizerServiceTrait;

    /**
     * @var MembershipDao|null
     */
    private ?MembershipDao $membershipDao = null;

    /**
     * @return MembershipDao
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

    /**
     * @param Membership $membership
     * @return Membership
     * @throws DaoException
     */
    public function saveMembership(Membership $membership): Membership
    {
        return $this->getMembershipDao()->saveMembership($membership);
    }

    /**
     * @param int $id
     * @return Membership|null
     * @throws DaoException
     */
    public function getMembershipById(int $id): ?Membership
    {
        return $this->getMembershipDao()->getMembershipById($id);
    }

    /**
     * @param string $name
     * @return Membership|null
     * @throws DaoException
     */
    public function getMembershipByName(string $name): ?Membership
    {
        return $this->getMembershipDao()->getMembershipByName($name);
    }

    /**
     * @param MembershipSearchFilterParams $membershipSearchParamsHolder
     * @return array
     * @throws DaoException
     */
    public function getMembershipList(MembershipSearchFilterParams $membershipSearchParamsHolder): array
    {
        return $this->getMembershipDao()->getMembershipList($membershipSearchParamsHolder);
    }

    /**
     * @param MembershipSearchFilterParams $membershipSearchParamsHolder
     * @return int
     * @throws DaoException
     */
    public function getMembershipCount(MembershipSearchFilterParams $membershipSearchParamsHolder): int
    {
        return $this->getMembershipDao()->getMembershipCount($membershipSearchParamsHolder);
    }

    /**
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteMemberships(array $toDeleteIds): int
    {
        return $this->getMembershipDao()->deleteMemberships($toDeleteIds);
    }

    /**
     * @param string $membershipName
     * @return bool
     * @throws DaoException
     */
    public function isExistingMembershipName(string $membershipName): bool
    {
        return $this->getMembershipDao()->isExistingMembershipName($membershipName);
    }

    /**
     * @return array
     * @throws DaoException
     */
    public function getMembershipArray(): array
    {
        $membershipSearchParamsHolder = new MembershipSearchFilterParams();
        $membershipSearchParamsHolder->setLimit(0);
        $memberships = $this->getMembershipList($membershipSearchParamsHolder);
        return $this->getNormalizerService()->normalizeArray(MembershipModel::class, $memberships);
    }
}
