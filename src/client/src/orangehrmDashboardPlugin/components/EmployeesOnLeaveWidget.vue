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
    icon="leaveAlt"
    icon-type="svg"
    class="emp-leave-chart"
    :empty="isEmpty"
    :empty-text="emptyText"
    :loading="isLoading"
    :title="$t('dashboard.employees_on_leave_today')"
  >
    <template
      v-if="$can.update('dashboard_employees_on_leave_today_config')"
      #action
    >
      <oxd-icon
        class="orangehrm-leave-card-icon"
        name="gear-fill"
        @click="onClickConfig"
      />
    </template>
    <div v-for="leave in leaveList" :key="leave" class="orangehrm-leave-card">
      <div class="orangehrm-leave-card-profile-image">
        <img
          alt="profile picture"
          class="employee-image"
          :src="`../pim/viewPhoto/empNumber/${leave.empNumber}`"
        />
      </div>
      <div class="orangehrm-leave-card-details">
        <oxd-text tag="p" class="orangehrm-leave-card-emp-name">
          {{ leave.empName }}
        </oxd-text>
        <oxd-text
          v-if="leave.leaveType"
          tag="p"
          class="orangehrm-leave-card-leave-details"
        >
          {{ leave.leaveType }}
        </oxd-text>
      </div>
      <oxd-text tag="p" class="orangehrm-leave-card-emp-id">
        {{ leave.employeeId }}
      </oxd-text>
    </div>
  </base-widget>
  <employees-on-leave-config-modal
    v-if="showConfigModal"
    @close="onConfigModalClose"
  ></employees-on-leave-config-modal>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {freshDate, formatDate} from '@ohrm/core/util/helper/datefns';
import BaseWidget from '@/orangehrmDashboardPlugin/components/BaseWidget.vue';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import EmployeesOnLeaveConfigModal from '@/orangehrmDashboardPlugin/components/EmployeesOnLeaveConfigModal.vue';
import {OxdIcon} from '@ohrm/oxd';

export default {
  name: 'EmployeesOnLeaveWidget',

  components: {
    'oxd-icon': OxdIcon,
    'base-widget': BaseWidget,
    'employees-on-leave-config-modal': EmployeesOnLeaveConfigModal,
  },

  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/dashboard/employees/leaves',
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
    this.http
      .getAll({
        date: formatDate(freshDate(), 'yyyy-MM-dd'),
      })
      .then((response) => {
        const {data, meta} = response.data;
        this.leaveList = data.map((item) => {
          const {employee, leaveType, duration} = item;
          let _leaveType = leaveType?.name;
          if (_leaveType && duration === 'half_day_morning') {
            _leaveType += ` (${this.$t('leave.half_day_morning')})`;
          }
          if (_leaveType && duration === 'half_day_afternoon') {
            _leaveType += ` (${this.$t('leave.half_day_evening')})`;
          }
          if (_leaveType && duration === 'specify_time') {
            _leaveType += ` (${item.startTime} - ${item.endTime})`;
          }
          return {
            leaveType: _leaveType,
            empNumber: employee.empNumber,
            employeeId: employee.employeeId,
            empName: this.tEmpName(employee, {
              includeMiddle: false,
              excludePastEmpTag: false,
            }),
          };
        });
        this.leavePeriod = meta?.leavePeriodDefined;
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
