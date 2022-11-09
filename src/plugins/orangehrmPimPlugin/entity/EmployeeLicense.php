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

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\EmployeeLicenseDecorator;

/**
 * @method EmployeeLicenseDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_emp_license")
 * @ORM\Entity
 */
class EmployeeLicense
{
    use DecoratorTrait;

    /**
     * @var License
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\License", inversedBy="employeeLicenses")
     * @ORM\Id
     * @ORM\JoinColumn(name="license_id", referencedColumnName="id")
     */
    private License $license;

    /**
     * @var string|null
     *
     * @ORM\Column(name="license_no", type="string", length=50, nullable=true)
     */
    private ?string $licenseNo;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="license_issued_date", type="date", nullable=true)
     */
    private ?DateTime $licenseIssuedDate = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="license_expiry_date", type="date", nullable=true)
     */
    private ?DateTime $licenseExpiryDate = null;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="licenses", cascade={"persist"})
     * @ORM\Id
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @return License
     */
    public function getLicense(): License
    {
        return $this->license;
    }

    /**
     * @param License $license
     */
    public function setLicense(License $license): void
    {
        $this->license = $license;
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

    /**
     * @return Employee|null
     */
    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee $employee
     */
    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;
    }
}
