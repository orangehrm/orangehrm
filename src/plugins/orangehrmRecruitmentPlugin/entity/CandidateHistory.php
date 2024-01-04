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
use OrangeHRM\Entity\Decorator\CandidateHistoryDecorator;
use OrangeHRM\Entity\Decorator\DecoratorTrait;

/**
 * @method CandidateHistoryDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_job_candidate_history")
 * @ORM\Entity
 */
class CandidateHistory
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
     * @var Candidate
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Candidate", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="candidate_id", referencedColumnName="id", nullable=false)
     */
    private Candidate $candidate;

    /**
     * @var Vacancy|null
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Vacancy", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="vacancy_id", referencedColumnName="id", nullable=true)
     */
    private ?Vacancy $vacancy = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="candidate_vacancy_name", type="string", length=255, nullable=true)
     */
    private ?string $candidateVacancyName = null;

    /**
     * @var Interview|null
     *
     * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\Interview", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="interview_id", referencedColumnName="id", nullable=true)
     */
    private ?Interview $interview = null;

    /**
     * @var int
     *
     * @ORM\Column(name="action", type="integer", length=4, nullable=false)
     */
    private int $action;

    /**
     * @var Employee|null
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="candidates", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="performed_by", referencedColumnName="emp_number", nullable=true)
     */
    private ?Employee $performedBy = null;

    /**
     * @var DateTime
     * @ORM\Column(name="performed_date", type="datetime")
     */
    private DateTime $performedDate;

    /**
     * @var string|null
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private ?string $note = null;

    /**
     * @var string|null
     * @ORM\Column(name="interviewers", type="text", nullable=true)
     * @deprecated
     */
    private ?string $interviewers = null;

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
     * @return Candidate
     */
    public function getCandidate(): Candidate
    {
        return $this->candidate;
    }

    /**
     * @param Candidate $candidate
     */
    public function setCandidate(Candidate $candidate): void
    {
        $this->candidate = $candidate;
    }

    /**
     * @return Vacancy|null
     */
    public function getVacancy(): ?Vacancy
    {
        return $this->vacancy;
    }

    /**
     * @param Vacancy|null $vacancy
     */
    public function setVacancy(?Vacancy $vacancy): void
    {
        $this->vacancy = $vacancy;
    }

    /**
     * @return string|null
     */
    public function getCandidateVacancyName(): ?string
    {
        return $this->candidateVacancyName;
    }

    /**
     * @param string|null $candidateVacancyName
     */
    public function setCandidateVacancyName(?string $candidateVacancyName): void
    {
        $this->candidateVacancyName = $candidateVacancyName;
    }

    /**
     * @return Interview|null
     */
    public function getInterview(): ?Interview
    {
        return $this->interview;
    }

    /**
     * @param Interview|null $interview
     */
    public function setInterview(?Interview $interview): void
    {
        $this->interview = $interview;
    }

    /**
     * @return int
     */
    public function getAction(): int
    {
        return $this->action;
    }

    /**
     * @param int $action
     */
    public function setAction(int $action): void
    {
        $this->action = $action;
    }

    /**
     * @return Employee|null
     */
    public function getPerformedBy(): ?Employee
    {
        return $this->performedBy;
    }

    /**
     * @param Employee|null $performedBy
     */
    public function setPerformedBy(?Employee $performedBy): void
    {
        $this->performedBy = $performedBy;
    }

    /**
     * @return DateTime
     */
    public function getPerformedDate(): DateTime
    {
        return $this->performedDate;
    }

    /**
     * @param DateTime $performedDate
     */
    public function setPerformedDate(DateTime $performedDate): void
    {
        $this->performedDate = $performedDate;
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
     * @return string|null
     * @deprecated
     */
    public function getInterviewers(): ?string
    {
        return $this->interviewers;
    }

    /**
     * @param string|null $interviewers
     * @deprecated
     */
    public function setInterviewers(?string $interviewers): void
    {
        $this->interviewers = $interviewers;
    }
}
