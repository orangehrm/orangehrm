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

namespace OrangeHRM\Admin\Dao;

use OrangeHRM\Entity\Country;
use OrangeHRM\Core\Dao\BaseDao;
use DaoException;
use Exception;

class CountryDao extends BaseDao
{
    /**
     * Get Country list
     * @return Country[]
     * @throws DaoException
     */
    public function getCountryList(): array
    {
        try {
            $q = $this->createQueryBuilder(Country::class, 'c');
            $q->orderBy('c.name');
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Fetch list of provinces
     *
     * @param String $countryCode Country code - defaults to null
     * @return Province
     */
    public function getProvinceList($countryCode = NULL) {
        try {
            $q = Doctrine_Query::create()
                    ->from('Province p');

            if (!empty($countryCode)) {
                $q->where('cou_code = ?', $countryCode);
            }

            $q->orderBy('p.province_name');

            $provinceList = $q->execute();

            return $provinceList;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function searchCountries(array $searchParams) {
        try {
            $query = Doctrine_Query::create()
                    ->from('Country c');

            foreach ($searchParams as $field => $filterValue) {
                $query->addWhere($field . ' = ?', $filterValue);
            }

            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get Country By Country Name
     *
     * @param $countryName
     * @return Doctrine_Record
     * @throws DaoException
     */
    public function getCountryByCountryName($countryName)
    {
        try {
            $country = Doctrine::getTable('Country')->findOneBy('cou_name', $countryName);
            return $country;
        } catch (Exception $exception) {
            throw new DaoException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Get Country by country code
     *
     * @param $countryName
     * @return Doctrine_Record
     * @throws DaoException
     */
    public function getCountryByCountryCode($countryCode)
    {
        try {

            $q = Doctrine_Query:: create()->from('Country c')
                ->where('c.cou_code = ?', $countryCode);

            return $q->execute();

        } catch (Exception $exception) {
            throw new DaoException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
