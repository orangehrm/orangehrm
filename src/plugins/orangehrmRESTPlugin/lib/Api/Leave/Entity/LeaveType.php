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

namespace Orangehrm\Rest\Api\Leave\Entity;

use Orangehrm\Rest\Api\Entity\Serializable;
class LeaveType implements Serializable
{

    /**
     * @var
     */
     private $leaveType;
     private $id;

    /**
     * @return mixed
     */
    public function getLeaveType()
    {
        return $this->leaveType;
    }

    /**
     * @param mixed $leaveType
     */
    public function setLeaveType($leaveType)
    {
        $this->leaveType = $leaveType;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * LeaveType constructor.
     *
     * @param $id
     * @param $type
     */
    public function __construct($id,$type)
    {
        $this->setId($id);
        $this->setLeaveType($type);
        return $this;
    }

    public function toArray()
    {
        return array(
            'type' => $this->getLeaveType(),
            'id' => $this->getId(),

        );
    }
}
