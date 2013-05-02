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
 *
 */

/**
 * Description of LeaveEvents
 */
class LeaveEvents {
    const ENTITLEMENT_ADD = 'leave_entitlement_add';
    const ENTITLEMENT_UPDATE = 'leave_entitlement_update';
    const ENTITLEMENT_BULK_ADD = 'leave_entitlement_bulk_update';
    const LEAVE_TYPE_ADD = 'leave_type_add';
    const LEAVE_TYPE_UPDATE = 'leave_type_update';
    
    const LEAVE_APPROVE = 'leave.approve';
    const LEAVE_CANCEL = 'leave.cancel';
    const LEAVE_REJECT = 'leave.reject';
    const LEAVE_ASSIGN = 'leave.assign';
    const LEAVE_APPLY = 'leave.apply';
    const LEAVE_CHANGE = 'leave.change';
}