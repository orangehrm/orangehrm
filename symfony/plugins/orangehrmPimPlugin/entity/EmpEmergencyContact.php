<?php

namespace OrangeHRM\Entity;

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
     * @var int
     *
     * @ORM\Column(name="emp_number", type="integer", length=7)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private int $empNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="eec_seqno", type="integer", length=2)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private int $seqNo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="eec_name", type="string", length=100 , nullable=true)
     */
    private ?string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="eec_relationship", type="string", length=100 ,nullable=true)
     */
    private string $relationship;

    /**
     * @var string
     *
     * @ORM\Column(name="eec_home_no", type="string", length=100)
     */
    private string $home_phone;

    /**
     * @var string
     *
     * @ORM\Column(name="eec_mobile_no", type="string", length=100)
     */
    private string $mobile_phone;

    /**
     * @var string
     *
     * @ORM\Column(name="eec_office_no", type="string", length=100)
     */
    private string $office_phone;

    /**
     * @return int
     */
    public function getEmpNumber(): int
    {
        return $this->emp_number;
    }

    /**
     * @param int $emp_number
     */
    public function setEmpNumber(int $emp_number): void
    {
        $this->emp_number = $emp_number;
    }

    /**
     * @return int
     */
    public function getSeqno(): int
    {
        return $this->seqno;
    }

    /**
     * @param int $seqno
     */
    public function setSeqno(int $seqno): void
    {
        $this->seqno = $seqno;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getRelationship(): string
    {
        return $this->relationship;
    }

    /**
     * @param string $relationship
     */
    public function setRelationship(string $relationship): void
    {
        $this->relationship = $relationship;
    }

    /**
     * @return string
     */
    public function getHomePhone(): string
    {
        return $this->home_phone;
    }

    /**
     * @param string $home_phone
     */
    public function setHomePhone(string $home_phone): void
    {
        $this->home_phone = $home_phone;
    }

    /**
     * @return string
     */
    public function getMobilePhone(): string
    {
        return $this->mobile_phone;
    }

    /**
     * @param string $mobile_phone
     */
    public function setMobilePhone(string $mobile_phone): void
    {
        $this->mobile_phone = $mobile_phone;
    }

    /**
     * @return string
     */
    public function getOfficePhone(): string
    {
        return $this->office_phone;
    }

    /**
     * @param string $office_phone
     */
    public function setOfficePhone(string $office_phone): void
    {
        $this->office_phone = $office_phone;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmployee()
    {
        return $this->Employee;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $Employee
     */
    public function setEmployee($Employee): void
    {
        $this->Employee = $Employee;
    }



    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmpEmergencyContact")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $Employee;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Employee = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
