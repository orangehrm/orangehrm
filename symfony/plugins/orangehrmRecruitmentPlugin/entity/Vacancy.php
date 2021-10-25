<?php

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\VacancyDecorator;

/**
 * @method VacancyDecorator getDecorator()
 * @ORM\Table(name="ohrm_job_vacancy")
 * @ORM\Entity
 *
 */
class Vacancy
{
    use DecoratorTrait;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=13)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private int $id;

    /**
     * @var JobTitle
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\JobTitle", inversedBy="vacancies", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="job_title_code", referencedColumnName="id",nullable=false)
     */
    private JobTitle $jobTitle;

    /**
     * @var Employee|null
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="vacancies", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="hiring_manager_id", referencedColumnName="emp_number",nullable=true)
     */
    private ?Employee $employee;

    /**
     * @var string
     * @ORM\Column(name="name",type="string",length=100)
     */
    private string $name;
    /**
     * @var string |null
     * @ORM\Column(name="description",type="text",nullable=true)
     */
    private ?string $description;

    /**
     * @var int |null
     * @ORM\Column(name="no_of_positions",type="integer",length=13,nullable=true)
     */
    private ?int $numOfPositions;

    /**
     * @var int
     * @ORM\Column(name="status",type="integer",length=4)
     */
    private int $status;

    /**
     * @var bool
     * @ORM\Column(name="published_in_feed",type="boolean",options={"default":0})
     */
    private bool $isPublished = false;

    /**
     * @var DateTime
     * @ORM\Column(name="defined_time",type="datetime")
     */
    private DateTime $definedTime;

    /**
     * @var DateTime
     * @ORM\Column(name="updated_time",type="datetime")
     */
    private DateTime $updatedTime;
//

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param  int  $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return JobTitle
     */
    public function getJobTitle(): JobTitle
    {
        return $this->jobTitle;
    }

    /**
     * @param  JobTitle  $jobTitle
     */
    public function setJobTitle(JobTitle $jobTitle): void
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * @return Employee|null
     */
    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    /**
     * @param  Employee|null  $employee
     */
    public function setEmployee(?Employee $employee): void
    {
        $this->employee = $employee;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  string  $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param  string|null  $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int|null
     */
    public function getNumOfPositions(): ?int
    {
        return $this->numOfPositions;
    }

    /**
     * @param  int|null  $numOfPositions
     */
    public function setNumOfPositions(?int $numOfPositions): void
    {
        $this->numOfPositions = $numOfPositions;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param  int  $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function getIsPublished(): bool
    {
        return $this->isPublished;
    }

    /**
     * @param  bool  $isPublished
     */
    public function setIsPublished(bool $isPublished): void
    {
        $this->isPublished = $isPublished;
    }

    /**
     * @return DateTime
     */
    public function getDefinedTime(): DateTime
    {
        return $this->definedTime;
    }

    /**
     * @param  DateTime  $definedTime
     */
    public function setDefinedTime(DateTime $definedTime): void
    {
        $this->definedTime = $definedTime;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedTime(): DateTime
    {
        return $this->updatedTime;
    }

    /**
     * @param  DateTime  $updatedTime
     */
    public function setUpdatedTime(DateTime $updatedTime): void
    {
        $this->updatedTime = $updatedTime;
    }


}