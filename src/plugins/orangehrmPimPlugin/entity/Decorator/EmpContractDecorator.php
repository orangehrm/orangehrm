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
use OrangeHRM\Entity\EmpContract;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Dto\PartialEmployeeAttachment;
use OrangeHRM\Pim\Service\EmploymentContractService;

class EmpContractDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    /**
     * @var EmpContract
     */
    protected EmpContract $empContract;

    /**
     * @var EmploymentContractService|null
     */
    protected ?EmploymentContractService $employmentContractService = null;

    /**
     * @param EmpContract $empContract
     */
    public function __construct(EmpContract $empContract)
    {
        $this->empContract = $empContract;
    }

    /**
     * @return EmpContract
     */
    protected function getEmpContract(): EmpContract
    {
        return $this->empContract;
    }

    /**
     * @return EmploymentContractService
     */
    public function getEmploymentContractService(): EmploymentContractService
    {
        if (!$this->employmentContractService instanceof EmploymentContractService) {
            $this->employmentContractService = new EmploymentContractService();
        }
        return $this->employmentContractService;
    }

    /**
     * @return string|null
     */
    public function getStartDate(): ?string
    {
        $date = $this->getEmpContract()->getStartDate();
        return $this->getDateTimeHelper()->formatDate($date);
    }

    /**
     * @return string|null
     */
    public function getEndDate(): ?string
    {
        $date = $this->getEmpContract()->getEndDate();
        return $this->getDateTimeHelper()->formatDate($date);
    }

    /**
     * @return PartialEmployeeAttachment|null
     */
    public function getContractAttachment(): ?PartialEmployeeAttachment
    {
        $empNumber = $this->getEmpContract()->getEmployee()->getEmpNumber();
        return $this->getEmploymentContractService()->getContractAttachment($empNumber);
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getEmpContract()->setEmployee($employee);
    }
}
