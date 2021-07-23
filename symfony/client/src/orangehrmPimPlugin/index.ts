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

import SaveEmployee from './pages/employee/SaveEmployee.vue';
import Employee from './pages/employee/Employee.vue';
import EmployeePersonalDetails from './pages/employee/EmployeePersonalDetails.vue';
import EmployeeContactDetails from './pages/employee/EmployeeContactDetails.vue';
import EmployeeEmergencyContacts from './pages/employee/EmployeeEmergencyContacts.vue';
import EmployeeDependents from './pages/employee/EmployeeDependents.vue';
import EmployeeProfilePicture from './pages/employee/EmployeeProfilePicture.vue';
import EmployeeSalary from './pages/employee/EmployeeSalary.vue';
import EmployeeJob from './pages/employee/EmployeeJob.vue';
import EmployeeQualifications from './pages/employee/EmployeeQualifications.vue';
import EmployeeImmigration from './pages/employee/EmployeeImmigration.vue';
import EmployeeReportTo from './pages/employee/EmployeeReportTo.vue';
import TerminationReason from './pages/terminationReason/TerminationReason.vue';
import EditTerminationReason from './pages/terminationReason/EditTerminationReason.vue';
import SaveTerminationReason from './pages/terminationReason/SaveTerminationReason.vue';
import ReportingMethod from './pages/reportingMethod/ReportingMethod.vue';
import EditReportingMethod from './pages/reportingMethod/EditReportingMethod.vue';
import SaveReportingMethod from './pages/reportingMethod/SaveReportingMethod.vue';
import CustomField from './pages/customField/CustomField.vue';
import EditCustomField from './pages/customField/EditCustomField.vue';
import SaveCustomField from './pages/customField/SaveCustomField.vue';
import OptionalField from './pages/optionalField/OptionalField.vue';

export default {
  'employee-save': SaveEmployee,
  'employee-list': Employee,
  'employee-personal-details': EmployeePersonalDetails,
  'employee-contact-details': EmployeeContactDetails,
  'employee-emergency-contacts': EmployeeEmergencyContacts,
  'employee-dependents': EmployeeDependents,
  'employee-profile-picture': EmployeeProfilePicture,
  'employee-salary': EmployeeSalary,
  'employee-job': EmployeeJob,
  'employee-qualifications': EmployeeQualifications,
  'employee-immigration': EmployeeImmigration,
  'termination-reason-list': TerminationReason,
  'termination-reason-edit': EditTerminationReason,
  'termination-reason-save': SaveTerminationReason,
  'reporting-method-list': ReportingMethod,
  'reporting-method-edit': EditReportingMethod,
  'reporting-method-save': SaveReportingMethod,
  'employee-report-to': EmployeeReportTo,
  'custom-field-list': CustomField,
  'custom-field-edit': EditCustomField,
  'custom-field-save': SaveCustomField,
  'optional-field-list': OptionalField,
};
