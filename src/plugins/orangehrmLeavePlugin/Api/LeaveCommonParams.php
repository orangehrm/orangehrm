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

namespace OrangeHRM\Leave\Api;

final class LeaveCommonParams
{
    public const PARAMETER_LEAVE_TYPE_ID = 'leaveTypeId';
    public const PARAMETER_FROM_DATE = 'fromDate';
    public const PARAMETER_TO_DATE = 'toDate';
    public const PARAMETER_COMMENT = 'comment';
    public const PARAMETER_DURATION = 'duration';
    public const PARAMETER_END_DURATION = 'endDuration';
    public const PARAMETER_PARTIAL_OPTION = 'partialOption';

    public const PARAMETER_DURATION_TYPE = 'type';
    public const PARAMETER_DURATION_FROM_TIME = 'fromTime';
    public const PARAMETER_DURATION_TO_TIME = 'toTime';
}
