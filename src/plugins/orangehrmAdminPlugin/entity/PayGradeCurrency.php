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

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_pay_grade_currency")
 * @ORM\Entity
 */
class PayGradeCurrency
{
    /**
     * @var PayGrade
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\PayGrade", inversedBy="payGradeCurrencies")
     * @ORM\JoinColumn(name="pay_grade_id", referencedColumnName="id")
     */
    private PayGrade $payGrade;

    /**
     * @var CurrencyType
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\CurrencyType")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="currency_id")
     */
    private CurrencyType $currencyType;

    /**
     * @var float|null
     *
     * @ORM\Column(name="min_salary", type="float", nullable=true)
     */
    private ?float $minSalary;

    /**
     * @var float|null
     *
     * @ORM\Column(name="max_salary", type="float", nullable=true)
     */
    private ?float $maxSalary;

    /**
     * @var int
     * @ORM\Column(name="pay_grade_id", type="integer", nullable=false)
     */
    private int $payGradeId;

    /**
     * @var string
     * @ORM\Column(name="currency_id", type="string", nullable=false)
     */
    private string $currencyId;

    /**
     * @return string
     */
    public function getCurrencyId(): string
    {
        return $this->currencyId;
    }

    /**
     * @param string $currencyId
     */
    public function setCurrencyId(string $currencyId): void
    {
        $this->currencyId = $currencyId;
    }

    /**
     * @return int
     */
    public function getPayGradeId(): int
    {
        return $this->payGradeId;
    }

    /**
     * @param int $payGradeId
     */
    public function setPayGradeId(int $payGradeId): void
    {
        $this->payGradeId = $payGradeId;
    }

    /**
     * @return PayGrade
     */
    public function getPayGrade(): PayGrade
    {
        return $this->payGrade;
    }

    /**
     * @param PayGrade $payGrade
     */
    public function setPayGrade(PayGrade $payGrade): void
    {
        $this->payGrade = $payGrade;
    }

    /**
     * @return CurrencyType
     */
    public function getCurrencyType(): CurrencyType
    {
        return $this->currencyType;
    }

    /**
     * @param CurrencyType $currencyType
     */
    public function setCurrencyType(CurrencyType $currencyType): void
    {
        $this->currencyType = $currencyType;
    }

    /**
     * @return float|null
     */
    public function getMinSalary(): ?float
    {
        return $this->minSalary;
    }

    /**
     * @param float|null $minSalary
     */
    public function setMinSalary(?float $minSalary): void
    {
        $this->minSalary = $minSalary;
    }

    /**
     * @return float|null
     */
    public function getMaxSalary(): ?float
    {
        return $this->maxSalary;
    }

    /**
     * @param float|null $maxSalary
     */
    public function setMaxSalary(?float $maxSalary): void
    {
        $this->maxSalary = $maxSalary;
    }
}
