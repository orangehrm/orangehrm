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

use \BaseService;
use OrangeHRM\Admin\Dao\EmploymentStatusDao;
use \DaoException;
use OrangeHRM\Entity\EmploymentStatus;

class EmploymentStatusService {//extends \BaseService {

	private $empStatusDao;

	/**
	 * Construct
	 */
	public function __construct() {
		$this->empStatusDao = new EmploymentStatusDao();
	}

	/**
	 *
	 * @return EmploymentStatusDao
	 */
	public function getEmploymentStatusDao() {
		return $this->empStatusDao;
	}

	/**
	 *
	 * @param EmploymentStatusDao $employmentStatusDao
	 */
	public function setEmploymentStatusDao(EmploymentStatusDao $employmentStatusDao) {
		$this->empStatusDao = $employmentStatusDao;
	}

	public function getEmploymentStatusById($id){
		return $this->empStatusDao->getEmploymentStatusById($id);
	}

    /**
     * @param string $sortField
     * @param string $sortOrder
     * @param null $limit
     * @param null $offset
     * @param false $count
     * @return int|mixed|string
     * @throws DaoException
     */
    public function getEmploymentStatusList(
        $sortField = 'es.name',
        $sortOrder = 'ASC',
        $limit = null,
        $offset = null,
        $count = false
    ) {
        return $this->getEmploymentStatusDao()->getEmploymentStatusList($sortField, $sortOrder, $limit, $offset, $count);
    }

    /**
     * @param EmploymentStatus $employmentStatus
     * @return EmploymentStatus
     * @throws DaoException
     */
    public function saveEmploymentStatus(EmploymentStatus $employmentStatus): EmploymentStatus
    {
        return $this->getEmploymentStatusDao()->saveEmploymentStatus($employmentStatus);
    }

    public function deleteEmploymentStatus(array $toBeDeletedEmploymentStatusIds)
    {
        return $this->getEmploymentStatusDao()->deleteEmploymentStatus($toBeDeletedEmploymentStatusIds);
    }
}
