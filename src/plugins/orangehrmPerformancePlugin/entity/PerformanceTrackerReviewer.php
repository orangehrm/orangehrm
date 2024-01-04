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
use OrangeHRM\Entity\Decorator\PerformanceTrackerReviewerDecorator;

/**
 * @method PerformanceTrackerReviewerDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_performance_tracker_reviewer")
 * @ORM\Entity
 */
class PerformanceTrackerReviewer
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
     * @var DateTime
     *
     * @ORM\Column(name="added_date", type="datetime")
     */
    private DateTime $added_date;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private ?int $status;

    /**
     * @var PerformanceTracker
     *
     * @ORM\ManyToOne(targetEntity="PerformanceTracker")
     * @ORM\JoinColumn(name="performance_track_id", referencedColumnName="id")
     */
    private PerformanceTracker $performanceTracker;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="Employee")
     * @ORM\JoinColumn(name="reviewer_id", referencedColumnName="emp_number")
     */
    private Employee $reviewer;

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
     * @return Employee
     */
    public function getReviewer(): Employee
    {
        return $this->reviewer;
    }

    /**
     * @param Employee $reviewer
     */
    public function setReviewer(Employee $reviewer): void
    {
        $this->reviewer = $reviewer;
    }

    /**
     * @return PerformanceTracker
     */
    public function getPerformanceTracker(): PerformanceTracker
    {
        return $this->performanceTracker;
    }

    /**
     * @param PerformanceTracker $performanceTracker
     */
    public function setPerformanceTracker(PerformanceTracker $performanceTracker): void
    {
        $this->performanceTracker = $performanceTracker;
    }

    /**
     * @return DateTime
     */
    public function getAddedDate(): DateTime
    {
        return $this->added_date;
    }

    /**
     * @param DateTime $added_date
     */
    public function setAddedDate(DateTime $added_date): void
    {
        $this->added_date = $added_date;
    }
}
