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

namespace OrangeHRM\LDAP\Dto;

class LDAPEmployeeSearchFilterParams
{
    private ?int $empNumber = null;
    private ?string $employeeId = null;

    private ?string $workEmail = null;
    private ?string $drivingLicenseNo = null;
    private ?string $otherId = null;
    private ?string $otherEmail = null;

    private ?string $ssnNumber = null;
    private ?string $sinNumber = null;

    /**
     * @return int|null
     */
    public function getEmpNumber(): ?int
    {
        return $this->empNumber;
    }

    /**
     * @param int|null $empNumber
     */
    public function setEmpNumber(?int $empNumber): void
    {
        $this->empNumber = $empNumber;
    }

    /**
     * @return string|null
     */
    public function getEmployeeId(): ?string
    {
        return $this->employeeId;
    }

    /**
     * @param string|null $employeeId
     */
    public function setEmployeeId(?string $employeeId): void
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @return string|null
     */
    public function getWorkEmail(): ?string
    {
        return $this->workEmail;
    }

    /**
     * @param string|null $workEmail
     */
    public function setWorkEmail(?string $workEmail): void
    {
        $this->workEmail = $workEmail;
    }

    /**
     * @return string|null
     */
    public function getDrivingLicenseNo(): ?string
    {
        return $this->drivingLicenseNo;
    }

    /**
     * @param string|null $drivingLicenseNo
     */
    public function setDrivingLicenseNo(?string $drivingLicenseNo): void
    {
        $this->drivingLicenseNo = $drivingLicenseNo;
    }

    /**
     * @return string|null
     */
    public function getOtherId(): ?string
    {
        return $this->otherId;
    }

    /**
     * @param string|null $otherId
     */
    public function setOtherId(?string $otherId): void
    {
        $this->otherId = $otherId;
    }

    /**
     * @return string|null
     */
    public function getOtherEmail(): ?string
    {
        return $this->otherEmail;
    }

    /**
     * @param string|null $otherEmail
     */
    public function setOtherEmail(?string $otherEmail): void
    {
        $this->otherEmail = $otherEmail;
    }

    /**
     * @return string|null
     */
    public function getSsnNumber(): ?string
    {
        return $this->ssnNumber;
    }

    /**
     * @param string|null $ssnNumber
     */
    public function setSsnNumber(?string $ssnNumber): void
    {
        $this->ssnNumber = $ssnNumber;
    }

    /**
     * @return string|null
     */
    public function getSinNumber(): ?string
    {
        return $this->sinNumber;
    }

    /**
     * @param string|null $sinNumber
     */
    public function setSinNumber(?string $sinNumber): void
    {
        $this->sinNumber = $sinNumber;
    }
}
