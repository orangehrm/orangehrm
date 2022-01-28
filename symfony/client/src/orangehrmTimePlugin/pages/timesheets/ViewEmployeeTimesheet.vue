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
          {{ title }}
        </oxd-text>
      </template>
      <template #header-options>
        <timesheet-period
          v-model="date"
          :value="timesheetPeriod"
          @next="onClickNext"
          @previous="onClickPrevious"
        ></timesheet-period>
      </template>
      <template #footer-title>
        <oxd-text v-show="timesheetStatus" type="subtitle-2">
          {{ $t('general.status') }}: {{ timesheetStatus }}
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
          v-if="canResetTimesheet"
          display-type="ghost"
          :label="$t('general.reset')"
          @click="onClickReset"
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
    <save-timesheet-action
      v-if="timesheetId && (canRejectTimesheet || canApproveTimesheet)"
      :key="timesheetId"
      :is-loading="isLoading"
      :reject-timesheet="onClickReject"
      :approve-timesheet="onClickApprove"
      :can-reject-timesheet="!!canRejectTimesheet"
      :can-approve-timesheet="!!canApproveTimesheet"
    ></save-timesheet-action>
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
import SaveTimesheetAction from '@/orangehrmTimePlugin/components/SaveTimesheetAction.vue';

export default {
  components: {
    timesheet: Timesheet,
    'timesheet-period': TimesheetPeriod,
    'timesheet-actions': TimesheetActions,
    'save-timesheet-action': SaveTimesheetAction,
  },

  props: {
    employee: {
      type: Object,
      required: true,
    },
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
      onClickReset,
      onClickSubmit,
      onClickReject,
      onClickApprove,
      onClickPrevious,
      timesheetPeriod,
      canEditTimesheet,
      canResetTimesheet,
      canSubmitTimesheet,
      canRejectTimesheet,
      canCreateTimesheet,
      canApproveTimesheet,
      showCreateTimesheet,
      onClickCreateTimesheet,
    } = useTimesheet(http, props.startDate, props.employee.empNumber);

    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const {employee, ...rest} = toRefs(state);

    return {
      ...rest,
      onClickEdit,
      onClickNext,
      onClickReset,
      onClickSubmit,
      onClickReject,
      onClickApprove,
      onClickPrevious,
      timesheetPeriod,
      canEditTimesheet,
      canResetTimesheet,
      canSubmitTimesheet,
      canRejectTimesheet,
      canCreateTimesheet,
      canApproveTimesheet,
      showCreateTimesheet,
      onClickCreateTimesheet,
    };
  },

  computed: {
    title() {
      const empName = this.employee.terminationId
        ? `${this.employee.firstName} ${this.employee.lastName} (Past Employee)`
        : `${this.employee.firstName} ${this.employee.lastName}`;
      return `${this.$t('time.timesheet_for')} ${empName}`;
    },
  },
};
</script>
