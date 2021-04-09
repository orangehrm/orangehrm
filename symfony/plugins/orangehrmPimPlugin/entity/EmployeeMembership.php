<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeMembership
 *
 * @ORM\Table(name="hs_hr_emp_member_detail")
 * @ORM\Entity
 */
class EmployeeMembership
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=6)
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
     * @ORM\Column(name="membship_code", type="integer")
     */
    private $membershipId;

    /**
     * @var string
     *
     * @ORM\Column(name="ememb_subscript_amount", type="decimal", length=15)
     */
    private $subscriptionFee;

    /**
     * @var string
     *
     * @ORM\Column(name="ememb_subscript_ownership", type="string", length=30)
     */
    private $subscriptionPaidBy;

    /**
     * @var string
     *
     * @ORM\Column(name="ememb_subs_currency", type="string", length=13)
     */
    private $subscriptionCurrency;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ememb_commence_date", type="date", length=25)
     */
    private $subscriptionCommenceDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ememb_renewal_date", type="date", length=25)
     */
    private $subscriptionRenewalDate;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Membership", mappedBy="EmployeeMembership")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="membershipId", referencedColumnName="id")
     * })
     */
    private $Membership;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmployeeMembership")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="empNumber", referencedColumnName="empNumber")
     * })
     */
    private $Employee;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Membership = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Employee = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
