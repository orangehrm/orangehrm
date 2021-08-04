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
  <!-- Always use inside OXD-Grid within OXD-Form -->
  <oxd-grid-item v-if="duration?.label === 'Half Day'">
    <oxd-input-field
      type="select"
      label="&nbsp;"
      :modelValue="halfday"
      :rules="rules.halfday"
      :options="halfdayOptions"
      @update:modelValue="$emit('update:halfday', $event)"
    />
  </oxd-grid-item>
  <template v-if="duration?.label === 'Specify Time'">
    <oxd-grid-item>
      <oxd-input-field
        type="time"
        :label="$t('general.from')"
        :modelValue="fromTime"
        :rules="rules.fromTime"
        @update:modelValue="$emit('update:fromTime', $event)"
        required
      />
    </oxd-grid-item>
    <oxd-grid-item>
      <oxd-input-field
        type="time"
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

export default {
  name: 'leave-duration-input',
  inheritAttrs: false,
  props: {
    duration: {
      type: Object,
      required: false,
    },
    halfday: {
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
    rules: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      halfdayOptions: [
        {id: 1, label: 'Morning'},
        {id: 2, label: 'Afternoon'},
      ],
    };
  },
  computed: {
    selectedTimeDuration() {
      const timeDifference = diffInTime(this.fromTime, this.toTime);
      return secondsTohhmm(timeDifference);
    },
  },
};
</script>
