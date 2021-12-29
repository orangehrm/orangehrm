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
          @previous="onClickPrevious"
          @next="onClickNext"
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
import {computed, reactive, toRefs, watchEffect} from 'vue';
import useToast from '@/core/util/composable/useToast';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import {freshDate, formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import Timesheet from '@/orangehrmTimePlugin/components/Timesheet.vue';
import TimesheetPeriod from '@/orangehrmTimePlugin/components/TimesheetPeriod.vue';
import TimesheetActions from '@/orangehrmTimePlugin/components/TimesheetActions.vue';

export default {
  components: {
    timesheet: Timesheet,
    'timesheet-period': TimesheetPeriod,
    'timesheet-actions': TimesheetActions,
  },

  setup() {
    const {noRecordsFound, success} = useToast();
    const http = new APIService(window.appGlobal.baseUrl, '');
    const state = reactive({
      isLoading: false,
      timesheetId: null,
      timesheetRecords: [],
      timesheetStatus: null,
      timesheetColumns: null,
      timesheetSubtotal: null,
      timesheetAllowedActions: [],
      date: formatDate(freshDate(), 'yyyy-MM-dd'),
    });

    const fetchTimesheet = date => {
      return new Promise(resolve => {
        http
          .request({
            method: 'GET',
            url: '/api/v2/time/timesheets',
            params: {date},
          })
          .then(response => {
            const {data} = response.data;
            if (Array.isArray(data) && data.length > 0) {
              const {id} = data[0];
              resolve(id);
            } else {
              resolve(null);
            }
          });
      });
    };

    const updateTimesheet = (timesheetId, action, comment = null) => {
      return new Promise(resolve => {
        http
          .request({
            method: 'PUT',
            url: `/api/v2/time/timesheets/${timesheetId}`,
            data: {
              action,
              comment,
            },
          })
          .then(response => {
            const {data} = response.data;
            resolve(data);
          });
      });
    };

    const fetchTimesheetEntries = timesheetId => {
      return new Promise(resolve => {
        http
          .request({
            type: 'GET',
            url: `api/v2/time/timesheets/${timesheetId}/entries`,
          })
          .then(response => {
            const {data, meta} = response.data;
            const {timesheet, allowedActions, ...rest} = meta;
            resolve({data, meta: rest, timesheet, allowedActions});
          });
      });
    };

    const loadTimesheet = date => {
      state.isLoading = true;
      fetchTimesheet(date)
        .then(id => {
          state.timesheetId = id;
          return id ? fetchTimesheetEntries(id) : null;
        })
        .then(response => {
          if (response !== null) {
            const {data, meta, timesheet, allowedActions} = response;
            state.timesheetRecords = data;
            state.timesheetColumns = meta.columns;
            state.timesheetSubtotal = meta.sum.label;
            state.timesheetStatus = timesheet.status.name;
            state.timesheetAllowedActions = allowedActions;
            data.length === 0 && noRecordsFound();
          } else {
            state.timesheetRecords = [];
            state.timesheetColumns = null;
            state.timesheetStatus = null;
            state.timesheetSubtotal = null;
            state.timesheetAllowedActions = [];
          }
        })
        .finally(() => {
          state.isLoading = false;
        });
    };

    watchEffect(async () => loadTimesheet(state.date));

    const onClickPrevious = () => {
      const currDate = parseDate(state.date, 'yyyy-MM-dd') ?? freshDate();
      currDate.setDate(currDate.getDate() - 7);
      state.date = formatDate(currDate, 'yyyy-MM-dd');
    };

    const onClickNext = () => {
      const currDate = parseDate(state.date, 'yyyy-MM-dd') ?? freshDate();
      currDate.setDate(currDate.getDate() + 7);
      state.date = formatDate(currDate, 'yyyy-MM-dd');
    };

    const onClickEdit = () => {
      navigate('/time/editTimesheet/{id}', {id: state.timesheetId});
    };

    const onClickSubmit = () => {
      state.isLoading = true;
      updateTimesheet(state.timesheetId, 'SUBMIT').then(() => {
        success({
          title: 'Success',
          message: 'Timesheet Submitted',
        });
        state.timesheetId = null;
        loadTimesheet(state.date);
      });
    };

    const onClickCreateTimesheet = () => {
      state.isLoading = true;
      http
        .request({
          method: 'POST',
          url: '/api/v2/time/timesheets',
          data: {date: state.date},
        })
        .then(() => {
          success({
            title: 'Success',
            message: 'Timesheet Successfully Created',
          });
          loadTimesheet(state.date);
        });
    };

    const showCreateTimesheet = computed(() => {
      return !state.isLoading && !state.timesheetId;
    });

    const canSubmitTimesheet = computed(() => {
      return state.timesheetAllowedActions.find(i => i.action === 'SUBMIT');
    });

    const canEditTimesheet = computed(() => {
      return state.timesheetAllowedActions.find(i => i.action === 'MODIFY');
    });

    const canCreateTimesheet = computed(() => {
      const currDate = parseDate(state.date, 'yyyy-MM-dd') ?? freshDate();
      return currDate > freshDate();
    });

    return {
      http,
      onClickEdit,
      onClickNext,
      onClickSubmit,
      onClickPrevious,
      ...toRefs(state),
      canEditTimesheet,
      canSubmitTimesheet,
      canCreateTimesheet,
      showCreateTimesheet,
      onClickCreateTimesheet,
    };
  },
};
</script>
