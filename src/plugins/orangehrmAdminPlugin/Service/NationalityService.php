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

namespace OrangeHRM\Admin\Service;

use OrangeHRM\Admin\Dao\NationalityDao;
use OrangeHRM\Admin\Dto\NationalitySearchFilterParams;
use OrangeHRM\Admin\Service\Model\NationalityModel;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\Nationality;

class NationalityService
{
    use NormalizerServiceTrait;

    /**
     * @var NationalityDao|null
     */
    private ?NationalityDao $nationalityDao = null;

    /**
     * @return NationalityDao
     */
    public function getNationalityDao(): NationalityDao
    {
        if (!$this->nationalityDao instanceof NationalityDao) {
            $this->nationalityDao = new NationalityDao();
        }
        return $this->nationalityDao;
    }

    /**
     * @param NationalityDao $nationalityDao
     */
    public function setNationalityDao(NationalityDao $nationalityDao): void
    {
        $this->nationalityDao = $nationalityDao;
    }

    /**
     * @param NationalitySearchFilterParams $nationalitySearchParamHolder
     * @return array
     */
    public function getNationalityList(NationalitySearchFilterParams $nationalitySearchParamHolder): array
    {
        return $this->getNationalityDao()->getNationalityList($nationalitySearchParamHolder);
    }

    /**
     * @param NationalitySearchFilterParams $nationalitySearchParamHolder
     * @return int
     */
    public function getNationalityCount(NationalitySearchFilterParams $nationalitySearchParamHolder): int
    {
        return $this->getNationalityDao()->getNationalityCount($nationalitySearchParamHolder);
    }

    /**
     * @param Nationality $nationality
     * @return Nationality
     */
    public function saveNationality(Nationality $nationality): Nationality
    {
        return $this->getNationalityDao()->saveNationality($nationality);
    }

    /**
     * @param int $id
     * @return Nationality|null
     */
    public function getNationalityById(int $id): ?Nationality
    {
        return $this->getNationalityDao()->getNationalityById($id);
    }

    /**
     * @param string $name
     * @return Nationality|null
     */
    public function getNationalityByName(string $name): ?Nationality
    {
        return $this->getNationalityDao()->getNationalityByName($name);
    }

    /**
     * @param array $toDeleteIds
     * @return int
     */
    public function deleteNationalities(array $toDeleteIds): int
    {
        return $this->getNationalityDao()->deleteNationalities($toDeleteIds);
    }

    /**
     * @param string $nationalityName
     * @return bool
     */
    public function isExistingNationalityName(string $nationalityName): bool
    {
        return $this->getNationalityDao()->isExistingNationalityName($nationalityName);
    }

    /**
     * @return array
     */
    public function getNationalityArray(): array
    {
        $nationalitySearchParamHolder = new NationalitySearchFilterParams();
        $nationalitySearchParamHolder->setLimit(0);
        $nationalities = $this->getNationalityList($nationalitySearchParamHolder);
        return $this->getNormalizerService()->normalizeArray(NationalityModel::class, $nationalities);
    }
}
