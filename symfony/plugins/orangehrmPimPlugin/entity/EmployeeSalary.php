<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeSalary
 *
 * @ORM\Table(name="hs_hr_emp_basicsalary")
 * @ORM\Entity
 */
class EmployeeSalary
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="emp_number", type="integer", length=4)
     */
    private $empNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="sal_grd_code", type="integer")
     */
    private $payGradeId;

    /**
     * @var string
     *
     * @ORM\Column(name="currency_id", type="string", length=6)
     */
    private $currencyCode;

    /**
     * @var string
     *
     * @ORM\Column(name="ebsal_basic_salary", type="string", length=100)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="payperiod_code", type="string", length=13)
     */
    private $payPeriodId;

    /**
     * @var string
     *
     * @ORM\Column(name="salary_component", type="string", length=100)
     */
    private $salaryName;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="string", length=255)
     */
    private $notes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\CurrencyType", mappedBy="EmployeeSalary")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="currencyCode", referencedColumnName="currency_id")
     * })
     */
    private $currencyType;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmployeeSalary")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="empNumber", referencedColumnName="empNumber")
     * })
     */
    private $employee;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Payperiod", mappedBy="EmployeeSalary")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="payPeriodId", referencedColumnName="payperiod_code")
     * })
     */
    private $payperiod;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\EmpDirectdebit", mappedBy="EmployeeSalary")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="salary_id")
     * })
     */
    private $directDebit;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\PayGrade", mappedBy="EmployeeSalary")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="payGradeId", referencedColumnName="id")
     * })
     */
    private $payGrade;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->currencyType = new \Doctrine\Common\Collections\ArrayCollection();
        $this->employee = new \Doctrine\Common\Collections\ArrayCollection();
        $this->payperiod = new \Doctrine\Common\Collections\ArrayCollection();
        $this->directDebit = new \Doctrine\Common\Collections\ArrayCollection();
        $this->payGrade = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
