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

class LeaveEntitlement implements Serializable
{

     private $id;
     private $entitlementType;
     private $validFrom;
     private $validTo;
     private $days;

    /**
     * @return mixed
     */
    public function getEntitlementType()
    {
        return $this->entitlementType;
    }

    /**
     * @param mixed $entitlementType
     */
    public function setEntitlementType($entitlementType)
    {
        $this->entitlementType = $entitlementType;
    }

    /**
     * @return mixed
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * @param mixed $validFrom
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;
    }

    /**
     * @return mixed
     */
    public function getValidTo()
    {
        return $this->validTo;
    }

    /**
     * @param mixed $validTo
     */
    public function setValidTo($validTo)
    {
        $this->validTo = $validTo;
    }

    /**
     * @return mixed
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param mixed $days
     */
    public function setDays($days)
    {
        $this->days = $days;
    }


    /**
     * @return mixed
     */


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
     * LeaveEntitlement constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->setId($id);
        return $this;
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'type' => $this->getEntitlementType(),
            'validFrom' => $this->getValidFrom(),
            'validTo' => $this->getValidTo(),
            'days' => $this->getDays()

        );
    }
    public function buildEntitlement(\LeaveEntitlement $entitlement){

        $this->setDays($entitlement->getNoOfDays()+ 0); // adding a zero to remove unwanted zero digits in decimals
        $this->setValidFrom(substr($entitlement->getFromDate(), 0, -9));
        $this->setValidTo(substr($entitlement->getToDate(), 0, -9));
        $this->setEntitlementType($entitlement->getLeaveType()->getName());
    }
}
