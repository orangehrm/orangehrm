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

class Customer implements Serializable
{
    /**
     * @var
     */
    private $customerId;
    private $name;
    private $description;
    private $isDeleted;

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param mixed $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @param mixed $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }



    /**
     * To array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'customerId' => $this->getCustomerId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'isDeleted' => $this->getIsDeleted(),
        );
    }

    /**
     * Build Customer
     *
     * @param \Customer $customer
     */
    public function build(\Customer $customer)
    {
        $this->setCustomerId($customer->getCustomerId());
        $this->setName($customer->getName());
        $this->setIsDeleted($customer->getIsDeleted());
        $this->setDescription($customer->getDescription());
    }
}