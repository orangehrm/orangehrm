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

type LeaveAction = {
  component: string;
  props: object;
};

type LeaveSecondaryAction = {
  label: string;
  context: string;
};

type useLeaveActionsArgs = {
  primaryActions?: {[name: string]: LeaveAction};
  secondaryActions?: LeaveSecondaryAction[];
};

const approve: LeaveAction = {
  component: 'oxd-button',
  props: {
    label: 'Approve',
    displayType: 'label-success',
    size: 'medium',
    onClick: null,
  },
};

const reject: LeaveAction = {
  component: 'oxd-button',
  props: {
    label: 'Reject',
    displayType: 'label-danger',
    size: 'medium',
    onClick: null,
  },
};

const cancel: LeaveAction = {
  component: 'oxd-button',
  props: {
    label: 'Cancel',
    displayType: 'label-warn',
    size: 'medium',
    onClick: null,
  },
};

const more: LeaveAction = {
  component: 'oxd-table-dropdown',
  props: {
    options: [],
    style: {'margin-left': 'auto'},
    onClick: null,
  },
};

export default function useLeaveActions(
  http: APIService,
  {primaryActions = {approve, reject, cancel, more}}: useLeaveActionsArgs = {},
) {
  const processLeaveAction = (id: number, actionType: string) => {
    return http.request({
      method: 'PUT',
      url: `api/v2/leave/leaves/${id}`,
      data: {
        action: actionType,
      },
    });
  };

  const processLeaveRequestAction = (id: number, actionType: string) => {
    return http.request({
      method: 'PUT',
      url: `api/v2/leave/employees/leave-requests/${id}`,
      data: {
        action: actionType,
      },
    });
  };

  const processLeaveRequestBulkAction = (ids: number[], actionType: string) => {
    return http.request({
      method: 'PUT',
      url: 'api/v2/leave/employees/leave-requests/bulk',
      data: {
        data: ids.map(id => {
          return {
            leaveRequestId: id,
            action: actionType,
          };
        }),
      },
    });
  };

  return {
    leaveActions: primaryActions,
    processLeaveAction,
    processLeaveRequestAction,
    processLeaveRequestBulkAction,
  };
}
