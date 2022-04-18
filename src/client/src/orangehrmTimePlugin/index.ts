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

import TimeSheetPeriodConfig from './pages/configure/TimeSheetPeriod.vue';
import Customer from './pages/customer/Customer.vue';
import SaveCustomer from './pages/customer/SaveCustomer.vue';
import EditCustomer from './pages/customer/EditCustomer.vue';
import MyTimesheet from './pages/timesheets/MyTimesheet.vue';
import EditTimeSheet from './pages/timesheets/EditTimeSheet.vue';
import Project from './pages/project/Project.vue';
import ProjectSave from './pages/project/ProjectSave.vue';
import ProjectEdit from './pages/project/ProjectEdit.vue';
import EmployeeTimesheet from './pages/timesheets/EmployeeTimesheet.vue';
import ViewEmployeeTimesheet from './pages/timesheets/ViewEmployeeTimesheet.vue';
import EmployeeTimeReport from './pages/reports/EmployeeTimeReport.vue';
import ProjectActivityReport from './pages/reports/ProjectActivityReport.vue';
import ActivityDetailsReport from './pages/reports/ActivityDetailsReport.vue';

export default {
  'time-sheet-period': TimeSheetPeriodConfig,
  'customer-list': Customer,
  'customer-save': SaveCustomer,
  'customer-edit': EditCustomer,
  'my-timesheet': MyTimesheet,
  'edit-timesheet': EditTimeSheet,
  'project-list': Project,
  'project-save': ProjectSave,
  'project-edit': ProjectEdit,
  'employee-timesheet': EmployeeTimesheet,
  'view-employee-timesheet': ViewEmployeeTimesheet,
  'employee-time-report': EmployeeTimeReport,
  'project-activity-report': ProjectActivityReport,
  'activity-details-report': ActivityDetailsReport,
};
