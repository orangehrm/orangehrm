<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeSkill
 *
 * @ORM\Table(name="hs_hr_emp_skill")
 * @ORM\Entity
 */
class EmployeeSkill
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
     * @var int
     *
     * @ORM\Column(name="skill_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $skillId;

    /**
     * @var string
     *
     * @ORM\Column(name="years_of_exp", type="decimal", length=2)
     */
    private $years_of_exp;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="string", length=100)
     */
    private $comments;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmployeeSkill")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $Employee;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Skill", mappedBy="EmployeeSkill")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="skillId", referencedColumnName="id")
     * })
     */
    private $Skill;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Employee = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Skill = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
