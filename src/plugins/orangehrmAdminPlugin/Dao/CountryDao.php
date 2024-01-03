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

namespace OrangeHRM\Admin\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Country;
use OrangeHRM\Entity\Province;
use OrangeHRM\ORM\ListSorter;

class CountryDao extends BaseDao
{
    /**
     * Get Country list
     * @return Country[]
     */
    public function getCountryList(): array
    {
        $q = $this->createQueryBuilder(Country::class, 'c');
        $q->orderBy('c.name', ListSorter::ASCENDING);
        return $q->getQuery()->execute();
    }

    /**
     * Fetch list of provinces
     *
     * @param string|null $countryCode Country code - defaults to null
     * @return Province[]
     */
    public function getProvinceList(?string $countryCode = null): array
    {
        $q = $this->createQueryBuilder(Province::class, 'p');

        if (!empty($countryCode)) {
            $q->andWhere('p.countryCode = :countryCode')
                    ->setParameter('countryCode', $countryCode);
        }

        $q->addOrderBy('p.provinceName', ListSorter::ASCENDING);

        return $q->getQuery()->execute();
    }

    /**
     * Get Country By Country Name
     *
     * @param string $countryName
     * @return Country|null
     */
    public function getCountryByCountryName(string $countryName): ?Country
    {
        $country = $this->getRepository(Country::class)->findOneBy(['countryName' => $countryName]);
        if ($country instanceof Country) {
            return $country;
        }
        return null;
    }

    /**
     * Get Country by country code
     *
     * @param string $countryCode
     * @return Country|null
     */
    public function getCountryByCountryCode(string $countryCode): ?Country
    {
        $q = $this->createQueryBuilder(Country::class, 'c');
        $q->where('c.countryCode = :countryCode')
                ->setParameter('countryCode', $countryCode);

        return $this->fetchOne($q);
    }

    /**
     * @param string $provinceCode
     * @return Province|null
     */
    public function getProvinceByProvinceCode(string $provinceCode): ?Province
    {
        $q = $this->createQueryBuilder(Province::class, 'p');
        $q->where('p.provinceCode = :provinceCode')
                ->setParameter('provinceCode', $provinceCode);

        return $this->fetchOne($q);
    }

    /**
     * @param string $provinceName
     * @return Province|null
     */
    public function getProvinceByProvinceName(string $provinceName): ?Province
    {
        $q = $this->createQueryBuilder(Province::class, 'p');
        $q->where('p.provinceName = :provinceName')
            ->setParameter('provinceName', $provinceName);

        return $this->fetchOne($q);
    }
}
