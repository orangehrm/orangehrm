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
use OrangeHRM\Entity\Decorator\CandidateDecorator;
use OrangeHRM\Entity\Decorator\DecoratorTrait;

/**
 * @method CandidateDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_job_candidate")
 * @ORM\Entity
 *
 */
class Candidate
{
    use DecoratorTrait;

    public const MODE_OF_APPLICATION_MANUAL = 1;
    public const MODE_OF_APPLICATION_ONLINE = 2;

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
     * @ORM\Column(name="first_name", type="string", length=30)
     */
    private string $firstName;

    /**
     * @var string|null
     * @ORM\Column(name="middle_name", type="string", length=30, nullable=true)
     */
    private ?string $middleName = null;

    /**
     * @var string
     * @ORM\Column(name="last_name", type="string", length=30)
     */
    private string $lastName;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=100)
     */
    private string $email;

    /**
     * @var string|null
     * @ORM\Column(name="contact_number", type="string", length=30, nullable=true)
     */
    private ?string $contactNumber = null;

    /**
     * @var int
     * @ORM\Column(name="status", type="integer", length=4)
     * @deprecated
     */
    private int $status = 1;

    /**
     * @var string|null
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private ?string $comment = null;

    /**
     * @var int
     * @ORM\Column(name="mode_of_application", type="integer", length=4)
     */
    private int $modeOfApplication;

    /**
     * @var DateTime
     * @ORM\Column(name="date_of_application", type="date")
     */
    private DateTime $dateOfApplication;

    /**
     * @var int|null
     * @ORM\Column(name="cv_file_id", type="integer", length=13, nullable=true)
     * @deprecated
     */
    private ?int $cvFileId = null;

    /**
     * @var string|null
     * @ORM\Column(name="cv_text_version", type="text", nullable=true)
     * @deprecated
     */
    private ?string $cvTextVersion = null;

    /**
     * @var string|null
     * @ORM\Column(name="keywords", type="string", length=255, nullable=true)
     */
    private ?string $keywords = null;

    /**
     * @var Employee|null
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="candidates", cascade={"persist"})
     * @ORM\JoinColumn(name="added_person", referencedColumnName="emp_number", nullable=true)
     */
    private ?Employee $addedPerson = null;

    /**
     * @var bool
     * @ORM\Column(name="consent_to_keep_data", type="boolean")
     */
    private bool $consentToKeepData = false;

    /**
     * @var iterable|CandidateVacancy[]
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\CandidateVacancy", mappedBy="candidate")
     */
    private iterable $candidateVacancy;

    /**
     * @var iterable|CandidateAttachment[]
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\CandidateAttachment", mappedBy="candidate")
     */
    private iterable $candidateAttachment;

    /**
     * @var iterable|CandidateHistory[]
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\CandidateHistory", mappedBy="candidate")
     */
    private iterable $candidateHistory;

    public function __construct()
    {
        $this->candidateVacancy = new ArrayCollection();
        $this->candidateAttachment = new ArrayCollection();
        $this->candidateHistory = new ArrayCollection();
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
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    /**
     * @param string|null $middleName
     */
    public function setMiddleName(?string $middleName): void
    {
        $this->middleName = $middleName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getContactNumber(): ?string
    {
        return $this->contactNumber;
    }

    /**
     * @param string|null $contactNumber
     */
    public function setContactNumber(?string $contactNumber): void
    {
        $this->contactNumber = $contactNumber;
    }

    /**
     * @return int
     * @deprecated
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @deprecated
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
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
     * @return int
     */
    public function getModeOfApplication(): int
    {
        return $this->modeOfApplication;
    }

    /**
     * @param int $modeOfApplication
     */
    public function setModeOfApplication(int $modeOfApplication): void
    {
        $this->modeOfApplication = $modeOfApplication;
    }

    /**
     * @return DateTime
     */
    public function getDateOfApplication(): DateTime
    {
        return $this->dateOfApplication;
    }

    /**
     * @param DateTime $dateOfApplication
     */
    public function setDateOfApplication(DateTime $dateOfApplication): void
    {
        $this->dateOfApplication = $dateOfApplication;
    }

    /**
     * @return int|null
     * @deprecated
     */
    public function getCvFileId(): ?int
    {
        return $this->cvFileId;
    }

    /**
     * @param int|null $cvFileId
     * @deprecated
     */
    public function setCvFileId(?int $cvFileId): void
    {
        $this->cvFileId = $cvFileId;
    }

    /**
     * @return string|null
     * @deprecated
     */
    public function getCvTextVersion(): ?string
    {
        return $this->cvTextVersion;
    }

    /**
     * @param string|null $cvTextVersion
     * @deprecated
     */
    public function setCvTextVersion(?string $cvTextVersion): void
    {
        $this->cvTextVersion = $cvTextVersion;
    }

    /**
     * @return string|null
     */
    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    /**
     * @param string|null $keywords
     */
    public function setKeywords(?string $keywords): void
    {
        $this->keywords = $keywords;
    }

    /**
     * @return Employee|null
     */
    public function getAddedPerson(): ?Employee
    {
        return $this->addedPerson;
    }

    /**
     * @param Employee|null $addedPerson
     */
    public function setAddedPerson(?Employee $addedPerson): void
    {
        $this->addedPerson = $addedPerson;
    }

    /**
     * @return bool
     */
    public function isConsentToKeepData(): bool
    {
        return $this->consentToKeepData;
    }

    /**
     * @param bool $consentToKeepData
     */
    public function setConsentToKeepData(bool $consentToKeepData): void
    {
        $this->consentToKeepData = $consentToKeepData;
    }

    /**
     * @return CandidateVacancy[]|iterable
     */
    public function getCandidateVacancy()
    {
        return $this->candidateVacancy;
    }

    /**
     * @return CandidateAttachment[]|iterable
     */
    public function getCandidateAttachment()
    {
        return $this->candidateAttachment;
    }

    /**
     * @return iterable|CandidateHistory[]
     */
    public function getCandidateHistory()
    {
        return $this->candidateHistory;
    }
}
