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

use OrangeHRM\Admin\Dao\CountryDao;
use OrangeHRM\Admin\Service\Model\CountryModel;
use OrangeHRM\Admin\Service\Model\ProvinceModel;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\Country;
use OrangeHRM\Entity\Province;

class CountryService
{
    use NormalizerServiceTrait;

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
     * @throws DaoException
     */
    public function getCountryList(): array
    {
        return $this->getCountryDao()->getCountryList();
    }

    /**
     *
     * @return Province[]
     */
    public function getProvinceList(): array
    {
        return $this->getCountryDao()->getProvinceList();
    }

    /**
     * Get Country By Country Name
     * @param string $countryName
     * @return Country|null
     * @throws DaoException
     */
    public function getCountryByCountryName(string $countryName): ?Country
    {
        return $this->getCountryDao()->getCountryByCountryName($countryName);
    }

    /**
     * Get country by country code
     *
     * @param string $countryCode
     * @return Country|null
     * @throws DaoException
     */
    public function getCountryByCountryCode(string $countryCode): ?Country
    {
        return $this->getCountryDao()->getCountryByCountryCode($countryCode);
    }

    /**
     * @param string $provinceCode
     * @return Province|null
     * @throws DaoException
     */
    public function getProvinceByProvinceCode(string $provinceCode): ?Province
    {
        return $this->getCountryDao()->getProvinceByProvinceCode($provinceCode);
    }

    /**
     * @return array
     */
    public function getCountryArray(): array
    {
        $countries = $this->getCountryList();
        return $this->getNormalizerService()->normalizeArray(CountryModel::class, $countries);
    }

    /**
     * @return array
     */
    public function getProvinceArray(): array
    {
        $provinces = $this->getProvinceList();
        return $this->getNormalizerService()->normalizeArray(ProvinceModel::class, $provinces);
    }
}
