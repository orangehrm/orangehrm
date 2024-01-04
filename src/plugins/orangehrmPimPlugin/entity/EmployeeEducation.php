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

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\EmployeeEducationDecorator;

/**
 * @method EmployeeEducationDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_emp_education")
 * @ORM\Entity
 */
class EmployeeEducation
{
    use DecoratorTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="educations", cascade={"persist"})
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var Education
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Education", inversedBy="employeeEducation")
     * @ORM\JoinColumn(name="education_id", referencedColumnName="id")
     */
    private Education $education;

    /**
     * @var string | null
     *
     * @ORM\Column(name="institute", type="string", length=100, nullable=true)
     */
    private ?string $institute;

    /**
     * @var string | null
     *
     * @ORM\Column(name="major", type="string", length=100, nullable=true)
     */
    private ?string $major;

    /**
     * @var int | null
     *
     * @ORM\Column(name="year", type="decimal", length=4, nullable=true)
     */
    private ?int $year;

    /**
     * @var string | null
     *
     * @ORM\Column(name="score", type="string", length=25, nullable=true)
     */
    private ?string $score;

    /**
     * @var DateTime | null
     *
     * @ORM\Column(name="start_date", type="date", nullable=true)
     */
    private ?DateTime $startDate = null;

    /**
     * @var DateTime | null
     *
     * @ORM\Column(name="end_date", type="date", nullable=true)
     */
    private ?DateTime $endDate = null;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Education | null
     */
    public function getEducation(): ?Education
    {
        return $this->education;
    }

    /**
     * @param Education $education
     */
    public function setEducation(Education $education): void
    {
        $this->education = $education;
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
     * @return string | null
     */
    public function getInstitute(): ?string
    {
        return $this->institute;
    }

    /**
     * @param string | null $institute
     */
    public function setInstitute(?string $institute): void
    {
        $this->institute = $institute;
    }

    /**
     * @return string | null
     */
    public function getMajor(): ?string
    {
        return $this->major;
    }

    /**
     * @param string | null $major
     */
    public function setMajor(?string $major): void
    {
        $this->major = $major;
    }

    /**
     * @return int | null
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int | null $year
     */
    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return string | null
     */
    public function getScore(): ?string
    {
        return $this->score;
    }

    /**
     * @param string | null $score
     */
    public function setScore(?string $score): void
    {
        $this->score = $score;
    }

    /**
     * @return DateTime | null
     */
    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    /**
     * @param DateTime | null $startDate
     */
    public function setStartDate(?DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return DateTime | null
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @param DateTime | null $endDate
     */
    public function setEndDate(?DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }
}
