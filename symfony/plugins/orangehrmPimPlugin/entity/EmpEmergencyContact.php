<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmpEmergencyContact
 *
 * @ORM\Table(name="hs_hr_emp_emergency_contacts")
 * @ORM\Entity
 */
class EmpEmergencyContact
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
     * @ORM\Column(name="eec_seqno", type="decimal", length=2)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $seqno;

    /**
     * @var string
     *
     * @ORM\Column(name="eec_name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="eec_relationship", type="string", length=100)
     */
    private $relationship;

    /**
     * @var string
     *
     * @ORM\Column(name="eec_home_no", type="string", length=100)
     */
    private $home_phone;

    /**
     * @var string
     *
     * @ORM\Column(name="eec_mobile_no", type="string", length=100)
     */
    private $mobile_phone;

    /**
     * @var string
     *
     * @ORM\Column(name="eec_office_no", type="string", length=100)
     */
    private $office_phone;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmpEmergencyContact")
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
