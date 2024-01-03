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
  <oxd-grid-item>
    <time-input
      :label="$t('general.from')"
      :model-value="fromTime"
      :rules="rules.fromTime"
      required
      @update:model-value="$emit('update:fromTime', $event)"
    />
  </oxd-grid-item>
  <oxd-grid-item>
    <time-input
      :label="$t('general.to')"
      :model-value="toTime"
      :rules="rules.toTime"
      required
      @update:model-value="$emit('update:toTime', $event)"
    />
  </oxd-grid-item>
</template>

<script>
export default {
  name: 'TimeRange',
  inheritAttrs: false,
  props: {
    fromTime: {
      type: String,
      required: false,
      default: null,
    },
    toTime: {
      type: String,
      required: false,
      default: null,
    },
    rules: {
      type: Object,
      required: true,
      default: () => ({}),
    },
    workShift: {
      type: Object,
      required: true,
      default: () => ({}),
    },
  },
  emits: ['update:fromTime', 'update:toTime'],
  mounted() {
    if (this.workShift?.startTime) {
      this.$emit('update:fromTime', this.workShift.startTime);
    }
    if (this.workShift?.endTime) {
      this.$emit('update:toTime', this.workShift.endTime);
    }
  },
};
</script>
