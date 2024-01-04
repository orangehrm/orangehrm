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
use InvalidArgumentException;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\EmployeeLanguageDecorator;

/**
 * @method EmployeeLanguageDecorator getDecorator()
 *
 * @ORM\Table(name="hs_hr_emp_language")
 * @ORM\Entity
 */
class EmployeeLanguage
{
    use DecoratorTrait;

    public const FLUENCIES = [
        1 => 'Writing',
        2 => 'Speaking',
        3 => 'Reading'
    ];

    public const COMPETENCIES = [
        1 => 'Poor',
        2 => 'Basic',
        3 => 'Good',
        4 => 'Mother Tongue'
    ];

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="languages", cascade={"persist"})
     * @ORM\Id
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private Employee $employee;

    /**
     * @var Language
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Language", inversedBy="employeeLanguages")
     * @ORM\Id
     * @ORM\JoinColumn(name="lang_id", referencedColumnName="id")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private Language $language;

    /**
     * @var int
     *
     * @ORM\Column(name="fluency", type="smallint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private int $fluency;

    /**
     * @var int|null
     *
     * @ORM\Column(name="competency", type="smallint", nullable=true)
     */
    private ?int $competency;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comments", type="string", length=100, nullable=true)
     */
    private ?string $comment;

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
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
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }

    /**
     * @return int
     */
    public function getFluency(): int
    {
        return $this->fluency;
    }

    /**
     * @param int $fluency
     */
    public function setFluency(int $fluency): void
    {
        if (!in_array($fluency, array_keys(self::FLUENCIES))) {
            throw new InvalidArgumentException("Invalid `fluency`");
        }
        $this->fluency = $fluency;
    }

    /**
     * @return int|null
     */
    public function getCompetency(): ?int
    {
        return $this->competency;
    }

    /**
     * @param int|null $competency
     */
    public function setCompetency(?int $competency): void
    {
        if (!in_array($competency, array_keys(self::COMPETENCIES))) {
            throw new InvalidArgumentException("Invalid `competency`");
        }
        $this->competency = $competency;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }
}
