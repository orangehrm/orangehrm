<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\EmployeeSkillDecorator;

/**
 * @method EmployeeSkillDecorator getDecorator()
 *
 * @ORM\Table(name="hs_hr_emp_skill")
 * @ORM\Entity
 */
class EmployeeSkill
{
    use DecoratorTrait;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="skills", cascade={"persist"})
     * @ORM\Id
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var Skill
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Skill", inversedBy="employeeSkills")
     * @ORM\Id
     * @ORM\JoinColumn(name="skill_id", referencedColumnName="id")
     */
    private Skill $skill;

    /**
     * @var float | null
     *
     * @ORM\Column(name="years_of_exp", type="decimal", length=2, nullable=true)
     */
    private ?float $yearsOfExp;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="string", length=100, nullable=false)
     */
    private string $comments;

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
     * @return float | null
     */
    public function getYearsOfExp(): ?float
    {
        return $this->yearsOfExp;
    }

    /**
     * @param float | null $yearsOfExp
     */
    public function setYearsOfExp(?float $yearsOfExp): void
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
