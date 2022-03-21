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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\PerformanceTrackerDecorator;

/**
 * @method PerformanceTrackerDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_performance_track")
 * @ORM\Entity
 */
class PerformanceTracker
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
     * @var string
     *
     * @ORM\Column(name="tracker_name", type="string", length=200)
     */
    private string $trackerName;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="added_date", type="datetime")
     */
    private DateTime $addedDate;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="modified_date", type="datetime", nullable=true)
     */
    private ?DateTime $modifiedDate = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private ?int $status = null;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="added_by", referencedColumnName="emp_number")
     */
    private Employee $addedBy;

    /**
     * @var PerformanceTrackerReviewer[]
     *
     * @ORM\OneToMany (targetEntity="OrangeHRM\Entity\PerformanceTrackerReviewer", mappedBy="performanceTracker")
     */
    private iterable $performanceTrackerReviewer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->performanceTrackerReviewer = new ArrayCollection();
    }

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
     * @return string
     */
    public function getTrackerName(): string
    {
        return $this->trackerName;
    }

    /**
     * @param string $trackerName
     */
    public function setTrackerName(string $trackerName): void
    {
        $this->trackerName = $trackerName;
    }

    /**
     * @return DateTime
     */
    public function getAddedDate(): DateTime
    {
        return $this->addedDate;
    }

    /**
     * @param DateTime $addedDate
     */
    public function setAddedDate(DateTime $addedDate): void
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
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee $employee
     */
    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return ArrayCollection|iterable|PerformanceTrackerReviewer[]
     */

    public function getPerformanceTrackerReviewer()
    {
        return $this->performanceTrackerReviewer;
    }

    /**
     * @param $performanceTrackerReviewer
     */
    public function setPerformanceTrackerReviewer($performanceTrackerReviewer): void
    {
        $this->performanceTrackerReviewer = $performanceTrackerReviewer;
    }

    /**
     * @return Employee
     */
    public function getAddedBy(): Employee
    {
        return $this->addedBy;
    }

    /**
     * @param Employee $addedBy
     */
    public function setAddedBy(Employee $addedBy): void
    {
        $this->addedBy = $addedBy;
    }
}
