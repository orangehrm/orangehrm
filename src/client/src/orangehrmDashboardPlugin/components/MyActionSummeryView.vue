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
    icon="list-check"
    :empty="isEmpty"
    :empty-text="emptyText"
    :loading="isLoading"
    :title="$t('general.my_actions')"
  >
    <div v-if="myActions.length > 0" class="orangehrm-todo-list">
      <div v-if="leaveRequestCount > 0" class="orangehrm-todo-list-item">
        <oxd-icon-button
          class="orangehrm-report-icon"
          name="clock"
          display-type="success"
          @click="onClickPendingLeave"
        />
        <oxd-text tag="p" @click="onClickPendingLeave">
          {{
            $t('general.n_pending_leave_request', {
              pendingActionsCount: leaveRequestCount,
            })
          }}
        </oxd-text>
      </div>
      <div v-if="timeSheetCount > 0" class="orangehrm-todo-list-item">
        <oxd-icon-button
          class="orangehrm-report-icon"
          name="calendar3"
          display-type="warn"
          @click="onClickPendingTimesheet"
        />
        <oxd-text tag="p" @click="onClickPendingTimesheet">
          {{
            $t('general.n_pending_time_sheet', {
              pendingActionsCount: timeSheetCount,
            })
          }}
        </oxd-text>
      </div>
      <div v-if="reviewCount > 0" class="orangehrm-todo-list-item">
        <oxd-icon-button
          class="orangehrm-report-icon"
          name="person-fill"
          display-type="danger"
          @click="onClickPendingReview"
        />
        <oxd-text tag="p" @click="onClickPendingReview">
          {{
            $t('general.n_pending_performance_evaluate', {
              pendingActionsCount: reviewCount,
            })
          }}
        </oxd-text>
      </div>
      <div v-if="interviewCount > 0" class="orangehrm-todo-list-item">
        <oxd-icon-button
          class="orangehrm-report-icon"
          name="people-fill"
          display-type="info"
          @click="onClickPendingInterview"
        />
        <oxd-text tag="p" @click="onClickPendingInterview">
          {{
            $t('general.n_pending_candidate_interview', {
              pendingActionsCount: interviewCount,
            })
          }}
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
    return {
      myActions: [],
      leaveRequestCount: 0,
      timeSheetCount: 0,
      reviewCount: 0,
      interviewCount: 0,
    };
  },

  computed: {
    isEmpty() {
      return this.myActions.length === 0;
    },
    emptyText() {
      return this.$t('general.no_pending_actions');
    },
  },

  beforeMount() {
    this.isLoading = false;
    this.http
      .getAll()
      .then(response => {
        const {data} = response.data;
        this.myActions = data.map(item => {
          const {group, pendingActionCount} = item;
          if (group === 'Leave Requests To Approve') {
            this.leaveRequestCount = pendingActionCount;
          }
          if (group === 'Timesheets To Approve') {
            this.timeSheetCount = pendingActionCount;
          }
          if (group === 'Performance Reviews To Evaluate') {
            this.reviewCount = pendingActionCount;
          }
          if (group === 'Candidates To Interview') {
            this.interviewCount = pendingActionCount;
          }
        });
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
      cursor: pointer;
    }
  }
}
</style>
