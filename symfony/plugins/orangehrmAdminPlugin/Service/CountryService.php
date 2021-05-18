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

namespace OrangeHRM\Admin\Service;

use Exception;
use OrangeHRM\Admin\Dao\CountryDao;
use OrangeHRM\Admin\Dto\EmploymentStatusSearchFilterParams;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Entity\Country;

class CountryService
{
    /**
     * @var CountryDao|null
     */
    private ?CountryDao $countryDao = null;

    /**
     * @return CountryDao
     */
    public function getCountryDao(): CountryDao
    {
        if (is_null($this->countryDao)) {
            $this->countryDao = new CountryDao();
        }
        return $this->countryDao;
    }

    /**
     * @param CountryDao $countryDao
     */
    public function setCountryDao(CountryDao $countryDao): void
    {
        $this->countryDao = $countryDao;
    }

    /**
     * Get Country list
     * @return Country[]
     * @throws ServiceException
     */
    public function getCountryList()
    {
        try {
            return $this->getCountryDao()->getCountryList();
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     *
     * @return Province
     */
    public function getProvinceList() {
        try {
            $q = Doctrine_Query::create()
                    ->from('Province p')
                    ->orderBy('p.province_name');

            $provinceList = $q->execute();

            return $provinceList;
        } catch (Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

    /**
     *
     * @param array $searchParams
     */
    public function searchCountries(array $searchParams) {
        try {
            return $this->getCountryDao()->searchCountries($searchParams);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }

    /**
     * Get Country By Country Name
     * @param $countryName
     * @return Doctrine_Record
     * @throws DaoException
     */
    public function getCountryByCountryName($countryName){
        return $this->getCountryDao()->getCountryByCountryName($countryName);
    }

    /**
     * Get country by country code
     *
     * @param $countryCode
     * @return Doctrine_Record
     */
    public function getCountryByCountryCode($countryCode){
        return $this->getCountryDao()->getCountryByCountryCode($countryCode);
    }

    /**
     * @return array
     * @throws ServiceException
     */
    public function getCountryCodeAndNameFromList()
    {
        $countryList = $this->getCountryList();
        $countries = [];
        foreach ($countryList as $country) {
            array_push($countries, ['id' => $country->getCountryCode(), "label" => $country->getCountryName()]);
        }
        return $countries;
    }
}
