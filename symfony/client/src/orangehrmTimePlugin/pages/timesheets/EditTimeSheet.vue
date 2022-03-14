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
      v-model:records="timesheetRecords"
      :editable="true"
      :loading="isLoading"
      :timesheet-id="timesheetId"
      :columns="timesheetColumns"
      @submitValid="onSave"
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
import {onBeforeMount, toRefs, onUpdated} from 'vue';
import useToast from '@/core/util/composable/useToast';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import Timesheet from '@/orangehrmTimePlugin/components/Timesheet.vue';
import useTimesheetAPIs from '@/orangehrmTimePlugin/util/composable/useTimesheetAPIs';

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
    let tble = document.querySelector('.orangehrm-timesheet-body');
    let tdFromRight = 0;
    let windowScrollValue = 0;
    let activityTds = document.querySelector(
      '.orangehrm-timesheet-table-body-cell--dropdown',
    );
    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/time/timesheets`,
    );

    let timesheetModal = [];

    const {saveSuccess} = useToast();
    const {
      state,
      fetchTimesheetEntries,
      updateTimesheetEntries,
    } = useTimesheetAPIs(http);

    const loadTimesheet = () => {
      state.isLoading = true;
      timesheetModal = [];
      state.timesheetRecords = [];
      fetchTimesheetEntries(props.timesheetId, !props.myTimesheet).then(
        response => {
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
        entries: state.timesheetRecords.map(record => {
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
          .filter(record => {
            if (!record.project) return false;
            return (
              state.timesheetRecords.findIndex(
                item =>
                  item.project.id === record.project.id &&
                  item.activity.id === record.activity.id,
              ) < 0
            );
          })
          .map(record => ({
            projectId: record.project.id,
            activityId: record.activity.id,
          })),
      };
      updateTimesheetEntries(props.timesheetId, payload, !props.myTimesheet)
        .then(() => {
          return saveSuccess();
        })
        .then(() => {
          onClickCancel();
        });
    };

    const triggerEvent = function(e) {
      if (e.type === 'scroll') {
        windowScrollValue = 0;
        if (activityTds[0].style.right === '') {
          activityTds.forEach(el => {
            el.style.position = 'absolute';
          });
          tdFromRight = getComputedStyle(activityTds[0]).right;
        }
        activityTds.forEach(el => {
          el.style.right = parseFloat(tdFromRight) + tble.scrollLeft + 'px';
        });
        return;
      }
      if (e.type === 'resize') {
        windowScrollValue = tble.scrollLeft;
        activityTds.forEach(el => {
          el.style.position = 'relative';
          el.style.right = '';
          el.style.left = '';
        });
        return;
      }
      if (e.type === 'click') {
        const el = e.target.closest('.oxd-input-field-bottom-space');
        const tdElement = el?.closest(
          '.orangehrm-timesheet-table-body-cell--dropdown',
        );
        if (tdElement) {
          activityTds.forEach(ele => {
            ele.style.position = 'absolute';
          });
          tdFromRight = getComputedStyle(activityTds[0]).right;
          activityTds.forEach(ele => {
            ele.style.right =
              parseFloat(tdFromRight) + windowScrollValue + 'px';
          });
          windowScrollValue = 0;
        }
      }
    };

    const triggerEvents = () => {
      tdFromRight = getComputedStyle(activityTds[0]).right;
      tble.addEventListener('scroll', triggerEvent);
      tble.addEventListener('click', triggerEvent);
      window.addEventListener('resize', triggerEvent);
    };

    onUpdated(() => {
      if (!tble) {
        tble = document.querySelector('.orangehrm-timesheet-body');
        activityTds = document.querySelectorAll(
          '.orangehrm-timesheet-table-body-cell--dropdown',
        );
        triggerEvents();
      }
    });

    onBeforeMount(() => loadTimesheet());

    return {
      onSave,
      onClickReset,
      onClickCancel,
      ...toRefs(state),
    };
  },

  computed: {
    title() {
      if (this.myTimesheet) {
        return this.$t('time.edit_timesheet');
      } else if (this.employee) {
        const empName = this.employee?.terminationId
          ? `${this.employee.firstName} ${this.employee.lastName} (Past Employee)`
          : `${this.employee.firstName} ${this.employee.lastName}`;
        return `${this.$t('time.edit_timesheet_for')} ${empName}`;
      }
      return '';
    },
    timesheetDateRange() {
      return this.timesheet
        ? `${this.timesheet.startDate} - ${this.timesheet.endDate}`
        : '';
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
::v-deep(.orangehrm-timesheet-table-body-cell--dropdown) {
  position: absolute;
}
::v-deep(.orangehrm-timesheet-table-header-activity) {
  padding: 0 4rem !important;
}
</style>
