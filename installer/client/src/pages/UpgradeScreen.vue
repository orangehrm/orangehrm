<!--
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
 -->
<template>
  <div class="orangehrm-installer-page">
    <oxd-text tag="h5" class="orangehrm-installer-page-title">
      Upgrading OrangeHRM
    </oxd-text>
    <br />
    <oxd-text
      v-if="!taskFailed"
      tag="p"
      class="orangehrm-installer-page-content"
    >
      This may take some time. Please do not close the window till progress
      becomes 100%
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

    <oxd-form-actions
      v-if="progress === 100"
      class="orangehrm-installer-page-action"
    >
      <oxd-button display-type="secondary" label="Next" @click="onClickNext" />
    </oxd-form-actions>

    <oxd-text
      v-else
      tag="p"
      :class="{
        'orangehrm-installer-page-content': true,
        '--center': true,
        '--error': taskFailed,
      }"
    >
      <span v-if="taskFailed">
        {{ errorMessage }}. To learn more, check our FAQ at
        <a :href="faqUrl" target="_blank"> starterhelp.orangehrm.com </a>
      </span>
      <span v-else>Please Wait. Upgrading in Progress</span>
    </oxd-text>
  </div>
</template>

<script>
import {onBeforeMount, ref, computed} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import InstallerTasks from '@/components/InstallerTasks.vue';
import useBeforeUnload from '@/core/util/composable/useBeforeUnload';
import useUpgrader from '@/core/util/composable/useUpgrader';
import {navigate} from '@/core/util/helper/navigation.ts';
import useProgress from '@/core/util/composable/useProgress';
import {OxdProgressbar} from '@ohrm/oxd';

export default {
  name: 'UpgradeScreen',
  components: {
    'oxd-progress': OxdProgressbar,
    'installer-tasks': InstallerTasks,
  },
  setup() {
    const errorText = ref(null);
    const {progress, start, stop, end} = useProgress();

    const {runAllMigrations, createConfigFiles, preMigrationCheck} =
      useUpgrader(new APIService(window.appGlobal.baseUrl, ''));
    const tasks = ref([
      {
        name: 'Checking database permissions',
        state: 0,
        task: preMigrationCheck,
      },
      {name: 'Applying database changes', state: 0, task: runAllMigrations},
      {name: 'Creating configuration files', state: 0, task: createConfigFiles},
    ]);

    onBeforeMount(async () => {
      start();
      for (let index = 0; index < tasks.value.length; index++) {
        try {
          tasks.value[index].state = 1;
          await tasks.value[index].task();
          tasks.value[index].state = 2;
        } catch (error) {
          if (error?.message) {
            errorText.value = error?.message;
          }
          tasks.value[index].state = 3;
          stop();
          break;
        }
        if (index === tasks.value.length - 1) end();
      }
    });

    const onClickNext = () => {
      navigate('/upgrader/complete');
    };

    const progressText = computed(() => {
      return `${Math.floor(progress.value)}%`;
    });

    const taskFailed = computed(() => {
      return tasks.value.findIndex((task) => task.state === 3) > -1;
    });

    const errorMessage = computed(() =>
      taskFailed.value ? errorText.value ?? 'Upgrading has failed' : null,
    );

    const progressType = computed(() => {
      return !taskFailed.value ? 'secondary' : 'error';
    });

    const overrideUnload = computed(() => {
      return taskFailed.value || progress.value === 100;
    });

    useBeforeUnload(overrideUnload);

    return {
      tasks,
      progress,
      taskFailed,
      progressText,
      progressType,
      errorMessage,
      onClickNext,
    };
  },
  data() {
    return {
      faqUrl:
        'https://starterhelp.orangehrm.com/hc/en-us/categories/360002856800-FAQs',
    };
  },
};
</script>

<style src="./installer-page.scss" lang="scss" scoped></style>
<style scoped lang="scss">
.orangehrm-installer-page-content {
  &.--center {
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
