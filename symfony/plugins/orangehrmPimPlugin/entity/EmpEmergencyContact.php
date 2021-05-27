<?php

namespace OrangeHRM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * EmpEmergencyContact
 *
 * @ORM\Table(name="hs_hr_emp_emergency_contacts")
 * @ORM\Entity
 */
class EmpEmergencyContact
{
    /**
     * @var Employee
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="dependents", cascade={"persist"})
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var int
     *
     * @ORM\Column(name="emp_number", type="integer", length=7)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private int $empNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="eec_seqno", type="decimal", precision=2, scale=0, options={"default" : 0})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private string $seqNo = '0';

    /**
     * @var string|null
     * @ORM\Column(name="eec_name", type="string", length=100 , nullable=true, options={"default" : ""})
     */
    private ?string $name = "";

    /**
     * @var string|null
     *
     * @ORM\Column(name="eec_relationship", type="string", length=100 , nullable=true, options={"default" : ""})
     */
    private ?string $relationship = "";

    /**
     * @var string|null
     *
     * @ORM\Column(name="eec_home_no", type="string", length=100, nullable=true, options={"default" : ""})
     */
    private ?string $homePhone = "";

    /**
     * @var string|null
     *
     * @ORM\Column(name="eec_mobile_no", type="string", length=100, nullable=true options={"default" : ""})
     */
    private ?string $mobilePhone = "";

    /**
     * @var string|null
     *
     * @ORM\Column(name="eec_office_no", type="string", length=100, nullable=true options={"default" : ""})
     */
    private ?string $officePhone = "";

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
     * @return int
     */
    public function getSeqNo(): int
    {
        return $this->seqNo;
    }

    /**
     * @param int $seqNo
     */
    public function setSeqNo(int $seqNo): void
    {
        $this->seqNo = $seqNo;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    /**
     * @param string|null $relationship
     */
    public function setRelationship(?string $relationship): void
    {
        $this->relationship = $relationship;
    }

    /**
     * @return string|null
     */
    public function getHomePhone(): ?string
    {
        return $this->homePhone;
    }

    /**
     * @param string|null $homePhone
     */
    public function setHomePhone(?string $homePhone): void
    {
        $this->homePhone = $homePhone;
    }

    /**
     * @return string|null
     */
    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    /**
     * @param string|null $mobilePhone
     */
    public function setMobilePhone(?string $mobilePhone): void
    {
        $this->mobilePhone = $mobilePhone;
    }

    /**
     * @return string|null
     */
    public function getOfficePhone(): ?string
    {
        return $this->officePhone;
    }

    /**
     * @param string|null $officePhone
     */
    public function setOfficePhone(?string $officePhone): void
    {
        $this->officePhone = $officePhone;
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
        $this->Employee = $employee;
    }


    /**
     * @var Collection
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private $Employee;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Employee = new ArrayCollection();
    }

}
