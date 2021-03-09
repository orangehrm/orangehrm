<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmpDirectdebit
 *
 * @ORM\Table(name="hs_hr_emp_directdebit")
 * @ORM\Entity
 */
class EmpDirectdebit
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
     * @ORM\Column(name="salary_id", type="integer", length=4)
     */
    private $salary_id;

    /**
     * @var int
     *
     * @ORM\Column(name="dd_routing_num", type="integer", length=9)
     */
    private $routing_num;

    /**
     * @var string
     *
     * @ORM\Column(name="dd_account", type="string", length=100)
     */
    private $account;

    /**
     * @var string
     *
     * @ORM\Column(name="dd_amount", type="decimal", length=11, scale=)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="dd_account_type", type="string", length=20)
     */
    private $account_type;

    /**
     * @var string
     *
     * @ORM\Column(name="dd_transaction_type", type="string", length=20)
     */
    private $transaction_type;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\EmployeeSalary", mappedBy="EmpDirectdebit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="salary_id", referencedColumnName="id", onDelete="Cascade")
     * })
     */
    private $salary;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->salary = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
