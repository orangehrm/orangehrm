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
import EmployeeMembership from './pages/employee/EmployeeMembership.vue';
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
import EmployeeTaxExemption from './pages/employee/EmployeeTaxExemption.vue';
import EmployeeDataImport from './pages/dataImport/EmployeeDataImport.vue';
import EmployeeReport from './pages/reports/EmployeeReport.vue';
import SaveEmployeeReport from './pages/reports/SaveEmployeeReport.vue';
import ViewEmployeeReport from './pages/reports/ViewEmployeeReport.vue';
import EditEmployeeReport from './pages/reports/EditEmployeeReport.vue';
import UpdatePassword from './pages/updatePassword/UpdatePassword.vue';

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
  'employee-membership': EmployeeMembership,
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
  'employee-tax-exemption': EmployeeTaxExemption,
  'employee-data-import': EmployeeDataImport,
  'employee-report-list': EmployeeReport,
  'employee-report-save': SaveEmployeeReport,
  'employee-report-view': ViewEmployeeReport,
  'employee-report-edit': EditEmployeeReport,
  'update-password': UpdatePassword,
};
