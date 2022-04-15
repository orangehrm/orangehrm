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

use OrangeHRM\Admin\Dao\EducationDao;
use OrangeHRM\Admin\Dto\QualificationEducationSearchFilterParams;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Education;

class EducationService
{
    /**
     * @var EducationDao|null
     */
    private ?EducationDao $educationDao = null;

    /**
     * Saves an education object
     * Can be used for a new record or updating.
     *
     * @param Education $education
     * @return Education
     * @throws DaoException
     */
    public function saveEducation(Education $education): Education
    {
        return $this->getEducationDao()->saveEducation($education);
    }

    /**
     * @return EducationDao
     *
     */
    public function getEducationDao(): EducationDao
    {
        if (!($this->educationDao instanceof EducationDao)) {
            $this->educationDao = new EducationDao();
        }

        return $this->educationDao;
    }

    /**
     * @param $educationDao
     * @return void
     */
    public function setEducationDao(EducationDao $educationDao): void
    {
        $this->educationDao = $educationDao;
    }

    /**
     * Retrieves an education object by ID
     *
     * @param int $id
     * @return Education An instance of Education or NULL
     * @throws DaoException
     */
    public function getEducationById(int $id): ?Education
    {
        return $this->getEducationDao()->getEducationById($id);
    }

    /**
     * Retrieves an education object by name
     *
     * Case insensitive
     *
     * @param string $name
     * @return Education An instance of Education or false
     * @throws DaoException
     */
    public function getEducationByName(string $name): ?Education
    {
        return $this->getEducationDao()->getEducationByName($name);
    }

    /**
     * @param QualificationEducationSearchFilterParams $educationSearchParamHolder
     * @return array
     * @throws DaoException
     */
    public function getEducationList(QualificationEducationSearchFilterParams $educationSearchParamHolder): array
    {
        return $this->getEducationDao()->getEducationList($educationSearchParamHolder);
    }

    /**
     * @param QualificationEducationSearchFilterParams $educationSearchParamHolder
     * @return int
     * @throws DaoException
     */
    public function getEducationCount(QualificationEducationSearchFilterParams $educationSearchParamHolder): int
    {
        return $this->getEducationDao()->getEducationCount($educationSearchParamHolder);
    }

    /**
     * Deletes education records
     *
     * @param array $toDeleteIds An array of IDs to be deleted
     * @return int Number of records deleted
     * @throws DaoException
     */
    public function deleteEducations(array $toDeleteIds): int
    {
        return $this->getEducationDao()->deleteEducations($toDeleteIds);
    }

    /**
     * Checks whether the given education name exists
     *
     * Case insensitive
     *
     * @param string $educationName Education name that needs to be checked
     * @return bool
     * @throws DaoException
     */
    public function isExistingEducationName(string $educationName): bool
    {
        return $this->getEducationDao()->isExistingEducationName($educationName);
    }
}
