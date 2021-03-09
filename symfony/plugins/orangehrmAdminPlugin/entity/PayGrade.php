<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PayGrade
 *
 * @ORM\Table(name="ohrm_pay_grade")
 * @ORM\Entity
 */
class PayGrade
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=60)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeSalary", mappedBy="PayGrade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="payGradeId")
     * })
     */
    private $EmployeeSalary;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\PayGradeCurrency", mappedBy="PayGrade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="sal_grd_code")
     * })
     */
    private $PayGradeCurrency;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->EmployeeSalary = new \Doctrine\Common\Collections\ArrayCollection();
        $this->PayGradeCurrency = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
