<?php

namespace OrangeHRM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * JobTitle
 *
 * @ORM\Table(name="ohrm_job_title")
 * @ORM\Entity
 */
class JobTitle
{
    const NO_OF_RECORDS_PER_PAGE = 50;
    const DELETED = 1;
    const ACTIVE = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=13)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="job_title", type="string", length=100)
     */
    private $jobTitleName;

    /**
     * @var string
     *
     * @ORM\Column(name="job_description", type="string", length=400)
     */
    private $jobDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=400)
     */
    private $note;

    /**
     * @var int
     *
     * @ORM\Column(name="is_deleted", type="integer", length=1)
     */
    private $isDeleted;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\JobSpecificationAttachment", mappedBy="JobTitle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="jobTitleId")
     * })
     */
    private $jobSpecificationAttachment;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="JobTitle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="job_title_code")
     * })
     */
    private $employees;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Kpi", mappedBy="JobTitle")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="job_title_code")
     * })
     */
    private $kpi;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->jobSpecificationAttachment = new ArrayCollection();
        $this->employees = new ArrayCollection();
        $this->kpi = new ArrayCollection();
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
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getJobTitleName(): string
    {
        return $this->jobTitleName;
    }

    /**
     * @param string $jobTitleName
     */
    public function setJobTitleName(string $jobTitleName)
    {
        $this->jobTitleName = $jobTitleName;
    }

    /**
     * @return string
     */
    public function getJobDescription(): string
    {
        return $this->jobDescription;
    }

    /**
     * @param string $jobDescription
     */
    public function setJobDescription(string $jobDescription)
    {
        $this->jobDescription = $jobDescription;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote(string $note)
    {
        $this->note = $note;
    }

    /**
     * @return int
     */
    public function getIsDeleted(): int
    {
        return $this->isDeleted;
    }

    /**
     * @param int $isDeleted
     */
    public function setIsDeleted(int $isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return Collection
     */
    public function getJobSpecificationAttachment()
    {
        return $this->jobSpecificationAttachment;
    }

    /**
     * @param Collection $jobSpecificationAttachment
     */
    public function setJobSpecificationAttachment($jobSpecificationAttachment)
    {
        $this->jobSpecificationAttachment = $jobSpecificationAttachment;
    }

    /**
     * @return Collection
     */
    public function getEmployees()
    {
        return $this->employees;
    }

    /**
     * @param Collection $employees
     */
    public function setEmployees($employees)
    {
        $this->employees = $employees;
    }

    /**
     * @return Collection
     */
    public function getKpi()
    {
        return $this->kpi;
    }

    /**
     * @param Collection $kpi
     */
    public function setKpi($kpi)
    {
        $this->kpi = $kpi;
    }
}
