<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeTerminationRecord
 *
 * @ORM\Table(name="ohrm_emp_termination")
 * @ORM\Entity
 */
class EmployeeTerminationRecord
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
     * @ORM\Column(name="reason_id", type="integer", length=4)
     */
    private $reasonId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="termination_date", type="date", length=25)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=255)
     */
    private $note;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\TerminationReason", mappedBy="EmployeeTerminationRecord")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reasonId", referencedColumnName="id")
     * })
     */
    private $TerminationReason;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmployeeTerminationRecord")
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
        $this->TerminationReason = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Employee = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
