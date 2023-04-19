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

namespace OrangeHRM\Claim\Service;

use OrangeHRM\Claim\Api\Traits\ClaimRequestAPIHelperTrait;
use OrangeHRM\Claim\Dao\ClaimDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\ClaimAttachment;

class ClaimService
{
    use DateTimeHelperTrait;
    use UserRoleManagerTrait;
    use ClaimRequestAPIHelperTrait;

    /**
     * @var ClaimDao
     */
    protected ClaimDao $claimDao;

    /**
     * @return ClaimDao
     */
    public function getClaimDao(): ClaimDao
    {
        return $this->claimDao ??= new ClaimDao();
    }

    /**
     * @return string
     */
    public function getReferenceId(): string
    {
        $nextId = $this->getClaimDao()->getNextId();
        $date = $this->getDateTimeHelper()->getNow()->format('Ymd');
        return $date . str_pad("$nextId", 7, 0, STR_PAD_LEFT);
    }

    /**
     * @param int $requestId
     * @param int $attachId
     * @return ClaimAttachment|null
     * @throws DaoException
     */
    public function getClaimAttachment(int $requestId, int $attachId): ?ClaimAttachment
    {
        return $this->getClaimDao()->getClaimAttachmentFile($requestId, $attachId);
    }

    /**
     * @param int $requestId
     * @param int $attachId
     * @return ClaimAttachment|null
     */
    public function getAccessibleClaimAttachment(int $requestId, int $attachId): ?ClaimAttachment
    {
        $this->getClaimRequest($requestId);
        return $this->getClaimAttachment($requestId, $attachId);
    }
}
