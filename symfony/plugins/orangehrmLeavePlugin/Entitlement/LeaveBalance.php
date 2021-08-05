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

use OrangeHRM\Leave\Traits\Service\LeaveConfigServiceTrait;

class LeaveBalance
{
    use LeaveConfigServiceTrait;

    private int $entitled;
    private int $used;
    private int $scheduled;
    private int $pending;
    private int $notLinked;
    private int $taken;
    private int $adjustment;
    private int $balance;

    /**
     * @param int $entitled
     * @param int $used
     * @param int $scheduled
     * @param int $pending
     * @param int $notLinked
     * @param int $taken
     * @param int $adjustment
     */
    public function __construct(
        int $entitled = 0,
        int $used = 0,
        int $scheduled = 0,
        int $pending = 0,
        int $notLinked = 0,
        int $taken = 0,
        int $adjustment = 0
    ) {
        $this->entitled = $entitled;
        $this->used = $used;
        $this->scheduled = $scheduled;
        $this->pending = $pending;
        $this->notLinked = $notLinked;
        $this->taken = $taken;
        $this->adjustment = $adjustment;
        $this->updateBalance();
    }

    public function updateBalance(): void
    {
        $balance = ($this->entitled + $this->adjustment) - ($this->scheduled + $this->taken);
        $includePending = $this->getLeaveConfigService()->includePendingLeaveInBalance();

        if ($includePending) {
            $balance = $balance - $this->pending;
        }

        $this->balance = $balance;
    }

    /**
     * @return int
     */
    public function getAdjustment(): int
    {
        return $this->adjustment;
    }

    /**
     * @param int $adjustment
     */
    public function setAdjustment(int $adjustment): void
    {
        $this->adjustment = $adjustment;
    }

    /**
     * @return int
     */
    public function getBalance(): int
    {
        return $this->balance;
    }

    /**
     * @return int
     */
    public function getEntitled(): int
    {
        return $this->entitled;
    }

    /**
     * @param int $entitled
     */
    public function setEntitled(int $entitled): void
    {
        $this->entitled = $entitled;
    }

    /**
     * @return int
     */
    public function getUsed(): int
    {
        return $this->used;
    }

    /**
     * @param int $used
     */
    public function setUsed(int $used): void
    {
        $this->used = $used;
    }

    /**
     * @return int
     */
    public function getScheduled(): int
    {
        return $this->scheduled;
    }

    /**
     * @param int $scheduled
     */
    public function setScheduled(int $scheduled): void
    {
        $this->scheduled = $scheduled;
    }

    /**
     * @return int
     */
    public function getPending(): int
    {
        return $this->pending;
    }

    /**
     * @param int $pending
     */
    public function setPending(int $pending): void
    {
        $this->pending = $pending;
    }

    /**
     * @return int
     */
    public function getNotLinked(): int
    {
        return $this->notLinked;
    }

    /**
     * @param int $notLinked
     */
    public function setNotLinked(int $notLinked): void
    {
        $this->notLinked = $notLinked;
    }

    /**
     * @return int
     */
    public function getTaken(): int
    {
        return $this->taken;
    }

    /**
     * @param int $taken
     */
    public function setTaken(int $taken): void
    {
        $this->taken = $taken;
    }
}
