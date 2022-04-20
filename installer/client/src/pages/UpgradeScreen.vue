<!--
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
 -->
<template>
  <div class="orangehrm-installer-page">
    <oxd-text tag="h5" class="orangehrm-installer-page-title">
      Upgrading OrangeHRM
    </oxd-text>
    <br />
    <oxd-text tag="p" class="orangehrm-installer-page-content">
      This may take some time. Please do not close the window of the progress
      become 100%
    </oxd-text>
    <br />
    <installer-tasks :tasks="tasks"></installer-tasks>
    <br />
    <oxd-text tag="h5" class="orangehrm-installer-page-content--progress">
      {{ progressText }}
    </oxd-text>
    <br />
    <oxd-progress :progress="progress" type="secondary" :show-label="false" />
    <br />
    <oxd-text tag="p" class="orangehrm-installer-page-content--center">
      Please Wait. Upgrading in Progress
    </oxd-text>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import InstallerTasks from '@/components/InstallerTasks.vue';
import ProgressBar from '@ohrm/oxd/core/components/Progressbar/Progressbar.vue';
export default {
  name: 'UpgradeScreen',
  components: {
    'oxd-progress': ProgressBar,
    'installer-tasks': InstallerTasks,
  },
  setup() {
    const http = new APIService(
      'https://884b404a-f4d0-4908-9eb5-ef0c8afec15c.mock.pstmn.io',
      'upgrader/upgrade',
    );
    return {
      http,
    };
  },
  data() {
    return {
      progress: 0,
      tasks: [
        {name: 'Applying database changes', state: 1},
        {name: 'Creating configuration files', state: 0},
      ],
    };
  },
  computed: {
    progressText() {
      return `${this.progress}%`;
    },
  },
  methods: {},
};
</script>

<style src="./installer-page.scss" lang="scss" scoped></style>
<style scoped lang="scss">
.orangehrm-installer-page-content {
  &--center {
    text-align: center;
  }
  &--progress {
    text-align: center;
    font-weight: 700;
    color: $oxd-secondary-four-color;
  }
}
</style>
