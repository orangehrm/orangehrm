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

namespace OrangeHRM\Core\Api;

final class CommonParams
{
    public const PARAMETER_ID = 'id';
    public const PARAMETER_IDS = 'ids';
    public const PARAMETER_EMP_NUMBER = 'empNumber';

    public const PARAMETER_SORT_FIELD = 'sortField';
    public const PARAMETER_SORT_ORDER = 'sortOrder';
    public const PARAMETER_OFFSET = 'offset';
    public const PARAMETER_LIMIT = 'limit';

    public const PARAMETER_TOTAL = 'total';
}
