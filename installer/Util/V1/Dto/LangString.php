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

namespace OrangeHRM\Installer\Util\V1\Dto;

class LangString
{
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
    public function __construct(
        string $unitId,
        int $groupId,
        string $value,
        ?string $version = null,
        ?string $note = null
    ) {
        $this->unitId = $unitId;
        $this->groupId = $groupId;
        $this->value = $value;
        $this->version = $version;
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getUnitId(): string
    {
        return $this->unitId;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param array $langString
     * @return static
     */
    public static function createFromArray(array $langString): self
    {
        return new self(
            $langString['unitId'],
            $langString['groupId'],
            $langString['value'],
            $langString['version'] ?? null,
            $langString['note'] ?? null,
        );
    }
}
