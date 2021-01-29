<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmpDependent
 *
 * @ORM\Table(name="hs_hr_emp_dependents")
 * @ORM\Entity
 */
class EmpDependent
{
    /**
     * @var int
     *
     * @ORM\Column(name="emp_number", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $emp_number;

    /**
     * @var string
     *
     * @ORM\Column(name="ed_seqno", type="decimal", length=2)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $seqno;

    /**
     * @var string
     *
     * @ORM\Column(name="ed_name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="ed_relationship_type", type="string")
     */
    private $relationship_type;

    /**
     * @var string
     *
     * @ORM\Column(name="ed_relationship", type="string", length=100)
     */
    private $relationship;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ed_date_of_birth", type="date", length=25)
     */
    private $date_of_birth;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmpDependent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $Employee;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Employee = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
