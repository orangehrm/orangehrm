<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms
 * and conditions on using this software.
 *
 */


namespace OrangeHRM\Entity\Decorator;


use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Admin\Service\LocationService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Country;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\Location;

class LocationDecorator {

    use EntityManagerHelperTrait;
    protected ?CountryService $countryService = null;
    protected ?LocationService $locationService = null;

    /**
     * Set Country Service
     *
     * @param CountryService $countryService
     */
    public function setCountryService(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    /**
     * Returns Country Service
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
    public function setLocationService(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Returns Location Service
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
     * @var Location
     */
    protected Location $location;

    /**
     * LocationDecorator constructor.
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
     * @param string|null  $countryCode
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
     * @return int - The number of employees in the given location
     * @throws \OrangeHRM\Core\Exception\DaoException
     */
    public function getNoOfEmployees(): int
    {
        return $this->getLocationService()->getNumberOfEmplyeesForLocation($this->getLocation()->getId());
    }

}
