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

namespace OrangeHRM\Leave\Entitlement;

use DateTime;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Leave\Traits\Service\LeaveConfigServiceTrait;

class LeaveBalance
{
    use LeaveConfigServiceTrait;
    use DateTimeHelperTrait;

    private float $entitled;
    private float $used;
    private float $scheduled;
    private float $pending;
    private float $taken;
    private float $balance;

    private ?DateTime $asAtDate = null;
    private ?DateTime $endDate = null;

    /**
     * @param float $entitled
     * @param float $used
     * @param float $scheduled
     * @param float $pending
     * @param float $taken
     */
    public function __construct(
        float $entitled = 0,
        float $used = 0,
        float $scheduled = 0,
        float $pending = 0,
        float $taken = 0
    ) {
        $this->entitled = $entitled;
        $this->used = $used;
        $this->scheduled = $scheduled;
        $this->pending = $pending;
        $this->taken = $taken;
        $this->updateBalance();
    }

    public function updateBalance(): void
    {
        $balance = $this->entitled - ($this->scheduled + $this->taken);
        $includePending = $this->getLeaveConfigService()->includePendingLeaveInBalance();

        if ($includePending) {
            $balance = $balance - $this->pending;
        }

        $this->balance = $balance;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @return float
     */
    public function getEntitled(): float
    {
        return $this->entitled;
    }

    /**
     * @param float $entitled
     */
    public function setEntitled(float $entitled): void
    {
        $this->entitled = $entitled;
    }

    /**
     * @return float
     */
    public function getUsed(): float
    {
        return $this->used;
    }

    /**
     * @param float $used
     */
    public function setUsed(float $used): void
    {
        $this->used = $used;
    }

    /**
     * @return float
     */
    public function getScheduled(): float
    {
        return $this->scheduled;
    }

    /**
     * @param float $scheduled
     */
    public function setScheduled(float $scheduled): void
    {
        $this->scheduled = $scheduled;
    }

    /**
     * @return float
     */
    public function getPending(): float
    {
        return $this->pending;
    }

    /**
     * @param float $pending
     */
    public function setPending(float $pending): void
    {
        $this->pending = $pending;
    }

    /**
     * @return float
     */
    public function getTaken(): float
    {
        return $this->taken;
    }

    /**
     * @param float $taken
     */
    public function setTaken(float $taken): void
    {
        $this->taken = $taken;
    }

    /**
     * @return DateTime|null
     */
    public function getAsAtDate(): ?DateTime
    {
        return $this->asAtDate;
    }

    /**
     * @return string|null
     */
    public function getYmdAsAtDate(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd($this->getAsAtDate());
    }

    /**
     * @param DateTime|null $asAtDate
     */
    public function setAsAtDate(?DateTime $asAtDate): void
    {
        $this->asAtDate = $asAtDate;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @return string|null
     */
    public function getYmdEndDate(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd($this->getEndDate());
    }

    /**
     * @param DateTime|null $endDate
     */
    public function setEndDate(?DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }
}
