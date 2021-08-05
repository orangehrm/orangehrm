<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\HolidayDecorator;

/**
 * @method HolidayDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_holiday")
 * @ORM\Entity
 */
class Holiday
{
    use DecoratorTrait;

    public const HOLIDAY_FULL_DAY_LENGTH = 0;
    public const HOLIDAY_HALF_DAY_LENGTH = 4;

    public const HOLIDAY_FULL_DAY_LENGTH_NAME = 'Full Day';
    public const HOLIDAY_HALF_DAY_LENGTH_NAME = 'Half Day';

    public const HOLIDAY_LENGTH_MAP = [
        self::HOLIDAY_FULL_DAY_LENGTH => self::HOLIDAY_FULL_DAY_LENGTH_NAME,
        self::HOLIDAY_HALF_DAY_LENGTH => self::HOLIDAY_HALF_DAY_LENGTH_NAME,
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=10, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="recurring", type="boolean", nullable=true)
     */
    private bool $recurring;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private string $name;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private DateTime $date;

    /**
     * @var int
     *
     * @ORM\Column(name="length", type="integer", length=10, nullable=true, options={"unsigned":true})
     */
    private int $length;

    /**
     * @var OperationalCountry|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\OperationalCountry")
     * @ORM\JoinColumn(name="operational_country_id", referencedColumnName="id", nullable=true)
     */
    private ?OperationalCountry $operationalCountry = null;

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
     * @return bool
     */
    public function isRecurring(): bool
    {
        return $this->recurring;
    }

    /**
     * @param bool $recurring
     */
    public function setRecurring(bool $recurring): void
    {
        $this->recurring = $recurring;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param int $length
     */
    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    /**
     * @return OperationalCountry|null
     */
    public function getOperationalCountry(): ?OperationalCountry
    {
        return $this->operationalCountry;
    }

    /**
     * @param OperationalCountry|null $operationalCountry
     */
    public function setOperationalCountry(?OperationalCountry $operationalCountry): void
    {
        $this->operationalCountry = $operationalCountry;
    }
}
