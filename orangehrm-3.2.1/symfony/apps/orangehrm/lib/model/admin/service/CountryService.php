<?php

/*
 * 
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
 * 
 */

class CountryService extends BaseService {

    protected $countryDao;

    /**
     * 
     * @return CountryDao
     */
    public function getCountryDao() {
        if (!($this->countryDao instanceof CountryDao)) {
            $this->countryDao = new CountryDao();
        }
        return $this->countryDao;
    }

    /**
     *
     * @param CountryDao $dao 
     */
    public function setCountryDao(CountryDao $dao) {
        $this->countryDao = $dao;
    }

    /**
     * Get Country list
     * @return Country
     */
    public function getCountryList() {
        try {
            $q = Doctrine_Query::create()
                    ->from('Country c')
                    ->orderBy('c.name');

            $countryList = $q->execute();

            return $countryList;
        } catch (Exception $e) {
            throw new AdminServiceException($e->getMessage());
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

}