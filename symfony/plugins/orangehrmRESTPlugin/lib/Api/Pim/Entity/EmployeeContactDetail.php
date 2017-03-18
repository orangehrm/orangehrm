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

class EmployeeContactDetail implements Serializable
{
    /**
     * @var
     */
    private $fullName = '';

    private $id = '';

    private $empNumber = '';

    private $workTelephone = '';

    private $workEmail = '';

    private $addressStreet1 = '';

    private $addressStreet2 = '';

    private $city = '';

    private $state = '';

    private $zip = '';

    private $homeTelephone = '';

    private $mobile = '';

    private $otherEmail = '';

    private $country = '';

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAddressStreet2()
    {
        return $this->addressStreet2;
    }

    /**
     * @param string $addressStreet2
     */
    public function setAddressStreet2($addressStreet2)
    {
        $this->addressStreet2 = $addressStreet2;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getEmpNumber()
    {
        return $this->empNumber;
    }

    /**
     * @param string $empNumber
     */
    public function setEmpNumber($empNumber)
    {
        $this->empNumber = $empNumber;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getHomeTelephone()
    {
        return $this->homeTelephone;
    }

    /**
     * @param string $homeTelephone
     */
    public function setHomeTelephone($homeTelephone)
    {
        $this->homeTelephone = $homeTelephone;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getOtherEmail()
    {
        return $this->otherEmail;
    }

    /**
     * @param string $otherEmail
     */
    public function setOtherEmail($otherEmail)
    {
        $this->otherEmail = $otherEmail;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getWorkTelephone()
    {
        return $this->workTelephone;
    }

    /**
     * @param string $workTelephone
     */
    public function setWorkTelephone($workTelephone)
    {
        $this->workTelephone = $workTelephone;
    }

    /**
     * @return string
     */
    public function getWorkEmail()
    {
        return $this->workEmail;
    }

    /**
     * @param string $workEmail
     */
    public function setWorkEmail($workEmail)
    {
        $this->workEmail = $workEmail;
    }

    /**
     * @return string
     */
    public function getAddressStreet1()
    {
        return $this->addressStreet1;
    }

    /**
     * @param string $addressStreet1
     */
    public function setAddressStreet1($addressStreet1)
    {
        $this->addressStreet1 = $addressStreet1;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }


    /**
     * EmployeeContactDetail constructor.
     * @param $name
     * @param $number
     */
    public function __construct($name, $number)
    {
        $this->setFullName($name);
        $this->setEmpNumber($number);
        return $this;
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'code' => $this->getEmpNumber(),
            'fullName' => $this->getFullName(),
            'addressStreet1' => $this->getAddressStreet1(),
            'addressStreet2' => $this->getAddressStreet2(),
            'city' => $this->getCity(),
            'state' => $this->getState(),
            'zip' => $this->getZip(),
            'county' => $this->getCountry(),
            'homeTelephone' => $this->getHomeTelephone(),
            'workTelephone' => $this->getWorkTelephone(),
            'mobile' => $this->getMobile(),
            'workEmail' => $this->getWorkEmail(),
            'otherEmail' => $this->getOtherEmail()
        );
    }

    public function buildContactDetails(\Employee $employee)
    {

        $this->setWorkTelephone($employee->getEmpWorkTelephone());
        $this->setWorkEmail($employee->getEmpWorkEmail());
        $this->setAddressStreet1($employee->getStreet1());
        $this->setAddressStreet2($employee->getStreet2());
        $this->setCity($employee->getCity());
        $this->setState($employee->getProvince());
        $this->setZip($employee->getEmpZipcode());
        $this->setCountry($employee->getCountry());
        $this->setHomeTelephone($employee->getEmpHmTelephone());
        $this->setMobile($employee->getEmpMobile());
        $this->setOtherEmail($employee->getEmpOthEmail());
        $this->setId($employee->getEmpNumber());
    }
}