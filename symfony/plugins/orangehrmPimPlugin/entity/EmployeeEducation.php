<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeEducation
 *
 * @ORM\Table(name="ohrm_emp_education")
 * @ORM\Entity
 */
class EmployeeEducation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="emp_number", type="integer")
     */
    private $empNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="education_id", type="integer")
     */
    private $educationId;

    /**
     * @var string
     *
     * @ORM\Column(name="institute", type="string", length=100)
     */
    private $institute;

    /**
     * @var string
     *
     * @ORM\Column(name="major", type="string", length=100)
     */
    private $major;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="decimal", length=4)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="score", type="string", length=25)
     */
    private $score;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="date")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="date")
     */
    private $endDate;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="education")
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private $employee;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Education", mappedBy="EmployeeEducation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="educationId", referencedColumnName="id")
     * })
     */
    private $Education;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->employee = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Education = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
