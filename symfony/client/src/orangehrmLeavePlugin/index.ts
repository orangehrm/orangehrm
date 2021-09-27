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

import LeavePeriod from './pages/configure/LeavePeriod.vue';
import LeaveApply from './pages/LeaveApply.vue';
import LeaveAssign from './pages/LeaveAssign.vue';
import LeaveType from './pages/leaveType/LeaveType.vue';
import EditLeaveType from './pages/leaveType/EditLeaveType.vue';
import SaveLeaveType from './pages/leaveType/SaveLeaveType.vue';
import AddEntitlement from './pages/entitlements/AddEntitlement.vue';
import EditEntitlement from './pages/entitlements/EditEntitlement.vue';
import LeaveEntitlements from './pages/entitlements/LeaveEntitlements.vue';
import MyLeaveEntitlements from './pages/entitlements/MyLeaveEntitlements.vue';
import WorkWeek from './pages/configure/WorkWeek.vue';
import Holiday from './pages/configure/holiday/Holiday.vue';
import SaveHoliday from './pages/configure/holiday/SaveHoliday.vue';
import EditHoliday from './pages/configure/holiday/EditHoliday.vue';
import LeaveList from './pages/LeaveList.vue';
import LeaveRequest from './pages/LeaveRequest.vue';
import MyLeaveList from './pages/MyLeaveList.vue';
import LeaveEntitlementReport from './pages/reports/LeaveEntitlementReport.vue';
import MyLeaveEntitlementReport from './pages/reports/MyLeaveEntitlementReport.vue';

export default {
  'leave-period': LeavePeriod,
  'leave-apply': LeaveApply,
  'leave-assign': LeaveAssign,
  'leave-type-edit': EditLeaveType,
  'leave-type-list': LeaveType,
  'leave-type-save': SaveLeaveType,
  'leave-add-entitlement': AddEntitlement,
  'leave-edit-entitlement': EditEntitlement,
  'leave-view-entitlement': LeaveEntitlements,
  'leave-view-my-entitlement': MyLeaveEntitlements,
  'work-week': WorkWeek,
  'holiday-list': Holiday,
  'holiday-save': SaveHoliday,
  'holiday-edit': EditHoliday,
  'leave-list': LeaveList,
  'leave-view-request': LeaveRequest,
  'my-leave-list': MyLeaveList,
  'leave-entitlement-report': LeaveEntitlementReport,
  'my-leave-entitlement-report': MyLeaveEntitlementReport,
};
