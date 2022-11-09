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

namespace Orangehrm\Rest\Api\Leave\Entity;

use Orangehrm\Rest\Api\Entity\Serializable;

class LeaveBalance implements Serializable
{
    /**
     * @var null|float
     */
    private $entitled = null;

    /**
     * @var null|float
     */
    private $used = null;

    /**
     * @var null|float
     */
    private $scheduled = null;

    /**
     * @var null|float
     */
    private $pending = null;

    /**
     * @var null|float
     */
    private $notLinked = null;

    /**
     * @var null|float
     */
    private $taken = null;

    /**
     * @var null|float
     */
    private $adjustment = null;

    /**
     * @var null|float
     */
    private $balance = null;

    /**
     * LeaveBalance constructor.
     * @param \LeaveBalance $leaveBalance
     */
    public function __construct(\LeaveBalance $leaveBalance)
    {
        $this->entitled = (float)$leaveBalance->getEntitled();
        $this->used = (float)$leaveBalance->getUsed();
        $this->scheduled = (float)$leaveBalance->getScheduled();
        $this->pending = (float)$leaveBalance->getPending();
        $this->notLinked = (float)$leaveBalance->getNotLinked();
        $this->taken = (float)$leaveBalance->getTaken();
        $this->adjustment = (float)$leaveBalance->getAdjustment();
        $this->balance = (float)$leaveBalance->getBalance();
    }

    /**
     * @return float|null
     */
    public function getEntitled(): float
    {
        return $this->entitled;
    }

    /**
     * @param float|null $entitled
     */
    public function setEntitled(float $entitled)
    {
        $this->entitled = $entitled;
    }

    /**
     * @return float|null
     */
    public function getUsed(): float
    {
        return $this->used;
    }

    /**
     * @param float|null $used
     */
    public function setUsed(float $used)
    {
        $this->used = $used;
    }

    /**
     * @return float|null
     */
    public function getScheduled(): float
    {
        return $this->scheduled;
    }

    /**
     * @param float|null $scheduled
     */
    public function setScheduled(float $scheduled)
    {
        $this->scheduled = $scheduled;
    }

    /**
     * @return float|null
     */
    public function getPending(): float
    {
        return $this->pending;
    }

    /**
     * @param float|null $pending
     */
    public function setPending(float $pending)
    {
        $this->pending = $pending;
    }

    /**
     * @return float|null
     */
    public function getNotLinked(): float
    {
        return $this->notLinked;
    }

    /**
     * @param float|null $notLinked
     */
    public function setNotLinked(float $notLinked)
    {
        $this->notLinked = $notLinked;
    }

    /**
     * @return float|null
     */
    public function getTaken(): float
    {
        return $this->taken;
    }

    /**
     * @param float|null $taken
     */
    public function setTaken(float $taken)
    {
        $this->taken = $taken;
    }

    /**
     * @return float|null
     */
    public function getAdjustment(): float
    {
        return $this->adjustment;
    }

    /**
     * @param float|null $adjustment
     */
    public function setAdjustment(float $adjustment)
    {
        $this->adjustment = $adjustment;
    }

    /**
     * @return float|null
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @param float|null $balance
     */
    public function setBalance(float $balance)
    {
        $this->balance = $balance;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            "entitled" => $this->getEntitled(),
            "used" => $this->getUsed(),
            "scheduled" => $this->getScheduled(),
            "pending" => $this->getPending(),
            "notLinked" => $this->getNotLinked(),
            "taken" => $this->getTaken(),
            "adjustment" => $this->getAdjustment(),
            "balance" => $this->getBalance(),
        ];
    }
}
