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

use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Admin\Service\LocationService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Entity\Country;
use OrangeHRM\Entity\Location;
use OrangeHRM\Framework\Services;

class LocationDecorator
{
    use EntityManagerHelperTrait;
    use ServiceContainerTrait;


    protected ?LocationService $locationService = null;

    /**
     * @var Location
     */
    protected Location $location;

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
     */
    public function setCountryByCountryCode(?string $countryCode): void
    {
        /** @var CountryService $countryService */
        $countryService = $this->getContainer()->get(Services::COUNTRY_SERVICE);
        /** @var Country|null $country */
        $country = is_null($countryCode) ? null : $countryService->getCountryByCountryCode($countryCode);
        $this->getLocation()->setCountry($country);
    }

    /**
     * Returns the number of employees in the given location.
     *
     * @return int
     */
    public function getNoOfEmployees(): int
    {
        return $this->getLocationService()->getNumberOfEmployeesForLocation($this->getLocation()->getId());
    }
}
