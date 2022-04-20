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

    <oxd-form-actions
      v-if="progress === 100"
      class="orangehrm-installer-page-action"
    >
      <oxd-button display-type="secondary" label="Next" @click="onClickNext" />
    </oxd-form-actions>

    <oxd-text v-else tag="p" class="orangehrm-installer-page-content--center">
      {{ progressNotice }}
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
import {navigate} from '@/core/util/helper/navigation.ts';

export default {
  name: 'UpgradeScreen',
  components: {
    'oxd-progress': ProgressBar,
    'installer-tasks': InstallerTasks,
  },
  setup() {
    const progress = ref(0);
    const tasks = ref([
      {name: 'Applying database changes', state: 1},
      {name: 'Creating configuration files', state: 0},
    ]);
    useBeforeUnload(progress);

    const http = new APIService(window.appGlobal.baseUrl, '');
    const {runAllMigrations} = useMigrations(http);
    runAllMigrations()
      .then(() => {
        tasks.value[0].state = 2;
        return http.request({
          method: 'POST',
          url: 'upgrader/api/config-file',
        });
      })
      .then(() => {
        tasks.value[1].state = 2;
        progress.value = 100;
      })
      .catch(() => {
        const currentTask = tasks.value.findIndex((task) => task.state === 1);
        tasks.value[currentTask].state = 3;
      });

    return {
      tasks,
      progress,
    };
  },
  computed: {
    progressText() {
      return `${this.progress}%`;
    },
    taskFailed() {
      return this.tasks.findIndex((task) => task.state === 3) > -1;
    },
    progressNotice() {
      return !this.taskFailed
        ? 'Please Wait. Upgrading in Progress'
        : 'One or more tasks has failed, Please restore database and try again.';
    },
  },
  methods: {
    onClickNext() {
      navigate('/upgrader/complete');
    },
  },
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
