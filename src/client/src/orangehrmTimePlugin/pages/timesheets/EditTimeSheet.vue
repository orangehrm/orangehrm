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
      v-model:records="timesheetRecords"
      :editable="true"
      :loading="isLoading"
      :timesheet-id="timesheetId"
      :columns="timesheetColumns"
      @submit-valid="onSave"
    >
      <template #header-title>
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ title }}
        </oxd-text>
      </template>
      <template #header-options>
        <oxd-text
          v-if="timesheetDateRange"
          tag="p"
          class="orangehrm-timeperiod-title"
        >
          {{ $t('time.timesheet_period') }}
        </oxd-text>
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ timesheetDateRange }}
        </oxd-text>
      </template>

      <template #footer-options>
        <oxd-button
          display-type="ghost"
          :label="$t('general.cancel')"
          @click="onClickCancel"
        />
        <oxd-button
          display-type="ghost"
          :label="$t('general.reset')"
          @click="onClickReset"
        />
        <oxd-button
          type="submit"
          display-type="secondary"
          :label="$t('general.save')"
        />
      </template>
    </timesheet>
  </div>
</template>

<script>
import {
  secondsTohhmm,
  parseTimeInSeconds,
} from '@ohrm/core/util/helper/datefns';
import {onBeforeMount, toRefs} from 'vue';
import useToast from '@/core/util/composable/useToast';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import Timesheet from '@/orangehrmTimePlugin/components/Timesheet.vue';
import useTimesheetAPIs from '@/orangehrmTimePlugin/util/composable/useTimesheetAPIs';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useLocale from '@/core/util/composable/useLocale';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  components: {
    timesheet: Timesheet,
  },

  props: {
    myTimesheet: {
      type: Boolean,
      default: false,
    },
    timesheetId: {
      type: Number,
      required: true,
    },
  },

  setup(props) {
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/time/timesheets`,
    );

    http.setIgnorePath('/api/v2/time/timesheets/[0-9]+/entries');

    let timesheetModal = [];

    const {saveSuccess} = useToast();
    const {state, fetchTimesheetEntries, updateTimesheetEntries} =
      useTimesheetAPIs(http);
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();
    const {$tEmpName} = useEmployeeNameTranslate();

    const loadTimesheet = () => {
      state.isLoading = true;
      timesheetModal = [];
      state.timesheetRecords = [];
      fetchTimesheetEntries(props.timesheetId, !props.myTimesheet).then(
        (response) => {
          const {data, meta, timesheet, allowedActions} = response;
          state.timesheet = timesheet;
          state.employee = meta.employee;
          state.timesheetColumns = meta.columns;
          state.timesheetSubtotal = meta.sum.label;
          state.timesheetStatus = timesheet.status.name;
          state.timesheetAllowedActions = allowedActions;
          if (data.length > 0) {
            state.timesheetRecords = data;
            timesheetModal = JSON.parse(JSON.stringify(data));
          } else {
            state.timesheetRecords.push({
              project: null,
              activity: null,
              dates: {},
            });
            timesheetModal.push({
              project: null,
              activity: null,
              dates: {},
            });
          }
          state.isLoading = false;
        },
      );
    };

    const onClickReset = () => loadTimesheet();

    const onClickCancel = () => {
      if (props.myTimesheet) {
        navigate(
          '/time/viewMyTimesheet',
          {},
          {
            startDate: state.timesheet.startDate,
          },
        );
      } else {
        navigate(
          '/time/viewTimesheet/employeeId/{id}',
          {
            id: state.employee?.empNumber,
          },
          {startDate: state.timesheet.startDate},
        );
      }
    };

    const onSave = () => {
      state.isLoading = true;
      const payload = {
        entries: state.timesheetRecords.map((record) => {
          const dates = {};
          for (const date in record.dates) {
            const _duration = parseTimeInSeconds(record.dates[date].duration);
            dates[date] = {
              duration: _duration > 0 ? secondsTohhmm(_duration) : '00:00',
            };
          }
          return {
            projectId: record.project.id,
            activityId: record.activity.id,
            dates,
          };
        }),
        deletedEntries: timesheetModal
          .filter((record) => {
            if (!record.project) return false;
            return (
              state.timesheetRecords.findIndex(
                (item) =>
                  item.project.id === record.project.id &&
                  item.activity.id === record.activity.id,
              ) < 0
            );
          })
          .map((record) => ({
            projectId: record.project.id,
            activityId: record.activity.id,
          })),
      };
      updateTimesheetEntries(props.timesheetId, payload, !props.myTimesheet)
        .then(() => {
          return saveSuccess();
        })
        .catch(() => {
          // Catch invalid parameter error when submitting without any time
          return saveSuccess();
        })
        .then(() => {
          onClickCancel();
        });
    };

    onBeforeMount(() => loadTimesheet());

    return {
      onSave,
      onClickReset,
      onClickCancel,
      ...toRefs(state),
      jsDateFormat,
      locale,
      translateEmpName: $tEmpName,
    };
  },

  computed: {
    title() {
      if (this.myTimesheet) {
        return this.$t('time.edit_timesheet');
      } else if (this.employee) {
        const empName = this.translateEmpName(this.employee, {
          includeMiddle: false,
          excludePastEmpTag: false,
        });
        return `${this.$t('time.edit_timesheet_for')} ${empName}`;
      }
      return '';
    },
    timesheetDateRange() {
      if (!this.timesheet) return '';
      const startDate = formatDate(
        parseDate(this.timesheet.startDate),
        this.jsDateFormat,
        {locale: this.locale},
      );
      const endDate = formatDate(
        parseDate(this.timesheet.endDate),
        this.jsDateFormat,
        {locale: this.locale},
      );
      return `${startDate} - ${endDate}`;
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-timeperiod-title {
  font-size: 12px;
  margin-right: 10px;
}
.orangehrm-form-hint {
  margin-right: auto;
  font-weight: 600;
  font-size: 0.75rem;
  text-overflow: ellipsis;
  overflow: hidden;
}
</style>
