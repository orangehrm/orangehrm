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

namespace OrangeHRM\Core\Report\DisplayField\Education\Dto;

use OrangeHRM\Core\Report\DisplayField\NormalizableDTO;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;

class Education extends NormalizableDTO
{
    use EntityManagerHelperTrait;

    private ?int $empNumber = null;

    /**
     * @param int|null $empNumber
     */
    public function __construct(?int $empNumber)
    {
        $this->empNumber = $empNumber;
    }

    /**
     * @inheritDoc
     */
    public function toArray(array $fields): ?array
    {
        /** @var Employee $employee */
        $employee = $this->getReference(Employee::class, $this->empNumber);
        return $this->normalizeArray($employee->getEducations(), $fields);
    }

    /**
     * @inheritDoc
     */
    protected function getFieldGetterMap(): array
    {
        return [
            'eduProgram' => ['getEducation', 'getName'],
            'eduYear' => ['getYear'],
            'eduGPAOrScore' => ['getScore'],
            'empEduCode' => ['getEducation', 'getId'],
            'getInstitute' => ['getInstitute'],
            'getMajor' => ['getMajor'],
            'getStartDate' => ['getDecorator', 'getStartDate'],
            'getEndDate' => ['getDecorator', 'getEndDate'],
        ];
    }
}
