<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_leave_adjustment")
 * @ORM\Entity
 */
class LeaveAdjustment
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
     * @ORM\Column(name="emp_number", type="integer", length=7)
     */
    private $emp_number;

    /**
     * @var string
     *
     * @ORM\Column(name="no_of_days", type="decimal", length=6, scale=)
     */
    private $no_of_days;

    /**
     * @var int
     *
     * @ORM\Column(name="leave_type_id", type="integer", length=4)
     */
    private $leave_type_id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="from_date", type="datetime", length=25)
     */
    private $from_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="to_date", type="datetime", length=25)
     */
    private $to_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="credited_date", type="datetime", length=25)
     */
    private $credited_date;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=255)
     */
    private $note;

    /**
     * @var int
     *
     * @ORM\Column(name="adjustment_type", type="integer", length=4)
     */
    private $adjustment_type;

    /**
     * @var int
     *
     * @ORM\Column(name="deleted", type="integer", length=1)
     */
    private $deleted;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by_id", type="integer", length=10)
     */
    private $created_by_id;

    /**
     * @var string
     *
     * @ORM\Column(name="created_by_name", type="string", length=255)
     */
    private $created_by_name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="LeaveType", mappedBy="LeaveAdjustment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="leave_type_id", referencedColumnName="id")
     * })
     */
    private $LeaveType;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Employee", mappedBy="LeaveAdjustment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $Employee;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="SystemUser", mappedBy="LeaveAdjustment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by_id", referencedColumnName="id")
     * })
     */
    private $SystemUser;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="LeaveEntitlementType", mappedBy="LeaveAdjustment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="adjustment_type", referencedColumnName="id")
     * })
     */
    private $LeaveEntitlementType;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->LeaveType = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Employee = new \Doctrine\Common\Collections\ArrayCollection();
        $this->SystemUser = new \Doctrine\Common\Collections\ArrayCollection();
        $this->LeaveEntitlementType = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
