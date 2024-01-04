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

namespace OrangeHRM\Admin\Service;

use OrangeHRM\Admin\Dao\PayGradeDao;
use OrangeHRM\Admin\Dto\PayGradeCurrencySearchFilterParams;
use OrangeHRM\Admin\Dto\PayGradeSearchFilterParams;
use OrangeHRM\Admin\Service\Model\CurrencyModel;
use OrangeHRM\Admin\Service\Model\PayGradeModel;
use OrangeHRM\Admin\Service\Model\PayPeriodModel;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\CurrencyType;
use OrangeHRM\Entity\PayGrade;
use OrangeHRM\Entity\PayGradeCurrency;

class PayGradeService
{
    use NormalizerServiceTrait;

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
     */
    public function getPayGradeById(int $payGradeId): ?PayGrade
    {
        return $this->getPayGradeDao()->getPayGradeById($payGradeId);
    }

    /**
     * @param PayGradeSearchFilterParams|null $payGradeSearchFilterParams
     * @return PayGrade[]
     */
    public function getPayGradeList(PayGradeSearchFilterParams $payGradeSearchFilterParams = null): array
    {
        if (is_null($payGradeSearchFilterParams)) {
            $payGradeSearchFilterParams = new PayGradeSearchFilterParams();
        }
        return $this->getPayGradeDao()->getPayGradeList($payGradeSearchFilterParams);
    }

    /**
     * @param int $payGradeId
     * @return PayGradeCurrency[]
     */
    public function getCurrencyListByPayGradeId(int $payGradeId): array
    {
        return $this->getPayGradeDao()->getCurrencyListByPayGradeId($payGradeId);
    }

    /**
     * @param PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams
     * @return PayGradeCurrency[]
     */
    public function getPayGradeCurrencyList(PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams): array
    {
        return $this->getPayGradeDao()->getPayGradeCurrencyList($payGradeCurrencySearchFilterParams);
    }

    /**
     * @param PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams
     * @return int
     */
    public function getPayGradeCurrencyListCount(PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams): int
    {
        return $this->getPayGradeDao()->getPayGradeCurrencyListCount($payGradeCurrencySearchFilterParams);
    }

    /**
     * @param string $currencyId
     * @param int $payGradeId
     * @return PayGradeCurrency|null
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

    /**
     * @return array
     */
    public function getPayPeriodArray(): array
    {
        $payPeriods = $this->getPayGradeDao()->getPayPeriods();
        return $this->getNormalizerService()->normalizeArray(PayPeriodModel::class, $payPeriods);
    }

    /**
     * @return array
     */
    public function getPayGradeArray(): array
    {
        $payGrades = $this->getPayGradeList();
        return $this->getNormalizerService()->normalizeArray(PayGradeModel::class, $payGrades);
    }

    /**
     * @return array
     */
    public function getCurrencyArray(): array
    {
        $currencies = $this->getPayGradeDao()->getCurrencies();
        return $this->getNormalizerService()->normalizeArray(CurrencyModel::class, $currencies);
    }

    /**
     * @param string $id
     * @return CurrencyType|null
     */
    public function getCurrencyById(string $id): ?CurrencyType
    {
        return $this->getPayGradeDao()->getCurrencyById($id);
    }

    /**
     * @param PayGrade $payGrade
     * @return PayGrade
     */
    public function savePayGrade(PayGrade $payGrade): PayGrade
    {
        return $this->getPayGradeDao()->savePayGrade($payGrade);
    }

    /**
     * @param PayGradeCurrency $payGradeCurrency
     * @return PayGradeCurrency
     */
    public function savePayGradeCurrency(PayGradeCurrency $payGradeCurrency): PayGradeCurrency
    {
        return $this->getPayGradeDao()->savePayGradeCurrency($payGradeCurrency);
    }

    /**
     * @param array $toBeDeletedIds
     * @return int
     */
    public function deletePayGrades(array $toBeDeletedIds): int
    {
        return $this->getPayGradeDao()->deletePayGrades($toBeDeletedIds);
    }

    /**
     * @param PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams
     * @return int|mixed|string
     */
    public function getAllowedPayCurrencies(PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams)
    {
        return $this->getPayGradeDao()->getAllowedPayCurrencies($payGradeCurrencySearchFilterParams);
    }

    public function getAllowedPayCurrenciesCount(PayGradeCurrencySearchFilterParams $payGradeCurrencySearchFilterParams)
    {
        return $this->getPayGradeDao()->getAllowedPayCurrenciesCount($payGradeCurrencySearchFilterParams);
    }

    /**
     * @param int $payGradeId
     * @param array $toBeDeletedIds
     * @return int|mixed|string
     */
    public function deletePayGradeCurrency(int $payGradeId, array $toBeDeletedIds): int
    {
        return $this->getPayGradeDao()->deletePayGradeCurrency($payGradeId, $toBeDeletedIds);
    }
}
