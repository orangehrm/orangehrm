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
use InvalidArgumentException;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\EmpDependentDecorator;

/**
 * @method EmpDependentDecorator getDecorator()
 *
 * @ORM\Table(name="hs_hr_emp_dependents")
 * @ORM\Entity
 */
class EmpDependent
{
    use DecoratorTrait;

    public const RELATIONSHIP_TYPE_CHILD = 'child';
    public const RELATIONSHIP_TYPE_OTHER = 'other';

    /**
     * @var Employee
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="dependents", cascade={"persist"})
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var string
     *
     * @ORM\Column(name="ed_seqno", type="decimal", precision=2, scale=0, options={"default" : 0})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private string $seqNo = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="ed_name", type="string", length=100, nullable=true, options={"default" : ""})
     */
    private ?string $name = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="ed_relationship_type", type="string", nullable=true)
     */
    private ?string $relationshipType = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ed_relationship", type="string", length=100, nullable=true)
     */
    private ?string $relationship = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="ed_date_of_birth", type="date", nullable=true)
     */
    private ?DateTime $dateOfBirth = null;

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
     * @return string
     */
    public function getSeqNo(): string
    {
        return $this->seqNo;
    }

    /**
     * @param string $seqNo
     */
    public function setSeqNo(string $seqNo): void
    {
        $this->seqNo = $seqNo;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getRelationshipType(): ?string
    {
        return $this->relationshipType;
    }

    /**
     * @param string|null $relationshipType
     */
    public function setRelationshipType(?string $relationshipType): void
    {
        if (!in_array($relationshipType, [self::RELATIONSHIP_TYPE_CHILD, self::RELATIONSHIP_TYPE_OTHER])) {
            throw new InvalidArgumentException('Invalid `relationshipType`');
        }
        $this->relationshipType = $relationshipType;
    }

    /**
     * @return string|null
     */
    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    /**
     * @param string|null $relationship
     */
    public function setRelationship(?string $relationship): void
    {
        $this->relationship = $relationship;
    }

    /**
     * @return DateTime|null
     */
    public function getDateOfBirth(): ?DateTime
    {
        return $this->dateOfBirth;
    }

    /**
     * @param DateTime|null $dateOfBirth
     */
    public function setDateOfBirth(?DateTime $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }
}
