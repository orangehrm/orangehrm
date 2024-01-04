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
use OrangeHRM\Entity\Decorator\CandidateVacancyDecorator;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method CandidateVacancyDecorator getDecorator()
 * @ORM\Table(name="ohrm_job_candidate_vacancy")
 * @ORM\Entity
 */
class CandidateVacancy
{
    use DecoratorTrait;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", length=13)
     */
    private int $id;

    /**
     * @var Candidate
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Candidate", inversedBy="candidateVacancy", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="candidate_id", referencedColumnName="id")
     */
    private Candidate $candidate;

    /**
     * @var Vacancy
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Vacancy", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="vacancy_id", referencedColumnName="id")
     */
    private Vacancy $vacancy;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=100)
     */
    private string $status;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="applied_date", type="date")
     */
    private DateTime $appliedDate;

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
     * @return Vacancy
     */
    public function getVacancy(): Vacancy
    {
        return $this->vacancy;
    }

    /**
     * @param Vacancy $vacancy
     */
    public function setVacancy(Vacancy $vacancy): void
    {
        $this->vacancy = $vacancy;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return DateTime
     */
    public function getAppliedDate(): DateTime
    {
        return $this->appliedDate;
    }

    /**
     * @param DateTime $appliedDate
     */
    public function setAppliedDate(DateTime $appliedDate): void
    {
        $this->appliedDate = $appliedDate;
    }
}
