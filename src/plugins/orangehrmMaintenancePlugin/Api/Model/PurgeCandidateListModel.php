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

namespace OrangeHRM\Maintenance\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Candidate;

class PurgeCandidateListModel implements Normalizable
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
        $candidateVacancies = $this->candidate->getCandidateVacancy();
        $candidateVacancy = !empty($candidateVacancies) ? $candidateVacancies[0] : null;

        return [
            'id' => $this->candidate->getId(),
            'firstName' => $this->candidate->getFirstName(),
            'middleName' => $this->candidate->getMiddleName(),
            'lastName' => $this->candidate->getLastName(),
            'dateOfApplication' => $this->candidate->getDecorator()->getDateOfApplication(),
            'status' => is_null($candidateVacancy) ? null :
                $candidateVacancy->getDecorator()->getCandidateVacancyStatus(),
        ];
    }
}
