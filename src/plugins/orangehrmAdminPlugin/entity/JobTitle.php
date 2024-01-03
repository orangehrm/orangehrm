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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_job_title")
 * @ORM\Entity
 */
class JobTitle
{
    public const DELETED = 1;
    public const ACTIVE = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=13)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="job_title", type="string", length=100)
     */
    private string $jobTitleName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="job_description", type="string", length=400, nullable=true)
     */
    private ?string $jobDescription = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="note", type="string", length=400, nullable=true)
     */
    private ?string $note = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=true, options={"default" : 0})
     */
    private bool $isDeleted = false;

    /**
     * @var JobSpecificationAttachment|null
     *
     * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\JobSpecificationAttachment", mappedBy="jobTitle", cascade={"persist", "remove"})
     */
    private ?JobSpecificationAttachment $jobSpecificationAttachment = null;

    /**
     * @var Collection|Employee[]
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="jobTitle")
     */
    private $employees;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
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
     * @return string|null
     */
    public function getJobDescription(): ?string
    {
        return $this->jobDescription;
    }

    /**
     * @param string|null $jobDescription
     */
    public function setJobDescription(?string $jobDescription): void
    {
        $this->jobDescription = $jobDescription;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string|null $note
     */
    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
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
     * @return JobSpecificationAttachment|null
     */
    public function getJobSpecificationAttachment(): ?JobSpecificationAttachment
    {
        return $this->jobSpecificationAttachment;
    }

    /**
     * @param JobSpecificationAttachment|null $jobSpecificationAttachment
     */
    public function setJobSpecificationAttachment(?JobSpecificationAttachment $jobSpecificationAttachment)
    {
        $this->jobSpecificationAttachment = $jobSpecificationAttachment;
    }
}
