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

use OrangeHRM\Claim\Service\ClaimService;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\ClaimExpense;
use OrangeHRM\Entity\ClaimRequest;
use OrangeHRM\Framework\Services;

class ClaimExpenseDecorator
{
    use EntityManagerHelperTrait;
    use AuthUserTrait;
    use DateTimeHelperTrait;

    /**
     * @var ClaimExpense
     */
    private ClaimExpense $claimExpense;

    /**
     * @param ClaimExpense $claimExpense
     */
    public function __construct(ClaimExpense $claimExpense)
    {
        $this->claimExpense = $claimExpense;
    }

    /**
     * @param int $requestId
     */
    public function setClaimRequestByRequestId(int $requestId): void
    {
        $claimRequest = $this->getClaimService()->getClaimDao()->getClaimRequestById($requestId);
        $this->getClaimExpense()->setClaimRequest($claimRequest);
    }

    /**
     * @param int $requestId
     * @return ClaimRequest|null
     */
    public function getClaimRequestById(int $requestId): ?ClaimRequest
    {
        return $this->getClaimService()->getClaimDao()->getClaimRequestById($requestId);
    }

    /**
     * @return ClaimService
     */
    public function getClaimService(): ClaimService
    {
        return $this->getContainer()->get(Services::CLAIM_SERVICE);
    }

    /**
     * @return ClaimExpense
     */
    public function getClaimExpense(): ClaimExpense
    {
        return $this->claimExpense;
    }

    /**
     * @param int $expenseTypeId
     */
    public function setExpenseTypeByExpenseTypeId(int $expenseTypeId): void
    {
        $expenseType = $this->getClaimService()->getClaimDao()->getExpenseTypeById($expenseTypeId);
        $this->getClaimExpense()->setExpenseType($expenseType);
    }

    /**
     * @return string|null in Y-m-d format
     */
    public function getDate(): string
    {
        return $this->getDateTimeHelper()->formatDate($this->getClaimExpense()->getDate());
    }
}
