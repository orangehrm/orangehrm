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
  <div class="orangehrm-timeperiod-wrapper">
    <oxd-text tag="p" class="orangehrm-timeperiod-title">
      {{ $t('time.timesheet_period') }}
    </oxd-text>
    <div class="orangehrm-timeperiod-picker">
      <oxd-icon-button
        class="orangehrm-timeperiod-icon"
        name="chevron-left"
        @click="onClickPervious"
      />
      <date-input v-model="date" />
      <oxd-icon-button
        class="orangehrm-timeperiod-icon"
        name="chevron-right"
        @click="onClickNext"
      />
    </div>
  </div>
</template>

<script>
import {reactive, toRefs} from 'vue';
import {formatDate} from '@ohrm/core/util/helper/datefns';

export default {
  name: 'TimesheetPeriod',
  inheritAttrs: false,

  props: {
    employeeId: {
      type: Number,
      required: false,
      default: null,
    },
  },
  setup() {
    const state = reactive({
      date: formatDate(new Date(), 'yyyy-MM-dd'),
      periods: [],
    });

    const onClickPervious = () => {
      // do nothing
    };

    const onClickNext = () => {
      // do nothing
    };

    return {
      onClickNext,
      onClickPervious,
      ...toRefs(state),
    };
  },
};
</script>

<style src="./timesheet-period.scss" lang="scss" scoped></style>
