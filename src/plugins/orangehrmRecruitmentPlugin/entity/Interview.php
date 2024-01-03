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
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\InterviewDecorator;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method InterviewDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_job_interview")
 * @ORM\Entity
 */
class Interview
{
    use DecoratorTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=13)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var CandidateVacancy|null
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\CandidateVacancy", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="candidate_vacancy_id", referencedColumnName="id", nullable=true)
     */
    private ?CandidateVacancy $candidateVacancy;

    /**
     * @var Candidate|null
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Candidate", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="candidate_id", referencedColumnName="id", nullable=true)
     */
    private ?Candidate $candidate;

    /**
     * @var string
     *
     * @ORM\Column(name="interview_name", type="string", length=100, nullable=false)
     */
    private string $interviewName;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="interview_date", type="date", nullable=true)
     */
    private ?DateTime $interviewDate;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="interview_time", type="time", nullable=true)
     */
    private ?DateTime $interviewTime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private ?string $note;

    /**
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinTable(name="ohrm_job_interview_interviewer",
     *     joinColumns={@ORM\JoinColumn(name="interview_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="interviewer_id", referencedColumnName="emp_number")}
     * )
     */
    private iterable $interviewers;

    public function __construct()
    {
        $this->interviewers = new ArrayCollection();
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
     * @return CandidateVacancy|null
     */
    public function getCandidateVacancy(): ?CandidateVacancy
    {
        return $this->candidateVacancy;
    }

    /**
     * @param CandidateVacancy|null $candidateVacancy
     */
    public function setCandidateVacancy(?CandidateVacancy $candidateVacancy): void
    {
        $this->candidateVacancy = $candidateVacancy;
    }

    /**
     * @return Candidate|null
     */
    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    /**
     * @param Candidate|null $candidate
     */
    public function setCandidate(?Candidate $candidate): void
    {
        $this->candidate = $candidate;
    }

    /**
     * @return string
     */
    public function getInterviewName(): string
    {
        return $this->interviewName;
    }

    /**
     * @param string $interviewName
     */
    public function setInterviewName(string $interviewName): void
    {
        $this->interviewName = $interviewName;
    }

    /**
     * @return DateTime|null
     */
    public function getInterviewDate(): ?DateTime
    {
        return $this->interviewDate;
    }

    /**
     * @param DateTime|null $interviewDate
     */
    public function setInterviewDate(?DateTime $interviewDate): void
    {
        $this->interviewDate = $interviewDate;
    }

    /**
     * @return DateTime|null
     */
    public function getInterviewTime(): ?DateTime
    {
        return $this->interviewTime;
    }

    /**
     * @param DateTime|null $interviewTime
     */
    public function setInterviewTime(?DateTime $interviewTime): void
    {
        $this->interviewTime = $interviewTime;
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
     * @return iterable
     */
    public function getInterviewers(): iterable
    {
        return $this->interviewers;
    }
}
