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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateVacancy;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Interview;

class InterviewDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    /**
     * @var Interview
     */
    protected Interview $interview;

    /**
     * @param Interview $interview
     */
    public function __construct(Interview $interview)
    {
        $this->interview = $interview;
    }

    /**
     * @param int $id
     */
    public function setCandidateVacancyById(int $id): void
    {
        $candidateVacancy = $this->getReference(CandidateVacancy::class, $id);
        $this->interview->setCandidateVacancy($candidateVacancy);
    }

    /**
     * @param int $id
     */
    public function setCandidateById(int $id): void
    {
        $candidate = $this->getReference(Candidate::class, $id);
        $this->interview->setCandidate($candidate);
    }

    /**
     * @param array $empNumbers
     */
    public function setInterviewerByEmpNumbers(array $empNumbers)
    {
        foreach ($empNumbers as $empNumber) {
            $interviewer = $this->getReference(Employee::class, $empNumber);
            $this->addInterviewer($interviewer);
        }
    }

    /**
     * @param Employee $employee
     */
    private function addInterviewer(Employee $employee): void
    {
        $interviewers = $this->interview->getInterviewers();
        if ($interviewers->contains($employee)) {
            return;
        }
        $interviewers[] = $employee;
    }

    public function removeInterviewers(): void
    {
        $interviewers = $this->interview->getInterviewers();
        foreach ($interviewers as $interviewer) {
            $interviewers->removeElement($interviewer);
        }
    }

    /**
     * @return string
     */
    public function getInterviewDate(): string
    {
        $interviewDate = $this->interview->getInterviewDate();
        return $this->getDateTimeHelper()->formatDate($interviewDate);
    }

    /**
     * @return string|null
     */
    public function getInterviewTime(): ?string
    {
        $interviewTime = $this->interview->getInterviewTime();
        return $this->getDateTimeHelper()->formatDateTimeToTimeString($interviewTime);
    }
}
