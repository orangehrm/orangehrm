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

namespace OrangeHRM\Recruitment\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\Vacancy;

class CandidateDetailedModel implements Normalizable
{
    /**
     * @var Candidate
     */
    private Candidate $candidate;

    public function __construct(Candidate $candidate)
    {
        $this->candidate = $candidate;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $addedPerson = $this->candidate->getEmployee();
        $candidateVacancies = $this->candidate->getCandidateVacancy();
        $candidateVacancy = !empty($candidateVacancies) ? $candidateVacancies[0] : null;
        /**
         * @var Vacancy
         */
        $vacancy = !is_null($candidateVacancy) ? $candidateVacancy->getVacancy() : null;

        if (is_null($vacancy)) {
            $vacancyDetails = [
                'id' => null,
                'name' => null,
                'jobTitle' => [
                    'id' => null,
                    'title' => null,
                    'isDeleted' => null,
                ],
                'hiringManger' => [
                    'id' => null,
                    'firstName' => null,
                    'middleName' => null,
                    'lastName' => null,
                    'terminationId' => null,
                ]
            ];
        } else {
            $vacancyDetails = [
                'id' => $vacancy->getId(),
                'name' => $vacancy->getName(),
                'jobTitle' => [
                    'id' => $vacancy->getJobTitle()->getId(),
                    'title' => $vacancy->getJobTitle()->getJobTitleName(),
                    'isDeleted' => $vacancy->getJobTitle()->isDeleted(),
                ],
                'hiringManger' => [
                    'id' => $vacancy->getEmployee()->getEmpNumber(),
                    'firstName' => $vacancy->getEmployee()->getFirstName(),
                    'middleName' => $vacancy->getEmployee()->getMiddleName(),
                    'lastName' => $vacancy->getEmployee()->getLastName(),
                    'terminationId' => $vacancy->getEmployee()->getEmployeeTerminationRecord(),
                ]
            ];
        }
        return [
            'id' => $this->candidate->getId(),
            'firstName' => $this->candidate->getFirstName(),
            'middleName' => $this->candidate->getMiddleName(),
            'lastName' => $this->candidate->getLastName(),
            'email' => $this->candidate->getEmail(),
            'contactNumber' => $this->candidate->getContactNumber(),
            'status' => $this->candidate->getDecorator()->getStatus(),
            'comment' => $this->candidate->getComment(),
            'keywords' => $this->candidate->getKeywords(),
            'modeOfApplication' => $this->candidate->getModeOfApplication(),
            'dateOfApplication' => $this->candidate->getDecorator()->getDateOfApplication(),
            'addedPerson' => [
                'id' => $addedPerson->getEmpNumber(),
                'firstName' => $addedPerson->getFirstName(),
                'middleName' => $addedPerson->getMiddleName(),
                'lastName' => $addedPerson->getLastName(),
                'terminationId' => $addedPerson->getEmployeeTerminationRecord()
            ],
            'vacancy' => array_merge($vacancyDetails)
        ];
    }
}
