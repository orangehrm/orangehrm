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

import {computed, watchEffect} from 'vue';
import useTimesheetAPIs from './useTimesheetAPIs';
import useToast from '@/core/util/composable/useToast';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import {freshDate, formatDate, parseDate} from '@ohrm/core/util/helper/datefns';
import {translate as translatorFactory} from '@/core/plugins/i18n/translate';

const translate = translatorFactory();

export default function useTimesheet(
  http: APIService,
  date: string | null,
  empNumber?: number,
) {
  const {
    state,
    fetchTimesheet,
    updateTimesheet,
    fetchTimesheetEntries,
  } = useTimesheetAPIs(http);
  const {noRecordsFound, success} = useToast();
  state.date = date ? date : formatDate(freshDate(), 'yyyy-MM-dd');

  const loadTimesheet = (date: string | null): void => {
    state.isLoading = true;
    fetchTimesheet(date, empNumber)
      .then(response => {
        const {data} = response.data;
        state.timesheet = data;
        state.timesheetId = data.id;
        return data.id
          ? fetchTimesheetEntries(data.id, empNumber !== undefined)
          : null;
      })
      .then(response => {
        if (response !== null) {
          const {data, meta, timesheet, allowedActions} = response;
          state.timesheetRecords = data;
          state.employee = meta.employee;
          state.timesheetColumns = meta.columns;
          state.timesheetSubtotal = meta.sum.label;
          state.timesheetStatus = timesheet.status.name;
          state.timesheetAllowedActions = allowedActions;
          data.length === 0 && noRecordsFound();
        } else {
          state.employee = null;
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

  watchEffect(async () => state.date && loadTimesheet(state.date));

  const onClickPrevious = (): void => {
    const currDate = parseDate(String(state.date), 'yyyy-MM-dd') ?? freshDate();
    currDate.setDate(currDate.getDate() - 7);
    state.date = formatDate(currDate, 'yyyy-MM-dd');
  };

  const onClickNext = (): void => {
    const currDate = parseDate(String(state.date), 'yyyy-MM-dd') ?? freshDate();
    currDate.setDate(currDate.getDate() + 7);
    state.date = formatDate(currDate, 'yyyy-MM-dd');
  };

  const onClickEdit = (): void => {
    state.timesheetId &&
      navigate('/time/editTimesheet/{id}', {id: state.timesheetId});
  };

  const onClickSubmit = (): void => {
    if (state.timesheetId) {
      state.isLoading = true;
      updateTimesheet(state.timesheetId, 'SUBMIT', null, empNumber).then(() => {
        success({
          title: translate('general.success'),
          message: translate('time.timesheet_submitted'),
        });
        state.timesheetId = null;
        loadTimesheet(state.date);
      });
    }
  };

  const onClickReset = (): void => {
    if (state.timesheetId) {
      state.isLoading = true;
      updateTimesheet(state.timesheetId, 'RESET', null, empNumber).then(() => {
        success({
          title: translate('general.success'),
          message: translate('time.timesheet_reset'),
        });
        state.timesheetId = null;
        loadTimesheet(state.date);
      });
    }
  };

  const onClickApprove = (comment?: string): void => {
    if (state.timesheetId) {
      state.isLoading = true;
      updateTimesheet(state.timesheetId, 'APPROVE', comment, empNumber).then(
        () => {
          success({
            title: translate('general.success'),
            message: translate('time.timesheet_approved'),
          });
          state.timesheetId = null;
          loadTimesheet(state.date);
        },
      );
    }
  };

  const onClickReject = (comment?: string): void => {
    if (state.timesheetId) {
      state.isLoading = true;
      updateTimesheet(state.timesheetId, 'REJECT', comment, empNumber).then(
        () => {
          success({
            title: translate('general.success'),
            message: translate('time.timesheet_rejected'),
          });
          state.timesheetId = null;
          loadTimesheet(state.date);
        },
      );
    }
  };

  const onClickCreateTimesheet = (): void => {
    state.isLoading = true;
    http
      .request({
        method: 'POST',
        url: empNumber
          ? `api/v2/time/employees/${empNumber}/timesheets`
          : '/api/v2/time/timesheets',
        data: {date: state.date},
      })
      .then(() => {
        success({
          title: translate('general.success'),
          message: translate('time.timesheet_successfully_created'),
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

  const canApproveTimesheet = computed(() => {
    return state.timesheetAllowedActions.find(i => i.action === 'APPROVE');
  });

  const canRejectTimesheet = computed(() => {
    return state.timesheetAllowedActions.find(i => i.action === 'REJECT');
  });

  const canResetTimesheet = computed(() => {
    return state.timesheetAllowedActions.find(i => i.action === 'RESET');
  });

  const canEditTimesheet = computed(() => {
    return state.timesheetAllowedActions.find(i => i.action === 'MODIFY');
  });

  const canCreateTimesheet = computed(() => {
    const currDate = parseDate(String(state.date), 'yyyy-MM-dd') ?? freshDate();
    return currDate > freshDate();
  });

  const timesheetPeriod = computed(() => {
    return state.timesheet
      ? `${state.timesheet.startDate} to ${state.timesheet.endDate}`
      : null;
  });

  return {
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
  };
}
