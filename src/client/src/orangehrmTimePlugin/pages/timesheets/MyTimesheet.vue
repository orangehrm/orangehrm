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
  <div class="orangehrm-background-container">
    <timesheet
      :loading="isLoading"
      :columns="timesheetColumns"
      :records="timesheetRecords"
      :timesheet-id="timesheetId"
      :subtotal="timesheetSubtotal"
    >
      <template #header-title>
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('time.my_timesheet') }}
        </oxd-text>
      </template>
      <template #header-options>
        <timesheet-period
          v-model="date"
          :value="timesheetPeriod"
          @previous="onClickPrevious"
          @next="onClickNext"
        ></timesheet-period>
      </template>
      <template #footer-title>
        <oxd-text v-show="timesheetStatus" type="subtitle-2">
          {{ $t('general.status') }}: {{ myTimesheetStatus }}
        </oxd-text>
      </template>
      <template #footer-options>
        <oxd-button
          v-if="showCreateTimesheet"
          display-type="secondary"
          :disabled="canCreateTimesheet"
          :label="$t('time.create_timesheet')"
          @click="onClickCreateTimesheet"
        />
        <oxd-button
          v-if="canEditTimesheet"
          display-type="ghost"
          :label="$t('general.edit')"
          @click="onClickEdit"
        />
        <oxd-button
          v-if="canSubmitTimesheet"
          display-type="secondary"
          :label="$t('general.submit')"
          @click="onClickSubmit"
        />
      </template>
    </timesheet>
    <br />
    <timesheet-actions
      v-if="timesheetId"
      :key="timesheetId"
      :timesheet-id="timesheetId"
    ></timesheet-actions>
  </div>
</template>

<script>
import {toRefs} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import Timesheet from '@/orangehrmTimePlugin/components/Timesheet.vue';
import useTimesheet from '@/orangehrmTimePlugin/util/composable/useTimesheet';
import TimesheetPeriod from '@/orangehrmTimePlugin/components/TimesheetPeriod.vue';
import TimesheetActions from '@/orangehrmTimePlugin/components/TimesheetActions.vue';

export default {
  components: {
    timesheet: Timesheet,
    'timesheet-period': TimesheetPeriod,
    'timesheet-actions': TimesheetActions,
  },

  props: {
    startDate: {
      type: String,
      required: false,
      default: null,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/time/timesheets',
    );

    const {
      state,
      onClickEdit,
      onClickNext,
      onClickSubmit,
      onClickPrevious,
      timesheetPeriod,
      canEditTimesheet,
      canSubmitTimesheet,
      canCreateTimesheet,
      showCreateTimesheet,
      onClickCreateTimesheet,
    } = useTimesheet(http, props.startDate);

    return {
      onClickEdit,
      onClickNext,
      onClickSubmit,
      onClickPrevious,
      ...toRefs(state),
      timesheetPeriod,
      canEditTimesheet,
      canSubmitTimesheet,
      canCreateTimesheet,
      showCreateTimesheet,
      onClickCreateTimesheet,
    };
  },
  data() {
    return {
      statuses: [
        {id: 1, label: this.$t('time.submitted'), name: 'Submitted'},
        {id: 2, label: this.$t('leave.rejected'), name: 'Rejected'},
        {id: 3, label: this.$t('time.not_submitted'), name: 'Not Submitted'},
        {id: 4, label: this.$t('time.approved'), name: 'Approved'},
      ],
    };
  },
  computed: {
    myTimesheetStatus() {
      return (
        this.statuses.find((item) => item.name === this.timesheetStatus)
          ?.label || null
      );
    },
  },
};
</script>
