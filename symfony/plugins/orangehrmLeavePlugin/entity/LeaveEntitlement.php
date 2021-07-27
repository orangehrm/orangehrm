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

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_leave_entitlement")
 * @ORM\Entity
 */
class LeaveEntitlement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=10, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var string
     *
     * @ORM\Column(name="no_of_days", type="decimal", precision=19, scale=15)
     */
    private string $noOfDays;

    /**
     * @var string
     *
     * @ORM\Column(name="days_used", type="decimal", precision=8, scale=4, options={"default":0.0000})
     */
    private string $daysUsed;

    /**
     * @var LeaveType
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\LeaveType")
     * @ORM\JoinColumn(name="leave_type_id", referencedColumnName="id")
     */
    private LeaveType $leaveType;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="from_date", type="datetime")
     */
    private DateTime $fromDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="to_date", type="datetime")
     */
    private DateTime $toDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="credited_date", type="datetime")
     */
    private DateTime $creditedDate;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=255)
     */
    private string $note;

    /**
     * @var LeaveEntitlementType
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\LeaveEntitlementType")
     * @ORM\JoinColumn(name="entitlement_type", referencedColumnName="id")
     */
    private LeaveEntitlementType $entitlementType;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", options={"default":0})
     */
    private bool $deleted;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\User")
     * @ORM\JoinColumn(name="created_by_id", referencedColumnName="id")
     */
    private User $createdBy;
}
