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

import JobTitle from './pages/jobTitle/JobTitle.vue';
import EditJobTitle from './pages/jobTitle/EditJobTitle.vue';
import SaveJobTitle from './pages/jobTitle/SaveJobTitle.vue';
import JobCategory from './pages/jobCategory/JobCategory.vue';
import EditJobCategory from './pages/jobCategory/EditJobCategory.vue';
import SaveJobCategory from './pages/jobCategory/SaveJobCategory.vue';
import SystemUser from './pages/systemUser/SystemUser.vue';
import SaveSystemUser from './pages/systemUser/SaveSystemUser.vue';
import EditSystemUser from './pages/systemUser/EditSystemUser.vue';
import OrgStructure from './pages/orgStructure/OrgStructure.vue';
import EditEmploymentStatus from './pages/employmentStatus/EditEmploymentStatus.vue';
import EmploymentStatus from './pages/employmentStatus/EmploymentStatus.vue';
import SaveEmploymentStatus from './pages/employmentStatus/SaveEmploymentStatus.vue';
import QualificationEducation from './pages/qualificationEducation/QualificationEducation.vue';
import EditQualificationEducation from './pages/qualificationEducation/EditQualificationEducation.vue';
import SaveQualificationEducation from './pages/qualificationEducation/SaveQualificationEducation.vue';
import QualificationSkill from './pages/qualificationSkill/QualificationSkill.vue';
import EditQualificationSkill from './pages/qualificationSkill/EditQualificationSkill.vue';
import SaveQualificationSkill from './pages/qualificationSkill/SaveQualificationSkill.vue';
import EditQualificationLicense from './pages/qualificationLicense/EditLicense.vue';
import SaveQualificationLicense from './pages/qualificationLicense/SaveLicense.vue';
import QualificationLicense from './pages/qualificationLicense/License.vue';
import EditQualificationLanguage from './pages/qualificationLanguage/EditQualificationLanguage.vue';
import SaveQualificationLanguage from './pages/qualificationLanguage/SaveQualificationLanguage.vue';
import QualificationLanguage from './pages/qualificationLanguage/QualificationLanguage.vue';
import QualificationMembership from './pages/qualificationMembership/QualificationMembership.vue';
import EditQualificationMembership from './pages/qualificationMembership/EditQualificationMembership.vue';
import SaveQualificationMembership from './pages/qualificationMembership/SaveQualificationMembership.vue';
import ViewOrganizationGeneralInformation from './pages/organizationGeneralInformation/ViewOrganizationGeneralInformation.vue';
import Nationality from './pages/nationality/Nationality.vue';
import EditNationality from './pages/nationality/EditNationality.vue';
import SaveNationality from './pages/nationality/SaveNationality.vue';
import ViewEmailConfiguration from './pages/emailConfiguration/ViewEmailConfiguration.vue';
import LocationList from './pages/location/LocationList.vue';
import SaveLocation from './pages/location/SaveLocation.vue';
import EditLocation from './pages/location/EditLocation.vue';
import PayGrade from './pages/payGrade/PayGrade.vue';
import AddPayGrade from './pages/payGrade/AddPayGrade.vue';
import EditPayGrade from './pages/payGrade/EditPayGrade.vue';
import EditModuleConfiguration from './pages/moduleConfiguration/EditModuleConfiguration.vue';
import WorkShift from './pages/workShift/WorkShift.vue';
import SaveWorkShift from './pages/workShift/SaveWorkShift.vue';
import EditWorkShift from './pages/workShift/EditWorkShift.vue';
import EmailSubscription from './pages/emailSubscription/EmailSubscription.vue';
import EditEmailSubscription from './pages/emailSubscription/EditEmailSubscription.vue';
import LocalizationConfiguration from './pages/localization/LocalizationConfiguration.vue';
import CorporateBranding from './pages/corporateBranding/CorporateBranding.vue';
import LanguagePackages from '@/orangehrmAdminPlugin/pages/languagePackage/LanguagePackage.vue';
import EditLanguagePackage from '@/orangehrmAdminPlugin/pages/languageTranslation/languageTranslation.vue';
import LanguageImport from './pages/languageImport/LanguageImport.vue';
import LdapConfiguration from './pages/ldap/LdapConfiguration.vue';
import ProviderList from './pages/socialMediaAuth/ProviderList.vue';
import AddProvider from './pages/socialMediaAuth/AddProvider.vue';
import EditProvider from './pages/socialMediaAuth/EditProvider.vue';
import FixLanguageErrors from './pages/languageImport/FixLanguageStringErrors.vue';

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
  'pay-grade-list': PayGrade,
  'pay-grade-add': AddPayGrade,
  'pay-grade-edit': EditPayGrade,
  'work-shift-list': WorkShift,
  'work-shift-save': SaveWorkShift,
  'work-shift-edit': EditWorkShift,
  'email-subscription-list': EmailSubscription,
  'email-subscription-edit': EditEmailSubscription,
  'localization-configuration': LocalizationConfiguration,
  'corporate-branding': CorporateBranding,
  'language-package-list': LanguagePackages,
  'language-translation-edit': EditLanguagePackage,
  'language-import': LanguageImport,
  'ldap-configuration': LdapConfiguration,
  'auth-provider-list': ProviderList,
  'add-auth-provider': AddProvider,
  'edit-auth-provider': EditProvider,
  'fix-language-errors': FixLanguageErrors,
};
