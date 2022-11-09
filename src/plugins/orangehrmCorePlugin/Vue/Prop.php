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

namespace OrangeHRM\Core\Vue;

class Prop
{
    public const TYPE_STRING = 'String';
    public const TYPE_NUMBER = 'Number';
    public const TYPE_BOOLEAN = 'Boolean';
    public const TYPE_ARRAY = 'Array';
    public const TYPE_OBJECT = 'Object';

    /**
     * @var string
     */
    protected string $name;
    /**
     * @var string
     */
    protected string $type = self::TYPE_STRING;
    /**
     * @var null|string|int|float|bool|array|object
     */
    protected $rawValue = null;

    /**
     * @param string $name
     * @param string $type
     * @param null|string|int|float|bool|array|object $value
     */
    public function __construct(string $name, string $type = self::TYPE_STRING, $value = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->rawValue = $value;
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array|bool|float|int|object|string|null
     */
    public function getRawValue()
    {
        return $this->rawValue;
    }

    /**
     * @param array|bool|float|int|object|string|null $rawValue
     */
    public function setRawValue($rawValue): void
    {
        $this->rawValue = $rawValue;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return json_encode($this->rawValue);
    }
}
