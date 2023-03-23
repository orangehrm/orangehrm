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

use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\ClaimRequest;
use OrangeHRM\Entity\CurrencyType;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;

class ClaimRequestDecorator
{
    use EntityManagerHelperTrait;
    use AuthUserTrait;

    /**
     * @return PayGradeService
     */
    public function getPayGradeService(): PayGradeService
    {
        return $this->getContainer()->get(Services::PAY_GRADE_SERVICE);
    }

    /**
     * @var ClaimRequest
     */
    protected ClaimRequest $claimRequest;

    /**
     * @param ClaimRequest $claimRequest
     */
    public function __construct(ClaimRequest $claimRequest)
    {
        $this->claimRequest = $claimRequest;
    }

    /**
     * @return ClaimRequest
     */
    protected function getClaimRequest(): ClaimRequest
    {
        return $this->claimRequest;
    }

    /**
     * @param int $userId
     */
    public function setUserByUserId(int $userId): void
    {
        $user = $this->getReference(User::class, $userId);
        $this->getClaimRequest()->setUser($user);
    }

    /**
     * @param string $currencyId
     */
    public function setCurrencyByCurrencyId(string $currencyId): void
    {
        $this->getClaimRequest()->setCurrencyType(
            $this->getPayGradeService()->getPayGradeDao()->getCurrencyById($currencyId)
        );
    }

    /**
     * @param string $currencyId
     *
     * @return CurrencyType|null
     */
    public function getCurrencyByCurrencyId(string $currencyId): ?CurrencyType
    {
        return $this->getPayGradeService()->getPayGradeDao()->getCurrencyById($currencyId);
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getClaimRequest()->setEmployee($employee);
    }
}
