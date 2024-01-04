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
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateVacancy;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Recruitment\Service\CandidateService;

class CandidateVacancyDecorator
{
    use EntityManagerHelperTrait;

    protected CandidateVacancy $candidateVacancy;

    /**
     * @param CandidateVacancy $candidateVacancy
     */
    public function __construct(CandidateVacancy $candidateVacancy)
    {
        $this->candidateVacancy = $candidateVacancy;
    }

    /**
     * @param int $id
     */
    public function setVacancyById(int $id): void
    {
        $vacancy = $this->getReference(Vacancy::class, $id);
        $this->candidateVacancy->setVacancy($vacancy);
    }

    /**
     * @param int $id
     */
    public function setCandidateById(int $id): void
    {
        $candidate = $this->getReference(Candidate::class, $id);
        $this->candidateVacancy->setCandidate($candidate);
    }

    /**
     * @return array
     */
    public function getCandidateVacancyStatus(): array
    {
        $candidateVacancyStatus = $this->candidateVacancy->getStatus();
        return [
            'id' => array_flip(CandidateService::STATUS_MAP)[$candidateVacancyStatus],
            'label' => ucwords(strtolower($candidateVacancyStatus))
        ];
    }
}
