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

/**
 * @ORM\Table(name="ohrm_work_week")
 * @ORM\Entity
 */
class WorkWeek
{
    public const WORKWEEK_LENGTH_FULL_DAY = 0;
    public const WORKWEEK_LENGTH_HALF_DAY = 4;
    public const WORKWEEK_LENGTH_NON_WORKING_DAY = 8;

    public const WORKWEEK_LENGTHS = [
        self::WORKWEEK_LENGTH_FULL_DAY,
        self::WORKWEEK_LENGTH_HALF_DAY,
        self::WORKWEEK_LENGTH_NON_WORKING_DAY,
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=10, options={"unsigned" : true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var OperationalCountry|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\OperationalCountry")
     * @ORM\JoinColumn(name="operational_country_id", referencedColumnName="id", nullable=true)
     */
    private ?OperationalCountry $operationalCountry = null;

    /**
     * @var int
     *
     * @ORM\Column(name="mon", type="integer", length=3, options={"default" : 0, "unsigned" : true})
     */
    private int $monday = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="tue", type="integer", length=3, options={"default" : 0, "unsigned" : true})
     */
    private int $tuesday = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="wed", type="integer", length=3, options={"default" : 0, "unsigned" : true})
     */
    private int $wednesday = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="thu", type="integer", length=3, options={"default" : 0, "unsigned" : true})
     */
    private int $thursday = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="fri", type="integer", length=3, options={"default" : 0, "unsigned" : true})
     */
    private int $friday = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="sat", type="integer", length=3, options={"default" : 0, "unsigned" : true})
     */
    private int $saturday = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="sun", type="integer", length=3, options={"default" : 0, "unsigned" : true})
     */
    private int $sunday = 0;

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

    /**
     * @return int
     */
    public function getMonday(): int
    {
        return $this->monday;
    }

    /**
     * @param int $monday
     */
    public function setMonday(int $monday): void
    {
        $this->monday = $monday;
    }

    /**
     * @return int
     */
    public function getTuesday(): int
    {
        return $this->tuesday;
    }

    /**
     * @param int $tuesday
     */
    public function setTuesday(int $tuesday): void
    {
        $this->tuesday = $tuesday;
    }

    /**
     * @return int
     */
    public function getWednesday(): int
    {
        return $this->wednesday;
    }

    /**
     * @param int $wednesday
     */
    public function setWednesday(int $wednesday): void
    {
        $this->wednesday = $wednesday;
    }

    /**
     * @return int
     */
    public function getThursday(): int
    {
        return $this->thursday;
    }

    /**
     * @param int $thursday
     */
    public function setThursday(int $thursday): void
    {
        $this->thursday = $thursday;
    }

    /**
     * @return int
     */
    public function getFriday(): int
    {
        return $this->friday;
    }

    /**
     * @param int $friday
     */
    public function setFriday(int $friday): void
    {
        $this->friday = $friday;
    }

    /**
     * @return int
     */
    public function getSaturday(): int
    {
        return $this->saturday;
    }

    /**
     * @param int $saturday
     */
    public function setSaturday(int $saturday): void
    {
        $this->saturday = $saturday;
    }

    /**
     * @return int
     */
    public function getSunday(): int
    {
        return $this->sunday;
    }

    /**
     * @param int $sunday
     */
    public function setSunday(int $sunday): void
    {
        $this->sunday = $sunday;
    }
}
