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

namespace OrangeHRM\Core\Report\FilterField;

final class Operator
{
    public const IN = 'in';
    public const EQUAL = 'eq';
    public const LESS_THAN = 'lt';
    public const GREATER_THAN = 'gt';
    public const BETWEEN = 'between';
    public const IS_NULL = 'isNull';
    public const IS_NOT_NULL = 'isNotNull';

    public const WHERE_IN = 'IN';
    public const WHERE_EQUAL = '=';
    public const WHERE_LESS_THAN = '<';
    public const WHERE_GREATER_THAN = '>';
    public const WHERE_BETWEEN = 'BETWEEN';
    public const WHERE_IS_NULL = 'IS NULL';
    public const WHERE_IS_NOT_NULL = 'IS NOT NULL';

    public const MAP = [
        self::WHERE_IN => self::IN,
        self::WHERE_EQUAL => self::EQUAL,
        self::WHERE_LESS_THAN => self::LESS_THAN,
        self::WHERE_GREATER_THAN => self::GREATER_THAN,
        self::WHERE_BETWEEN => self::BETWEEN,
        self::WHERE_IS_NULL => self::IS_NULL,
        self::WHERE_IS_NOT_NULL => self::IS_NOT_NULL,
    ];
}
