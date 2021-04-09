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
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=false, options={"default":0})
     */
    private $isDeleted;

    /**
     * @var JobSpecificationAttachment
     *
     * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\JobSpecificationAttachment", mappedBy="jobTitle", cascade={"persist", "remove"})
     */
    private $jobSpecificationAttachment;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="jobTitle")
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
     * @return bool
     */
    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(bool $isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return JobSpecificationAttachment
     */
    public function getJobSpecificationAttachment(): JobSpecificationAttachment
    {
        return $this->jobSpecificationAttachment;
    }

    /**
     * @param JobSpecificationAttachment $jobSpecificationAttachment
     */
    public function setJobSpecificationAttachment(JobSpecificationAttachment $jobSpecificationAttachment)
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
