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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_pay_grade")
 * @ORM\Entity
 */
class PayGrade
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=60, nullable=true)
     */
    private ?string $name;

    /**
     * @var Collection|PayGradeCurrency[]
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\PayGradeCurrency", mappedBy="payGrade")
     */
    private $payGradeCurrencies;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->payGradeCurrencies = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection|PayGradeCurrency[]
     */
    public function getPayGradeCurrencies()
    {
        return $this->payGradeCurrencies;
    }

    /**
     * @param Collection|PayGradeCurrency[] $payGradeCurrencies
     */
    public function setPayGradeCurrencies($payGradeCurrencies): void
    {
        $this->payGradeCurrencies = $payGradeCurrencies;
    }
}
