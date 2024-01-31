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
    icon="leaveAlt"
    icon-type="svg"
    class="emp-leave-chart"
    :empty="isEmpty"
    :empty-text="emptyText"
    :loading="isLoading"
    :title="$t('dashboard.up_coming_holidays')"
  >
    <div v-for="leave in leaveList" :key="leave" class="orangehrm-leave-card">
      <div
        style="
          padding: 0.5rem;
          border-radius: 0.75rem;
          width: 100%;
          border: 1px solid #e8eaef;
          display: flex;
          justify-content: start;
        "
      >
        <div class="orangehrm-leave-card-emp-name" style="color: #64728c">
          {{ leave.date }} -
        </div>
        <div class="orangehrm-leave-card-details">
          <oxd-text tag="p" class="orangehrm-leave-card-emp-name">
            {{ leave.name }}
          </oxd-text>
        </div>
      </div>
    </div>
  </base-widget>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {freshDate, formatDate} from '@ohrm/core/util/helper/datefns';
import BaseWidget from '@/orangehrmDashboardPlugin/components/BaseWidget.vue';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';

export default {
  name: 'HolidayListWidget',

  components: {
    'base-widget': BaseWidget,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/leave/holidays',
    );
    const {$tEmpName} = useEmployeeNameTranslate();

    return {
      http,
      tEmpName: $tEmpName,
    };
  },

  data() {
    return {
      leaveList: [],
      isLoading: false,
      leavePeriod: null,
      showConfigModal: false,
    };
  },

  computed: {
    isEmpty() {
      return this.leaveList.length === 0;
    },
    emptyText() {
      return this.leavePeriod
        ? this.$t('dashboard.no_employees_are_on_leave_today')
        : this.$t('dashboard.leave_period_not_defined');
    },
  },

  beforeMount() {
    this.isLoading = true;
    const currentDate = freshDate();
    const sixMonthsLater = new Date(currentDate);
    sixMonthsLater.setMonth(sixMonthsLater.getMonth() + 12);
    this.http
      .getAll({
        limit: 10,
        fromDate: formatDate(currentDate, 'yyyy-MM-dd'),
        toDate: formatDate(sixMonthsLater, 'yyyy-MM-dd'),
      })
      .then((response) => {
        const {data} = response.data;
        this.leaveList = data;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onClickConfig() {
      this.showConfigModal = true;
    },
    onConfigModalClose() {
      this.showConfigModal = false;
    },
  },
};
</script>

<style src="./employee-on-leave-widget.scss" lang="scss" scoped></style>
