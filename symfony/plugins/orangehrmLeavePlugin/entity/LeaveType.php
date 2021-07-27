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

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_leave_type")
 * @ORM\Entity
 */
class LeaveType
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private string $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="exclude_in_reports_if_no_entitlement", type="boolean", options={"default": 0})
     */
    private bool $excludeInReportsIfNoEntitlement = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", options={"default": 0})
     */
    private bool $deleted = false;

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
     * @return bool
     */
    public function isExcludeInReportsIfNoEntitlement(): bool
    {
        return $this->excludeInReportsIfNoEntitlement;
    }

    /**
     * @param bool $excludeInReportsIfNoEntitlement
     */
    public function setExcludeInReportsIfNoEntitlement(bool $excludeInReportsIfNoEntitlement): void
    {
        $this->excludeInReportsIfNoEntitlement = $excludeInReportsIfNoEntitlement;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
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
