<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payperiod
 *
 * @ORM\Table(name="hs_hr_payperiod")
 * @ORM\Entity
 */
class Payperiod
{
    /**
     * @var string
     *
     * @ORM\Column(name="payperiod_code", type="string", length=13)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="payperiod_name", type="string", length=100)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeSalary", mappedBy="Payperiod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="payperiod_code", referencedColumnName="payperiod_code")
     * })
     */
    private $EmployeeSalary;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->EmployeeSalary = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
