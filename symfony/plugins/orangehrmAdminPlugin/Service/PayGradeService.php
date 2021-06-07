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

namespace OrangeHRM\Admin\Service;

use OrangeHRM\Admin\Dao\PayGradeDao;
use OrangeHRM\Admin\Dto\PayGradeSearchFilterParams;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\PayGrade;
use OrangeHRM\Entity\PayGradeCurrency;

class PayGradeService
{
    /**
     * @var PayGradeDao|null
     */
    private ?PayGradeDao $payGradeDao = null;

    /**
     * @return PayGradeDao
     */
    public function getPayGradeDao(): PayGradeDao
    {
        if (!$this->payGradeDao instanceof PayGradeDao) {
            $this->payGradeDao = new PayGradeDao();
        }
        return $this->payGradeDao;
    }

    /**
     * @param PayGradeDao $payGradeDao
     */
    public function setPayGradeDao(PayGradeDao $payGradeDao): void
    {
        $this->payGradeDao = $payGradeDao;
    }

    /**
     * @param int $payGradeId
     * @return PayGrade|null
     * @throws DaoException
     */
    public function getPayGradeById(int $payGradeId): ?PayGrade
    {
        return $this->getPayGradeDao()->getPayGradeById($payGradeId);
    }

    /**
     * @param PayGradeSearchFilterParams $payGradeSearchFilterParams
     * @return PayGrade[]
     * @throws DaoException
     */
    public function getPayGradeList(PayGradeSearchFilterParams $payGradeSearchFilterParams): array
    {
        return $this->getPayGradeDao()->getPayGradeList($payGradeSearchFilterParams);
    }

    /**
     * @param int $payGradeId
     * @return PayGradeCurrency[]
     * @throws DaoException
     */
    public function getCurrencyListByPayGradeId(int $payGradeId): array
    {
        return $this->getPayGradeDao()->getCurrencyListByPayGradeId($payGradeId);
    }

    /**
     * @param string $currencyId
     * @param int $payGradeId
     * @return PayGradeCurrency|null
     * @throws DaoException
     */
    public function getCurrencyByCurrencyIdAndPayGradeId(string $currencyId, int $payGradeId): ?PayGradeCurrency
    {
        return $this->getPayGradeDao()->getCurrencyByCurrencyIdAndPayGradeId($currencyId, $payGradeId);
    }

    /**
     * @param string $salaryAmount
     * @param string $currencyId
     * @param int $payGradeId
     * @return bool
     * @throws DaoException
     */
    public function isValidSalaryAmountForPayGradeCurrency(
        string $salaryAmount,
        string $currencyId,
        int $payGradeId
    ): bool {
        $payGradeCurrency = $this->getCurrencyByCurrencyIdAndPayGradeId($currencyId, $payGradeId);
        if (empty($payGradeCurrency)) {
            return false;
        } elseif ((!empty($payGradeCurrency->getMinSalary()) && ($salaryAmount < $payGradeCurrency->getMinSalary())) ||
            (!empty($payGradeCurrency->getMaxSalary()) && ($salaryAmount > $payGradeCurrency->getMaxSalary()))) {
            return false;
        }

        return true;
    }
}
