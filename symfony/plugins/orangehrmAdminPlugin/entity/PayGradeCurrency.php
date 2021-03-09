<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PayGradeCurrency
 *
 * @ORM\Table(name="ohrm_pay_grade_currency")
 * @ORM\Entity
 */
class PayGradeCurrency
{
    /**
     * @var int
     *
     * @ORM\Column(name="pay_grade_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $pay_grade_id;

    /**
     * @var string
     *
     * @ORM\Column(name="currency_id", type="string", length=6)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $currency_id;

    /**
     * @var float
     *
     * @ORM\Column(name="min_salary", type="float", length=2147483647)
     */
    private $minSalary;

    /**
     * @var float
     *
     * @ORM\Column(name="max_salary", type="float", length=2147483647)
     */
    private $maxSalary;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\CurrencyType", mappedBy="PayGradeCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="currency_id", referencedColumnName="currency_id")
     * })
     */
    private $CurrencyType;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\PayGrade", mappedBy="PayGradeCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pay_grade_id", referencedColumnName="id")
     * })
     */
    private $PayGrade;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->CurrencyType = new \Doctrine\Common\Collections\ArrayCollection();
        $this->PayGrade = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
