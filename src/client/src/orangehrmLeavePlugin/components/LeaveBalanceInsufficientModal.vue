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
  <oxd-dialog
    :gutters="false"
    class="orangehrm-dialog-modal"
    @update:show="onCancel"
  >
    <div class="orangehrm-dialog-header-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('leave.insufficient_leave_balance') }}
      </oxd-text>
    </div>
    <oxd-divider
      class="orangehrm-dialog-horizontal-margin orangehrm-clear-margins"
    />
    <div
      class="orangehrm-dialog-horizontal-padding orangehrm-dialog-vertical-padding"
    >
      <oxd-grid :cols="3">
        <oxd-grid-item>
          <oxd-input-group :label="$t('general.employee_name')">
            <oxd-text class="orangehrm-leave-balance-text" tag="p">
              {{ employeeName }}
            </oxd-text>
          </oxd-input-group>
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-input-group :label="$t('leave.leave_type')">
            <oxd-text class="orangehrm-leave-balance-text" tag="p">
              {{ leaveType }}
            </oxd-text>
          </oxd-input-group>
        </oxd-grid-item>
        <oxd-grid-item>
          <oxd-input-group :label="$t('leave.balance')">
            <oxd-text class="orangehrm-leave-balance-text" tag="p">
              {{ leaveBalance }}
            </oxd-text>
          </oxd-input-group>
        </oxd-grid-item>
      </oxd-grid>
    </div>
    <div class="orangehrm-container">
      <oxd-card-table
        :headers="headers"
        :items="items"
        :clickable="false"
        class="orangehrm-horizontal-padding"
        row-decorator="oxd-table-decorator-card"
      />
    </div>
    <div
      class="orangehrm-dialog-horizontal-padding orangehrm-dialog-vertical-padding"
    >
      <oxd-form-actions>
        <oxd-button
          type="submit"
          display-type="secondary"
          :label="$t('general.ok')"
          @click="onCancel"
        />
      </oxd-form-actions>
    </div>
  </oxd-dialog>
</template>

<script>
import {OxdDialog} from '@ohrm/oxd';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import useLocale from '@/core/util/composable/useLocale';

export default {
  name: 'LeaveBalanceInsufficientModal',
  components: {
    'oxd-dialog': OxdDialog,
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
    meta: {
      type: Object,
      default: () => null,
    },
  },
  emits: ['close'],
  setup() {
    const {jsDateFormat} = useDateFormat();
    const {locale} = useLocale();

    return {
      locale,
      jsDateFormat,
    };
  },
  data() {
    return {
      headers: [
        {
          title: this.$t('leave.leave_period'),
          name: 'period',
          style: {flex: 1},
        },
        {
          title: this.$t('general.date'),
          name: 'date',
          style: {flex: 1},
        },
        {
          title: this.$t('leave.available_balance'),
          name: 'balance',
          style: {flex: 1},
        },
      ],
    };
  },
  computed: {
    items() {
      if (this.data.length > 0) {
        const leavePeriods = this.data.map((item) => item.period);
        return leavePeriods.flatMap((period, index) => {
          return this.data[index].leaves.map((leave) => {
            const startDate = formatDate(
              parseDate(period.startDate),
              this.jsDateFormat,
              {locale: this.locale},
            );
            const endDate = formatDate(
              parseDate(period.endDate),
              this.jsDateFormat,
              {locale: this.locale},
            );
            const leaveDate = formatDate(
              parseDate(leave.date),
              this.jsDateFormat,
              {locale: this.locale},
            );

            return {
              period: `${startDate} - ${endDate}`,
              date: leaveDate,
              balance: leave.status?.name || leave.balance.toFixed(2),
            };
          });
        });
      }
      return [];
    },
    leaveType() {
      return this.meta?.leaveType?.name;
    },
    employeeName() {
      const employee = this.meta?.employee;
      if (employee) {
        return `${employee.firstName} ${employee.lastName}
          ${employee.terminationId ? this.$t('general.past_employee') : ''}`;
      }
      return '';
    },
    leaveBalance() {
      return this.data[0]?.balance
        ? `${parseFloat(this.data[0].balance.balance).toFixed(2)} Day(s)`
        : '0.00 Day(s)';
    },
  },
  methods: {
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>

<style src="./leave-balance-modal.scss" lang="scss" scoped></style>
