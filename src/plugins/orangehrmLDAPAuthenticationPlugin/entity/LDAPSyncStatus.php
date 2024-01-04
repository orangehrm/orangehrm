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

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\LDAPSyncStatusDecorator;

/**
 * @method LDAPSyncStatusDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_ldap_sync_status")
 * @ORM\Entity
 */
class LDAPSyncStatus
{
    use DecoratorTrait;

    public const SYNC_STATUS_FAILED = 0;
    public const SYNC_STATUS_SUCCEEDED = 1;

    //If LDAP is not configured, the default last sync status not available
    public const SYNC_STATUS_NOT_AVAILABLE = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="sync_started_at", type="datetime", nullable=false)
     */
    private ?DateTime $syncStartedAt = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="sync_finished_at", type="datetime", nullable=true)
     */
    private ?DateTime $syncFinishedAt = null;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\User")
     * @ORM\JoinColumn(name="synced_by", referencedColumnName="id")
     */
    private ?User $syncedBy = null;

    /**
     * @var int
     *
     * @ORM\Column(name="sync_status", type="integer")
     */
    private int $syncStatus = self::SYNC_STATUS_NOT_AVAILABLE;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return DateTime|null
     */
    public function getSyncStartedAt(): ?DateTime
    {
        return $this->syncStartedAt;
    }

    /**
     * @param DateTime|null $syncStartedAt
     */
    public function setSyncStartedAt(?DateTime $syncStartedAt): void
    {
        $this->syncStartedAt = $syncStartedAt;
    }

    /**
     * @return DateTime|null
     */
    public function getSyncFinishedAt(): ?DateTime
    {
        return $this->syncFinishedAt;
    }

    /**
     * @param DateTime|null $syncFinishedAt
     */
    public function setSyncFinishedAt(?DateTime $syncFinishedAt): void
    {
        $this->syncFinishedAt = $syncFinishedAt;
    }

    /**
     * @return User|null
     */
    public function getSyncedBy(): ?User
    {
        return $this->syncedBy;
    }

    /**
     * @param User|null $syncedBy
     */
    public function setSyncedBy(?User $syncedBy): void
    {
        $this->syncedBy = $syncedBy;
    }

    /**
     * @return int
     */
    public function getSyncStatus(): int
    {
        return $this->syncStatus;
    }

    /**
     * @param int $syncStatus
     */
    public function setSyncStatus(int $syncStatus): void
    {
        $this->syncStatus = $syncStatus;
    }
}
