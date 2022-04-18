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

import PunchIn from './pages/PunchIn.vue';
import PunchOut from './pages/PunchOut.vue';
import EditAttendance from './pages/EditAttendance.vue';
import ViewMyAttendance from './pages/ViewMyAttendance.vue';
import AttendanceConfiguration from './pages/AttendanceConfiguration.vue';
import AttendanceSummaryReport from './pages/AttendanceSummaryReport.vue';
import ViewEmployeeAttendanceSummary from './pages/ViewEmployeeAttendanceSummary.vue';
import ViewEmployeeAttendanceDetailed from './pages/ViewEmployeeAttendanceDetailed.vue';

export default {
  'attendance-punch-in': PunchIn,
  'attendance-punch-out': PunchOut,
  'edit-attendance': EditAttendance,
  'view-my-attendance': ViewMyAttendance,
  'attendance-configuration': AttendanceConfiguration,
  'attendance-summary-report': AttendanceSummaryReport,
  'view-employee-attendance-summary': ViewEmployeeAttendanceSummary,
  'view-employee-attendance-detailed': ViewEmployeeAttendanceDetailed,
};
