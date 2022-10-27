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

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Table(name="ohrm_buzz_like_on_share")
 * @ORM\Entity
 */
class LikeOnShare
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Share
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Share")
     * @ORM\JoinColumn(name="share_id", referencedColumnName="id")
     */
    private Share $share;

    /**
     * @var Employee|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="likeOnShare", cascade={"persist"})
     * @ORM\JoinColumn(name="employee_number", referencedColumnName="emp_number", nullable=true)
     */
    private ?Employee $employee = null;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="like_time", type="datetime")
     */
    private DateTime $likeTime;

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
     * @return Share
     */
    public function getShare(): Share
    {
        return $this->share;
    }

    /**
     * @param Share $share
     */
    public function setShare(Share $share): void
    {
        $this->share = $share;
    }

    /**
     * @return Employee|null
     */
    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee|null $employee
     */
    public function setEmployee(?Employee $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return DateTime
     */
    public function getLikeTime(): DateTime
    {
        return $this->likeTime;
    }

    /**
     * @param DateTime $likeTime
     */
    public function setLikeTime(DateTime $likeTime): void
    {
        $this->likeTime = $likeTime;
    }
}
