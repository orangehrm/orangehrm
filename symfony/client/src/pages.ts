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
};
