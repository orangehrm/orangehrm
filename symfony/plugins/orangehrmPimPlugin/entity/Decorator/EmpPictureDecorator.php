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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmpPicture;

class EmpPictureDecorator
{
    use EntityManagerHelperTrait;

    /**
     * @var EmpPicture
     */
    protected EmpPicture $empPicture;

    /**
     * This property to read `picture` resource in `EmpPicture`
     * @var string|null
     */
    private ?string $pictureString = null;

    /**
     * @param EmpPicture $employee
     */
    public function __construct(EmpPicture $employee)
    {
        $this->empPicture = $employee;
    }

    /**
     * @return EmpPicture
     */
    protected function getEmpPicture(): EmpPicture
    {
        return $this->empPicture;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getEmpPicture()->setEmployee($employee);
    }

    /**
     * @return string
     */
    public function getPicture(): string
    {
        $picture = $this->getEmpPicture()->getPicture();
        if (is_string($picture)) {
            return $picture;
        }
        if (is_null($this->pictureString) && is_resource($picture)) {
            $this->pictureString = stream_get_contents($picture);
        }
        return $this->pictureString;
    }
}
