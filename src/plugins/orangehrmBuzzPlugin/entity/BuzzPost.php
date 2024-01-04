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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Decorator\BuzzPostDecorator;
use OrangeHRM\Entity\Decorator\DecoratorTrait;

/**
 * @method BuzzPostDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_buzz_post")
 * @ORM\Entity
 */
class BuzzPost
{
    use DecoratorTrait;
    use DateTimeHelperTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="employee_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var string|null
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private ?string $text = null;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="post_utc_time", type="datetime")
     */
    private DateTime $createdAtUtc;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="updated_utc_time", type="datetime", nullable=true)
     */
    private ?DateTime $updatedAtUtc = null;

    /**
     * @var BuzzPhoto[]
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\BuzzPhoto", mappedBy="post")
     */
    private iterable $photos;

    /**
     * @var BuzzLink[]
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\BuzzLink", mappedBy="post")
     */
    private iterable $links;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->links = new ArrayCollection();
    }

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
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAtUtc(): DateTime
    {
        return $this->createdAtUtc;
    }

    public function setCreatedAtUtc(): void
    {
        $this->createdAtUtc = $this->getDateTimeHelper()->getNowInUTC();
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAtUtc(): ?DateTime
    {
        return $this->updatedAtUtc;
    }

    public function setUpdatedAtUtc(): void
    {
        $this->updatedAtUtc = $this->getDateTimeHelper()->getNowInUTC();
    }
}
