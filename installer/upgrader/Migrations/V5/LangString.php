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

namespace OrangeHRM\Tools\Migrations\V5;

class LangString
{
    /**
     * @var int
     */
    private int $id;
    /**
     * @var string
     */
    private string $unitId;
    /**
     * @var int
     */
    private int $groupId;
    /**
     * @var string
     */
    private string $value;
    /**
     * @var string|null
     */
    private ?string $version;
    /**
     * @var string|null
     */
    private ?string $note;

    /**
     * @param string $unitId
     * @param int $groupId
     * @param string $value
     * @param string|null $version
     * @param string|null $note
     */
    public function __construct(string $unitId, int $groupId, string $value, ?string $version, ?string $note)
    {
        $this->unitId = $unitId;
        $this->groupId = $groupId;
        $this->value = $value;
        $this->version = $version;
        $this->note = $note;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUnitId(): string
    {
        return $this->unitId;
    }

    /**
     * @param string $unitId
     * @return LangString
     */
    public function setUnitId(string $unitId): LangString
    {
        $this->unitId = $unitId;
        return $this;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     * @return LangString
     */
    public function setGroupId(int $groupId): LangString
    {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return LangString
     */
    public function setValue(string $value): LangString
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return LangString
     */
    public function setVersion(string $version): LangString
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string $note
     * @return LangString
     */
    public function setNote(string $note): LangString
    {
        $this->note = $note;
        return $this;
    }

}
