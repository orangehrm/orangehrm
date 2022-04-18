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

namespace Orangehrm\Rest\Api\Pim\Entity;

use Orangehrm\Rest\Api\Entity\Serializable;

class EmployeeDependent implements Serializable
{
    /**
     * @var
     */
    private $name = '';

    private $relationship = '';

    private $dateOfBirth = '';

    private $dependentSeqNumber = '';


    /**
     * EmployeeDependant constructor.
     * @param $name
     * @param $relationship
     * @param $dob
     */
    public function __construct($name, $relationship, $dob, $seqNumber)
    {

        $this->setName($name);
        $this->setRelationship($relationship);
        $this->setDateOfBirth($dob);
        $this->setDependentSeqNumber($seqNumber);

        return $this;
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
     * @return string
     */
    public function getRelationship()
    {
        return $this->relationship;
    }

    /**
     * @param string $relationShip
     */
    public function setRelationship($relationship)
    {
        $this->relationship = $relationship;
    }

    /**
     * @return string
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @param string $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return string
     */
    public function getDependentSeqNumber()
    {
        return $this->dependentSeqNumber;
    }

    /**
     * @param string $dependentSeqNumber
     */
    public function setDependentSeqNumber($dependentSeqNumber)
    {
        $this->dependentSeqNumber = $dependentSeqNumber;
    }


    public function toArray()
    {
        return array(
            'name' => $this->getName(),
            'relationship' => $this->getRelationship(),
            'dob' => $this->getDateOfBirth(),
            'sequenceNumber'=> $this->getDependentSeqNumber()
        );
    }
}