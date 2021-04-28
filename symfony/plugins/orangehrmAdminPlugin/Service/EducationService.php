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
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Education;

class EducationService
{
    /**
     * @var EducationDao|null
     */

    private ?EducationDao $educationDao = null;

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
     */
    public function setEducationDao($educationDao)
    {
        $this->educationDao = $educationDao;
    }

    /**
     * Saves an education object
     *
     * Can be used for a new record or updating.
     *
     * @param Education $education
     * @return NULL Doesn't return a value
     * @version 2.6.12
     */
    public function saveEducation(Education $education)
    {
       return $this->getEducationDao()->saveEducation($education);
    }

    /**
     * Retrieves an education object by ID
     *
     * @param int $id
     * @return Education An instance of Education or NULL
     * @version 2.6.12
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
     * @version 2.6.12
     */
    public function getEducationByName(string $name): ?Education
    {
        return $this->getEducationDao()->getEducationByName($name);
    }

    /**
     * Retrieves all education records ordered by name
     * @param string $sortField //change to the code base
     * @param string $sortOrder
     * @param null $limit
     * @param null $offset
     * @param false $count
     * @return int|mixed|string
     * @throws DaoException
     *
     */
    public function getEducationList(
        $sortField = 'jc.name', //change start here
        $sortOrder = 'ASC',
        $limit = null,
        $offset = null,
        $count = false //end here
    )
    {
        return $this->getEducationDao()->getEducationList($sortField, $sortOrder, $limit, $offset, $count);
    }

    /**
     * Deletes education records
     *
     * @param array $toDeleteIds An array of IDs to be deleted
     * @return int Number of records deleted
     * @version 2.6.12
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
     * @version 2.6.12
     */
    public function isExistingEducationName( string $educationName)
    {
        return $this->getEducationDao()->isExistingEducationName($educationName);
    }

}