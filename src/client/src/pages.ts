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

import CorePages from '@/core/pages';
import AdminPages from '@/orangehrmAdminPlugin';
import PimPages from '@/orangehrmPimPlugin';
import HelpPages from '@/orangehrmHelpPlugin';
import TimePages from '@/orangehrmTimePlugin';
import LeavePages from '@/orangehrmLeavePlugin';
import OAuthPages from '@/orangehrmCoreOAuthPlugin';
import AttendancePages from '@/orangehrmAttendancePlugin';
import MaintenancePages from '@/orangehrmMaintenancePlugin';
import RecruitmentPages from '@/orangehrmRecruitmentPlugin';
import PerformancePages from '@/orangehrmPerformancePlugin';
import CorporateDirectoryPages from '@/orangehrmCorporateDirectoryPlugin';
import authenticationPages from '@/orangehrmAuthenticationPlugin';
import languagePages from '@/orangehrmAdminPlugin';
import dashboardPages from '@/orangehrmDashboardPlugin';
import buzzPages from '@/orangehrmBuzzPlugin';
import systemCheckPages from '@/orangehrmSystemCheckPlugin';
import claimPages from '@/orangehrmClaimPlugin';

export default {
  ...AdminPages,
  ...PimPages,
  ...CorePages,
  ...HelpPages,
  ...TimePages,
  ...OAuthPages,
  ...LeavePages,
  ...AttendancePages,
  ...MaintenancePages,
  ...RecruitmentPages,
  ...PerformancePages,
  ...CorporateDirectoryPages,
  ...authenticationPages,
  ...languagePages,
  ...dashboardPages,
  ...buzzPages,
  ...systemCheckPages,
  ...claimPages,
};
