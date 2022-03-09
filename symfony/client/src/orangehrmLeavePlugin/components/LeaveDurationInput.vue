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
      :model-value="duration"
      :rules="rules.duration"
      :show-empty-selector="partial"
      :required="partial"
      @update:modelValue="$emit('update:duration', $event)"
    />
  </oxd-grid-item>
  <template v-if="duration && duration.id === 4">
    <time-range
      :rules="rules"
      :from-time="fromTime"
      :to-time="toTime"
      :work-shift="workShift"
      @update:fromTime="$emit('update:fromTime', $event)"
      @update:toTime="$emit('update:toTime', $event)"
    ></time-range>
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
import {diffInTime} from '@ohrm/core/util/helper/datefns';
import {
  required,
  validTimeFormat,
  endTimeShouldBeAfterStartTime,
  startTimeShouldBeBeforeEndTime,
} from '@/core/util/validation/rules';
import TimeRange from '@/orangehrmLeavePlugin/components/TimeRange';

export default {
  name: 'LeaveDurationInput',
  components: {
    'time-range': TimeRange,
  },
  inheritAttrs: false,
  props: {
    duration: {
      type: Object,
      required: false,
      default: () => null,
    },
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
    partial: {
      type: Boolean,
      default: false,
    },
    workShift: {
      type: Object,
      required: true,
      default: () => ({}),
    },
  },
  emits: ['update:fromTime', 'update:toTime', 'update:duration'],
  data() {
    return {
      rules: {
        duration: [required],
        fromTime: [
          required,
          validTimeFormat,
          startTimeShouldBeBeforeEndTime(
            () => this.toTime,
            this.$t('general.from_time_should_be_before_to_time'),
          ),
        ],
        toTime: [
          required,
          validTimeFormat,
          endTimeShouldBeAfterStartTime(
            () => this.fromTime,
            this.$t('general.to_time_should_be_after_from_time'),
          ),
          value => {
            if (value) {
              const workLength = diffInTime(
                this.workShift.startTime,
                this.workShift.endTime,
              );
              const selectedLength = diffInTime(this.fromTime, value);
              if (selectedLength > workLength)
                return this.$t(
                  'leave.duration_should_be_less_than_work_shift_length',
                );
            }
            return true;
          },
        ],
      },
    };
  },
  computed: {
    selectedTimeDuration() {
      const timeDifference = diffInTime(this.fromTime, this.toTime);
      return (timeDifference / 3600).toFixed(2);
    },
    options() {
      const durations = [
        {id: 1, label: this.$t('leave.full_day'), key: 'full_day'},
        {
          id: 2,
          label: this.$t('leave.half_day_morning'),
          key: 'half_day_morning',
        },
        {
          id: 3,
          label: this.$t('leave.half_day_evening'),
          key: 'half_day_afternoon',
        },
        {id: 4, label: this.$t('leave.specify_time'), key: 'specify_time'},
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
