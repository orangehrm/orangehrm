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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\LDAP\Dto;

class LDAPEmployee
{
    private int $empNumber;
    private string $lastName;
    private string $firstName;
    private string $middleName = '';
    private ?string $employeeId = null;

    private ?string $workEmail = null;
    private ?string $drivingLicenseNo = null;
    private ?string $otherId = null;
    private ?string $otherEmail = null;
    private ?string $homeTelephone = null;
    private ?string $mobile = null;
    private ?string $workTelephone = null;

    private ?string $ssnNumber = null;
    private ?string $sinNumber = null;

    /**
     * @param int $empNumber
     * @param string $lastName
     * @param string $firstName
     * @param string $middleName
     * @param string|null $employeeId
     * @param string|null $workEmail
     * @param string|null $drivingLicenseNo
     * @param string|null $otherId
     * @param string|null $otherEmail
     * @param string|null $homeTelephone
     * @param string|null $mobile
     * @param string|null $workTelephone
     * @param string|null $ssnNumber
     * @param string|null $sinNumber
     */
    public function __construct(
        int $empNumber,
        string $lastName,
        string $firstName,
        string $middleName,
        ?string $employeeId,
        ?string $workEmail,
        ?string $drivingLicenseNo,
        ?string $otherId,
        ?string $otherEmail,
        ?string $homeTelephone,
        ?string $mobile,
        ?string $workTelephone,
        ?string $ssnNumber,
        ?string $sinNumber
    ) {
        $this->empNumber = $empNumber;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->employeeId = $employeeId;
        $this->workEmail = $workEmail;
        $this->drivingLicenseNo = $drivingLicenseNo;
        $this->otherId = $otherId;
        $this->otherEmail = $otherEmail;
        $this->homeTelephone = $homeTelephone;
        $this->mobile = $mobile;
        $this->workTelephone = $workTelephone;
        $this->ssnNumber = $ssnNumber;
        $this->sinNumber = $sinNumber;
    }

    /**
     * @return int
     */
    public function getEmpNumber(): int
    {
        return $this->empNumber;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    /**
     * @return string|null
     */
    public function getEmployeeId(): ?string
    {
        return $this->employeeId;
    }

    /**
     * @return string|null
     */
    public function getWorkEmail(): ?string
    {
        return $this->workEmail;
    }

    /**
     * @return string|null
     */
    public function getDrivingLicenseNo(): ?string
    {
        return $this->drivingLicenseNo;
    }

    /**
     * @return string|null
     */
    public function getOtherId(): ?string
    {
        return $this->otherId;
    }

    /**
     * @return string|null
     */
    public function getOtherEmail(): ?string
    {
        return $this->otherEmail;
    }

    /**
     * @return string|null
     */
    public function getHomeTelephone(): ?string
    {
        return $this->homeTelephone;
    }

    /**
     * @return string|null
     */
    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    /**
     * @return string|null
     */
    public function getWorkTelephone(): ?string
    {
        return $this->workTelephone;
    }

    /**
     * @return string|null
     */
    public function getSsnNumber(): ?string
    {
        return $this->ssnNumber;
    }

    /**
     * @return string|null
     */
    public function getSinNumber(): ?string
    {
        return $this->sinNumber;
    }
}
