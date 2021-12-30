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

import {reactive} from 'vue';
import {AxiosResponse} from 'axios';
import {APIService} from '@/core/util/services/api.service';

interface State {
  isLoading: boolean;
  employee: Employee | null;
  timesheetId: number | null;
  timesheetRecords: Record[];
  timesheet: Timesheet | null;
  timesheetStatus: string | null;
  timesheetColumns: Columns | null;
  timesheetSubtotal: string | null;
  timesheetAllowedActions: AllowedAction[];
  date: string | null;
}

export interface Project {
  id: number;
  name: string;
  deleted: boolean;
}

export interface Customer {
  id: number;
  name: string;
  deleted: boolean;
}

export interface Activity {
  id: number;
  name: string;
  deleted: boolean;
}

export interface Total {
  hours: number;
  minutes: number;
  label: string;
}

export interface Entry {
  id: number;
  date: string;
  comment?: string;
  duration: string;
}

export interface UpdatedEntry {
  projectId: number;
  activityId: number;
  dates: Dates;
}

export interface DeletedEntry {
  projectId: number;
  activityId: number;
}

export interface Dates {
  [date: string]: Entry;
}

export interface Status {
  id: string;
  name: string;
}

export interface Timesheet {
  id: number;
  status: Status;
  startDate: string;
  endDate: string;
}

export interface Sum {
  hours: number;
  minutes: number;
  label: string;
}

export interface Columns {
  [date: string]: {
    total: Total;
  };
}

export interface Employee {
  empNumber: number;
  lastName: string;
  firstName: string;
  middleName: string;
  employeeId: string;
  terminationId?: number;
}

export interface AllowedAction {
  action: string;
  name: string;
}

export interface Meta {
  timesheet: Timesheet;
  sum: Sum;
  columns: Columns;
  dates: string[];
  employee: Employee;
  allowedActions: AllowedAction[];
}

export interface Record {
  project: Project;
  customer: Customer;
  activity: Activity;
  total: Total;
  dates: Dates;
}

export interface TimesheetResponse {
  data: Timesheet[];
  meta: {
    total: number;
  };
}

export interface TimesheetUpdateResponse {
  data: Timesheet;
}

export interface EntriesResponse {
  data: Record[];
  meta: Meta;
}

export interface EntriesUpdateRequest {
  entries: UpdatedEntry[];
  deletedEntries: DeletedEntry[];
}

export default function useTimesheetAPIs(http: APIService) {
  const state = reactive<State>({
    isLoading: false,
    employee: null,
    timesheet: null,
    timesheetId: null,
    timesheetRecords: [],
    timesheetStatus: null,
    timesheetColumns: null,
    timesheetSubtotal: null,
    timesheetAllowedActions: [],
    date: null,
  });

  const fetchTimesheet = (date: string | null): Promise<number | null> => {
    return new Promise(resolve => {
      http
        .request({
          method: 'GET',
          url: '/api/v2/time/timesheets',
          params: {date},
        })
        .then((response: AxiosResponse<TimesheetResponse>) => {
          const {data} = response.data;
          if (data.length > 0) {
            const {id} = data[0];
            resolve(id);
          } else {
            resolve(null);
          }
        });
    });
  };

  const updateTimesheet = (
    timesheetId: number,
    action: string,
    comment: string | null = null,
  ): Promise<AxiosResponse<TimesheetUpdateResponse>> => {
    return http.request({
      method: 'PUT',
      url: `/api/v2/time/timesheets/${timesheetId}`,
      data: {
        action,
        comment,
      },
    });
  };

  const fetchTimesheetEntries = (
    timesheetId: number,
  ): Promise<{
    data: Record[];
    meta: Meta;
    timesheet: Timesheet;
    allowedActions: AllowedAction[];
  }> => {
    return new Promise(resolve => {
      http
        .request({
          method: 'GET',
          url: `api/v2/time/timesheets/${timesheetId}/entries`,
        })
        .then((response: AxiosResponse<EntriesResponse>) => {
          const {data, meta} = response.data;
          const {timesheet, allowedActions} = meta;
          resolve({data, meta, timesheet, allowedActions});
        });
    });
  };

  const updateTimesheetEntries = (
    timesheetId: number,
    payload: EntriesUpdateRequest,
  ): Promise<AxiosResponse<TimesheetUpdateResponse>> => {
    return http.request({
      method: 'PUT',
      url: `/api/v2/time/timesheets/${timesheetId}/entries`,
      data: {
        ...payload,
      },
    });
  };

  return {
    state,
    fetchTimesheet,
    updateTimesheet,
    fetchTimesheetEntries,
    updateTimesheetEntries,
  };
}
