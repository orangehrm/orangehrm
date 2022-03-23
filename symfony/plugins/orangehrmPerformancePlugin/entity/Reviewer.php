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
use phpDocumentor\Reflection\Types\Iterable_;

/**
 * @ORM\Table(name="ohrm_reviewer")
 * @ORM\Entity
 */
class Reviewer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=7)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;


    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="integer", length=7,nullable=true)
     */
    private ?int $status;


    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="completed_date", type="datetime",nullable=true)
     */
    private ?DateTime $completedDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", length=65532,nullable=true)
     */
    private ?string $comment;

    /**
     * @var ReviewerRating[]
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\ReviewerRating", mappedBy="Reviewer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="reviewer_id")
     * })
     */
    private iterable $ratings;

    /**
     * @var ReviewerGroup|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\ReviewerGroup", inversedBy="Reviewer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reviewer_group_id", referencedColumnName="id")
     * })
     */
    private ?ReviewerGroup $group;

    /**
     * @var PerformanceReview|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\PerformanceReview", inversedBy="Reviewer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="review_id", referencedColumnName="id")
     * })
     */
    private ?PerformanceReview $review;

    /**
     * @var Employee|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="Reviewer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="employee_number", referencedColumnName="emp_number")
     * })
     */
    private ?Employee $employee;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ratings = new ArrayCollection();
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
     * @return DateTime|null
     */
    public function getCompletedDate(): ?DateTime
    {
        return $this->completedDate;
    }

    /**
     * @param DateTime|null $completedDate
     */
    public function setCompletedDate(?DateTime $completedDate): void
    {
        $this->completedDate = $completedDate;
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
     * @return ReviewerGroup|null
     */
    public function getGroup(): ?ReviewerGroup
    {
        return $this->group;
    }

    /**
     * @param ReviewerGroup|null $group
     */
    public function setGroup(?ReviewerGroup $group): void
    {
        $this->group = $group;
    }

    /**
     * @return PerformanceReview|null
     */
    public function getReview(): ?PerformanceReview
    {
        return $this->review;
    }

    /**
     * @param PerformanceReview|null $review
     */
    public function setReview(?PerformanceReview $review): void
    {
        $this->review = $review;
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
}
