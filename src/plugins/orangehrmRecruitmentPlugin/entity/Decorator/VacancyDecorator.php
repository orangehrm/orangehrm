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
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\Vacancy;

class VacancyDecorator
{
    use EntityManagerHelperTrait;

    /**
     * @var Vacancy
     */
    protected Vacancy $vacancy;

    /**
     * @param Vacancy $vacancy
     */
    public function __construct(Vacancy $vacancy)
    {
        $this->vacancy = $vacancy;
    }

    /**
     * @param int $id
     */
    public function setJobTitleById(int $id): void
    {
        /** @var JobTitle|null $jobTitle */
        $jobTitle = $this->getReference(JobTitle::class, $id);
        $this->getVacancy()->setJobTitle($jobTitle);
    }

    /**
     * @return Vacancy
     */
    protected function getVacancy(): Vacancy
    {
        return $this->vacancy;
    }

    /**
     * @param int $id
     */
    public function setEmployeeById(int $id): void
    {
        $employee = $this->getReference(Employee::class, $id);
        $this->getVacancy()->setHiringManager($employee);
    }

    /**
     * @return bool
     */
    public function isActiveAndPublished(): bool
    {
        return $this->getVacancy()->isPublished() && $this->getVacancy()->getStatus();
    }
}
