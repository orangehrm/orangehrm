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

import {APIService} from '@/core/util/services/api.service';
import {diffInDays} from '@ohrm/core/util/helper/datefns';
interface Duration {
  type: DurationOption | null;
  fromTime: string | null;
  toTime: string | null;
}

interface Option {
  id: number;
  label: string;
}
interface PartialOption extends Option {
  key: string;
}
interface DurationOption extends Option {
  key: string;
}

interface LeaveModel {
  type: Option | null;
  employee: Option | null;
  fromDate: string | null;
  toDate: string | null;
  comment: string | null;
  partialOptions: PartialOption | null;
  duration: Duration;
  endDuration: Duration;
}
interface LeaveRequestBody {
  leaveTypeId: number;
  fromDate: string;
  toDate: string;
  comment: string | null;
  duration?: DurationObject;
  endDuration?: DurationObject;
  partialOption?: string;
  empNumber?: number;
}

interface ParamsObj {
  [key: string]: string | number | undefined;
}

interface DurationObject {
  type: string;
  fromTime?: string;
  toTime?: string;
}
interface BalanceObj {
  balance: number;
  breakdown: object | null;
  metaData: object | null;
}

interface OverlapObj {
  isConflict: boolean;
  isOverWorkshift: boolean;
  data: Array<object>;
}

export default function useLeaveValidators(http: APIService) {
  const serializeBody = (leave: LeaveModel) => {
    const payload: LeaveRequestBody = {
      leaveTypeId: leave.type ? leave.type.id : 1,
      fromDate: leave.fromDate ? leave.fromDate : '',
      toDate: leave.toDate ? leave.toDate : '',
      comment: leave.comment === '' ? null : leave.comment,
      empNumber: leave.employee ? leave.employee.id : undefined,
    };

    if (leave.duration.type) {
      const duration: DurationObject = {
        type: leave.duration.type?.key,
      };
      if (duration.type === 'specify_time') {
        if (leave.duration.fromTime) {
          duration.fromTime = leave.duration.fromTime;
        }
        if (leave.duration.toTime) {
          duration.toTime = leave.duration.toTime;
        }
      }
      payload.duration = duration;
    }

    const leaveDuration = diffInDays(payload.fromDate, payload.toDate);

    if (leaveDuration > 1 && leave.partialOptions) {
      payload.partialOption = leave.partialOptions.key;
      if (leave.endDuration.type) {
        const endDuration: DurationObject = {
          type: leave.endDuration.type.key,
        };
        if (leave.endDuration.fromTime) {
          endDuration.fromTime = leave.endDuration.fromTime;
        }
        if (leave.endDuration.toTime) {
          endDuration.toTime = leave.endDuration.toTime;
        }
        if (payload.partialOption === 'start_end') {
          payload.endDuration = endDuration;
        } else if (payload.partialOption === 'end') {
          payload.duration = endDuration;
        }
      }
    }

    // Validation to prevent fromTime & toTime being sent with incorrect duration.type
    if (
      payload.duration?.type !== 'specify_time' &&
      (payload.duration?.fromTime || payload.duration?.toTime)
    ) {
      payload.duration.fromTime = undefined;
      payload.duration.toTime = undefined;
    }

    if (
      payload.endDuration?.type !== 'specify_time' &&
      (payload.endDuration?.fromTime || payload.endDuration?.toTime)
    ) {
      payload.endDuration.fromTime = undefined;
      payload.endDuration.toTime = undefined;
    }

    return payload;
  };

  const serializeParams = (leave: LeaveModel) => {
    const payload: ParamsObj = {
      fromDate: undefined,
      toDate: undefined,
      partialOption: undefined,
      empNumber: leave.employee?.id,
    };

    if (leave.duration.type) {
      payload['duration[type]'] = leave.duration.type.key;
      if (payload['duration[type]'] === 'specify_time') {
        if (leave.duration.fromTime) {
          payload['duration[fromTime]'] = leave.duration.fromTime;
        }
        if (leave.duration.toTime) {
          payload['duration[toTime]'] = leave.duration.toTime;
        }
      }
    }

    if (leave.fromDate && leave.toDate) {
      payload.fromDate = leave.fromDate;
      payload.toDate = leave.toDate;
      const leaveDuration = diffInDays(leave.fromDate, leave.toDate);

      if (leaveDuration > 1 && leave.partialOptions) {
        payload.partialOption = leave.partialOptions.key;
        if (leave.endDuration.type) {
          if (payload.partialOption === 'start_end') {
            payload['endDuration[type]'] = leave.endDuration.type.key;
            if (leave.endDuration.fromTime) {
              payload['endDuration[fromTime]'] = leave.endDuration.fromTime;
            }
            if (leave.endDuration.toTime) {
              payload['endDuration[toTime]'] = leave.endDuration.toTime;
            }
          } else if (payload.partialOption === 'end') {
            payload['duration[type]'] = leave.endDuration.type.key;
            if (leave.endDuration.fromTime) {
              payload['duration[fromTime]'] = leave.endDuration.fromTime;
            }
            if (leave.endDuration.toTime) {
              payload['duration[toTime]'] = leave.endDuration.toTime;
            }
          }
        }
      }
    }

    // Validation to prevent fromTime & toTime being sent with incorrect duration[type]
    if (
      payload['duration[type]'] !== 'specify_time' &&
      (payload['duration[fromTime]'] || payload['duration[toTime]'])
    ) {
      payload['duration[fromTime]'] = undefined;
      payload['duration[toTime]'] = undefined;
    }

    if (
      payload['endDuration[type]'] !== 'specify_time' &&
      (payload['endDuration[fromTime]'] || payload['endDuration[toTime]'])
    ) {
      payload['endDuration[fromTime]'] = undefined;
      payload['endDuration[toTime]'] = undefined;
    }

    return payload;
  };

  const validateOverlapLeaves = (
    leaveData: LeaveModel,
  ): Promise<OverlapObj> => {
    return new Promise((resolve, reject) => {
      http
        .request({
          method: 'GET',
          url: 'api/v2/leave/overlap-leaves',
          params: serializeParams(leaveData),
        })
        .then(response => {
          const {data, meta} = response.data;

          if (Array.isArray(data) && data.length > 0) {
            resolve({
              isConflict: true,
              isOverWorkshift: meta.isWorkShiftLengthExceeded === true,
              data,
            });
          } else {
            resolve({
              isConflict: false,
              isOverWorkshift: false,
              data: [],
            });
          }
        })
        .catch(error => {
          reject(error);
        });
    });
  };

  const validateLeaveBalance = (leaveData: LeaveModel): Promise<BalanceObj> => {
    return new Promise((resolve, reject) => {
      http
        .request({
          method: 'GET',
          url: `api/v2/leave/leave-balance/leave-type/${leaveData.type?.id}`,
          params: serializeParams(leaveData),
        })
        .then(response => {
          let balance = 0;
          let breakdown = null;
          let metaData = null;
          if (response.status === 200) {
            const {data, meta} = response.data;
            metaData = meta;
            if (data.balance) {
              // response sends balance directly when no duration defined
              breakdown = data.balance;
              balance = data.balance?.balance;
            } else if (data.breakdown && data.negative === false) {
              // if duration is defined and the balance is not exceeded
              breakdown = data.breakdown[0].balance;
              balance = data.breakdown[0].balance?.balance;
            } else if (data.breakdown && data.negative === true) {
              // if duration is defined and the balance is exceeded
              breakdown = data.breakdown;
              balance = -1;
            } else {
              breakdown = null;
              balance = 0;
            }
          }
          resolve({balance, breakdown, metaData});
        })
        .catch(error => {
          reject(error);
        });
    });
  };

  return {
    serializeBody,
    serializeParams,
    validateLeaveBalance,
    validateOverlapLeaves,
  };
}
