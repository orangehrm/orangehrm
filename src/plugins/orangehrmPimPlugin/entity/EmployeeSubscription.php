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

/**
 * EmployeeSubscription
 *
 * @ORM\Table(name="ohrm_employee_subscription")
 * @ORM\Entity
 */
class EmployeeSubscription
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="employee_id", type="integer", length=4)
     */
    private $employeeId;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", length=4)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="date", length=25)
     */
    private $createdAt;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Employee", mappedBy="EmployeeSubscription")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="employee_id", referencedColumnName="emp_number")
     * })
     */
    private $Employee;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Employee = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
