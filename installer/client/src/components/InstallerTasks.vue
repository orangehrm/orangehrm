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
  <div class="orangehrm-installer-task">
    <div
      v-for="task in tasks"
      :key="task"
      class="orangehrm-installer-task-item"
    >
      <oxd-text
        tag="p"
        :class="{
          'orangehrm-installer-task-item-name': true,
          '--active': task.state === 1,
          '--error': task.state === 3,
        }"
      >
        {{ task.name }}
      </oxd-text>
      <div class="orangehrm-installer-task-item-progress">
        <oxd-loading-spinner v-if="task.state === 1" :with-container="false" />
        <div
          v-else-if="task.state === 2"
          class="orangehrm-installer-task-icon --done"
        >
          <oxd-icon name="check" />
        </div>
        <div
          v-else-if="task.state === 3"
          class="orangehrm-installer-task-icon --error"
        >
          <oxd-icon name="exclamation" />
        </div>
        <div v-else class="orangehrm-installer-task-icon --pending">
          <oxd-icon name="dash" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Icon from '@ohrm/oxd/core/components/Icon/Icon.vue';
import Spinner from '@ohrm/oxd/core/components/Loader/Spinner.vue';

export default {
  name: 'InstallerTasks',
  components: {
    'oxd-icon': Icon,
    'oxd-loading-spinner': Spinner,
  },
  props: {
    tasks: {
      type: Array,
      default: () => [],
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-installer-task {
  &-item {
    width: 70%;
    max-width: 320px;
    display: flex;
    padding: 0.5rem 0;
    justify-content: space-between;
    align-items: center;
  }
  &-item-name {
    font-size: 16px;
    &.--active {
      font-weight: 700;
    }
    &.--error {
      font-weight: 700;
      color: $oxd-feedback-danger-color;
    }
  }
  &-icon {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    text-align: center;
    line-height: 20px;
    font-size: 16px;
    &.--done {
      background-color: $oxd-secondary-four-color;
    }
    &.--pending {
      background-color: $oxd-interface-gray-darken-1-color;
    }
    &.--error {
      background-color: $oxd-feedback-danger-color;
    }
  }
}
::v-deep(.oxd-loading-spinner) {
  width: 10px;
  height: 10px;
}
::v-deep(.oxd-icon) {
  color: $oxd-white-color;
}
</style>
