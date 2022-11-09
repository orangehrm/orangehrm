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

import WelcomeScreen from '@/pages/WelcomeScreen.vue';
import DatabaseInfoScreen from '@/pages/DatabaseInfoScreen.vue';
import SystemCheckScreen from '@/pages/SystemCheckScreen.vue';
import CurrentVersionScreen from '@/pages/CurrentVersionScreen.vue';
import UpgraderCompleteScreen from '@/pages/UpgraderCompleteScreen.vue';
import UpgradeScreen from '@/pages/UpgradeScreen.vue';
import DatabaseConfigScreen from '@/pages/DatabaseConfigScreen.vue';
import AdminUserCreationScreen from '@/pages/AdminUserCreationScreen.vue';
import LicenceAcceptanceScreen from '@/pages/LicenceAcceptanceScreen.vue';
import InstanceCreationScreen from '@/pages/InstanceCreationScreen.vue';
import ConfirmationScreen from '@/pages/ConfirmationScreen.vue';
import InstallerCompleteScreen from '@/pages/InstallerCompleteScreen.vue';
import InstallScreen from '@/pages/InstallScreen.vue';

export default {
  'welcome-screen': WelcomeScreen,
  'database-info-screen': DatabaseInfoScreen,
  'system-check-screen': SystemCheckScreen,
  'current-version-screen': CurrentVersionScreen,
  'upgrader-complete-screen': UpgraderCompleteScreen,
  'upgrade-process-screen': UpgradeScreen,
  'database-config-screen': DatabaseConfigScreen,
  'admin-user-creation-screen': AdminUserCreationScreen,
  'licence-acceptance-screen': LicenceAcceptanceScreen,
  'instance-creation-screen': InstanceCreationScreen,
  'confirmation-screen': ConfirmationScreen,
  'installer-complete-screen': InstallerCompleteScreen,
  'install-process-screen': InstallScreen,
};
