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
      Installation
    </oxd-text>
    <br />
    <installer-tasks :tasks="tasks"></installer-tasks>
    <br />
    <oxd-text
      tag="h5"
      :class="{
        'orangehrm-installer-page-content': true,
        '--progress': true,
        '--error': taskFailed,
      }"
    >
      {{ progressText }}
    </oxd-text>
    <br />
    <oxd-progress
      :progress="progress"
      :type="progressType"
      :show-label="false"
    />
    <br />
    <oxd-text
      v-show="progress < 100"
      tag="p"
      class="orangehrm-installer-page-content--center"
    >
      {{ progressNotice }}
    </oxd-text>

    <oxd-form-actions class="orangehrm-installer-page-action">
      <oxd-button
        v-show="progress === 100"
        label="Next"
        display-type="secondary"
        @click="onClickNext"
      />
      <oxd-button
        v-show="taskFailed"
        label="Clean up Install"
        display-type="secondary"
        @click="onClickCleanup"
      />
    </oxd-form-actions>
  </div>
</template>

<script>
import {onBeforeMount, ref} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import InstallerTasks from '@/components/InstallerTasks.vue';
import ProgressBar from '@ohrm/oxd/core/components/Progressbar/Progressbar.vue';
import useBeforeUnload from '@/core/util/composable/useBeforeUnload';
import useInstaller from '@/core/util/composable/useInstaller';
import {navigate} from '@/core/util/helper/navigation.ts';
import useProgress from '@/core/util/composable/useProgress';

export default {
  name: 'InstallScreen',
  components: {
    'oxd-progress': ProgressBar,
    'installer-tasks': InstallerTasks,
  },
  setup() {
    const {progress, start, stop, end} = useProgress();
    useBeforeUnload(progress);

    const {
      runCleanup,
      runMigrations,
      createInstance,
      createDatabase,
      createConfigFiles,
      createDatabaseUser,
    } = useInstaller(new APIService(window.appGlobal.baseUrl, ''));

    const tasks = ref([
      {name: 'Database Creation', state: 1, task: createDatabase},
      {name: 'Create Database Tables', state: 0, task: runMigrations},
      {
        name: 'Fill default data into the database',
        state: 0,
        task: createInstance,
      },
      {name: 'Create Default User', state: 0, task: createDatabaseUser},
      {name: 'Write Configuration file', state: 0, task: createConfigFiles},
    ]);

    const onClickCleanup = () => {
      runCleanup().then(() => {
        navigate('/installer/confirmation');
      });
    };

    onBeforeMount(async () => {
      start();
      for (let index = 0; index < tasks.value.length; index++) {
        try {
          await tasks.value[index].task();
          tasks.value[index].state = 2;
        } catch (error) {
          // eslint-disable-next-line no-console
          console.error(error);
          tasks.value[index].state = 3;
          stop();
          break;
        }
        if (index === tasks.value.length - 1) end();
      }
    });

    return {
      tasks,
      progress,
      onClickCleanup,
    };
  },
  computed: {
    progressText() {
      return `${Math.floor(this.progress)}%`;
    },
    taskFailed() {
      return this.tasks.findIndex((task) => task.state === 3) > -1;
    },
    progressNotice() {
      return !this.taskFailed
        ? 'Please Wait. Installation in Progress'
        : 'One or more tasks has failed, Please clean up installtion and try again.';
    },
    progressType() {
      return !this.taskFailed ? 'secondary' : 'error';
    },
  },
  methods: {
    onClickNext() {
      navigate('/installer/complete');
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
  &.--progress {
    text-align: center;
    font-weight: 700;
    color: $oxd-secondary-four-color;
  }
  &.--error {
    color: $oxd-feedback-danger-color;
  }
}
</style>
