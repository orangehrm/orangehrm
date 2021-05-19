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
 * Country
 *
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
    private $countryCode;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="cou_name", type="string", length=80)
     */
    private $countryName;

    /**
     * @var string
     *
     * @ORM\Column(name="iso3", type="string", length=3)
     */
    private $iso3;

    /**
     * @var int
     *
     * @ORM\Column(name="numcode", type="integer", length=2)
     */
    private $numcode;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Location", mappedBy="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cou_code", referencedColumnName="country")
     * })
     */
    private $locations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->locations = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
}
