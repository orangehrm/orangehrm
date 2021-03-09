<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmpContract
 *
 * @ORM\Table(name="hs_hr_emp_contract_extend")
 * @ORM\Entity
 */
class EmpContract
{
    /**
     * @var int
     *
     * @ORM\Column(name="emp_number", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $emp_number;

    /**
     * @var string
     *
     * @ORM\Column(name="econ_extend_id", type="decimal", length=10)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $contract_id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="econ_extend_start_date", type="datetime", length=25)
     */
    private $start_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="econ_extend_end_date", type="datetime", length=25)
     */
    private $end_date;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmpContract")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $employee;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->employee = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
    public function setEmpNumber(int $emp_number)
    {
        $this->emp_number = $emp_number;
    }

    /**
     * @return string
     */
    public function getContractId(): string
    {
        return $this->contract_id;
    }

    /**
     * @param string $contract_id
     */
    public function setContractId(string $contract_id)
    {
        $this->contract_id = $contract_id;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->start_date;
    }

    /**
     * @param \DateTime $start_date
     */
    public function setStartDate(\DateTime $start_date)
    {
        $this->start_date = $start_date;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        return $this->end_date;
    }

    /**
     * @param \DateTime $end_date
     */
    public function setEndDate(\DateTime $end_date)
    {
        $this->end_date = $end_date;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $employee
     */
    public function setEmployee($employee)
    {
        $this->employee = $employee;
    }
}
