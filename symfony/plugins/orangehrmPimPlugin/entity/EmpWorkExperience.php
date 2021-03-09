<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmpWorkExperience
 *
 * @ORM\Table(name="hs_hr_emp_work_experience")
 * @ORM\Entity
 */
class EmpWorkExperience
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
     * @ORM\Column(name="eexp_seqno", type="decimal", length=10)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $seqno;

    /**
     * @var string
     *
     * @ORM\Column(name="eexp_employer", type="string", length=100)
     */
    private $employer;

    /**
     * @var string
     *
     * @ORM\Column(name="eexp_jobtit", type="string", length=120)
     */
    private $jobtitle;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="eexp_from_date", type="datetime", length=25)
     */
    private $from_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="eexp_to_date", type="datetime", length=25)
     */
    private $to_date;

    /**
     * @var string
     *
     * @ORM\Column(name="eexp_comments", type="string", length=200)
     */
    private $comments;

    /**
     * @var int
     *
     * @ORM\Column(name="eexp_internal", type="integer", length=4)
     */
    private $internal;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmpWorkExperience")
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
