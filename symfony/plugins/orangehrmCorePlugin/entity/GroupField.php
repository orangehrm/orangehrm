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
 * @ORM\Table(name="ohrm_group_field")
 * @ORM\Entity
 */
class GroupField
{
    /**
     * @var int
     *
     * @ORM\Column(name="group_field_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="group_by_clause", type="text")
     */
    private string $groupByClause;

    /**
     * @var string|null
     *
     * @ORM\Column(name="group_field_widget", type="string", length=255, nullable=true)
     */
    private ?string $groupFieldWidget = null;

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
     * @return string
     */
    public function getGroupByClause(): string
    {
        return $this->groupByClause;
    }

    /**
     * @param string $groupByClause
     */
    public function setGroupByClause(string $groupByClause): void
    {
        $this->groupByClause = $groupByClause;
    }

    /**
     * @return string|null
     */
    public function getGroupFieldWidget(): ?string
    {
        return $this->groupFieldWidget;
    }

    /**
     * @param string|null $groupFieldWidget
     */
    public function setGroupFieldWidget(?string $groupFieldWidget): void
    {
        $this->groupFieldWidget = $groupFieldWidget;
    }
}
