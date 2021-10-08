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

interface Duration {
  type: string;
  fromTime: string | undefined;
  toTime: string | undefined;
}

interface LeaveRequest {
  empNumber: number;
  leaveTypeId: number;
  fromDate: string;
  toDate: string;
  comment: string | undefined;
  partialOption: string | undefined;
  duration: Duration;
  endDuration: Duration | undefined;
}

interface ParamsObj {
  [key: string]: string | number | undefined;
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
  const serializeParams = (leaveData: LeaveRequest) => {
    const {duration, endDuration, ...rest} = leaveData;
    const payload: ParamsObj = {
      ...rest,
      leaveTypeId: undefined,
      comment: undefined,
    };

    if (duration?.type) {
      payload['duration[type]'] = duration.type;
      payload['duration[fromTime]'] =
        duration.type === 'specify_time' ? duration.fromTime : undefined;
      payload['duration[toTime]'] =
        duration.type === 'specify_time' ? duration.toTime : undefined;
    }
    if (endDuration?.type) {
      payload['endDuration[type]'] = endDuration.type;
      payload['endDuration[fromTime]'] =
        endDuration.type === 'specify_time' ? endDuration.fromTime : undefined;
      payload['endDuration[toTime]'] =
        endDuration.type === 'specify_time' ? endDuration.toTime : undefined;
    }
    return payload;
  };

  const validateOverlapLeaves = (
    leaveData: LeaveRequest,
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

  const validateLeaveBalance = (
    leaveData: LeaveRequest,
  ): Promise<BalanceObj> => {
    return new Promise((resolve, reject) => {
      http
        .request({
          method: 'GET',
          url: `api/v2/leave/leave-balance/leave-type/${leaveData.leaveTypeId}`,
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
    validateLeaveBalance,
    validateOverlapLeaves,
  };
}
