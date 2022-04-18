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

namespace OrangeHRM\Pim\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmpPicture;

class EmployeePictureDao extends BaseDao
{
    /**
     * @param EmpPicture $employee
     * @return EmpPicture
     * @throws DaoException
     */
    public function saveEmployeePicture(EmpPicture $employee): EmpPicture
    {
        try {
            $this->persist($employee);
            return $employee;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $empNumber
     * @return EmpPicture|null
     * @throws DaoException
     */
    public function getEmpPictureByEmpNumber(int $empNumber): ?EmpPicture
    {
        try {
            $empPicture = $this->getRepository(EmpPicture::class)->find($empNumber);
            if ($empPicture instanceof EmpPicture) {
                return $empPicture;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
