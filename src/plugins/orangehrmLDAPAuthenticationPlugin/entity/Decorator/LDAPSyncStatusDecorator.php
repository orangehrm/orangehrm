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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\LDAPSyncStatus;
use OrangeHRM\Entity\User;

class LDAPSyncStatusDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    protected LDAPSyncStatus $LDAPSyncStatus;

    /**
     * @param LDAPSyncStatus $LDAPSyncStatus
     */
    public function __construct(LDAPSyncStatus $LDAPSyncStatus)
    {
        $this->LDAPSyncStatus = $LDAPSyncStatus;
    }

    /**
     * @param int $userId
     */
    public function setSyncedUserByUserId(int $userId): void
    {
        $user = $this->getReference(User::class, $userId);
        $this->LDAPSyncStatus->setSyncedBy($user);
    }

    /**
     * @return string|null
     */
    public function getSyncStartedDate(): ?string
    {
        return $this->getDateTimeHelper()->formatDate($this->LDAPSyncStatus->getSyncStartedAt());
    }

    /**
     * @return string|null
     */
    public function getSyncStartedTime(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToTimeString($this->LDAPSyncStatus->getSyncStartedAt());
    }

    /**
     * @return string|null
     */
    public function getSyncFinishedDate(): ?string
    {
        return $this->getDateTimeHelper()->formatDate($this->LDAPSyncStatus->getSyncFinishedAt());
    }

    /**
     * @return string|null
     */
    public function getSyncFinishedTime(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToTimeString($this->LDAPSyncStatus->getSyncFinishedAt());
    }
}
