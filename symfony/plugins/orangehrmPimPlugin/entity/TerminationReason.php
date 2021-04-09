<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TerminationReason
 *
 * @ORM\Table(name="ohrm_emp_termination_reason")
 * @ORM\Entity
 */
class TerminationReason
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\EmployeeTerminationRecord", mappedBy="TerminationReason")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="reasonId")
     * })
     */
    private $EmployeeTerminationRecord;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->EmployeeTerminationRecord = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
