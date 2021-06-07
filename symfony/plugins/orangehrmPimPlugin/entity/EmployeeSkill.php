<?php

namespace OrangeHRM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="skills")
     * @ORM\Id
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var Skill
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Skill", inversedBy="employeeSkills")
     * @ORM\Id
     * @ORM\JoinColumn(name="skill_id")
     */
    private Skill $skill;

    /**
     * @var float
     *
     * @ORM\Column(name="years_of_exp", type="decimal", length=2)
     */
    private float $yearsOfExp;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="string", length=100)
     */
    private string $comments;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @return Employee|null
     */
    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee $employee
     */
    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return Skill|null
     */
    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    /**
     * @param Skill $skill
     */
    public function setSkill(Skill $skill): void
    {
        $this->skill = $skill;
    }

    /**
     * @return float
     */
    public function getYearsOfExp(): float
    {
        return $this->yearsOfExp;
    }

    /**
     * @param float $yearsOfExp
     */
    public function setYearsOfExp(float $yearsOfExp): void
    {
        $this->yearsOfExp = $yearsOfExp;
    }

    /**
     * @return string
     */
    public function getComments(): string
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     */
    public function setComments(string $comments): void
    {
        $this->comments = $comments;
    }
}
