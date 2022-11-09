<?php

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\EmpWorkExperienceDecorator;

/**
 * @method EmpWorkExperienceDecorator getDecorator()
 *
 * @ORM\Table(name="hs_hr_emp_work_experience")
 * @ORM\Entity
 */
class EmpWorkExperience
{
    use DecoratorTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="eexp_seqno", type="decimal", precision=10, scale=0, options={"default" : 0})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private int $seqNo = 0;

    /**
     * @var string | null
     *
     * @ORM\Column(name="eexp_employer", type="string", length=100, nullable=true)
     */
    private ?string $employer;

    /**
     * @var string | null
     *
     * @ORM\Column(name="eexp_jobtit", type="string", length=120, nullable=true)
     */
    private ?string $jobTitle;

    /**
     * @var DateTime | null
     *
     * @ORM\Column(name="eexp_from_date", type="datetime", length=25, nullable=true)
     */
    private ?DateTime $fromDate;

    /**
     * @var DateTime | null
     *
     * @ORM\Column(name="eexp_to_date", type="datetime", length=25, nullable=true)
     */
    private ?DateTime $toDate;

    /**
     * @var string | null
     *
     * @ORM\Column(name="eexp_comments", type="string", length=200, nullable=true)
     */
    private ?string $comments;

    /**
     * @deprecated
     * @var int | null
     *
     * @ORM\Column(name="eexp_internal", type="integer", length=4)
     */
    private ?int $internal;

    /**
     * @var Employee
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="workExperience", cascade={"persist"})
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @return int
     */
    public function getSeqNo(): int
    {
        return $this->seqNo;
    }

    /**
     * @param int $seqNo
     */
    public function setSeqNo(int $seqNo): void
    {
        $this->seqNo = $seqNo;
    }

    /**
     * @return string | null
     */
    public function getEmployer(): ?string
    {
        return $this->employer;
    }

    /**
     * @param string | null $employer
     */
    public function setEmployer(?string $employer): void
    {
        $this->employer = $employer;
    }

    /**
     * @return string | null
     */
    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    /**
     * @param string | null $jobTitle
     */
    public function setJobTitle(?string $jobTitle): void
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * @return DateTime | null
     */
    public function getFromDate(): ?DateTime
    {
        return $this->fromDate;
    }

    /**
     * @param DateTime | null $fromDate
     */
    public function setFromDate(?DateTime $fromDate): void
    {
        $this->fromDate = $fromDate;
    }


    /**
     * @return DateTime | null
     */
    public function getToDate(): ?DateTime
    {
        return $this->toDate;
    }

    /**
     * @param DateTime | null $toDate
     */
    public function setToDate(?DateTime $toDate): void
    {
        $this->toDate = $toDate;
    }

    /**
     * @return string | null
     */
    public function getComments(): ?string
    {
        return $this->comments;
    }

    /**
     * @param string | null $comments
     */
    public function setComments(?string $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return int | null
     * @deprecated
     */
    public function getInternal(): ?int
    {
        return $this->internal;
    }

    /**
     * @param int | null $internal
     * @deprecated
     */
    public function setInternal(?int $internal): void
    {
        $this->internal = $internal;
    }

    /**
     * @return Employee|null
     */
    public function getEmployee(): ?Employee
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
}
