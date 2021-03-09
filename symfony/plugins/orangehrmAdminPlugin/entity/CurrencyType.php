<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CurrencyType
 *
 * @ORM\Table(name="hs_hr_currency_type")
 * @ORM\Entity
 */
class CurrencyType
{
    /**
     * @var int
     *
     * @ORM\Column(name="code", type="integer", length=4)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="currency_id", type="string", length=3)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="currency_name", type="string", length=70)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\EmployeeSalary", mappedBy="CurrencyType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="currency_id", referencedColumnName="currency_id")
     * })
     */
    private $EmployeeSalary;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\PayGradeCurrency", mappedBy="CurrencyType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="currency_id", referencedColumnName="currency_id")
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
