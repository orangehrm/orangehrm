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

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hs_hr_province")
 * @ORM\Entity
 */
class Province
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
     * @var string
     *
     * @ORM\Column(name="province_name", type="string", length=40)
     */
    private string $provinceName;

    /**
     * @var string
     *
     * @ORM\Column(name="province_code", type="string", length=2)
     */
    private string $provinceCode;

    /**
     * @var string
     *
     * @ORM\Column(name="cou_code", type="string", length=2, options={"default" : "US"})
     */
    private string $countryCode = "US";

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
     * @return string
     */
    public function getProvinceName(): string
    {
        return $this->provinceName;
    }

    /**
     * @param string $provinceName
     */
    public function setProvinceName(string $provinceName): void
    {
        $this->provinceName = $provinceName;
    }

    /**
     * @return string
     */
    public function getProvinceCode(): string
    {
        return $this->provinceCode;
    }

    /**
     * @param string $provinceCode
     */
    public function setProvinceCode(string $provinceCode): void
    {
        $this->provinceCode = $provinceCode;
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
}
