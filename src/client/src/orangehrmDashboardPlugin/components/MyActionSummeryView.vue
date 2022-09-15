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
  <base-widget
    icon="leavelist"
    icon-type="svg"
    :empty="isEmpty"
    :empty-text="emptyText"
    :loading="isLoading"
    :title="$t('general.my_actions')"
  >
    <div class="orangehrm-todo-list">
      <div class="orangehrm-todo-list-item">
        <oxd-icon-button
          class="orangehrm-report-icon"
          name="clock"
          display-type="success"
          @click="onClickPendingLeave"
        />
        <oxd-text tag="p" @click="onClickPendingLeave">
          (100) Leave Request To Aprove
        </oxd-text>
      </div>
      <div class="orangehrm-todo-list-item">
        <oxd-icon-button
          class="orangehrm-report-icon"
          name="calendar3"
          display-type="warn"
          @click="onClickPendingTimesheet"
        />
        <oxd-text tag="p" @click="onClickPendingTimesheet">
          (4) Timesheet to aprove
        </oxd-text>
      </div>
      <div class="orangehrm-todo-list-item">
        <oxd-icon-button
          class="orangehrm-report-icon"
          name="person-fill"
          display-type="danger"
          @click="onClickPendingReview"
        />
        <oxd-text tag="p" @click="onClickPendingReview">
          (1) Performance Review to aprove
        </oxd-text>
      </div>
      <div class="orangehrm-todo-list-item">
        <oxd-icon-button
          class="orangehrm-report-icon"
          name="people-fill"
          display-type="info"
          @click="onClickPendingInterview"
        />
        <oxd-text tag="p" @click="onClickPendingInterview">
          (3) candidate to interview
        </oxd-text>
      </div>
    </div>
  </base-widget>
</template>
<script>
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import BaseWidget from '@/orangehrmDashboardPlugin/components/BaseWidget.vue';

export default {
  name: 'MyActionSummeryViewWidget',

  components: {
    'base-widget': BaseWidget,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      'api/v2/dashboard/employees/action-summary',
    );

    return {
      http,
    };
  },

  data() {
    return {};
  },

  beforeMount() {
    this.isLoading = false;
    this.http
      .getAll()
      .then(response => {
        const {data, meta} = response.data;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onClickPendingLeave() {
      navigate('/leave/viewLeaveList');
    },
    onClickPendingTimesheet() {
      navigate('/time/viewEmployeeTimesheet');
    },
    onClickPendingReview() {
      navigate('/performance/searchEvaluatePerformanceReview');
    },
    onClickPendingInterview() {
      navigate('/recruitment/viewCandidates');
    },
  },
};
</script>
<style lang="scss" scoped>
@import '@ohrm/oxd/styles/_mixins.scss';

.orangehrm-todo-list {
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;

  &-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;

    & p {
      font-size: 12px;
      margin-left: 0.5rem;
    }
  }
}
</style>
