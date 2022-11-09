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

namespace OrangeHRM\Core\Report\Header;

use InvalidArgumentException;

class Column
{
    public const PIN_COL_START = 'colPinStart';
    public const PIN_COL_END = 'colPinEnd';

    private ?string $name = null;

    private string $prop;

    private ?string $pin = null;

    private ?int $size = null;

    private ?array $cellProperties = null;

    /**
     * @param string $prop
     */
    public function __construct(string $prop)
    {
        $this->prop = $prop;
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
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getProp(): string
    {
        return $this->prop;
    }

    /**
     * @param string $prop
     * @return $this
     */
    public function setProp(string $prop): self
    {
        $this->prop = $prop;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPin(): ?string
    {
        return $this->pin;
    }

    /**
     * @param string|null $pin
     * @return $this
     */
    public function setPin(?string $pin): self
    {
        if (!in_array($pin, [null, self::PIN_COL_START, self::PIN_COL_END])) {
            throw new InvalidArgumentException("Invalid argument `$pin`");
        }
        $this->pin = $pin;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param int|null $size
     * @return $this
     */
    public function setSize(?int $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getCellProperties(): ?array
    {
        return $this->cellProperties;
    }

    /**
     * @param array|null $cellProperties
     * @return $this
     */
    public function setCellProperties(?array $cellProperties): self
    {
        $this->cellProperties = $cellProperties;
        return $this;
    }

    /**
     * @param array $cellProperties
     * @return $this
     */
    public function addCellProperties(array $cellProperties = []): self
    {
        $this->cellProperties = array_replace($this->cellProperties ?? [], $cellProperties);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'prop' => $this->getProp(),
            'size' => $this->getSize(),
            'pin' => $this->getPin(),
            'cellProperties' => $this->getCellProperties(),
        ];
    }
}
