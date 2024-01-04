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
 * @ORM\Table(name="hs_hr_country")
 * @ORM\Entity
 */
class Country
{
    /**
     * @var string
     *
     * @ORM\Column(name="cou_code", type="string", length=2)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private string $countryCode;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80)
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="cou_name", type="string", length=80)
     */
    private string $countryName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="iso3", type="string", length=3, nullable=true)
     */
    private ?string $iso3;

    /**
     * @var int|null
     *
     * @ORM\Column(name="numcode", type="smallint", nullable=true)
     */
    private ?int $numCode;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     */
    public function getCountryName(): string
    {
        return $this->countryName;
    }

    /**
     * @param string $countryName
     */
    public function setCountryName(string $countryName): void
    {
        $this->countryName = $countryName;
    }

    /**
     * @return string|null
     */
    public function getIso3(): ?string
    {
        return $this->iso3;
    }

    /**
     * @param string|null $iso3
     */
    public function setIso3(?string $iso3): void
    {
        $this->iso3 = $iso3;
    }

    /**
     * @return int|null
     */
    public function getNumCode(): ?int
    {
        return $this->numCode;
    }

    /**
     * @param int|null $numCode
     */
    public function setNumCode(?int $numCode): void
    {
        $this->numCode = $numCode;
    }
}
