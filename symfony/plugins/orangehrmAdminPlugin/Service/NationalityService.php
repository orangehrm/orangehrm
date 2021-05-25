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

use OrangeHRM\Admin\Dao\NationalityDao;
use OrangeHRM\Admin\Service\Model\NationalityModel;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Core\Exception\DaoException;
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
     * @return Nationality[]
     * @throws DaoException
     */
    public function getNationalityList(): array
    {
        return $this->getNationalityDao()->getNationalityList();
    }

    /**
     * @param int $id
     * @return Nationality|null
     * @throws DaoException
     */
    public function getNationalityById(int $id): ?Nationality
    {
        return $this->getNationalityDao()->getNationalityById($id);
    }

    /**
     * @param array $nationalityList
     * @return int
     * @throws DaoException
     */
    public function deleteNationalities(array $nationalityList): int
    {
        return $this->getNationalityDao()->deleteNationalities($nationalityList);
    }

    /**
     * @return array
     * @throws DaoException
     * @throws NormalizeException
     */
    public function getNationalityArray(): array
    {
        $nationalities = $this->getNationalityList();
        return $this->getNormalizerService()->normalizeArray(NationalityModel::class, $nationalities);
    }
}
