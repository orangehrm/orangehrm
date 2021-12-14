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

namespace OrangeHRM\Core\Report\DisplayField\ContactDetail\Dto;

use OrangeHRM\Core\Report\DisplayField\Stringable;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Country;

class EmployeeAddress implements Stringable
{
    use EntityManagerHelperTrait;

    public const SEPARATOR = ', ';

    private ?string $street1 = null;
    private ?string $street2 = null;
    private ?string $city = null;
    private ?string $province = null;
    private ?string $zipcode = null;
    private ?string $country = null;

    /**
     * @param string|null $street1
     * @param string|null $street2
     * @param string|null $city
     * @param string|null $province
     * @param string|null $zipcode
     * @param string|null $country
     */
    public function __construct(
        ?string $street1,
        ?string $street2,
        ?string $city,
        ?string $province,
        ?string $zipcode,
        ?string $country
    ) {
        $this->street1 = $street1;
        $this->street2 = $street2;
        $this->city = $city;
        $this->province = $province;
        $this->zipcode = $zipcode;
        $this->country = $country;
    }

    /**
     * @inheritDoc
     */
    public function toString(): ?string
    {
        $properties = [
            $this->street1,
            $this->street2,
            $this->city,
            $this->province,
            $this->zipcode,
        ];

        if (!empty($this->country)) {
            $country = $this->getRepository(Country::class)->find($this->country);
            if ($country instanceof Country) {
                $properties[] = $country->getCountryName();
            }
        }
        return implode(
            self::SEPARATOR,
            array_filter($properties, fn (?string $property) => !empty($property))
        );
    }
}
