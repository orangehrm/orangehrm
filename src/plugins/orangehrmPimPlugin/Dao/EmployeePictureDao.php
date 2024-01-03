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

namespace OrangeHRM\Pim\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\EmpPicture;

class EmployeePictureDao extends BaseDao
{
    /**
     * @param EmpPicture $employee
     * @return EmpPicture
     */
    public function saveEmployeePicture(EmpPicture $employee): EmpPicture
    {
        $this->persist($employee);
        return $employee;
    }

    /**
     * @param int $empNumber
     * @return EmpPicture|null
     */
    public function getEmpPictureByEmpNumber(int $empNumber): ?EmpPicture
    {
        $empPicture = $this->getRepository(EmpPicture::class)->find($empNumber);
        if ($empPicture instanceof EmpPicture) {
            return $empPicture;
        }
        return null;
    }
}
