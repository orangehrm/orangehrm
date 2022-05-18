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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Recruitment\Api\CandidateAPI;

class CandidateDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    protected Candidate $candidate;

    /**
     * @param Candidate $candidate
     */
    public function __construct(Candidate $candidate)
    {
        $this->candidate = $candidate;
    }

    /**
     * @param int $id
     */
    public function setEmployeeById(int $id): void
    {
        $employee = $this->getReference(Employee::class, $id);
        $this->candidate->setEmployee($employee);
    }

    /**
     * @return string
     */
    public function getDateOfApplication(): string
    {
        return $this->candidate->getDateOfApplication()->format('Y-m-d');
    }

    /**
     * @return array
     */
    public function getStatus(): array
    {
        $status = $this->candidate->getStatus();
        return [
            'id' => $status,
            'label' => ucwords(strtolower(CandidateAPI::STATUS_MAP[$status]))
        ];
    }

    /**
     * @param  Employee  $employee
     */
    private function addCandidateVacancy(Employee $employee): void
    {
        $projectAdmins = $this->candidate->getProjectAdmins();
        if ($projectAdmins->contains($employee)) {
            return;
        }
        $projectAdmins[] = $employee;
    }
}