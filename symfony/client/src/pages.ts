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

import JobTitle from '@/orangehrmAdminPlugin/pages/jobTitle/JobTitle.vue';
import EditJobTitle from '@/orangehrmAdminPlugin/pages/jobTitle/EditJobTitle.vue';
import SaveJobTitle from '@/orangehrmAdminPlugin/pages/jobTitle/SaveJobTitle.vue';
import JobCategory from '@/orangehrmAdminPlugin/pages/jobCategory/JobCategory.vue';
import EditJobCategory from '@/orangehrmAdminPlugin/pages/jobCategory/EditJobCategory.vue';
import SaveJobCategory from '@/orangehrmAdminPlugin/pages/jobCategory/SaveJobCategory.vue';
import authenticationPages from './orangehrmAuthenticationPlugin';
import SystemUser from '@/orangehrmAdminPlugin/pages/systemUser/SystemUser.vue';
import SaveSystemUser from '@/orangehrmAdminPlugin/pages/systemUser/SaveSystemUser.vue';
import EditSystemUser from '@/orangehrmAdminPlugin/pages/systemUser/EditSystemUser.vue';
import OrgStructure from '@/orangehrmAdminPlugin/pages/orgStructure/OrgStructure.vue';
import EditEmploymentStatus from '@/orangehrmAdminPlugin/pages/employmentStatus/EditEmploymentStatus.vue';
import EmploymentStatus from '@/orangehrmAdminPlugin/pages/employmentStatus/EmploymentStatus.vue';
import SaveEmploymentStatus from '@/orangehrmAdminPlugin/pages/employmentStatus/SaveEmploymentStatus.vue';
import QualificationEducation from '@/orangehrmAdminPlugin/pages/qualificationEducation/QualificationEducation.vue';
import EditQualificationEducation from '@/orangehrmAdminPlugin/pages/qualificationEducation/EditQualificationEducation.vue';
import SaveQualificationEducation from '@/orangehrmAdminPlugin/pages/qualificationEducation/SaveQualificationEducation.vue';
import QualificationSkill from '@/orangehrmAdminPlugin/pages/qualificationSkill/QualificationSkill.vue';
import EditQualificationSkill from '@/orangehrmAdminPlugin/pages/qualificationSkill/EditQualificationSkill.vue';
import SaveQualificationSkill from '@/orangehrmAdminPlugin/pages/qualificationSkill/SaveQualificationSkill.vue';
import EditQualificationLicense from '@/orangehrmAdminPlugin/pages/qualificationLicense/EditLicense.vue';
import SaveQualificationLicense from '@/orangehrmAdminPlugin/pages/qualificationLicense/SaveLicense.vue';
import QualificationLicense from '@/orangehrmAdminPlugin/pages/qualificationLicense/License.vue';
import PimPages from '@/orangehrmPimPlugin';
import CorePages from '@/core/pages';
import EditQualificationLanguage from '@/orangehrmAdminPlugin/pages/qualificationLanguage/EditQualificationLanguage.vue';
import SaveQualificationLanguage from '@/orangehrmAdminPlugin/pages/qualificationLanguage/SaveQualificationLanguage.vue';
import QualificationLanguage from '@/orangehrmAdminPlugin/pages/qualificationLanguage/QualificationLanguage.vue';
import QualificationMembership from '@/orangehrmAdminPlugin/pages/qualificationMembership/QualificationMembership.vue';
import EditQualificationMembership from '@/orangehrmAdminPlugin/pages/qualificationMembership/EditQualificationMembership.vue';
import SaveQualificationMembership from '@/orangehrmAdminPlugin/pages/qualificationMembership/SaveQualificationMembership.vue';
import ViewOrganizationGeneralInformation from '@/orangehrmAdminPlugin/pages/organizationGeneralInformation/ViewOrganizationGeneralInformation.vue';
import Nationality from '@/orangehrmAdminPlugin/pages/nationality/Nationality.vue';
import EditNationality from '@/orangehrmAdminPlugin/pages/nationality/EditNationality.vue';
import SaveNationality from '@/orangehrmAdminPlugin/pages/nationality/SaveNationality.vue';
import ViewEmailConfiguration from '@/orangehrmAdminPlugin/pages/emailConfiguration/ViewEmailConfiguration.vue';
import OAuthPages from '@/orangehrmCoreOAuthPlugin';
import LeavePages from '@/orangehrmLeavePlugin';
import LocationList from '@/orangehrmAdminPlugin/pages/location/LocationList.vue';
import SaveLocation from '@/orangehrmAdminPlugin/pages/location/SaveLocation.vue';
import EditLocation from '@/orangehrmAdminPlugin/pages/location/EditLocation.vue';
import PayGrade from '@/orangehrmAdminPlugin/pages/payGrade/PayGrade.vue';
import AddPayGrade from '@/orangehrmAdminPlugin/pages/payGrade/AddPayGrade.vue';
import EditPayGrade from '@/orangehrmAdminPlugin/pages/payGrade/EditPayGrade.vue';
import EditModuleConfiguration from '@/orangehrmAdminPlugin/pages/moduleConfiguration/EditModuleConfiguration.vue';
import WorkShift from '@/orangehrmAdminPlugin/pages/workShift/WorkShift.vue';
import SaveWorkShift from '@/orangehrmAdminPlugin/pages/workShift/SaveWorkShift.vue';
import EditWorkShift from '@/orangehrmAdminPlugin/pages/workShift/EditWorkShift.vue';
import EmailSubscription from '@/orangehrmAdminPlugin/pages/emailSubscription/EmailSubscription.vue';
import EditEmailSubscription from '@/orangehrmAdminPlugin/pages/emailSubscription/EditEmailSubscription.vue';
import LocalizationConfiguration from '@/orangehrmAdminPlugin/pages/localization/LocalizationConfiguration.vue';
import HelpPages from '@/orangehrmHelpPlugin';
import TimePages from '@/orangehrmTimePlugin';
import AttendancePages from '@/orangehrmAttendancePlugin';
import MaintenancePages from '@/orangehrmMaintenancePlugin';

export default {
  'job-title-list': JobTitle,
  'job-title-edit': EditJobTitle,
  'job-title-save': SaveJobTitle,
  'job-category-list': JobCategory,
  'job-category-edit': EditJobCategory,
  'job-category-save': SaveJobCategory,
  'qualification-education-list': QualificationEducation,
  'qualification-education-edit': EditQualificationEducation,
  'qualification-education-save': SaveQualificationEducation,
  ...authenticationPages,
  'system-user-list': SystemUser,
  'system-user-edit': EditSystemUser,
  'system-user-save': SaveSystemUser,
  'organization-structure': OrgStructure,
  'employment-status-list': EmploymentStatus,
  'employment-status-save': SaveEmploymentStatus,
  'employment-status-edit': EditEmploymentStatus,
  'qualification-skill-list': QualificationSkill,
  'qualification-skill-edit': EditQualificationSkill,
  'qualification-skill-save': SaveQualificationSkill,
  'license-list': QualificationLicense,
  'license-save': SaveQualificationLicense,
  'license-edit': EditQualificationLicense,
  'module-configuration-edit': EditModuleConfiguration,
  ...PimPages,
  ...LeavePages,
  ...CorePages,
  'qualification-language-list': QualificationLanguage,
  'qualification-language-edit': EditQualificationLanguage,
  'qualification-language-save': SaveQualificationLanguage,
  'qualification-membership-list': QualificationMembership,
  'qualification-membership-edit': EditQualificationMembership,
  'qualification-membership-save': SaveQualificationMembership,
  'organization-general-information-view': ViewOrganizationGeneralInformation,
  'nationality-list': Nationality,
  'nationality-edit': EditNationality,
  'nationality-save': SaveNationality,
  'location-list': LocationList,
  'location-save': SaveLocation,
  'location-edit': EditLocation,
  'email-configuration-view': ViewEmailConfiguration,
  ...OAuthPages,
  ...LeavePages,
  'pay-grade-list': PayGrade,
  'pay-grade-add': AddPayGrade,
  'pay-grade-edit': EditPayGrade,
  'work-shift-list': WorkShift,
  'work-shift-save': SaveWorkShift,
  'work-shift-edit': EditWorkShift,
  'email-subscription-list': EmailSubscription,
  'email-subscription-edit': EditEmailSubscription,
  'localization-configuration': LocalizationConfiguration,
  ...HelpPages,
  ...TimePages,
  ...AttendancePages,
  ...MaintenancePages,
};
