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
use OrangeHRM\Entity\Decorator\PerformanceTrackerLogDecorator;

/**
 * @method PerformanceTrackerLogDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_performance_tracker_log")
 * @ORM\Entity
 */
class PerformanceTrackerLog
{
    use DecoratorTrait;

    public const POSITIVE_ACHIEVEMENT = 1;
    public const NEGATIVE_ACHIEVEMENT = 2;

    public const STATUS_NOT_DELETED = 1;
    public const STATUS_DELETED = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=11)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;


    /**
     * @var string|null
     *
     * @ORM\Column(name="log", type="string", length=150, nullable=true)
     */
    private ?string $log;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="string", length=3000, nullable=true)
     */
    private ?string $comment;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="integer", length=11, nullable=true)
     */
    private ?int $status;

    /**
     * @var string|null
     *
     * @ORM\Column(name="achievement", type="string", length=45, nullable=true)
     */
    private ?string $achievement;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="added_date", type="datetime", nullable=true)
     */
    private ?DateTime $addedDate = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="modified_date", type="datetime", nullable=true)
     */
    private ?DateTime $modifiedDate = null;


    /**
     * @var PerformanceTracker|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\PerformanceTracker")
     * @ORM\JoinColumn(name="performance_track_id", referencedColumnName="id")
     *
     */
    private ?PerformanceTracker $performanceTracker;

    /**
     * @var Employee|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="reviewer_id", referencedColumnName="emp_number")
     *
     */
    private ?Employee $employee;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     */
    private ?User $user;

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
     * @return string|null
     */
    public function getLog(): ?string
    {
        return $this->log;
    }

    /**
     * @param string|null $log
     */
    public function setLog(?string $log): void
    {
        $this->log = $log;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     */
    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string|null
     */
    public function getAchievement(): ?string
    {
        return $this->achievement;
    }

    /**
     * @param string|null $achievement
     */
    public function setAchievement(?string $achievement): void
    {
        $this->achievement = $achievement;
    }

    /**
     * @return DateTime|null
     */
    public function getAddedDate(): ?DateTime
    {
        return $this->addedDate;
    }

    /**
     * @param DateTime|null $addedDate
     */
    public function setAddedDate(?DateTime $addedDate): void
    {
        $this->addedDate = $addedDate;
    }

    /**
     * @return DateTime|null
     */
    public function getModifiedDate(): ?DateTime
    {
        return $this->modifiedDate;
    }

    /**
     * @param DateTime|null $modifiedDate
     */
    public function setModifiedDate(?DateTime $modifiedDate): void
    {
        $this->modifiedDate = $modifiedDate;
    }

    /**
     * @return PerformanceTracker|null
     */
    public function getPerformanceTracker(): ?PerformanceTracker
    {
        return $this->performanceTracker;
    }

    /**
     * @param PerformanceTracker|null $performanceTracker
     */
    public function setPerformanceTracker(?PerformanceTracker $performanceTracker): void
    {
        $this->performanceTracker = $performanceTracker;
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
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
