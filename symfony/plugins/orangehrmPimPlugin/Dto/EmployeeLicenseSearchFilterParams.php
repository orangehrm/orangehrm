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

namespace OrangeHRM\Pim\Dto;

use DateTime;
use OrangeHRM\Core\Dto\FilterParams;

class EmployeeLicenseSearchFilterParams extends FilterParams
{
    public const ALLOWED_SORT_FIELDS = ['el.licenseIssuedDate'];

    /**
     * @var string|null
     */
    protected ?string $empNumber;

    /**
     * @var string|null
     */
    protected ?string $licenseNo = null ;

    /**
     * @var DateTime|null
     */
    protected ?DateTime  $licenseIssuedDate = null;

    /**
     * @var DateTime|null
     */
    protected ?DateTime  $licenseExpiryDate = null;

    public function __construct()
    {
        $this->setSortField('el.licenseIssuedDate');
    }

    /**
     * @return int
     */
    public function getEmpNumber(): int
    {
        return $this->empNumber;
    }

    /**
     * @param int $empNumber
     */
    public function setEmpNumber(int $empNumber): void
    {
        $this->empNumber = $empNumber;
    }

    /**
     * @return string|null
     */
    public function getLicenseNo(): ?string
    {
        return $this->licenseNo;
    }

    /**
     * @param string|null $licenseNo
     */
    public function setLicenseNo(?string $licenseNo): void
    {
        $this->licenseNo = $licenseNo;
    }

    /**
     * @return DateTime|null
     */
    public function getLicenseIssuedDate(): ?DateTime
    {
        return $this->licenseIssuedDate;
    }

    /**
     * @param DateTime|null $licenseIssuedDate
     */
    public function setLicenseIssuedDate(?DateTime $licenseIssuedDate): void
    {
        $this->licenseIssuedDate = $licenseIssuedDate;
    }

    /**
     * @return DateTime|null
     */
    public function getLicenseExpiryDate(): ?DateTime
    {
        return $this->licenseExpiryDate;
    }

    /**
     * @param DateTime|null $licenseExpiryDate
     */
    public function setLicenseExpiryDate(?DateTime $licenseExpiryDate): void
    {
        $this->licenseExpiryDate = $licenseExpiryDate;
    }


}
