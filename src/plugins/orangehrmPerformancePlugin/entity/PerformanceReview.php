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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\PerformanceReviewDecorator;

/**
 * @method PerformanceReviewDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_performance_review")
 * @ORM\Entity
 */
class PerformanceReview
{
    use DecoratorTrait;

    public const STATUS_INACTIVE = 1;
    public const STATUS_ACTIVATED = 2;
    public const STATUS_IN_PROGRESS = 3;
    public const STATUS_COMPLETED = 4;

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
     * @ORM\Column(name="status_id", type="integer", length=7, nullable=true)
     */
    private ?int $statusId;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="work_period_start", type="date", nullable=true)
     */
    private ?DateTime $reviewPeriodStart;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="work_period_end", type="date", nullable=true)
     */
    private ?DateTime $reviewPeriodEnd;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="due_date", type="date", nullable=true)
     */
    private ?DateTime $dueDate;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="completed_date", type="date", nullable=true)
     */
    private ?DateTime $completedDate;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="activated_date", type="datetime", nullable=true)
     */
    private ?DateTime $activatedDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="final_comment", type="text", nullable=true)
     */
    private ?string $finalComment;

    /**
     * @var float|null
     *
     * @ORM\Column(name="final_rate", type="decimal", precision=18, scale=2, nullable=true)
     */
    private ?float $finalRate;

    /**
     * @var ReviewerRating[]
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\ReviewerRating", mappedBy="PerformanceReview")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="id", referencedColumnName="review_id")
     * })
     */
    private iterable $reviewerRatings;

    /**
     * @var Reviewer[]
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Reviewer", mappedBy="review")
     */
    private iterable $reviewers;

    /**
     * @var JobTitle|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\JobTitle", inversedBy="PerformanceReview")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="job_title_code", referencedColumnName="id")
     * })
     */
    private ?JobTitle $jobTitle;

    /**
     * @var Subunit|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Subunit", inversedBy="PerformanceReview")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     * })
     */
    private ?Subunit $subunit;

    /**
     * @var Employee|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="PerformanceReview")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="employee_number", referencedColumnName="emp_number")
     * })
     */
    private ?Employee $employee;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reviewerRatings = new ArrayCollection();
        $this->reviewers = new ArrayCollection();
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
    public function getStatusId(): ?int
    {
        return $this->statusId;
    }

    /**
     * @param int|null $statusId
     */
    public function setStatusId(?int $statusId): void
    {
        $this->statusId = $statusId;
    }

    /**
     * @return DateTime|null
     */
    public function getReviewPeriodStart(): ?DateTime
    {
        return $this->reviewPeriodStart;
    }

    /**
     * @param DateTime|null $reviewPeriodStart
     */
    public function setReviewPeriodStart(?DateTime $reviewPeriodStart): void
    {
        $this->reviewPeriodStart = $reviewPeriodStart;
    }

    /**
     * @return DateTime|null
     */
    public function getReviewPeriodEnd(): ?DateTime
    {
        return $this->reviewPeriodEnd;
    }

    /**
     * @param DateTime|null $reviewPeriodEnd
     */
    public function setReviewPeriodEnd(?DateTime $reviewPeriodEnd): void
    {
        $this->reviewPeriodEnd = $reviewPeriodEnd;
    }

    /**
     * @return DateTime|null
     */
    public function getDueDate(): ?DateTime
    {
        return $this->dueDate;
    }

    /**
     * @param DateTime|null $dueDate
     */
    public function setDueDate(?DateTime $dueDate): void
    {
        $this->dueDate = $dueDate;
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
     * @return DateTime|null
     */
    public function getActivatedDate(): ?DateTime
    {
        return $this->activatedDate;
    }

    /**
     * @param DateTime|null $activatedDate
     */
    public function setActivatedDate(?DateTime $activatedDate): void
    {
        $this->activatedDate = $activatedDate;
    }

    /**
     * @return string|null
     */
    public function getFinalComment(): ?string
    {
        return $this->finalComment;
    }

    /**
     * @param string|null $finalComment
     */
    public function setFinalComment(?string $finalComment): void
    {
        $this->finalComment = $finalComment;
    }

    /**
     * @return float|null
     */
    public function getFinalRate(): ?float
    {
        return $this->finalRate;
    }

    /**
     * @param float|null $finalRate
     */
    public function setFinalRate(?float $finalRate): void
    {
        $this->finalRate = $finalRate;
    }

    /**
     * @return JobTitle|null
     */
    public function getJobTitle(): ?JobTitle
    {
        return $this->jobTitle;
    }

    /**
     * @param JobTitle|null $jobTitle
     */
    public function setJobTitle(?JobTitle $jobTitle): void
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * @return Subunit|null
     */
    public function getSubunit(): ?Subunit
    {
        return $this->subunit;
    }

    /**
     * @param Subunit|null $subunit
     */
    public function setSubunit(?Subunit $subunit): void
    {
        $this->subunit = $subunit;
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
     * @return Reviewer[]
     */
    public function getReviewers(): iterable
    {
        return $this->reviewers;
    }

    /**
     * @param Reviewer[] $reviewers
     */
    public function setReviewers(iterable $reviewers): void
    {
        $this->reviewers = $reviewers;
    }
}
