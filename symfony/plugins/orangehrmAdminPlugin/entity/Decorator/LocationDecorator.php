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


use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Admin\Service\LocationService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Country;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\Location;

class LocationDecorator
{

    use EntityManagerHelperTrait;

    protected ?CountryService $countryService = null;

    protected ?LocationService $locationService = null;

    /**
     * @var Location
     */
    protected Location $location;

    /**
     * Set Country Service
     *
     * @param CountryService $countryService
     */
    public function setCountryService(CountryService $countryService): void
    {
        $this->countryService = $countryService;
    }

    /**
     * Returns Country Service
     *
     * @returns CountryService
     */
    public function getCountryService(): CountryService
    {
        if (is_null($this->countryService)) {
            $this->countryService = new CountryService();
        }
        return $this->countryService;
    }

    /**
     * Set Location Service
     *
     * @param LocationService $locationService
     */
    public function setLocationService(LocationService $locationService): void
    {
        $this->locationService = $locationService;
    }

    /**
     * Returns Location Service
     *
     * @returns LocationService
     */
    public function getLocationService(): LocationService
    {
        if (is_null($this->locationService)) {
            $this->locationService = new LocationService();
        }
        return $this->locationService;
    }

    /**
     * LocationDecorator constructor.
     *
     * @param Location $location
     */
    public function __construct(Location $location)
    {
        $this->location = $location;
    }

    /**
     * @return Location
     */
    protected function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * Sets the given country code as the related country of the Location entity
     *
     * @param string|null $countryCode
     *
     * @throws \OrangeHRM\Core\Exception\DaoException
     */
    public function setCountryByCountryCode(?string $countryCode): void
    {
        /** @var Country|null $country */
        $country = is_null($countryCode) ? null : $this->getCountryService()->getCountryByCountryCode($countryCode);
        $this->getLocation()->setCountry($country);
    }

    /**
     * Returns the number of employees in the given location.
     *
     * @return int
     * @throws \OrangeHRM\Core\Exception\DaoException
     */
    public function getNoOfEmployees(): int
    {
        return $this->getLocationService()->getNumberOfEmployeesForLocation($this->getLocation()->getId());
    }

}
