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

namespace Orangehrm\Rest\Api\Time\Entity;

use Orangehrm\Rest\Api\Entity\Serializable;

class ProjectAdmin implements Serializable
{
    /**
     * @var
     */
    private $employeeId = '';

    private $name = '';

    /**
     * @return mixed
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @param mixed $employeeId
     */
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }



    /**
     * To array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'employeeId' => $this->getEmployeeId(),
            'name' => $this->getName()
        );
    }

    /**
     * Build Project admins
     *
     * @param \ProjectAdmin $projectAdmin
     */
    public function build(\ProjectAdmin $projectAdmin)
    {
        $this->setEmployeeId($projectAdmin->getEmpNumber());
        $this->setName($projectAdmin->getEmployee()->getFullName());
    }
}