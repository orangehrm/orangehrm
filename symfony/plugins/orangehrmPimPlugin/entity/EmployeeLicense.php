<?php

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
    private License $licenseId;

    /**
     * @var string
     *
     * @ORM\Column(name="license_no", type="string", length=50)
     */
    private string $licenseNo;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="license_issued_date", type="date")
     */
    private DateTime $licenseIssuedDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="license_expiry_date", type="date")
     */
    private DateTime $licenseExpiryDate;

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
    public function getLicenseId(): License
    {
        return $this->licenseId;
    }

    /**
     * @param License $licenseId
     */
    public function setLicenseId(License $licenseId): void
    {
        $this->licenseId = $licenseId;
    }

    /**
     * @return string
     */
    public function getLicenseNo(): string
    {
        return $this->licenseNo;
    }

    /**
     * @param string $licenseNo
     */
    public function setLicenseNo(string $licenseNo): void
    {
        $this->licenseNo = $licenseNo;
    }

    /**
     * @return DateTime
     */
    public function getLicenseIssuedDate(): DateTime
    {
        return $this->licenseIssuedDate;
    }

    /**
     * @param DateTime $licenseIssuedDate
     */
    public function setLicenseIssuedDate(DateTime $licenseIssuedDate): void
    {
        $this->licenseIssuedDate = $licenseIssuedDate;
    }

    /**
     * @return DateTime
     */
    public function getLicenseExpiryDate(): DateTime
    {
        return $this->licenseExpiryDate;
    }

    /**
     * @param DateTime $licenseExpiryDate
     */
    public function setLicenseExpiryDate(DateTime $licenseExpiryDate): void
    {
        $this->licenseExpiryDate = $licenseExpiryDate;
    }

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
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
