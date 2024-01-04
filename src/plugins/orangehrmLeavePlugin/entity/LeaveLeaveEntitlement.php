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

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\LeaveLeaveEntitlementDecorator;

/**
 * @method LeaveLeaveEntitlementDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_leave_leave_entitlement")
 * @ORM\Entity
 */
class LeaveLeaveEntitlement
{
    use DecoratorTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Leave
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Leave", inversedBy="leaveLeaveEntitlements")
     * @ORM\JoinColumn(name="leave_id", referencedColumnName="id")
     */
    private Leave $leave;

    /**
     * @var LeaveEntitlement
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\LeaveEntitlement", inversedBy="leaveLeaveEntitlements")
     * @ORM\JoinColumn(name="entitlement_id", referencedColumnName="id")
     */
    private LeaveEntitlement $entitlement;

    /**
     * @var float
     *
     * @ORM\Column(name="length_days", type="decimal", precision=6, scale=4, nullable=true, options={"unsigned" : true})
     */
    private float $lengthDays;

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
     * @return Leave
     */
    public function getLeave(): Leave
    {
        return $this->leave;
    }

    /**
     * @param Leave $leave
     */
    public function setLeave(Leave $leave): void
    {
        $this->leave = $leave;
    }

    /**
     * @return LeaveEntitlement
     */
    public function getEntitlement(): LeaveEntitlement
    {
        return $this->entitlement;
    }

    /**
     * @param LeaveEntitlement $entitlement
     */
    public function setEntitlement(LeaveEntitlement $entitlement): void
    {
        $this->entitlement = $entitlement;
    }

    /**
     * @return float
     */
    public function getLengthDays(): float
    {
        return $this->lengthDays;
    }

    /**
     * @param float $lengthDays
     */
    public function setLengthDays(float $lengthDays): void
    {
        $this->lengthDays = $lengthDays;
    }
}
