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
  <!-- Always use inside within OXD-Grid inside OXD-Form -->
  <oxd-grid-item style="grid-column-start: 1">
    <oxd-input-field
      type="select"
      v-bind="$attrs"
      :options="options"
      :modelValue="duration"
      :rules="rules.duration"
      @update:modelValue="$emit('update:duration', $event)"
      required
    />
  </oxd-grid-item>
  <template v-if="duration && duration.id === 4">
    <oxd-grid-item>
      <time-input
        :label="$t('general.from')"
        :modelValue="fromTime"
        :rules="rules.fromTime"
        @update:modelValue="$emit('update:fromTime', $event)"
        required
      />
    </oxd-grid-item>
    <oxd-grid-item>
      <time-input
        :label="$t('general.to')"
        :modelValue="toTime"
        :rules="rules.toTime"
        @update:modelValue="$emit('update:toTime', $event)"
        required
      />
    </oxd-grid-item>
    <oxd-grid-item>
      <oxd-input-group :label="$t('general.duration')">
        <oxd-text class="orangehrm-leave-duration" tag="p">
          {{ selectedTimeDuration }}
        </oxd-text>
      </oxd-input-group>
    </oxd-grid-item>
  </template>
</template>

<script>
import {diffInTime, secondsTohhmm} from '@orangehrm/core/util/helper/datefns';
import {
  endTimeShouldBeAfterStartTime,
  required,
  validTimeFormat,
} from '@/core/util/validation/rules';

export default {
  name: 'leave-duration-input',
  inheritAttrs: false,
  props: {
    duration: {
      type: Object,
      required: false,
    },
    fromTime: {
      type: String,
      required: false,
    },
    toTime: {
      type: String,
      required: false,
    },
    partial: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      rules: {
        duration: [required],
        fromTime: [required, validTimeFormat()],
        toTime: [
          required,
          validTimeFormat(),
          endTimeShouldBeAfterStartTime(
            () => this.fromTime,
            'To time should be after from time',
          ),
        ],
      },
    };
  },
  computed: {
    selectedTimeDuration() {
      const timeDifference = diffInTime(this.fromTime, this.toTime);
      return secondsTohhmm(timeDifference);
    },
    options() {
      const durations = [
        {id: 1, label: 'Full Day', key: 'full_day'},
        {id: 2, label: 'Half Day - Morning', key: 'half_day_morning'},
        {id: 3, label: 'Half Day - Afternoon', key: 'half_day_afternoon'},
        {id: 4, label: 'Specify Time', key: 'specify_time'},
      ];
      return this.partial ? durations.filter(i => i.id != 1) : durations;
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-leave-duration {
  padding: $oxd-input-control-vertical-padding 0rem;
}
</style>
