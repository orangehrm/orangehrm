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

use OrangeHRM\Claim\Api\Model\ClaimEventModel;
use OrangeHRM\Claim\Dao\ClaimEventDao;
use OrangeHRM\Claim\Dto\ClaimEventSearchFilterParams;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\ClaimEvent;

class ClaimService
{
    use NormalizerServiceTrait;

    /**
     * @var ClaimEventDao|null
     */
    protected ?ClaimEventDao $claimEventDao = null;

    /**
     * @return ClaimEventDao|null
     */
    public function getClaimEventDao(): ?ClaimEventDao
    {
        if (is_null($this->claimEventDao)) {
            $this->claimEventDao = new ClaimEventDao();
        }
        return $this->claimEventDao;
    }

    /**
     * @param ClaimEventDao|null $claimEventDao
     */
    public function setClaimEventDao(?ClaimEventDao $claimEventDao): void
    {
        $this->claimEventDao = $claimEventDao;
    }

    /**
     * @param ClaimEvent $claimEvent
     * @return ClaimEvent
     */
    public function saveEvent(ClaimEvent $claimEvent): ClaimEvent
    {
        return $this->getClaimEventDao()->saveEvent($claimEvent);
    }

    /**
     * @param ClaimEventSearchFilterParams $claimEventSearchFilterParams
     * @return ClaimEvent[]
     */
    public function getClaimEventList(ClaimEventSearchFilterParams $claimEventSearchFilterParams): array
    {
        return $this->getClaimEventDao()->getClaimEventList($claimEventSearchFilterParams);
    }

    /**
     * @return array
     */
    public function getClaimEventArray(): array
    {
        $claimEvents = $this->getClaimEventList();
        return $this->getNormalizerService()->normalizeArray(ClaimEventModel::class, $claimEvents);
    }

    public function getClaimEventByID(int $id): ClaimEvent
    {
        return $this->getClaimEventDao()->getClaimEventByID($id);
    }

    public function deleteClaimEvent(int $id): ClaimEvent
    {
        return $this->getClaimEventDao()->deleteClaimEvent($id);
    }
}
