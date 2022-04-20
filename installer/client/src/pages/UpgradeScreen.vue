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
      This may take some time. Please do not close the window till progress
      becomes 100%
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
import {ref} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import InstallerTasks from '@/components/InstallerTasks.vue';
import ProgressBar from '@ohrm/oxd/core/components/Progressbar/Progressbar.vue';
import useBeforeUnload from '@/core/util/composable/useBeforeUnload';
import useMigrations from '@/core/util/composable/useMigrations';

export default {
  name: 'UpgradeScreen',
  components: {
    'oxd-progress': ProgressBar,
    'installer-tasks': InstallerTasks,
  },
  setup() {
    const progress = ref(0);
    useBeforeUnload(progress);
    const {runAllMigrations} = useMigrations(
      new APIService(window.appGlobal.baseUrl, ''),
    );
    runAllMigrations().then((r) => console.log(r));
    return {
      progress,
    };
  },
  data() {
    return {
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
