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
  <base-widget
    icon="lightning-charge-fill"
    :empty="isEmpty"
    :loading="isLoading"
    :title="$t('dashboard.quick_launch')"
  >
    <oxd-grid :cols="3" class="orangehrm-quick-launch">
      <oxd-grid-item
        v-for="action in sortedActions"
        :key="action"
        class="orangehrm-quick-launch-card"
      >
        <oxd-icon-button
          icon-type="svg"
          class="orangehrm-quick-launch-icon"
          :name="action.icon"
          :title="action.label"
          @click="onClickAction(action.path)"
        />
        <div class="orangehrm-quick-launch-heading" :title="action.label">
          <oxd-text tag="p" class="--text">
            {{ action.label }}
          </oxd-text>
        </div>
      </oxd-grid-item>
    </oxd-grid>
  </base-widget>
</template>

<script>
import {navigate} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import BaseWidget from '@/orangehrmDashboardPlugin/components/BaseWidget.vue';

export default {
  name: 'QuickLaunchWidget',

  components: {
    'base-widget': BaseWidget,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/dashboard/shortcuts',
    );

    return {
      http,
    };
  },

  data() {
    return {
      isLoading: false,
      quickLaunchActions: [],
    };
  },

  computed: {
    isEmpty() {
      return this.quickLaunchActions.length === 0;
    },
    sortedActions() {
      return [...this.quickLaunchActions].sort(
        (prevItem, item) => prevItem.order - item.order,
      );
    },
  },

  beforeMount() {
    this.isLoading = true;

    const ACTIONS = {
      'leave.assign_leave': {
        order: 1,
        icon: 'leaveassign',
        label: this.$t('leave.assign_leave'),
        path: '/leave/assignLeave',
      },
      'leave.leave_list': {
        order: 2,
        icon: 'leavelist',
        label: this.$t('leave.leave_list'),
        path: '/leave/viewLeaveList',
      },
      'time.employee_timesheet': {
        order: 3,
        icon: 'timesheets',
        label: this.$t('general.timesheets'),
        path: '/time/viewEmployeeTimesheet',
      },
      'leave.apply_leave': {
        order: 4,
        icon: 'leaveapply',
        label: this.$t('leave.apply_leave'),
        path: '/leave/applyLeave',
      },
      'leave.my_leave': {
        order: 5,
        icon: 'myleaves',
        label: this.$t('general.my_leave'),
        path: '/leave/viewMyLeaveList',
      },
      'time.my_timesheet': {
        order: 6,
        icon: 'mytimesheet',
        label: this.$t('time.my_timesheet'),
        path: '/time/viewMyTimesheet',
      },
    };

    this.http
      .getAll()
      .then((response) => {
        const {data} = response.data;
        for (const key in data) {
          if (data[key]) {
            this.quickLaunchActions.push(ACTIONS[key]);
          }
        }
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onClickAction(path) {
      if (path) navigate(path);
    },
  },
};
</script>

<style src="./quick-launch-widget.scss" lang="scss" scoped></style>
